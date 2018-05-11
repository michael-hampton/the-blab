<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupFactory
 *
 * @author michael.hampton
 */
class GroupFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var array 
     */
    private $validationFailures = [];

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
     * @param type $results
     * @param GroupRequestFactory $objGroupRequestFactory
     * @return \Group|boolean
     */
    private function buildGroupObject ($results, GroupRequestFactory $objGroupRequestFactory = null)
    {
        try {
            if ( $results === false || !is_array ($results) )
            {
                trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
                return false;
            }

            if ( empty ($results[0]) )
            {
                return [];
            }

            $arrGroups = [];

            foreach ($results as $result) {

                if(empty($result['group_id']) || empty($result['name'])) {
                    continue;
                }

                $objGroup = new Group ($result['group_id']);
                $objGroup->setGroupName ($result['name']);
                $objGroup->setDescription ($result['description']);
                $objGroup->setMemberCount(isset($result['member_count']) ? $result['member_count'] : 0);
                
                if($objGroupRequestFactory !== null) {
                    $arrRequests = $objGroupRequestFactory->getAllGroupRequests($objGroup);
                    
                    $objGroup->setArrRequests($arrRequests);
                }

                $arrGroups[] = $objGroup;
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage ());
            return false;
        }

        return $arrGroups;
    }

    /**
     * 
     * @param type $searchText
     * @param type $page
     * @param type $pageLimit
     * @return type
     */
    public function getAllGroups (User $objUser, GroupRequestFactory $objGroupRequestFactory, $searchText = null, $page = null, $pageLimit = null)
    {

        $arrWhere = [];
        $sqlWhere = '';
        
        $arrWhere[':userId'] = $objUser->getId();

        $sql = "SELECT g.*,
                COUNT(gm.id) AS member_count 
                FROM `groups` g
                LEFT JOIN group_member gm ON gm.group_id = g.group_id
                WHERE  g.group_id NOT IN (SELECT group_id FROM group_member WHERE user_id = :userId)
                AND group_type Like '%public%'"; 

                if($searchText !== null) {
                    $sql .= " AND `name` LIKE :groupName";
                    $arrWhere[":groupName"] = '%' . $searchText . '%';
                }
   
                $sql .= " GROUP BY g.group_id ORDER BY name ASC";

                if($page !== null && $pageLimit !== null) {
                    $sql .= " LIMIT {$page}, {$pageLimit}";
                }

               $arrResults = $this->db->_query ($sql, $arrWhere);

        $arrGroups = $this->buildGroupObject ($arrResults, $objGroupRequestFactory);

        return $arrGroups;
    }

     /**
     * 
     * @param User $objUser
     * @return \Group|boolean
     */ 
    public function getGroupsForProfile (User $objUser)
    {
        $results = $this->db->_query ("SELECT COUNT(gm. 
id) AS member_count, g.* FROM group_member gm 
                                        INNER JOIN groups g ON g.group_id = gm.group_id
                                        WHERE gm.user_id = :userId", [':userId' => $objUser->getId ()]);

        $arrGroups = $this->buildGroupObject ($results);

        return $arrGroups;
    }
    
    /**
     * 
     * @param User $objUser
     * @return \Group|boolean
     */
    public function getGroupsForUser (User $objUser)
    {
        $results = $this->db->_select("groups", "created_by = :userId", [":userId" => $objUser->getId()]);

        $arrGroups = $this->buildGroupObject ($results);

        return $arrGroups;
    }
    
    /**
     * 
     * @param User $objUser
     * @param type $name
     * @param type $description
     * @param type $groupType
     * @return \Group|boolean
     */
    public function createGroup (User $objUser, $name, $description, $groupType)
    {
        $userId = $objUser->getId ();

        if ( trim ($userId) === "" || !is_numeric ($userId) )
        {
            $this->validationFailures[] = "User is a mandatory field";
        }

        if ( trim ($name) === "" )
        {
            $this->validationFailures[] = "Name is a mandatory field";
        }

        if ( trim ($description) === "" )
        {
            $this->validationFailures[] = "Description is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("groups", ["created_by" => $objUser->getId (), "name" => $name, "description" => $description, "group_type" => strtolower($groupType)]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $objGroup = new Group ($result);

        return $objGroup;
    }

    /**
     * 
     * @return array
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

}