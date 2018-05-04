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

    /**
     * 
     * @param type $username
     * @param type $password
     * @return boolean
     */
    public function validatePassword ($username, $password, UserFactory $objUserFactory)
    {
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
            return $objUserFactory->getUserById ($result[0]['uid']);
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

        $objUser = $this->validatePassword ($username, $password, $objUserFactory);

        if ( $objUser === false )
        {            
            return false;
        }

        return $objUser;
    }

}
