<?php

class Model {
    protected $db;
    protected $_table_name;
    protected $_primary_key = 'id';

    public static function factory($name, $db = 'default') {
        $_name = implode('_', array_map('ucfirst', explode('_', $name)));
        $class = 'Model_' . $_name;
        if (class_exists($class)) {
            return new $class($db);
        } else {
            $config = array();
            $config['db'] = $db;
            $config['tb'] = $name;
            return new Model($config);
        }
    }

    public function __construct($config = 'default') {
        if (is_array($config)) {
            $db = $config['db'];
            $this->_table_name = $config['tb'];
        } else {
            $db = $config;
        }
        $this->db = Database::instance($db);
    }
    
	public function __call($method, $args) {
		return call_user_func_array(array($this->db, $method), $args);
	}

    public function count($where = null) {
        return $this->db->query('SELECT COUNT(1) AS total FROM ' . $this->_table_name . $this->where_clause($where))->get('total');
    }

    public function count_distinct($column, $where = null) {
        return $this->db->query('SELECT COUNT(distinct ' . $column . ') AS total FROM ' . $this->_table_name . $this->where_clause($where))->get('total');
    }
    
    public function select($offset, $size, $where = null, $columns = '*') {
        $where['LIMIT'] = " $offset, $size ";
        return $this->db->select($this->_table_name, $columns, $this->where_clause($where));
    }

    public function getAll($where = null, $columns = '*') {
        return $this->db->select($this->_table_name, $columns, $this->where_clause($where));
    }

    public function insert($data) {
        return $this->db->insert($this->_table_name, $data);
    }
    
    public function replace_into($data) {
        return $this->db->replace_into($this->_table_name, $data);
    }

    public function update($data, $where) {
        return $this->db->update($this->_table_name, $data, $this->where_clause($where));
    }

    public function delete($where) {
        return $this->db->query('DELETE FROM ' . $this->_table_name . $this->where_clause($where));
    }

    public function getRow($where) {
        return $this->db->query('SELECT * FROM ' . $this->_table_name . $this->where_clause($where))->current();
    }
    
    public function updateById($data, $id) {
        return $this->update($data, array($this->_primary_key => $id));
    }
    
    public function deleteById($id) {
        return $this->delete(array($this->_primary_key => $id));
    }
    
    public function getRowById($id) {
        return $this->getRow(array($this->_primary_key => $id));
    }

    public function has($where) {
        return $this->db->query('SELECT EXISTS(SELECT 1 FROM ' . $this->_table_name . $this->where_clause($where) . ') AS has')->get('has') === '1';
    }

    public function max($column, $where = null) {
        return $this->db->query('SELECT MAX(' . $column . ') AS max FROM ' . $this->_table_name . $this->where_clause($where))->get('max');
    }

    public function min($column, $where = null) {
        return $this->db->query('SELECT MIN(' . $column . ') AS min FROM ' . $this->_table_name . $this->where_clause($where))->get('min');
    }

    public function avg($column, $where = null) {
        return $this->db->query('SELECT AVG(' . $column . ') AS avg FROM ' . $this->_table_name . $this->where_clause($where))->get('avg');
    }

    public function sum($column, $where = null) {
        return $this->db->query('SELECT SUM(' . $column . ') AS sum FROM ' . $this->_table_name . $this->where_clause($where))->get('sum');
    }

    public function incr($field, $step, $where) {
        return $this->db->query('UPDATE ' . $this->_table_name . " SET $field=$field+$step " . $this->where_clause($where));
    }

    public function decr($field, $step, $where) {
        return $this->incr($field, -$step, $where);
    }

    public function list_tables() {
        return $this->db->query('SHOW TABLES');
    }
    
    public function list_columns($table_name = NULL) {
        if (empty($table_name)) {
            $table_name = $this->_table_name;
        }
        return $this->db->query('SHOW FULL COLUMNS FROM ' . $table_name);
    }

    public function desc_table($table_name = NULL) {
        if (empty($table_name)) {
            $table_name = $this->_table_name;
        }
        return $this->db->query('SHOW CREATE TABLE ' . $table_name);
    }
            
    protected function where_clause($where) {
        $where_clause = '';
        if (is_array($where)) {
            $wheres = array();
            foreach($where as $key => $value) {
                if (in_array(strtoupper($key), array('GROUP', 'ORDER', 'LIMIT'))) continue;
                
                $column_op = explode('|', $key);
                $column = $column_op[0];
                $op = isset($column_op[1]) ? $column_op[1] : '';
                if ($op != '') {
                    if ($op == '!') {
                        if (is_array($value)) {
                            $value = array_map(array($this->db, 'escape'), $value);
                            $wheres[] = $column . ' NOT IN (' . implode(',', $value) . ')';
                        } elseif (is_null($value)) {
                            $wheres[] = $column . ' IS NOT NULL';
                        } else {
                            $wheres[] = $column . ' != ' . $this->db->escape($value);
                        }
                    } else {
                        $wheres[] = $column . ' ' . $op . ' ' . $this->db->escape($value);
                    }
                } else {
                    if (is_array($value)) {
                        $value = array_map(array($this->db, 'escape'), $value);
                        $wheres[] = $column . ' IN (' . implode(',', $value) . ')';
                    } elseif (is_null($value)) {
                        $wheres[] = $column . ' IS NULL';
                    } else {
                        $wheres[] = $column . ' = ' . $this->db->escape($value);
                    }
                }
            }
            if (!empty($wheres)) {
                $where_clause .= ' WHERE ' . implode(' AND ', $wheres);
            }
            if (isset($where['GROUP'])) $where_clause .= ' GROUP BY ' . $where['GROUP'];
            if (isset($where['ORDER'])) $where_clause .= ' ORDER BY ' . $where['ORDER'];
            if (isset($where['LIMIT'])) $where_clause .= ' LIMIT ' . $where['LIMIT'];
        } else {
            $where_clause = $where;
        }
        return $where_clause;
    }
}