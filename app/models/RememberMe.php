<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RememberMe
 *
 * @author michael.hampton
 */
class RememberMe
{

    /**
     *
     * @var type 
     */
    private $key;

    /**
     *
     * @var type 
     */
    private $db;

    private $username;

    private $userId;

    /**
     * 
     * @param type $key
     */
    public function __construct ($key)
    {
        $this->key = $key;

        $this->db = new Database();
        $this->db->connect ();
    }

    public function getUsername() {
        return $this->username;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * 
     * @return boolean
     * @throws Exception
     */
    public function auth ()
    {
        // Check if remeber me cookie is present
        if ( !isset ($_COOKIE["blab"]) || empty ($_COOKIE["blab"]) )
        {
            return false;
        }

        // Decode cookie value
        if ( !$cookie = @json_decode ($_COOKIE["blab"], true) )
        {
            return false;
        }
        
        // Check all parameters
        if ( !(isset ($cookie['user']) || isset ($cookie['token']) || isset ($cookie['signature'])) )
        {
            return false;
        }
        
        $var = $cookie['user'] . $cookie['token'];

        // Check Signature
        if ( !$this->verify ($var, $cookie['signature']) )
        {
            throw new Exception ("Cokies has been tampared with");
        }
        
        try {
            // Check Database
            $info = $this->getUserByToken ($cookie['token']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }


        if ( !$info )
        {
            return false; // User must have deleted accout
        }

        // Check User Data
        if ( empty ($info) || !is_array ($info) )
        {
            throw new Exception ("User Data corrupted");
        }


        // Verify Token
        if ( $info['session_token'] !== $cookie['token'] )
        {
            throw new Exception ("System Hijacked or User use another browser");
        }

        if ( session_id () === "" )
        {
            session_start ();
        }

        $this->username = $info['username'];
        $this->userId = $info['uid'];

        /**
         * Important
         * To make sure the cookie is always change
         * reset the Token information
         */
         $this->remember ($info['user']);
         return true;
    }

    /**
     * 
     * @param type $token
     * @return boolean
     */
    private function getUserByToken ($token)
    {
        $result = $this->db->_select ("users", "session_token = :token", [":token" => $token]);
        
        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            throw new Exception ("Db query to get user token failed");
        }

        if ( empty ($result[0]) )
        {
            return false;
        }

        return $result[0];
    }

    /**
     * 
     * @param type $data
     * @param type $hash
     * @return type
     */
    public function verify ($data, $hash)
    {
        $rand = substr ($hash, 0, 4);
        return $this->hash ($data, $rand) === $hash;
    }

    /**
     * 
     * @param type $value
     * @param type $rand
     * @return type
     */
    private function hash ($value, $rand = null)
    {
        $rand = $rand === null ? $this->getRand (4) : $rand;
        return $rand . bin2hex (hash_hmac ('sha256', $value . $rand, $this->key, true));
    }

    /**
     * 
     * @param type $length
     * @return type
     */
    private function getRand ($length)
    {
        switch (true) {
            case function_exists ("mcrypt_create_iv") :
                $r = mcrypt_create_iv ($length, MCRYPT_DEV_URANDOM);
                break;
            case function_exists ("openssl_random_pseudo_bytes") :
                $r = openssl_random_pseudo_bytes ($length);
                break;
            case is_readable ('/dev/urandom') : // deceze
                $r = file_get_contents ('/dev/urandom', false, null, 0, $length);
                break;
            default :
                $i = 0;
                $r = "";
                while ($i ++ < $length) {
                    $r .= chr (mt_rand (0, 255));
                }
                break;
        }
        return substr (bin2hex ($r), 0, $length);
    }

    /**
     * 
     * @param type $username
     * @return boolean
     */
    public function remember ($username)
    {
        $token = $this->getRand (64);

        $cookie = [
            "user" => $username,
            "token" => $token,
            "signature" => null
        ];
        $cookie['signature'] = $this->hash ($cookie['user'] . $cookie['token']);
        $encoded = json_encode ($cookie);

        // Add User to database
        $result = $this->db->update ("users", ["session_token" => $token], "username = :username", [":username" => $username]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        /**
         * Set Cookies
         * In production enviroment Use
         * setcookie("auto", $encoded, time() + $expiration, "/~root/",
         * "example.com", 1, 1);
         */
        $domain = $_SERVER["HTTP_HOST"];
        $cookie_expire = time () + (10 * 365 * 24 * 60 * 60);

        setcookie ('blab', $encoded, $cookie_expire, '/', $domain, FALSE, TRUE);
    }

}
