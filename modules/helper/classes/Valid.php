<?php

class Valid {

    public static function min_length($value, $length) {
        return mb_strlen($value, 'utf-8') >= $length;
    }

    public static function max_length($value, $length) {
        return mb_strlen($value, 'utf-8') <= $length;
    }

    public static function exact_length($value, $length) {
        if (is_array($length)) {
            foreach($length as $strlen) {
                if (mb_strlen($value, 'utf-8') === $strlen) return TRUE;
            }
            return FALSE;
        }
        return mb_strlen($value, 'utf-8') === $length;
    }

    public static function email($email, $strict = FALSE) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function phone($phone) {
        return preg_match('/^1[3458][0-9]{9}$/', $phone);
    }
    
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function date($str) {
        return (strtotime($str) !== FALSE);
    }
    
    /**
	 * Validate an IP.
	 *
	 * @param   string  $ip             IP address
	 * @param   boolean $allow_private  allow private IP networks
	 * @return  boolean
	 */
    public static function ip($ip, $allow_private = TRUE) {
        // Do not allow reserved addresses
        $flags = FILTER_FLAG_NO_RES_RANGE;
        
        if ($allow_private === FALSE) {
            // Do not allow private or reserved addresses
            $flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
        }
        
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, $flags);
    }

    /**
	 * Validates a credit card number, with a Luhn check if possible.
	 *
	 * @param   integer         $number credit card number
	 * @param   string|array    $type   card type, or an array of card types
	 * @return  boolean
	 * @uses    Valid::luhn
	 */
    public static function credit_card($number, $type = NULL) {
        // Remove all non-digit characters from the number
        if (($number = preg_replace('/\D+/', '', $number)) === '') return FALSE;
        
        if ($type == NULL) {
            // Use the default type
            $type = 'default';
        } elseif (is_array($type)) {
            foreach($type as $t) {
                // Test each type for validity
                if (Valid::credit_card($number, $t)) return TRUE;
            }
            
            return FALSE;
        }
        
        $cards = Kohana::config('credit_cards');
        
        // Check card type
        $type = strtolower($type);
        
        if (!isset($cards[$type])) return FALSE;
        
        // Check card number length
        $length = strlen($number);
        
        // Validate the card length by the card type
        if (!in_array($length, preg_split('/\D+/', $cards[$type]['length']))) return FALSE;
        
        // Check card number prefix
        if (!preg_match('/^' . $cards[$type]['prefix'] . '/', $number)) return FALSE;
        
        // No Luhn check required
        if ($cards[$type]['luhn'] == FALSE) return TRUE;
        
        return Valid::luhn($number);
    }

    /**
	 * Validate a number against the [Luhn](http://en.wikipedia.org/wiki/Luhn_algorithm)
	 * (mod10) formula.
	 *
	 * @param   string  $number number to check
	 * @return  boolean
	 */
    public static function luhn($number) {
        // Force the value to be a string as this method uses string functions.
        // Converting to an integer may pass PHP_INT_MAX and result in an error!
        $number = (string)$number;
        
        if (!ctype_digit($number)) {
            // Luhn can only be used on numbers!
            return FALSE;
        }
        
        // Check number length
        $length = strlen($number);
        
        // Checksum of the card number
        $checksum = 0;
        
        for($i = $length - 1; $i >= 0; $i -= 2) {
            // Add up every 2nd digit, starting from the right
            $checksum += substr($number, $i, 1);
        }
        
        for($i = $length - 2; $i >= 0; $i -= 2) {
            // Add up every 2nd digit doubled, starting from the right
            $double = substr($number, $i, 1) * 2;
            
            // Subtract 9 from the double where value is greater than 10
            $checksum += ($double >= 10) ? ($double - 9) : $double;
        }
        
        // If the checksum is a multiple of 10, the number is valid
        return ($checksum % 10 === 0);
    }

    /**
	 * Checks whether a string consists of alphabetical characters only.
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
    public static function alpha($str, $utf8 = FALSE) {
        $str = (string)$str;
        
        if ($utf8 === TRUE) {
            return (bool)preg_match('/^\pL++$/uD', $str);
        } else {
            return ctype_alpha($str);
        }
    }

    /**
	 * Checks whether a string consists of alphabetical characters and numbers only.
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
    public static function alpha_numeric($str, $utf8 = FALSE) {
        if ($utf8 === TRUE) {
            return (bool)preg_match('/^[\pL\pN]++$/uD', $str);
        } else {
            return ctype_alnum($str);
        }
    }

    /**
	 * Checks whether a string consists of alphabetical characters, numbers, underscores and dashes only.
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
    public static function alpha_dash($str, $utf8 = FALSE) {
        if ($utf8 === TRUE) {
            $regex = '/^[-\pL\pN_]++$/uD';
        } else {
            $regex = '/^[-a-z0-9_]++$/iD';
        }
        
        return (bool)preg_match($regex, $str);
    }

    /**
	 * Checks whether a string consists of digits only (no dots or dashes).
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
    public static function digit($str, $utf8 = FALSE) {
        if ($utf8 === TRUE) {
            return (bool)preg_match('/^\pN++$/uD', $str);
        } else {
            return (is_int($str) and $str >= 0) or ctype_digit($str);
        }
    }

    /**
	 * Checks whether a string is a valid number (negative and decimal numbers allowed).
	 *
	 * Uses {@link http://www.php.net/manual/en/function.localeconv.php locale conversion}
	 * to allow decimal point to be locale specific.
	 *
	 * @param   string  $str    input string
	 * @return  boolean
	 */
    public static function numeric($str) {
        // Get the decimal point for the current locale
        list($decimal) = array_values(localeconv());
        
        // A lookahead is used to make sure the string contains at least one digit (before or after the decimal point)
        return (bool)preg_match('/^-?+(?=.*[0-9])[0-9]*+' . preg_quote($decimal) . '?+[0-9]*+$/D', (string)$str);
    }

    /**
	 * Tests if a number is within a range.
	 *
	 * @param   string  $number number to check
	 * @param   integer $min    minimum value
	 * @param   integer $max    maximum value
	 * @param   integer $step   increment size
	 * @return  boolean
	 */
    public static function range($number, $min, $max, $step = NULL) {
        if ($number < $min or $number > $max) {
            // Number is outside of range
            return FALSE;
        }
        
        if (!$step) {
            // Default to steps of 1
            $step = 1;
        }
        
        // Check step requirements
        return (($number - $min) % $step === 0);
    }

    /**
	 * Checks if a string is a proper decimal format. Optionally, a specific
	 * number of digits can be checked too.
	 *
	 * @param   string  $str    number to check
	 * @param   integer $places number of decimal places
	 * @param   integer $digits number of digits
	 * @return  boolean
	 */
    public static function decimal($str, $places = 2, $digits = NULL) {
        if ($digits > 0) {
            // Specific number of digits
            $digits = '{' . ((int)$digits) . '}';
        } else {
            // Any number of digits
            $digits = '+';
        }
        
        // Get the decimal point for the current locale
        list($decimal) = array_values(localeconv());
        
        return (bool)preg_match('/^[+-]?[0-9]' . $digits . preg_quote($decimal) . '[0-9]{' . ((int)$places) . '}$/D', $str);
    }

    public static function color($str) {
        return (bool)preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
    }
}
