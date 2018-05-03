<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountRecovery
 *
 * @author michael.hampton
 */
class AccountRecovery
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
    private $token;

    /**
     *
     * @var type 
     */
    private $selector;

    /**
     *
     * @var type 
     */
    private $objUser;

    /**
     *
     * @var type 
     */
    private $arrValidationFailures = [];

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
     * @return boolean
     */
    private function generateToken ()
    {

        $this->selector = bin2hex (openssl_random_pseudo_bytes (12));
        $this->token = bin2hex (openssl_random_pseudo_bytes (32));

        return true;
    }

    /**
     * 
     * @param type $email
     * @return type
     */
    private function validateEmailAddress ($email)
    {
        $result = $this->db->_select ("users", "email = :email", [":email" => $email]);

        $this->objUser = new User ($result[0]['uid']);

        return !empty ($result[0]['email']);
    }

    /**
     * 
     * @param type $resetPassLink
     * @return boolean
     */
    private function sendEmail ($resetPassLink)
    {
        //send reset password email
        $to = $this->objUser->getEmail ();
        $subject = "Password Update Request";
        $mailContent = 'Dear ' . $this->objUser->getFirstName () . ', 
                <br/>Recently a request was submitted to reset a password for your account. If this was a mistake, just ignore this email and nothing will happen.
                <br/>To reset your password, visit the following link: <a href="' . $resetPassLink . '">' . $resetPassLink . '</a>
                <br/><br/>Regards,
                <br/>The Blab';
        //set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        //additional headers
        $headers .= 'From: CodexWorld<sender@example.com>' . "\r\n";

        trigger_error ($mailContent, E_USER_WARNING);

        //send email
        if ( mail ($to, $subject, $mailContent, $headers) === false )
        {
            trigger_error ("Unable to send email");
            return false;
        }

        return true;
    }

    private function createForgotPasswordEntry (User $objUser)
    {
        if ( $this->generateToken () === false )
        {
            $this->arrValidationFailures[] = "Unable to create token";
            return false;
        }

        date_default_timezone_set ("Europe/London");

        $urlToEmail = 'blab.x10host.com/blab/resetPassword/resetPassword?' . http_build_query ([
                    'selector' => $this->selector,
                    'validator' => bin2hex ($this->token)
        ]);

        $expires = date ("Y-m-d H:i:s", strtotime ("+2 hours"));

        $result = $this->db->create ("account_recovery", [
            "user_id" => $objUser->getId (),
            "selector" => $this->selector,
            "token" => $this->token,
            "expires" => $expires
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        $this->sendEmail ($urlToEmail);

        return true;
    }

    /**
     * 
     * @param type $email
     * @return boolean
     */
    public function forgotPassword ($email)
    {
        if ( $this->validateEmailAddress ($email) === false )
        {
            $this->arrValidationFailures[] = "The email address is invalid";
            return false;
        }

        if ( $this->createForgotPasswordEntry ($this->objUser) === false )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $selector
     * @return boolean
     */
    private function getTokenFromDatabase ($selector)
    {
        $arrResult = $this->db->_query ("SELECT * FROM account_recovery WHERE selector = :selector AND expires >= NOW()", [":selector" => $selector]);

        if ( $arrResult === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResult[0]) || empty ($arrResult[0]['token']) )
        {
            return false;
        }

        $token = $arrResult[0]['token'];
        $this->objUser = new User ($arrResult[0]['user_id']);

        return $token;
    }

    /**
     * 
     * @param type $selector
     * @param type $validator
     * @return \User|boolean
     */
    public function verifyToken ($selector, $validator)
    {

        $token = $this->getTokenFromDatabase ($selector);

        if ( $token === false )
        {
            return false;
        }

        if ( $validator == bin2hex ($token) )
        {
            return true;
        }

        return false;
    }

    private function lockAccountRecoveryToken($selector)            
    {
         $result = $this->db->update ("account_recovery", ["is_locked" => 1], "selector = :selector", [":selector" => $selector]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $newPassword
     * @param UserFactory $objUserFactory
     * @return boolean
     */
    public function resetPasswordInDb ($newPassword, UserFactory $objUserFactory, User $objUser){
        $password = $objUserFactory->blowfishCrypt ($newPassword, 12);

        if ( $password === false )
        {
            trigger_error ("Unable to generate new password", E_USER_WARNING);
            return false;
        }

        $result = $this->db->update ("users", ["password" => $password], "uid = :userId", [":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $selector
     * @param type $newPassword
     * @param UserFactory $objUserFactory
     * @return boolean
     */
    public function resetPasswordAction ($selector, $newPassword, UserFactory $objUserFactory)
    {
        $token = $this->getTokenFromDatabase ($selector);

        if ( $token === false )
        {
            return false;
        }
        
        if($this->resetPasswordInDb ($newPassword, $objUserFactory, $this->objUser) === false){
            return false;
        }

        $result2 = $this->db->delete ("account_recovery", "selector = :selector", [":selector" => $selector]);

        if ( $result2 === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        return true;
    }

    /**
     * 
     * @return array
     */
    public function getArrValidationFailures ()
    {
        return $this->arrValidationFailures;
    }

}