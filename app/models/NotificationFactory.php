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
class NotificationFactory
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
     * @param User $objUser
     * @param type $notification
     * @return boolean
     */
    public function createNotification (User $objUser, $notification)
    {
        $result = $this->db->create ("notifications", [
            "date_added" => date ("Y-m-d H:i:s"),
            "message" => $notification,
            "user_id" => $objUser->getId (),
            "has_read" => 0
                ]
        );

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function markAsRead (User $objUser)
    {
        $result = $this->db->update ("notifications", ['has_read' => 1], "user_id = :userId", [':userId' => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean|\Notification
     */
    public function getNotificationsForUser (User $objUser)
    {
        $arrResults = $this->db->_select ("notifications", "user_id = :userId", [':userId' => $objUser->getId ()], "*", "date_added DESC");

        if ( $arrResults === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrNotificatons = [];

        foreach ($arrResults as $arrResult) {
            $objNotification = new Notification();
            $objNotification->setDateAdded ($arrResult['date_added']);
            $objNotification->setMessage ($arrResult['message']);
            $objNotification->setUserId ($arrResult['user_id']);
            $objNotification->setHasRead ($arrResult['has_read']);

            $arrNotificatons[] = $objNotification;
        }

        return $arrNotificatons;
    }

    /**
     * 
     * @param FriendFactory $objFriend
     * @param User $objUser
     * @param type $notification
     * @return boolean
     */
    public function sendNotificationToFriends (UserFactory $objUserFactory, User $objUser, $notification)
    {
        $arrFriends = $objUserFactory->getFriendList ($objUser);

        if ( $arrFriends === false )
        {
            return false;
        }

        foreach ($arrFriends as $arrFriend) {
            $blResult = $this->createNotification ($arrFriend, $notification);

            if ( $blResult === false )
            {
                return false;
            }

        $objEmail = new EmailNotification ($arrFriend, $notification, $notification);
        $objEmail->sendEmail ();

        }

        return true;
    }

    /**
     * 
     * @param GroupMemberFactory $objGroupMembers
     * @param Group $objGroup
     * @param type $notification
     * @return boolean
     */
    public function sendNotificationToGroupMembers (GroupMemberFactory $objGroupMembers, Group $objGroup, $notification)
    {
        $arrMembers = $objGroupMembers->getGroupMembers ($objGroup);

        if ( $arrMembers === false )
        {
            return false;
        }

        foreach ($arrMembers as $arrMember) {
            $blResult = $this->createNotification ($arrMember, $notification);

            if ( $blResult === false )
            {
                return false;
            }
        }

        return true;
    }
    
   /**
    * 
    * @param EventMemberFactory $objEventMembers
    * @param Event $objEvent
    * @param type $notification
    * @return boolean
    */
    public function sendNotificationToEventMembers (EventMemberFactory $objEventMembers, Event $objEvent, $notification)
    {
        $arrMembers = $objEventMembers->getEventMembers ($objEvent);

        if ( $arrMembers === false )
        {
            return false;
        }

        foreach ($arrMembers as $arrMember) {
            $blResult = $this->createNotification ($arrMember, $notification);

            if ( $blResult === false )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @param PageReactionFactory $objPageReaction
     * @param Page $objPage
     * @param type $notification
     * @return boolean
     */
    public function sendNotificationToPageFollowers (PageReactionFactory $objPageReaction, Page $objPage, $notification)
    {
        $arrFollowers = $objPageReaction->getFollowersForPage ($objPage);

        if ( empty ($arrFollowers) )
        {
            return true;
        }

        foreach ($arrFollowers as $arrFollower) {
            $blResult = $this->createNotification (new User ($arrFollower->getId ()), $notification);

            if ( $blResult === false )
            {
                trigger_error ("Failed to send page notification", E_USER_WARNING);
                return false;
            }
        }

        return true;
    }

}