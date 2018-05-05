<?php

/**
 * Description of FriendRequest
 *
 * @author michael.hampton
 */
class FriendRequest
{

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
     * @param User $objFriend
     * @return boolean
     */
    public function cancelRequest (User $objUser, User $objFriend)
    {
        $result = $this->db->delete ("friends", "friend_two = :friend AND friend_one = :userId", [":friend" => $objFriend->getId (), ":userId" => $objUser->getId ()]);

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
     * @param UserSettings $objUserSettings
     * @param EmailNotificationFactory $objEmailNotificationFactory
     * @param User $objFriend
     * @param NotificationFactory $objNotification
     * @return boolean
     */
    public function sendRequest (User $objUser, UserSettings $objUserSettings, EmailNotificationFactory $objEmailNotificationFactory, User $objFriend, NotificationFactory $objNotification)
    {

        $result = $this->db->create ('friends', array(
            'friend_two' => $objFriend->getId (),
            'friend_one' => $objUser->getId (),
            'status' => '1'
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $notification = "You have received a friend request from {$objUser->getUsername ()} on the Blab Philippines";

        $objRecipient = new User ($friendId);

        $objNotification->createNotification ($objRecipient, $notification);

        if ( $objUserSettings->getEmailSetting ('friendRequest') === true )
        {
            $objEmail = $objEmailNotificationFactory->createNotification ($objRecipient, $notification, $notification);
            $objEmail->sendEmail ();
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param User $objFriend
     * @return boolean
     */
    public function acceptRequest (User $objUser, User $objFriend)
    {
        $result = $this->db->update ("friends", ["status" => 2], "friend_two + :1 AND friend_one = :2", [':1' => $objUser->getId (), ':2' => $objFriend->getId ()]
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
    public function readRequests (User $objUser)
    {
        $result = $this->db->update ("friends", ["has_read" => 1], "friend_one = :userId OR friend_two = :userId", [":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
