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

        $dbpassword = $result[0][‘password’];

        $hash = $objUserFactory->blowfishCrypt ($password, 12);
        
         if(!password_verify($password, $hash)){
        //if(crypt($password, $hash) != $hash){
            return false;
        }

        $_SESSION['user']['user_id'] = $result[0]['uid'];
        $_SESSION['user']['username'] = $result[0]['username'];

        return true;
    }

}