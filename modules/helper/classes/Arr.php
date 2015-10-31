<?php
defined('SYSPATH') or die('No direct access allowed.');

class Arr {

    public static function is_assoc(array $array) {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    public static function path($array, $path, $default = NULL) {
        if (array_key_exists($path, $array)) {
            return $array[$path];
        }
        
        $delimiter = '.';
        $path = trim($path, "{$delimiter}* ");
        $keys = explode($delimiter, $path);
        
        do {
            $key = array_shift($keys);
            
            if (ctype_digit($key)) {
                $key = (int)$key;
            }
            
            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        // Dig down into the next part of the path
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } elseif ($key === '*') {
                // Handle wildcards
                

                $values = array();
                foreach($array as $arr) {
                    $value = Arr::path($arr, implode('.', $keys));
                    if ($value) {
                        $values[] = $value;
                    }
                }
                
                if ($values) {
                    return $values;
                } else {
                    break;
                }
            } else {
                break;
            }
        } while($keys);
        
        return $default;
    }

    public static function range($step = 10, $max = 100) {
        if ($step < 1) return array();
        
        $array = array();
        for($i = $step; $i <= $max; $i += $step) {
            $array[$i] = $i;
        }
        
        return $array;
    }

    public static function get($array, $key, $default = NULL) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function extract($array, array $keys, $default = NULL) {
        $found = array();
        foreach($keys as $key) {
            $found[$key] = isset($array[$key]) ? $array[$key] : $default;
        }
        
        return $found;
    }
    
    /**
     * Retrieves muliple single-key values from a list of arrays.
     *
     *     // Get all of the "id" values from a result
     *     $ids = Arr::pluck($result, 'id');
     *
     * [!!] A list of arrays is an array that contains arrays, eg: array(array $a, array $b, array $c, ...)
     *
     * @param   array   $array  list of arrays to check
     * @param   string  $key    key to pluck
     * @return  array
     */
    public static function pluck($array, $key)
    {
        $values = array();
    
        foreach ($array as $row)
        {
            if (isset($row[$key]))
            {
                // Found a value in this row
                $values[] = $row[$key];
            }
        }
    
        return $values;
    }

    /**
     * Adds a value to the beginning of an associative array.
     *
     *     // Add an empty value to the start of a select list
     *     Arr::unshift($array, 'none', 'Select a value');
     *
     * @param   array   $array  array to modify
     * @param   string  $key    array key name
     * @param   mixed   $val    array value
     * @return  array
     */
    public static function unshift( array & $array, $key, $val)
    {
        $array = array_reverse($array, TRUE);
        $array[$key] = $val;
        $array = array_reverse($array, TRUE);
    
        return $array;
    }
    
    public static function diff($arr_1, $arr_2) {
        $arr_2 = array_flip($arr_2);
        foreach($arr_1 as $key => $value) {
            if (isset($arr_2[$value])) {
                unset($arr_1[$key]);
            }
        }
        
        return $arr_1;
    }

    public static function map($callback, $array) {
        foreach($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = Arr::map($callback, $val);
            } else {
                $array[$key] = call_user_func($callback, $val);
            }
        }
        
        return $array;
    }

    public static function merge(array $a1, array $a2) {
        $result = array();
        for($i = 0, $total = func_num_args(); $i < $total; $i++) {
            // Get the next array
            $arr = func_get_arg($i);
            
            // Is the array associative?
            $assoc = Arr::is_assoc($arr);
            
            foreach($arr as $key => $val) {
                if (isset($result[$key])) {
                    if (is_array($val) && is_array($result[$key])) {
                        if (Arr::is_assoc($val)) {
                            // Associative arrays are merged recursively
                            $result[$key] = Arr::merge($result[$key], $val);
                        } else {
                            // Find the values that are not already present
                            $diff = array_diff($val, $result[$key]);
                            
                            // Indexed arrays are merged to prevent duplicates
                            $result[$key] = array_merge($result[$key], $diff);
                        }
                    } else {
                        if ($assoc) {
                            // Associative values are replaced
                            $result[$key] = $val;
                        } elseif (!in_array($val, $result, TRUE)) {
                            // Indexed values are added only if they do not yet exist
                            $result[] = $val;
                        }
                    }
                } else {
                    // New values are added
                    $result[$key] = $val;
                }
            }
        }
        
        return $result;
    }

    public static function overwrite($array1, $array2) {
        foreach(array_intersect_key($array2, $array1) as $key => $value) {
            $array1[$key] = $value;
        }
        
        if (func_num_args() > 2) {
            foreach(array_slice(func_get_args(), 2) as $array2) {
                foreach(array_intersect_key($array2, $array1) as $key => $value) {
                    $array1[$key] = $value;
                }
            }
        }
        
        return $array1;
    }

    public static function flatten($array) {
        $flat = array();
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $flat += Arr::flatten($value);
            } else {
                $flat[$key] = $value;
            }
        }
        return $flat;
    }

    public static function rotate($source_array, $keep_keys = TRUE) {
        $new_array = array();
        foreach($source_array as $key => $value) {
            $value = ($keep_keys === TRUE) ? $value : array_values($value);
            foreach($value as $k => $v) {
                $new_array[$k][$key] = $v;
            }
        }
        
        return $new_array;
    }

    public static function toxml($data, $root = 'data', $xml = null) {
        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$root/>");
        }
        
        foreach($data as $key => $value) {
            if (is_array($value)) {
                $node = $xml->addChild($key);
                self::toxml($value, $root, $node);
            } else {
                $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                $xml->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }
}
