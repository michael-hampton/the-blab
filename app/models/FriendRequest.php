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

    /**
     * 
     * @param User $objUser
     */
    public function getFriends (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT 0 AS `status`, 
                                            u.uid AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'add' AS friend_status 
                                     FROM   users u 
                                     WHERE  uid NOT IN (SELECT friend_two 
                                                        FROM   friends 
                                                        WHERE  friend_one = :id1) 
                                            AND uid NOT IN (SELECT friend_one 
                                                            FROM   friends 
                                                            WHERE  friend_two = :id1) 
                                     UNION 
                                     SELECT f.status AS `status`, 
                                            friend_two  AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'requested' AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_two 
                                     WHERE  friend_one = :id2 
                                            AND f.status != '2' 
                                            AND has_read = 0 
                                     UNION 
                                     SELECT f.status   AS `status`, 
                                            friend_one AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'pending'  AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_one 
                                     WHERE  friend_two = :id3 
                                            AND f.status = '1' 
                                     UNION 
                                     SELECT f.status   AS `status`, 
                                            friend_one AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'friend'   AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_one 
                                     WHERE  friend_two = :id4 
                                            AND f.status = '2' ", [':id1' => $objUser->getId (), ':id2' => $objUser->getId (), ':id3' => $objUser->getId (), ':id4' => $objUser->getId ()]);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return FALSE;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        return $arrResults;
    }

}
