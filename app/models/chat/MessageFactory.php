<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author michael.hampton
 */
class MessageFactory
{

    use MessageEncrypt;

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
     * @param array $arrResults
     * @return \Message|boolean
     */
    private function populateObject (array $arrResults)
    {
        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrMessages = [];

        foreach ($arrResults as $arrResult) {
            $objMessage = new Message ($arrResult['chat_id']);
            $objMessage->setDate ($arrResult['sent_on']);
            $objMessage->setRecipient ($arrResult['sent_to']);
            $objMessage->setMessage ($this->encrypt_decrypt ('decrypt', $arrResult['message']));
            $objMessage->setSender ($arrResult['user_id']);
            $objMessage->setAuthor ($arrResult['author']);
            $objMessage->setUsername ($arrResult['username']);
            $objMessage->setType ($arrResult['type']);
            $objMessage->setFilename ($arrResult['filename']);

            $arrMessages[] = $objMessage;
        }

        return $arrMessages;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function getUnreadMessagesForUser (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT c.*, 
                                                u.username,
                                                 CONCAT(u.fname, ' ' , u.lname)  AS author
                                        FROM chat c
                                        INNER JOIN users u ON u.uid = c.user_id
                                        WHERE sent_to = :userId AND has_read = 0 ", ['userId' => $objUser->getId ()]);


        $arrMessages = $this->populateObject ($arrResults);

        if ( $arrResults === false )
        {
            return false;
        }

        return $arrMessages;
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getMessageById ($id)
    {
        $arrResults = $this->db->_query ("SELECT c.*, 
                                                u.username,
                                                 CONCAT(u.fname, ' ' , u.lname)  AS author
                                        FROM chat c
                                        INNER JOIN users u ON u.uid = c.user_id
                                        WHERE c.chat_id = :messageId", ['messageId' => $id]);


        $arrMessages = $this->populateObject ($arrResults);


        return $arrMessages;
    }

    /**
     * 
     * @param User $objUser
     * @return \Message|boolean
     */
    public function getMessagesNew (User $objUser, User $objUser2)
    {
        $arrResults = $this->db->_query ("SELECT c.*, 
                                                u.username,
                                                 CONCAT(u.fname, ' ' , u.lname)  AS author
                                        FROM chat c
                                        INNER JOIN users u ON u.uid = c.user_id
                                        WHERE (user_id = :userId1
                                        AND sent_to = :userId2) OR (user_id = :userId2 AND sent_to = :userId1) ORDER BY c.sent_on ASC", ['userId1' => $objUser->getId (), 'userId2' => $objUser2->getId ()]);


        $arrMessages = $this->populateObject ($arrResults);


        return $arrMessages;
    }

    /**
     * 
     * @param type $message
     * @param \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter
     * @param EmailNotificationFactory $objEmailFactory
     * @param User $objRecipient
     * @param User $objUser
     * @param type $filename
     * @param type $messageType
     * @param type $groupId
     * @return \Message|boolean
     */
    public function sendMessage ($message, \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, EmailNotificationFactory $objEmailFactory, User $objRecipient, User $objUser, $filename, $messageType, $groupId = null)
    {

        $userId = $objRecipient->getId ();

        if ( trim ($userId) === "" || !is_numeric ($userId) )
        {
            $this->validationFailures[] = "User Id is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $message = $objBadWordFilter->clean ($message);
        $message = $this->encrypt_decrypt ('encrypt', $message);

        $result = $this->db->create ('chat', array(
            'message' => $message,
            'user_id' => $objUser->getId (),
            'sent_on' => date ("Y-m-d H:i:s"),
            'sent_to' => $userId,
            "filename" => $filename,
            "type" => $messageType,
            "group_id" => $groupId
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $objMessage = new Message ($result);

        $body = $objUser->getUsername () . " said - {$objMessage->getMessage ()}";

        $this->sendNotification ($objUser, $objMessage, $objRecipient, $objEmailFactory, $body);

        return $objMessage;
    }

    private $validationFailures = [];

    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }


    /**
     * 
     * @param User $objUser
     * @param type $searchText
     * @return \Message|boolean
     */
    public function getChatUsers (User $objUser, $searchText = null)
    {
        $arrParams = [];

        $sql = "SELECT * 
                    FROM   (SELECT u.uid, 
                                   u.username, 
                                   u.fname, 
                                   u.lname, 
                                   u.last_login, 
                                   Concat(u.fname, ' ', u.lname) AS author, 
                                   sent_on, 
                                   message, 
                                   sent_to,
                                   chat_id
                            FROM   chat c 
                                   INNER JOIN users u 
                                           ON u.uid = c.sent_to 
                            WHERE  uid NOT IN (SELECT user_added 
                                               FROM   blocked_friend 
                                               WHERE  blocked_user = :userId) 
                            GROUP  BY u.uid 
                            UNION 
                            SELECT uid, 
                                   username, 
                                   fname, 
                                   lname, 
                                   last_login, 
                                   Concat(fname, ' ', lname) AS author, 
                                   NULL                      AS sent_on, 
                                   NULL                      AS message, 
                                   NULL                      AS sent_to, 
                                   NULL                      AS chat_id
                            FROM   users 
                            WHERE  uid NOT IN (SELECT user_added 
                                               FROM   blocked_friend 
                                               WHERE  blocked_user = :userId)) AS u ";

        $arrParams[':userId'] = $objUser->getId ();

        if ( $searchText !== null )
        {
            $sql .= " WHERE LOWER(fname) LIKE :username1 OR LOWER(lname) LIKE :username2 OR LOWER(username) LIKE :username3";
            $arrParams[':username1'] = '%' . strtolower ($searchText) . '%';
            $arrParams[':username2'] = '%' . strtolower ($searchText) . '%';
            $arrParams[':username3'] = '%' . strtolower ($searchText) . '%';
        }

        $sql .= " GROUP BY uid";

        $arrResults = $this->db->_query ($sql, $arrParams);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $key => $arrResult) {

            if ( !empty ($arrResult['chat_id']) )
            {
                $objMessage = new Message ($arrResult['chat_id']);
                if ( !empty ($arrResult['message']) )
                    $objMessage->setMessage ($arrResult['message']);
                $objMessage->setAuthor ($arrResult['author']);
                if ( !empty ($arrResult['sent_to']) )
                    $objMessage->setRecipient ($arrResult['sent_to']);
                $objMessage->setUsername ($arrResult['username']);
                if ( !empty ($arrResult['sent_on']) )
                    $objMessage->setDate ($arrResult['sent_on']);
                $objMessage->setLastLogin ($arrResult['last_login']);
                $objMessage->setUserId ($arrResult['uid']);
                $arrUsers['messages'][$key] = $objMessage;
            } else
            {
                $objUser = new User ($arrResult['uid']);
                $arrUsers['users'][$key] = $objUser;
            }
        }

        return $arrUsers;
    }

    /**
     * 
     * @param User $objUser
     * @param Message $objMessaage
     * @param \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter
     * @param EmailNotificationFactory $objEmailFactory
     * @param User $objRecipient
     * @param type $comment
     * @return boolean
     */
    public function cloneMessage (User $objUser, Message $objMessaage, \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, EmailNotificationFactory $objEmailFactory, User $objRecipient, $comment = '')
    {
        $comment = $objBadWordFilter->clean ($comment);
        $comment .= $this->encrypt_decrypt ('encrypt', $objMessaage->getMessage ());

        $blResult = $this->db->create ("chat", ["user_id" => $objUser->getId (), "message" => $comment, "sent_on" => date ("Y-m-d H:i:s"), "sent_to" => $objRecipient->getId (), "filename" => $objMessaage->getFilename (), "type" => $objMessaage->getType (), "group_id" => $objMessaage->getGroupId ()]);

        if ( $blResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        $body = $objUser->getUsername () . " said - {$objMessage->getMessage ()}";

        $this->sendNotification ($objUser, $objMessaage, $objRecipient, $objEmailFactory, $body);

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param Message $objMessage
     * @param User $objRecipient
     * @param EmailNotificationFactory $objEmailFactory
     * @param type $body
     * @return boolean
     */
    private function sendNotification (User $objUser, Message $objMessage, User $objRecipient, EmailNotificationFactory $objEmailFactory, $body)
    {
        $notification = "You have received a new chat message on The Blab Philippines:";

        try {
            $dbdate = strtotime ($objRecipient->getLastLogin ());
            if ( time () - $dbdate > 15 * 60 )
            {
                //$objEmail = new EmailNotification ($objRecipient, $notification, $body);
                $objEmail = $objEmailFactory->createNotification ($objRecipient, $notification, $body);
                $objEmail->sendEmail ();
            }
        } catch (Exception $e) {
            trigger_error ($e->getMessage (), E_USER_WARNING);
            return false;
        }

        return true;
    }

}
