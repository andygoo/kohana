<?php

class Database_MySQLi extends Database {

    public function connect() {
        if ($this->_conn) return $this->_conn;
        
        extract($this->_config['connection']);
        unset($this->_config['connection']);
        
        try {
            $this->_conn = mysqli_connect($hostname, $username, $password, $database);
        } catch(Exception $e) {
            $this->_conn = NULL;
            
            throw new Kohana_Exception(':error', array(
                ':error' => $e->getMessage() 
            ), $e->getCode());
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
                if ($status = mysqli_close($this->_conn)) {
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
        
        if (!function_exists('mysqli_set_charset')) {
            $status = (bool)mysqli_query($this->_conn, 'SET NAMES ' . $this->escape($charset));
        } else {
            $status = mysqli_set_charset($this->_conn, $charset);
        }
        
        if ($status === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysqli_error($this->_conn) 
            ), mysqli_errno($this->_conn));
        }
    }

    public function query($sql, $as_object = FALSE) {
        $this->_conn or $this->connect();
        
        if (($result = mysqli_query($this->_conn, $sql)) === FALSE) {
            throw new Kohana_Exception(':error [ :query ]', array(
                ':error' => mysqli_error($this->_conn),
                ':query' => $sql 
            ), mysqli_errno($this->_conn));
        }
        
        $this->last_query = $sql;
        
        if (preg_match('/^SELECT/i', $sql)) {
            return new Database_MySQLi_Result($result, $sql, $as_object);
        } elseif (preg_match('/^INSERT/i', $sql)) {
            return array(
                mysqli_insert_id($this->_conn),
                mysqli_affected_rows($this->_conn) 
            );
        } else {
            return mysqli_affected_rows($this->_conn);
        }
    }
    
    public function escape($value) {
        $this->_conn or $this->connect();
        
        if (($value = mysqli_real_escape_string($this->_conn, (string)$value)) === FALSE) {
            throw new Kohana_Exception(':error', array(
                ':error' => mysqli_error($this->_conn) 
            ), mysqli_errno($this->_conn));
        }
        
        return "'$value'";
    }
}