<?php defined('SYSPATH') or die('No direct script access.');

class LogReader {
    
    protected $_config = array(
        // 'time --- level: body in file:line'
        //"/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/"
        //'format' => '/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/',
        //'format' => '/(.*) --- ([A-Z]*): ([A-Za-z_]*Exception$) ([^:]*):? (.*)/',
        'format' => '/(.*) --- ([A-Z]*): (.*)/',
        'logs_path' => 'logs',
        'year' => NULL,
        'month' => NULL,
        'day' => NULL,
        'level' => NULL 
    );

    public function set_config(array $config = array()) {
        $this->_config = Arr::merge($this->_config, $config);
        return $this;
    }

    public function get_levels() {
        return array_keys((new ReflectionClass('Log'))->getConstants());
    }

    protected function _get_file() {
        $path = Arr::extract($this->_config, array('year','month','day'));
        $path = implode(DIRECTORY_SEPARATOR, $path);
        return Kohana::find_file('logs', $path);
    }
    
    public function get_messages($level = NULL) {
        $result = array();
        $file = $this->_get_file();
        if ($file) {
            $file = fopen($file, 'r');
            $i = 0;
            $msg = array();
            while(!feof($file)) {
                $str = trim(fgets($file));
                if (preg_match($this->_config['format'], $str, $msg)) {
                    if ($msg[2] == $level or empty($level)) {
                        $result[$i] = array(
                            'time' => explode(' ', $msg[1])[1],
                            'level' => $msg[2],
                            'exception' => '-',
                            'elevel' => '-',
                            'body' => $msg[3],
                        );
                    }
                }
                $i++;
            }
            fclose($file);
        }
        return $result;
    }
}
