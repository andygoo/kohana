<?php

class Auth_File extends Auth {
    
    // User list
    protected $_users;

    /**
	 * Constructor loads the user list into the class.
	 */
    public function __construct($config = array()) {
        parent::__construct($config);
        
        // Load user list
        $this->_users = Arr::get($config, 'users', array());
    }

    /**
	 * Logs a user in.
	 *
	 * @param   string   $username  Username
	 * @param   string   $password  Password
	 * @param   boolean  $remember  Enable autologin (not supported)
	 * @return  boolean
	 */
    protected function _login($username, $password, $remember) {
        $password = $this->hash($password);
        
        if (isset($this->_users[$username]) and $this->_users[$username] === $password) {
            // Complete the login
            return $this->complete_login(array(
                'username' => $username 
            ));
        }
        
        // Login failed
        return FALSE;
    }

    /**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    $username  Username
	 * @return  boolean
	 */
    public function force_login($username) {
        // Complete the login
        return $this->complete_login($username);
    }

    /**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   $username  Username
	 * @return  string
	 */
    public function password($username) {
        return Arr::get($this->_users, $username, FALSE);
    }

    /**
	 * Compare password with original (plain text). Works for current (logged in) user
	 *
	 * @param   string   $password  Password
	 * @return  boolean
	 */
    public function check_password($password) {
        $username = $this->get_user();
        
        if ($username === FALSE) {
            return FALSE;
        }
        
        return ($password === $this->password($username));
    }
}

