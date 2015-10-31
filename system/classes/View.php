<?php

class View {
    protected static $_global_data = array();
    protected $_file;
    protected $_data = array();

    public static function factory($file = NULL, array $data = NULL) {
        return new View($file, $data);
    }

    protected static function capture($view_filename, array $view_data) {
        extract($view_data, EXTR_SKIP);
        
        if (View::$_global_data) {
            extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
        }
        
        ob_start();
        try {
            include $view_filename;
        } catch(Exception $e) {
            ob_end_clean();
            throw $e;
        }
        
        return ob_get_clean();
    }

    public static function set_global($name, $value = NULL) {
        if (is_array($name)) {
            foreach($name as $key => $value) {
                View::$_global_data[$key] = $value;
            }
        } else {
            View::$_global_data[$name] = $value;
        }
    }

    public static function bind_global($key, & $value) {
        View::$_global_data[$key] = & $value;
    }

    public function __construct($file = NULL, array $data = NULL) {
        if ($file !== NULL) {
            $this->set_filename($file);
        }
        
        if ($data !== NULL) {
            $this->_data = $data + $this->_data;
        }
    }

    public function & __get($key) {
		if (array_key_exists($key, $this->_data)) {
			return $this->_data[$key];
		} elseif (array_key_exists($key, View::$_global_data)) {
			return View::$_global_data[$key];
		} else {
			throw new Kohana_Exception('View variable is not set: :var',
				array(':var' => $key));
		}
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __isset($key) {
        return (isset($this->_data[$key]) or isset(View::$_global_data[$key]));
    }

    public function __unset($key) {
        unset($this->_data[$key], View::$_global_data[$key]);
    }

    public function __toString() {
        try {
            return $this->render();
        } catch(Exception $e) {
            $error_response = Kohana_exception::_handler($e);
            return $error_response;
        }
    }

    public function set_filename($file) {
        if (($path = Kohana::find_file(Request::$theme, $file)) !== FALSE) {
            //
        } else if (($path = Kohana::find_file('views', $file)) === FALSE) {
            throw new Kohana_Exception('The requested view :file could not be found', array(
                ':file' => $file 
            ));
        }
        
        $this->_file = $path;
        return $this;
    }

    public function set($key, $value = NULL) {
        if (is_array($key)) {
            foreach($key as $name => $value) {
                $this->_data[$name] = $value;
            }
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function bind($key, & $value) {
        $this->_data[$key] = & $value;
        return $this;
    }

    public function render($file = NULL) {
        if ($file !== NULL) {
            $this->set_filename($file);
        }
        if (empty($this->_file)) {
            throw new Kohana_Exception('You must set the file to use within your view before rendering');
        }
        
        return View::capture($this->_file, $this->_data + View::$_global_data);
    }
}
