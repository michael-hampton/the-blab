<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserSession
 *
 * @author michael.hampton
 */
class UserSession
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $objUser;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    private function createCookie ($username, $password)
    {
        $days = 60;

        $value = $this->encryptCookie ($username);
        $value2 = $this->encryptCookie ($password);

        $arrUser = array('username' => $value, 'password' => $value2);

        setcookie ("blab_rememberme", json_encode ($arrUser), time () + ($days * 24 * 60 * 60 * 1000));

        return true;
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

    // Function to get the client ip address
    private function get_client_ip_server ()
    {
        $ipaddress = '';
        if ( $_SERVER['HTTP_CLIENT_IP'] )
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ( $_SERVER['HTTP_X_FORWARDED_FOR'] )
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ( $_SERVER['HTTP_X_FORWARDED'] )
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ( $_SERVER['HTTP_FORWARDED_FOR'] )
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ( $_SERVER['HTTP_FORWARDED'] )
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ( $_SERVER['REMOTE_ADDR'] )
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * 
     * @param User $objUser
     * @return type
     * @throws Exception
     */
    private function getUserSessions (User $objUser)
    {

        $arrResult = $this->db->_query ("SELECT * FROM `user_session` WHERE last_login_date > (now() - interval 30 minute) AND user_id = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }


        return count ($arrResult);
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    private function createNewUserSession (User $objUser)
    {
        $ipAddress = $this->get_client_ip_server ();

        $result = $this->db->create ("user_session", ["user_id" => $objUser->getId (), "last_login_date" => date ("Y-m-d H:i:s"), "ip_address" => $ipAddress]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     * @throws Exception
     */
    private function lockAccount ($username)
    {
        $result = $this->db->_select ('users', "username = :username", array(
            ':username' => $username,
                )
        );

        if ( empty ($result) )
        {
            return false;
        }

        $count = $result[0]['login_attempts'];

        if ( (int) $count >= 3 )
        {
            $result = $this->db->update ("users", ["is_active" => 0], "username = :username", [":username" => $username]);

            if ( $result === false )
            {
                trigger_error ("Db query failed", E_USER_WARNING);
                throw new Exception ("Db query failed");
            }

            return false;
        }

        $newCount = (int) $count + 1;

        $result = $this->db->update ("users", ["login_attempts" => $newCount], "username = :username", [":username" => $username]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            throw new Exception ("Db query failed");
        }

        return false;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    private function resetLoginAttempts (User $objUser)
    {
        $result = $this->db->update ("users", "login_attempts = 0", "uid = :userId", [":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param LoginModel $objLogin
     * @param type $username
     * @param type $password
     * @param UserFactory $objUserFactory
     * @param type $blRememberMe
     * @return boolean
     */
    public function createUserSession (LoginModel $objLogin, $username, $password, UserFactory $objUserFactory, $blRememberMe)
    {

        $objUser = $objLogin->login ($username, $password, $objUserFactory);

        if ( $objUser === false )
        {
            $this->lockAccount ($username);
            return false;
        }

        if ( (int) $this->getUserSessions ($objUser) > 3 )
        {
            return false;
        }

        if ( $this->createNewUserSession ($objUser) === false )
        {
            return false;
        }

        if ( (bool) $blRememberMe !== false )
        {
            $this->createCookie ($username, $password);
        }

        $_SESSION['user']['user_id'] = $result[0]['uid'];
        $_SESSION['user']['username'] = $result[0]['username'];

        return true;
    }

}
