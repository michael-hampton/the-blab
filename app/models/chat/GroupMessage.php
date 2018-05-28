<?php

/**
 * Description of GroupMessage
 *
 * @author michael.hampton
 */
class GroupMessage
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
    private $imageUrl;

    /**
     *
     * @var type 
     */
    private $userList;

    /**
     *
     * @var type 
     */
    private $nameList;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function __construct ($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Failed to load object");
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
     * @return type
     */
    public function getGroupName ()
    {
        return $this->groupName;
    }

    /**
     * 
     * @return type
     */
    public function getImageUrl ()
    {
        return $this->imageUrl;
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
     * @param type $groupName
     */
    public function setGroupName ($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * 
     * @param type $imageUrl
     */
    public function setImageUrl ($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * 
     * @return type
     */
    public function getUserList ()
    {
        return $this->userList;
    }

    /**
     * 
     * @return type
     */
    public function getNameList ()
    {
        return $this->nameList;
    }

    /**
     * 
     * @param type $userList
     */
    public function setUserList ($userList)
    {
        $this->userList = $userList;
    }

    /**
     * 
     * @param type $nameList
     */
    public function setNameList ($nameList)
    {
        $this->nameList = $nameList;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->db->_select ("group_chat", "group_id = :groupId", [":groupId" => $this->id]);

        if ( empty ($result) )
        {
            trigger_error ("Db query failed to load result", E_USER_WARNING);
            return false;
        }

        $this->groupName = $result[0]['name'];
        $this->imageUrl = $result[0]['image_url'];

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->delete ("chat", "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @var type 
     */
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
     * @return boolean
     */
    private function validate ()
    {
        if ( trim ($this->groupName) === "" )
        {
            $this->validationFailures[] = "Group name is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function update ()
    {
        if ( $this->validate () === false )
        {
            return false;
        }

        $result = $this->db->update ("group_chat", ["name" => $this->groupName, "image_url" => $this->imageUrl], "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

}
