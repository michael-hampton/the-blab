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
class EventMemberFactory
{

    private $db;

    public function __construct ()
    {

        $this->db = new Database();

        $this->db->connect ();
    }

    /**
     * 
     * @param array $arrMembers
     * @param Event $objEvent
     * @return boolean
     */
    public function createEventMembers (array $arrMembers, Event $objEvent)
    {
        $objNotification = new NotificationFactory();
        
        foreach ($arrMembers as $userId) {

            $objUser = new User ($userId);

            $blResult = $this->saveEventMember ($objEvent, $objUser);

            if ( $blResult === false )
            {
                return false;
            }

            $objNotification->createNotification ($objUser, "You have received an invitation to an event");
        }

        return true;
    }

    /**
     * 
     * @param Event $objEvent
     * @param User $objUser
     * @return boolean
     */
    private function saveEventMember (Event $objEvent, User $objUser)
    {
        $result = $this->db->create ("event_member", ["user_id" => $objUser->getId (), "event_id" => $objEvent->getId ()]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED - unable to save", E_USER_WARNING);
            return false;
        }
        
        return true;
    }

    /**
     * 
     * @param Event $objEvent
     * @return \User|boolean
     */
    public function getEventMembers (Event $objEvent)
    {
        $results = $this->db->_query ("SELECT 
                                        u.username, 
                                        u.uid,
                                        u.fname, 
                                        u.lname,
                                        m.status
                                    FROM `event_member` m 
                                    INNER JOIN users u ON u.uid = m.user_id
                                     WHERE event_id = :eventId", [':eventId' => $objEvent->getId ()]);

        if ( $results === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($results as $result) {
            $objUser = new User ($result['uid']);
            $objUser->setUsername ($result['username']);
            $objUser->setFirstName ($result['fname']);
            $objUser->setLastName ($result['lname']);
            $objUser->setIsActive($result['status']);
            
            $arrUsers[] = $objUser;
        }

        return $arrUsers;
    }

    /**
     * 
     * @param Event $objEvent
     * @param type $status
     * @return boolean|\EventMember
     */
    private function getEventStatusList (Event $objEvent, $status)
    {
        $arrResults = $this->db->_select ("event_member", "status = :status AND event_id = :eventId", [":status" => $status, ":eventId" => $objEvent->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrList = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['user_id']);
            $objMember = new EventMember ($objEvent);
            $objMember->setEventStatus ($arrResult['status']);
            $objMember->setEventMember ($objUser);

            $arrList[] = $objMember;
        }

        return $arrList;
    }

    /**
     * 
     * @param Event $objEvent
     * @return type
     */
    public function getMembersGoing (Event $objEvent)
    {
        return $this->getEventStatusList ($objEvent, 1);
    }

    /**
     * 
     * @param Event $objEvent
     * @return type
     */
    public function geMembersInterested (Event $objEvent)
    {
        return $this->getEventStatusList ($objEvent, 3);
    }

    /**
     * 
     * @param Event $objEvent
     * @return type
     */
    public function getMembersPending (Event $objEvent)
    {
        return $this->getEventStatusList ($objEvent, 4);
    }

    /**
     * 
     * @param Event $objEvent
     * @return type
     */
    public function getMembersNotGoing (Event $objEvent)
    {
        return $this->getEventStatusList ($objEvent, 2);
    }

    /**
     * 
     * @param Event $objEvent
     * @return \User|boolean
     */
    public function getFriendsNotMembers (Event $objEvent)
    {
        $results = $this->db->_query ("SELECT 
                                        u.username, 
                                        u.uid,
                                        u.fname, 
                                        u.lname 
                                    FROM users u
                                    WHERE u.uid NOT IN (SELECT user_id FROM event_member WHERE event_id = :eventId)", [':eventId' => $objEvent->getId ()]);

        if ( $results === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($results as $result) {
            $objUser = new User ($result['uid']);
            $objUser->setUsername ($result['username']);
            $objUser->setFirstName ($result['fname']);
            $objUser->setLastName ($result['lname']);

            $arrUsers[] = $objUser;
        }

        return $arrUsers;
    }

}
