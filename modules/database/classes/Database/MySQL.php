<?php

class Database_MySQL extends Database {

    public function _connect() {
        if ($this->_connection) return;
        
        extract($this->_config['connection']);
        unset($this->_config['connection']);
        
        try {
            if ($persistent) {
                $this->_connection = mysql_pconnect($hostname, $username, $password);
            } else {
                $this->_connection = mysql_connect($hostname, $username, $password, TRUE);
            }
        } catch(Exception $e) {
            $this->_connection = NULL;
            
            throw new Kohana_Exception(':error', array(
                ':error' => $e->getMessage() 
            ), $e->getCode());
        }
        
        if (!mysql_select_db($database, $this->_connection)) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_connection) 
            ), mysql_errno($this->_connection));
        }
        
        if (!empty($this->_config['charset'])) {
            $this->set_charset($this->_config['charset']);
        }
    }

    public function disconnect() {
        try {
            $status = TRUE;
            if (is_resource($this->_connection)) {
                if ($status = mysql_close($this->_connection)) {
                    $this->_connection = NULL;
                    parent::disconnect();
                }
            }
        } catch(Exception $e) {
            $status = !is_resource($this->_connection);
        }
        
        return $status;
    }

    public function set_charset($charset) {
        $this->_connection or $this->connect();
        
        if (!function_exists('mysql_set_charset')) {
            $status = (bool)mysql_query('SET NAMES ' . $this->escape($charset), $this->_connection);
        } else {
            $status = mysql_set_charset($charset, $this->_connection);
        }
        
        if ($status === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_connection) 
            ), mysql_errno($this->_connection));
        }
    }

    public function query($sql, $as_object = FALSE) {
        $this->_connection or $this->connect();
        
        if (($result = mysql_query($sql, $this->_connection)) === FALSE) {
            throw new Kohana_Exception(':error [ :query ]', array(
                ':error' => mysql_error($this->_connection),
                ':query' => $sql 
            ), mysql_errno($this->_connection));
        }
        
        $this->last_query = $sql;
        
        if (preg_match('/^SELECT/i', $sql)) {
            return new Database_MySQL_Result($result, $sql, $as_object);
        } elseif (preg_match('/^INSERT/i', $sql)) {
            return array(
                mysql_insert_id($this->_connection),
                mysql_affected_rows($this->_connection) 
            );
        } else {
            return mysql_affected_rows($this->_connection);
        }
    }

    public function begin($mode = NULL) {
        $this->_connection or $this->connect();
        
        if ($mode and !mysql_query("SET TRANSACTION ISOLATION LEVEL $mode", $this->_connection)) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_connection) 
            ), mysql_errno($this->_connection));
        }
        
        return (bool)mysql_query('START TRANSACTION', $this->_connection);
    }

    public function commit() {
        $this->_connection or $this->connect();
        return (bool)mysql_query('COMMIT', $this->_connection);
    }

    public function rollback() {
        $this->_connection or $this->connect();
        return (bool)mysql_query('ROLLBACK', $this->_connection);
    }

    public function escape($value) {
        $this->_connection or $this->connect();
        
        if (($value = mysql_real_escape_string((string)$value, $this->_connection)) === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysql_error($this->_connection) 
            ), mysql_errno($this->_connection));
        }
        
        return "'$value'";
    }
}