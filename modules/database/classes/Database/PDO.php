<?php defined('SYSPATH') OR die('No direct script access.');

class Database_PDO extends Database {

	protected function _connect() {
		if ($this->_conn) return $this->_conn;

		extract($this->_config['connection']);
		unset($this->_config['connection']);

		$attrs = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		if (!empty($persistent)) $attrs[PDO::ATTR_PERSISTENT] = TRUE;

		try {
			$this->_conn = new PDO($dsn, $username, $password, $attrs);
			$this->_conn->exec('SET NAMES utf8');
			//var_dump($this->_conn);
			return $this->_conn;
		} catch (PDOException $e) {
			throw new Kohana_Exception(':error', array(':error' => $e->getMessage()), $e->getCode());
		}
	}

	public function query($sql, $as_object = FALSE) {
		$sql_type = '';
		if (preg_match('/^SELECT/i', $sql) || preg_match('/^SHOW/i', $sql)) {
			$sql_type = 'select';
		} elseif (preg_match('/^INSERT/i', $sql)) {
			$sql_type = 'insert';
		}
		$this->connect(($sql_type=='select') ? 'slave' : 'master');

		if (Kohana::$profiling) {
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}
		
		try {
			$result = $this->_conn->query($sql);
		} catch (Exception $e) {
			if (isset($benchmark)) {
				Profiler::delete($benchmark);
			}
			throw new Kohana_Exception(':error [ :query ]',
				array(
					':error' => $e->getMessage(),
					':query' => $sql
				),
				$e->getCode());
		}

		if (Kohana::$profiling) {
			Profiler::stop($benchmark);
		}
		
		$this->last_query = $sql;
		//var_dump($this->last_query);
		
		if ($sql_type == 'select') {
			if ($as_object === FALSE) {
				$result->setFetchMode(PDO::FETCH_ASSOC);
			} elseif (is_string($as_object)) {
				$result->setFetchMode(PDO::FETCH_CLASS, $as_object);
			} else {
				$result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
			}
			
			$result = $result->fetchAll();
			return new Database_Result_Cached($result, $sql, $as_object);
		} elseif ($sql_type == 'insert') {
			return array(
			    $this->_conn->lastInsertId(),
			    $result->rowCount(),
			);
		} else {
			return $result->rowCount();
		}
	}

	public function escape($value) {
		$this->_conn OR $this->connect('slave');
		return $this->_conn->quote($value);
	}
}