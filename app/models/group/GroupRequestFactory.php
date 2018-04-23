<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupRequestFactory
 *
 * @author michael.hampton
 */
class GroupRequestFactory
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
        $this->db->connect();
    }

        /**
     * 
     * @param Group $objGroup
     * @return boolean
     */
    public function getAllGroupRequests (Group $objGroup)
    {
        $arrResults = $this->db->_select ("group_request", "group_id = :groupId", [":groupId" => $objGroup->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }
        
        if(empty($arrResults[0])) {
            return [];
        }
        
        $arrRequests = [];
        
        foreach ($arrResults as $arrResult) {
            
            $objUser = new User($arrResult['user_id']);
            $objGroupRequest = new GroupRequest();
            $objGroupRequest->setArrUser($objUser);
            
            $arrRequests[] = $objGroupRequest;
        }
        
        return $arrRequests;
    }
}
