<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChatUserLog
 *
 * @author michael.hampton
 */
class ChatUserLog
{

    /**
     *
     * @var type 
     */
    private $objDb;

    public function __construct ()
    {
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

    /**
     * 
     * @param GroupChat $objGroup
     * @param type $fullname
     * @param type $username
     * @param type $data
     * @return boolean
     */
    public function createLog (GroupChat $objGroup, $fullname, $username, $data)
    {
        $result = $this->objDb->create ("reported_chat_user", [
            "reported_username" => $username,
            "reported_fullname" => $fullname,
            "reported_data" => $data,
            "group_id" => $objGroup->getId ()
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
