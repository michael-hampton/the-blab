<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author michael.hampton
 */
class LoginModel
{

    private $db;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();

        $this->db->connect ();
    }

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    /**
     * Encrypt cookie
     * @param type $value
     * @return type
     */
    private function encryptCookie ($value)
    {
        $key = 'youkey';
        $newvalue = base64_encode (mcrypt_encrypt (MCRYPT_RIJNDAEL_256, md5 ($key), $value, MCRYPT_MODE_CBC, md5 (md5 ($key))));
        return( $newvalue );
    }

    /**
     * Decrypt cookie
     * @param type $value
     * @return type#
     */
    public function decryptCookie ($value)
    {
        $key = 'youkey';
        $newvalue = rtrim (mcrypt_decrypt (MCRYPT_RIJNDAEL_256, md5 ($key), base64_decode ($value), MCRYPT_MODE_CBC, md5 (md5 ($key))), "\0");
        return( $newvalue );
    }

    private function lockAccount($username)
    {
        $result = $this->db->_select ('users', "username = :username", array(
            ':username' => $username,
                )
        );

        if ( empty ($result))
        {
            return false;
        }

        $count = $result[0]['login_attempts'];

        if((int)$count >= 3){
            $result = $this->db->update ("users", ["is_active" => 0], "username = :username", [":username" => $username]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            throw new Exception("Db query failed");
        }

        return false;

       } 

       $newCount = (int)$count + 1;

       $result = $this->db->update ("users", ["login_attempts" => $newCount], "username = :username", [":username" => $username]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            throw new Exception("Db query failed");
        }

        return false;
    }

    /**
     * 
     * @param type $username
     * @param type $password
     * @param UserFactory $objUserFactory
     * @return boolean
     */
    public function login ($username, $password, UserFactory $objUserFactory)
    {
        if ( session_id () === "" )
        {
            session_start ();
        }

        if ( trim ($username) === "" || !is_string ($username) )
        {
            $this->validationFailures[] = "Username cannot be empty";
            return false;
        }

        if ( trim ($password) === "" || !is_string ($password) )
        {
            $this->validationFailures[] = "Password cannot be empty";
            return false;
        }

        $result = $this->db->_select ('users', "username = :username AND is_active = 1", array(
            ':username' => $username,
                )
        );

        if ( empty ($result[0]) )
        {
            return false;
        }


        $test = $result[0]['password'];

        if ( crypt ($password, $test) == $test )
        {
            // password is correct
            //die('good');
        }
        else
        {
            return false;
        }

           try {
            $days = 60;

            $value = $this->encryptCookie ($username);
            $value2 = $this->encryptCookie ($password);

            $arrUser = array('username' => $value, 'password' => $value2);

            setcookie ("blab_rememberme", json_encode ($arrUser), time () + ($days * 24 * 60 * 60 * 1000));
           } catch(Exception $e) {

            }
        

        $_SESSION['user']['user_id'] = $result[0]['uid'];
        $_SESSION['user']['username'] = $result[0]['username'];

        return true;
    }

}