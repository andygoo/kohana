<?php
defined('SYSPATH') or die('No direct access allowed.');

class Text {

    public static function limit_words($str, $limit = 100, $end_char = NULL) {
        $limit = (int)$limit;
        $end_char = ($end_char === NULL) ? '…' : $end_char;
        
        if (trim($str) === '') return $str;
        
        if ($limit <= 0) return $end_char;
        
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $str, $matches);
        
        // Only attach the end character if the matched string is shorter
        // than the starting string.
        return rtrim($matches[0]) . (strlen($matches[0]) === strlen($str) ? '' : $end_char);
    }

    public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE) {
        $end_char = ($end_char === NULL) ? '…' : $end_char;
        
        $limit = (int)$limit;
        
        if (trim($str) === '' or mb_strlen($str, 'utf-8') <= $limit) return $str;
        
        if ($limit <= 0) return $end_char;
        
        if ($preserve_words === FALSE) return rtrim(mb_substr($str, 0, $limit, 'utf-8')) . $end_char;
        
        // Don't preserve words. The limit is considered the top limit.
        // No strings with a length longer than $limit should be returned.
        if (!preg_match('/^.{0,' . $limit . '}\s/us', $str, $matches)) return $end_char;
        
        return rtrim($matches[0]) . (strlen($matches[0]) === strlen($str) ? '' : $end_char);
    }

    public static function random($type = NULL, $length = 8) {
        if ($type === NULL) {
            $type = 'alnum';
        }
        
        switch($type) {
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'hexdec':
                $pool = '0123456789abcdef';
                break;
            case 'numeric':
                $pool = '0123456789';
                break;
            case 'nozero':
                $pool = '123456789';
                break;
            case 'distinct':
                $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                break;
            default:
                $pool = (string)$type;
                break;
        }
        
        $pool = str_split($pool, 1);
        $max = count($pool) - 1;
        
        $str = '';
        for($i = 0; $i < $length; $i++) {
            // Select a random character from the pool and add it to the string
            $str .= $pool[mt_rand(0, $max)];
        }
        
        // Make sure alnum strings contain at least one letter and one digit
        if ($type === 'alnum' and $length > 1) {
            if (ctype_alpha($str)) {
                // Add a random digit
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
            } elseif (ctype_digit($str)) {
                // Add a random letter
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
            }
        }
        
        return $str;
    }
 
    /**
	 * Reduces multiple slashes in a string to single slashes.
	 *
	 *     $str = Text::reduce_slashes('foo//bar/baz'); // "foo/bar/baz"
	 *
	 * @param   string  string to reduce slashes of
	 * @return  string
	 */
    public static function reduce_slashes($str) {
        return preg_replace('#(?<!:)//+#', '/', $str);
    }

    /**
	 * Replaces the given words with a string.
	 *
	 *     // Displays "What the #####, man!"
	 *     echo Text::censor('What the frick, man!', array(
	 *         'frick' => '#####',
	 *     ));
	 *
	 * @param   string   phrase to replace words in
	 * @param   array    words to replace
	 * @param   string   replacement string
	 * @param   boolean  replace words across word boundries (space, period, etc)
	 * @return  string
	 * @uses    UTF8::strlen
	 */
    public static function censor($str, $badwords, $replacement = '#', $replace_partial_words = TRUE) {
        foreach((array)$badwords as $key => $badword) {
            $badwords[$key] = str_replace('\*', '\S*?', preg_quote((string)$badword));
        }
        
        $regex = '(' . implode('|', $badwords) . ')';
        
        if ($replace_partial_words === FALSE) {
            // Just using \b isn't sufficient when we need to replace a badword that already contains word boundaries itself
            $regex = '(?<=\b|\s|^)' . $regex . '(?=\b|\s|$)';
        }
        
        $regex = '!' . $regex . '!ui';
        
        if (strlen($replacement) == 1) {
            $regex .= 'e';
            return preg_replace($regex, 'str_repeat($replacement, strlen(\'$1\'))', $str);
        }
        
        return preg_replace($regex, $replacement, $str);
    }

    /**
	 * Finds the text that is similar between a set of words.
	 *
	 *     $match = Text::similar(array('fred', 'fran', 'free'); // "fr"
	 *
	 * @param   array   words to find similar text of
	 * @return  string
	 */
    public static function similar(array $words) {
        // First word is the word to match against
        $word = current($words);
        
        for($i = 0, $max = strlen($word); $i < $max; ++$i) {
            foreach($words as $w) {
                // Once a difference is found, break out of the loops
                if (!isset($w[$i]) or $w[$i] !== $word[$i]) break 2;
            }
        }
        
        // Return the similar text
        return substr($word, 0, $i);
    }

    /**
     * Returns human readable sizes. Based on original functions written by
     * [Aidan Lister](http://aidanlister.com/repos/v/function.size_readable.php)
     * and [Quentin Zervaas](http://www.phpriot.com/d/code/strings/filesize-format/).
     *
     *     echo Text::bytes(filesize($file));
     *
     * @param   integer $bytes      size in bytes
     * @param   string  $force_unit a definitive unit
     * @param   string  $format     the return string format
     * @param   boolean $si         whether to use SI prefixes or IEC
     * @return  string
     */
    public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE) {
        // Format string
        $format = ($format === NULL) ? '%01.2f %s' : (string) $format;
    
        // IEC prefixes (binary)
        if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE) {
            $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
            $mod   = 1024;
        } else  {
            // SI prefixes (decimal)
            $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
            $mod   = 1000;
        }
    
        // Determine unit to use
        if (($power = array_search( (string) $force_unit, $units)) === FALSE) {
            $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
        }
    
        return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
    }
    
    public static function user_agent($agent, $value) {
        if (is_array($value)) {
            $data = array();
            foreach($value as $part) {
                // Add each part to the set
                $data[$part] = Text::user_agent($agent, $part);
            }
            
            return $data;
        }
        
        if ($value === 'browser' or $value == 'version') {
            // Extra data will be captured
            $info = array();
            
            // Load browsers
            $browsers = Kohana::config('user_agents.browser');
            
            foreach($browsers as $search => $name) {
                if (stripos($agent, $search) !== FALSE) {
                    // Set the browser name
                    $info['browser'] = $name;
                    
                    if (preg_match('#' . preg_quote($search) . '[^0-9.]*+([0-9.][0-9.a-z]*)#i', Request::$user_agent, $matches)) {
                        // Set the version number
                        $info['version'] = $matches[1];
                    } else {
                        // No version number found
                        $info['version'] = FALSE;
                    }
                    
                    return $info[$value];
                }
            }
        } else {
            // Load the search group for this type
            $group = Kohana::config('user_agents.' . $value);
            
            foreach($group as $search => $name) {
                if (stripos($agent, $search) !== FALSE) {
                    // Set the value name
                    return $name;
                }
            }
        }
        
        // The value requested could not be found
        return FALSE;
    }

    public static function fliter_content($content) {
        $content = strip_tags($content);
        $content = str_replace(array('　', '&nbsp;'), '', $content);
        $content = preg_replace('/[\s+]/', '', $content);
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        return $content;
    }

    public static function start_with($haystack, $needles) {
        $needles = is_array($needles) ? $needles : array($needles);
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) === 0) return true;
        }
        return false;
    }

    public static function end_with($haystack, $needles) {
        $needles = is_array($needles) ? $needles : array($needles);
        foreach ($needles as $needle) {
            if ($needle == substr($haystack, strlen($haystack) - strlen($needle))) return true;
        }
        return false;
    }

    public static function contains($haystack, $needles) {
        $needles = is_array($needles) ? $needles : array($needles);
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) return true;
        }
        return false;
    }
    
    public static function create_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
