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

    abstract public function get_pay_url($order_info);
    abstract public function create_sign($params);
    abstract public function verify_sign($params);
}