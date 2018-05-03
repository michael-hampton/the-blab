<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notification
 *
 * @author michael.hampton
 */
class Notification
{

    /**
     *
     * @var type 
     */
    private $dateAdded;

    /**
     *
     * @var type 
     */
    private $message;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $hasRead;

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
     * @return type
     */
    public function getDateAdded ()
    {
        return $this->dateAdded;
    }

    /**
     * 
     * @return type
     */
    public function getMessage ()
    {
        return $this->message;
    }

    /**
     * 
     * @return type
     */
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @return type
     */
    public function getHasRead ()
    {
        return (int) $this->hasRead;
    }

    /**
     * 
     * @param type $dateAdded
     */
    public function setDateAdded ($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * 
     * @param type $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
    }

    /**
     * 
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $hasRead
     */
    public function setHasRead ($hasRead)
    {
        $this->hasRead = $hasRead;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function getUnreadCountForUser (User $objUser)
    {
        $result = $this->db->_query ("SELECT COUNT(*) AS count FROM notifications WHERE has_read = 0 AND user_id = :userId", [":userId" => $objUser->getId ()]);

        if ( $result === false || !isset ($result[0]['count']) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return (int) $result[0]['count'];
    }
}