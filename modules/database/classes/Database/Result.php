<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Database_Result implements Countable, Iterator,SeekableIterator, ArrayAccess {

	protected $_query;
	protected $_result;
	protected $_total_rows  = 0;
	protected $_current_row = 0;
	protected $_as_object;

	public function __construct($result, $sql, $as_object)
	{
		$this->_result = $result;
		$this->_query = $sql;
		$this->_as_object = $as_object;
	}

	abstract public function __destruct();

	public function cached()
	{
		return new Database_Result_Cached($this->as_array(), $this->_query, $this->_as_object);
	}
	
	/**
	 * Return all of the rows in the result as an array.
	 *
	 * @param   string  column for associative keys
	 * @param   string  column for values
	 * @return  array
	 */
	public function as_array($key = NULL, $value = NULL) 
	{
		$results = array();
		if ($key === NULL AND $value === NULL) {
			foreach ($this as $row) {
				$results[] = $row;
			}
		} elseif ($key === NULL) {
			if ($this->_as_object) {
				foreach ($this as $row) {
					$results[] = $row->$value;
				}
			} else {
				foreach ($this as $row) {
					$results[] = $row[$value];
				}
			}
		} elseif ($value === NULL) {
			if ($this->_as_object) {
				foreach ($this as $row) {
					$results[$row->$key] = $row;
				}
			} else {
				foreach ($this as $row) {
					$results[$row[$key]] = $row;
				}
			}
		} else {
			if ($this->_as_object) {
				foreach ($this as $row) {
					$results[$row->$key] = $row->$value;
				}
			} else {
				foreach ($this as $row) {
					$results[$row[$key]] = $row[$value];
				}
			}
		}
		return $results;
	}

	public function get($name, $default = NULL) 
	{
		$row = $this->current();

		if ($this->_as_object) {
			if (isset($row->$name))
				return $row->$name;
		} else {
			if (isset($row[$name]))
				return $row[$name];
		}

		return $default;
	}

	/**
	 * Countable: count
	 */
	public function count() 
	{
		return $this->_total_rows;
	}

	/**
	 * ArrayAccess: offsetExists
	 */
	public function offsetExists($offset)
	{
		return ($offset >= 0 AND $offset < $this->_total_rows);
	}

	/**
	 * ArrayAccess: offsetGet
	 */
	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return NULL;

		return $this->current();
	}

	/**
	 * ArrayAccess: offsetSet
	 *
	 * @throws  Kohana_Database_Exception
	 */
	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('Database results are read-only');
	}

	/**
	 * ArrayAccess: offsetUnset
	 *
	 * @throws  Kohana_Database_Exception
	 */
	final public function offsetUnset($offset)
	{
		throw new Kohana_Exception('Database results are read-only');
	}

	/**
	 * Iterator: key
	 */
	public function key()
	{
		return $this->_current_row;
	}

	/**
	 * Iterator: next
	 */
	public function next()
	{
		++$this->_current_row;
		return $this;
	}

	/**
	 * Iterator: prev
	 */
	public function prev()
	{
		--$this->_current_row;
		return $this;
	}

	/**
	 * Iterator: rewind
	 */
	public function rewind()
	{
		$this->_current_row = 0;
		return $this;
	}

	/**
	 * Iterator: valid
	 */
	public function valid()
	{
		return $this->offsetExists($this->_current_row);
	}
}
