<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventMember
 *
 * @author michael.hampton
 */
class EventMember
{

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $eventStatus;

    /**
     *
     * @var type 
     */
    private $eventMember;

    /**
     *
     * @var type 
     */
    private $objDb;

   /**
    * 
    * @param Event $objEvent
    * @throws Exception
    */
    public function __construct (Event $objEvent)
    {
        if ( trim ($objEvent->getId ()) === "" || !is_numeric ($objEvent->getId ()) )
        {
            trigger_error ("Invalid event given", E_USER_WARNING);
            throw new Exception ("Invalid event given");
        }

        $this->id = $objEvent->getId ();
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * 
     * @return type
     */
    public function getEventStatus ()
    {
        return $this->eventStatus;
    }

    /**
     * 
     * @return type
     */
    public function getEventMember ()
    {
        return $this->eventMember;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param type $eventStatus
     */
    public function setEventStatus ($eventStatus)
    {
        $this->eventStatus = $eventStatus;
    }

    /**
     * 
     * @param User $eventMember
     */
    public function setEventMember (User $eventMember)
    {
        $this->eventMember = $eventMember;
    }

    /**
     * 
     * @param User $objUser
     * @param type $status
     * @return boolean
     */
    public function updateEventStatus (User $objUser, $status)
    {
        $result = $this->objDb->update ("event_member", ["status" => $status], "event_id = :eventId AND user_id = :userId", [":eventId" => $this->id, ":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param Event $objEvent
     * @return boolean
     */
    public function getStatusForUser (User $objUser)
    {
        $result = $this->objDb->_select ("event_member", "user_id = :userId AND event_id = :eventId", [":userId" => $objUser->getId (), ":eventId" => $this->id]);

        if ( $result === false || empty ($result[0]['status']) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return (int) $result[0]['status'];
    }

}
