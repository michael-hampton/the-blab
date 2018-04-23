<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupMember
 *
 * @author michael.hampton
 */
class GroupMemberFactory
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
     * @param Group $objGroup
     * @return boolean
     */
    public function createGroupMembers (array $arrMembers, Group $objGroup)
    {
        $objNotification = new NotificationFactory();

        foreach ($arrMembers as $userId) {
            $result = $this->db->create ("group_member", ["user_id" => $userId, "group_id" => $objGroup->getId ()]);

            if ( $result === false )
            {
                return false;
            }

            $objUser = new User ($userId);

            $objNotification->createNotification ($objUser, "You have received an invitation to join a group");
        }

        return true;
    }

    /**
     * 
     * @param Group $objGroup
     * @return \User|boolean
     */
    public function getFriendsNotMembers (Group $objGroup)
    {
        $results = $this->db->_query ("SELECT 
                                        u.username, 
                                        u.uid,
                                        u.fname, 
                                        u.lname 
                                    FROM users u
                                    WHERE u.uid NOT IN (SELECT user_id FROM group_member WHERE group_id = :groupId)", [':groupId' => $objGroup->getId ()]);
        
        if ( $results === false )
        {
            trigger_error("Db query failed", E_USER_WARNING);
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

    /**
     * 
     * @param Event $objEvent
     * @return \User|boolean
     */
    public function getGroupMembers (Group $objGroup)
    {
        $results = $this->db->_query ("SELECT 
                                        u.username, 
                                        u.uid,
                                        u.fname, 
                                        u.lname 
                                    FROM `group_member` m 
                                    INNER JOIN users u ON u.uid = m.user_id
                                     WHERE group_id = :groupId", [':groupId' => $objGroup->getId ()]);

        if ( $results === false )
        {
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
