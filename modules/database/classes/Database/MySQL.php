<?php

class Database_MySQL extends Database {

    public function connect() {
        if ($this->_conn) return $this->_conn;
        
        extract($this->_config['connection']);
        unset($this->_config['connection']);
        
        try {
            if ($persistent) {
                $this->_conn = mysql_pconnect($hostname, $username, $password);
            } else {
                $this->_conn = mysql_connect($hostname, $username, $password, TRUE);
            }
        } catch(Exception $e) {
            $this->_conn = NULL;
            
            throw new Kohana_Exception(':error', array(
                ':error' => $e->getMessage() 
            ), $e->getCode());
        }
        
        if (!mysql_select_db($database, $this->_conn)) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_conn) 
            ), mysql_errno($this->_conn));
        }
        
        if (!empty($this->_config['charset'])) {
            $this->set_charset($this->_config['charset']);
        }
		return $this->_conn;
    }

    public function disconnect() {
        try {
            $status = TRUE;
            if (is_resource($this->_conn)) {
                if ($status = mysql_close($this->_conn)) {
                    $this->_conn = NULL;
                    parent::disconnect();
                }
            }
        } catch(Exception $e) {
            $status = !is_resource($this->_conn);
        }
        
        return $status;
    }

    public function set_charset($charset) {
        $this->_conn or $this->connect();
        
        if (!function_exists('mysql_set_charset')) {
            $status = (bool)mysql_query('SET NAMES ' . $this->escape($charset), $this->_conn);
        } else {
            $status = mysql_set_charset($charset, $this->_conn);
        }
        
        if ($status === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_conn) 
            ), mysql_errno($this->_conn));
        }
    }

    public function query($sql, $as_object = FALSE) {
        $this->_conn or $this->connect();
        
        if (($result = mysql_query($sql, $this->_conn)) === FALSE) {
            throw new Kohana_Exception(':error [ :query ]', array(
                ':error' => mysql_error($this->_conn),
                ':query' => $sql 
            ), mysql_errno($this->_conn));
        }
        
        $this->last_query = $sql;
        
        if (preg_match('/^SELECT/i', $sql)) {
            return new Database_MySQL_Result($result, $sql, $as_object);
        } elseif (preg_match('/^INSERT/i', $sql)) {
            return array(
                mysql_insert_id($this->_conn),
                mysql_affected_rows($this->_conn) 
            );
        } else {
            return mysql_affected_rows($this->_conn);
        }
    }

    public function begin($mode = NULL) {
        $this->_conn or $this->connect();
        
        if ($mode and !mysql_query("SET TRANSACTION ISOLATION LEVEL $mode", $this->_conn)) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_conn) 
            ), mysql_errno($this->_conn));
        }
        
        return (bool)mysql_query('START TRANSACTION', $this->_conn);
    }

    public function commit() {
        $this->_conn or $this->connect();
        return (bool)mysql_query('COMMIT', $this->_conn);
    }

    public function rollback() {
        $this->_conn or $this->connect();
        return (bool)mysql_query('ROLLBACK', $this->_conn);
    }

    public function escape($value) {
        $this->_conn or $this->connect();
        
        if (($value = mysql_real_escape_string((string)$value, $this->_conn)) === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_conn) 
            ), mysql_errno($this->_conn));
        }
        
        return "'$value'";
    }
}