<?php

class Token {

    public static function is_valid($token) {
        return !empty($token) and $token['expires'] > time() and $token['user_agent'] == sha1(Request::$user_agent);
    }

    public function generate($lifetime) {
        $expires = time() + $lifetime;
        $token = $this->_generate_token_value();
        if (!$this->user_agent) {
            // this is a new token, so we dont need to save it (yet)
            $this->user_agent = sha1(Request::$user_agent);
        } else {
            // save new token value & timestamp
            $this->save();
        }
    }

    protected function _generate_token_value() {
        do {
            $token = sha1(uniqid(Text::random('alnum', 32), TRUE));
            $m_token = Model::factory('user_tokens');
            $has_token = $m_token->has(array('token' => $token));
        } while($has_token);
        return $token;
    }
    
}