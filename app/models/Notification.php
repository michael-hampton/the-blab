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
        return (int)$this->hasRead;
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


}
