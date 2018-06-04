<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatingReaction
 *
 * @author michael.hampton
 */
class DatingReaction
{

    /**
     *
     * @var type 
     */
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
     * @param User $objSender
     * @param User $objRecipient
     * @return boolean
     */
    public function saveReaction (User $objSender, User $objRecipient)
    {
        $result = $this->db->create ("dating_reaction", ["user_id" => $objSender->getId (), "recipient_id" => $objRecipient->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param User $objRecipient
     * @return type
     * @throws Exception
     */
    public function checkIfUserReacted (User $objUser, User $objRecipient)
    {
        $arrResult = $this->db->_select ("dating_reaction", "user_id = :userId AND recipient_id = :recipientId", [":userId" => $objUser->getId (), ":recipientId" => $objRecipient->getId ()]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }
        
        return !empty($arrResult);
    }

}
