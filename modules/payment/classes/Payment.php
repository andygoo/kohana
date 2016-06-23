<?php

abstract class Payment {
    
    public static $default = 'alipay';
    public static $instances = array();
    
    protected $params;
    protected $_config;

    public static function instance($name = NULL) {
        if ($name === NULL) {
            $name = Payment::$default;
        }
        
        if (isset(Payment::$instances[$name])) {
            return Payment::$instances[$name];
        }
        
        $config = Kohana::config('payment.' . $name);
        $payment_class = 'Payment_' . ucfirst($name);
        
        Payment::$instances[$name] = new $payment_class($config);
        return Payment::$instances[$name];
    }

    protected function __construct(array $config) {
        $this->_config = $config;
    }

    public function set_order_info(array $order_info) {
        $this->_config = array_merge($this->_config, $order_info);
    }

    abstract public function get_request_url();
    abstract protected function create_sign($params);
    abstract protected function set_params();
}