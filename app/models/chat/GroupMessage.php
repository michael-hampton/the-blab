<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupChat
 *
 * @author michael.hampton
 */
class GroupChat
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
    private $db;

    /**
     * 
     * @param type $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect();
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
     * @return boolean
     */
    public function delete()
    {
        $result = $this->db->delete("chat", "group_id = :groupId", [":groupId" => $this->id]);
        
        if($result === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 
     * @return boolean
     */
    public function update()
    {
        $result = $this->db->update("group_chat", ["name" => $this->groupName, "image_url" => $this->imageUrl], "group_id = :groupId", [":groupId" => $this->id]);
    
        if($result === false) {
            return false;
        }
        
        return true;
    }
    


}
