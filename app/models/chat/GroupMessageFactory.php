<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupMessageFactory
 *
 * @author michael.hampton
 */
class GroupMessageFactory
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
     * @param GroupMessage $objGroupChat
     * @return \Message|boolean
     */
    public function getMessagesForGroup (GroupMessage $objGroupChat)
    {
        $arrResults = $this->db->_query ("SELECT c.*, 
                                                u.username,
                                                 CONCAT(u.fname, ' ' , u.lname)  AS author
                                        FROM chat c
                                        INNER JOIN users u ON u.uid = c.user_id
                                        WHERE group_id = :groupId", ['groupId' => $objGroupChat->getId ()]);

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
            $objMessage->setMessage ($arrResult['message']);
            $objMessage->setSender ($arrResult['user_id']);
            $objMessage->setAuthor ($arrResult['author']);
            $objMessage->setUsername ($arrResult['username']);
            $objMessage->setType ($arrResult['type']);
            $objMessage->setFilename ($arrResult['filename']);
            $objMessage->setGroupId ($arrResult['group_id']);

            $arrMessages[] = $objMessage;
        }

        return $arrMessages;
    }

    /**
     * 
     * @return \GroupMessage|boolean
     */
    public function getGroups ()
    {
        $arrResults = $this->db->_query ("SELECT gc.`group_id`, 
                                                gc.`name`, 
                                                gc.`image_url`, 
                                                Group_concat(u.username SEPARATOR '|')                    AS users, 
                                                Group_concat(Concat(u.fname, ' ', u.lname) SEPARATOR '|') AS fullname 
                                         FROM   `group_chat` gc 
                                                INNER JOIN group_users gu 
                                                        ON gu.group_id = gc.group_id 
                                                INNER JOIN users u 
                                                        ON u.username = gu.username 
                                         GROUP  BY gc.group_id 
                                         ORDER  BY gc.name ASC ");

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrGroups = [];

        foreach ($arrResults as $arrResult) {
            $objGroup = new GroupMessage ($arrResult['group_id']);
            $objGroup->setGroupName ($arrResult['name']);
            $objGroup->setImageUrl ($arrResult['image_url']);
            $objGroup->setNameList ($arrResult['fullname']);
            $objGroup->setUserList ($arrResult['users']);

            $arrGroups[$arrResult['group_id']] = $objGroup;
        }

        return $arrGroups;
    }

    /**
     * 
     * @param User $objUser
     * @return \Message|boolean
     */
    public function getGroupChats (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT c.chat_id, 
                                                c.message, 
                                                c.sent_on, 
                                                c.group_id, 
                                                u.username, 
                                                Concat(u.fname, ' ', u.lname) AS author 
                                         FROM   group_chat gc 
                                                LEFT JOIN chat c 
                                                       ON c.group_id = gc.group_id 
                                                LEFT JOIN users u 
                                                       ON u.uid = c.user_id 
                                         WHERE  :username IN (SELECT username 
                                                                      FROM   group_users 
                                                                      WHERE  group_id = c.group_id) 
                                         GROUP  BY gc.group_id 
                                         ORDER  BY c.chat_id DESC  ", [":username" => $objUser->getUsername ()]);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrMessages = [];

        foreach ($arrResults as $arrResult) {
            $objMessage = new Message ($arrResult['chat_id']);
            $objMessage->setAuthor ($arrResult['author']);
            $objMessage->setDate ($arrResult['sent_on']);
            $objMessage->setMessage ($arrResult['message']);
            $objMessage->setGroupId ($arrResult['group_id']);
            $objMessage->setUsername ($arrResult['username']);

            $arrMessages[] = $objMessage;
        }

        return $arrMessages;
    }

    /**
     * 
     * @return \GroupMessage|boolean
     */
    private function createNewGroupChat ()
    {
        $groupId = $this->db->create ("group_chat", ["name" => "[default]"]);

        if ( $groupId === false )
        {
            trigger_error ("query failed", E_USER_WARNING);
            return false;
        }

        return new GroupMessage ($groupId);
    }

    /**
     * 
     * @param type $groupId
     * @return type
     * @throws Exception
     */
    private function checkIfGroupExists ($groupId)
    {
        $arrResults = $this->db->_select ("group_chat", "group_id = :groupId", [":groupId" => $groupId]);

        if ( $arrResults === false )
        {
            throw new Exception ("Db query failed");
        }

        return !empty ($arrResults);
    }

    /**
     * 
     * @param User $objUser
     * @param type $groupId
     * @param type $username
     * @return \GroupMessage|boolean
     */
    public function addUserToGroupChat (User $objUser, $groupId, $username)
    {

        try {
            $blExists = $this->checkIfGroupExists ($groupId);
        } catch (Exception $e) {
            trigger_error ($e->getMessage (), E_USER_WARNING);
            return false;
        }

        if ( $blExists === false )
        {

            $objGroupChat = $this->createNewGroupChat ();

            if ( $objGroupChat === false )
            {
                return false;
            }

            $groupId = $objGroupChat->getId ();

            $result3 = $this->db->create ("group_users", ["group_id" => $groupId, "username" => $objUser->getUsername ()]);

            if ( $result3 === false )
            {
                trigger_error ("query failed", E_USER_WARNING);
                return false;
            }
        }

        $result2 = $this->db->create ("group_users", ["group_id" => $groupId, "username" => $username]);

        if ( $result2 === false )
        {
            trigger_error ("query failed", E_USER_WARNING);
            return false;
        }

        return new GroupMessage ($groupId);
    }

}
