<?php

class View {
    public static $_smarty_is_loaded;
    public static $_global_bound_variables = array();
    
    protected static $_global_data = array();
    protected $_file;
    protected $_data = array();

    public static function factory($file = NULL, array $data = NULL) {
        if (self::is_smarty_template($file)) {
            // backwards compatibility - translate smarty:template to template.tpl
            if (substr($file, 0, 7) == 'smarty:') {
                if (strlen($file) == 7) {
                    $file = NULL;
                } else {
                    $file = substr($file, 7) . '.tpl';
                }
            }
            return Smarty_View::factory($file, $data);
        } else {
            //return parent::factory($file, $data);
            return new View($file, $data);
        }
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

    /**
     * Sets a global variable, similar to [View::set], except that the
     * variable will be accessible to all views.
     *
     *     View::set_global($name, $value);
     *
     * @param   string  variable name or an array of variables
     * @param   mixed   value
     * @return  void
     */
    public static function set_global($key, $value = NULL) {
        if (is_array($key)) {
            foreach($key as $key2 => $value) {
                View::$_global_data[$key2] = $value;
                if (self::$_smarty_is_loaded) {
                    Smarty_View::smarty_prototype()->assignGlobal($key2, $value);
                }
            }
        } else {
            View::$_global_data[$key] = $value;
            if (self::$_smarty_is_loaded) {
                Smarty_View::smarty_prototype()->assignGlobal($key, $value);
            }
        }
    }

    /**
     * Assigns a global variable by reference, similar to [View::bind], except
     * that the variable will be accessible to all views.
     *
     *     View::bind_global($key, $value);
     *
     * @param   string  variable name
     * @param   mixed   referenced variable
     * @return  void
     */
    public static function bind_global($key, & $value) {
        View::$_global_data[$key] = &$value;
        View::$_global_bound_variables[$key] = TRUE;
        if (self::$_smarty_is_loaded) {
            Smarty::$global_tpl_vars[$key] = new Smarty_variable($value);
            Smarty::$global_tpl_vars[$key]->value = &$value;
        }
    }
    
    
    public function __construct($file = NULL, array $data = NULL) {
        if (self::is_smarty_template($file)) {
            throw new Kohana_Exception('Cannot initialise Smarty template :tpl as new View; use View::factory instead', array(
                ':tpl' => $file 
            ));
        }
        //return parent::__construct($file, $data);

        if ($file !== NULL) {
            $this->set_filename($file);
        }
        
        if ($data !== NULL) {
            $this->_data = $data + $this->_data;
        }
    }

    /**
     * Identify Smarty template file
     *
     * @param   string  template filename
     * @return  bool    TRUE iff file appears to be a Smarty template
     */
    public static function is_smarty_template($file) {
        return substr($file, -4, 4) == '.tpl' || substr($file, 0, 7) == 'smarty:';
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
            $error_response = Kohana_exception::handler($e);
            return $error_response;
        }
    }
    
    public function set_filename($file) {
        if (self::is_smarty_template($file)) {
            throw new Kohana_Exception('Cannot use set_filename to initialise Smarty template :tpl; use View::factory instead',
            array(':tpl' => $file));
        }
        
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
