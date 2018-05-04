<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GDPR
 *
 * @author michael.hampton
 */
class GDPR
{

    /**
     *
     * @var type 
     */
    private $db;

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     * @throws Exception
     */
    public function checkUser (User $objUser)
    {
        $arrResult = $this->db->_select ("gdpr", "user_id = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }

        if ( empty ($arrResult[0]) )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function saveUser (User $objUser)
    {
        $result = $this->db->create ("gdpr", ["user_id" => $objUser->getId (), "date_read" => date ("Y-m-d H:i:s")]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
