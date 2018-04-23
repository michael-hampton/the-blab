<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Group
 *
 * @author michael.hampton
 */
class Group
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
    private $groupName;

    /**
     *
     * @var type 
     */
    private $description;

    /**
     *
     * @var type 
     */
    private $imageLocation;

    /**
     *
     * @var type 
     */
    private $createdBy;

    /**
     *
     * @var type 
     */
    private $groupType;
    
    /**
     *
     * @var type 
     */
    private $memberCount;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->id = $id;

        $this->db = new Database();

        $this->db->connect ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate object");
        }
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
     * @param type $id
     */
    public function setId (type $id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getGroupName ()
    {
        return $this->groupName;
    }

    /**
     * 
     * @param type $groupName
     */
    public function setGroupName ($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * 
     * @return type
     */
    public function getDescription ()
    {
        return $this->description;
    }

    /**
     * 
     * @param type $description
     */
    public function setDescription ($description)
    {
        $this->description = $description;
    }

    /**
     * 
     * @return type
     */
    public function getImageLocation ()
    {
        return $this->imageLocation;
    }

    /**
     * 
     * @return type
     */
    public function getCreatedBy ()
    {
        return $this->createdBy;
    }

    /**
     * 
     * @param type $createdBy
     */
    public function setCreatedBy ($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * 
     * @param type $imageLocation
     */
    public function setImageLocation ($imageLocation)
    {
        $this->imageLocation = $imageLocation;
    }

    /**
     * 
     * @return type
     */
    public function getGroupType ()
    {
        return $this->groupType;
    }

    /**
     * 
     * @param type $groupType
     */
    public function setGroupType ($groupType)
    {
        $this->groupType = $groupType;
    }
    
    /**
     * 
     * @return type
     */
    public function getMemberCount ()
    {
        return $this->memberCount;
    }

    /**
     * 
     * @param type $memberCount
     */
    public function setMemberCount ($memberCount)
    {
        $this->memberCount = $memberCount;
    }

    
    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function checkUserAccess (User $objUser)
    {
        $result = $this->db->_select ("group_member", "user_id = :userId AND group_id = :groupId", [
            ':userId' => $objUser->getId (),
            ':groupId' => $this->id
                ]
        );

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return !empty ($result[0]);
    }

    /**
     * 
     * @return boolean
     */
    public function populateObject ()
    {
        $result = $this->db->_select ("groups", "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $this->groupName = $result[0]['name'];
        $this->description = $result[0]['description'];
        $this->imageLocation = $result[0]['image_location'];
        $this->createdBy = $result[0]['created_by'];
        $this->groupType = $result[0]['group_type'];

        return true;
    }

    /**
     * 
     * @param type $imageLocation
     * @return boolean
     */
    public function updateGroupImage ($imageLocation)
    {
        $result = $this->db->update ("groups", ["image_location" => $imageLocation], "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function save ()
    {
        $result = $this->db->update ("groups", ["name" => $this->groupName, "description" => $this->description, "group_type" => $this->groupType], "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }
        
        return true;
    }
    
      /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function likeGroup(User $objUser)
    {
           $result = $this->db->_query ("UPDATE group 
                            SET like_count = 
                                if(like_count is not null, like_count + 1, 1) 
                            WHERE id = :groupId", [":groupId" => $this->id], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->create ('group_like', array(
            'group_id' => $this->id,
            'user_id' => $objUser->getId (),
            'date_added' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
