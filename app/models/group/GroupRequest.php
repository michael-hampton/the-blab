<?php

/**
 * Description of GroupRequest
 *
 * @author michael.hampton
 */
class GroupRequest
{

    /**
     *
     * @var type 
     */
    private $db;
    
    /**
     *
     * @var type 
     */
    private $arrUser;
    
    /**
     *
     * @var type 
     */
    private $status;

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
     * @return type
     */
    public function getArrUser ()
    {
        return $this->arrUser;
    }

    /**
     * 
     * @return type
     */
    public function getStatus ()
    {
        return $this->status;
    }

    /**
     * 
     * @param User $arrUser
     */
    public function setArrUser (User $arrUser)
    {
        $this->arrUser = $arrUser;
    }

    /**
     * 
     * @param type $status
     */
    public function setStatus ($status)
    {
        $this->status = $status;
    }

    
    /**
     * 
     * @param User $objUser
     * @param Group $objGroup
     * @return boolean
     */
    public function addGroupRequest (User $objUser, Group $objGroup)
    {
        if($this->checkRequestSentForUser ($objUser, $objGroup) === true) {
            return false;
        }
        
        $result = $this->db->create ("group_request", ["user_id" => $objUser->getId (), "group_id" => $objGroup->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Unable to get results from db", E_USER_WARNING);
            return false;
        }

        return true;
    }

   /**
    * 
    * @param User $objUser
    * @param Group $objGroup
    * @return boolean
    */
    public function deleteRequest (User $objUser, Group $objGroup)
    {
        $result = $this->db->delete ("group_request", "user_id = :userId AND group_id = :groupId", [":userId" => $objUser->getId (), ":groupId" => $objGroup->getId ()]);

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
     * @param Group $objGroup
     * @return boolean
     * @throws Exception
     */
    public function checkRequestSentForUser (User $objUser, Group $objGroup)
    {
        $arrResult = $this->db->_select ("group_request", "user_id = :userId AND group_id = :groupId", [":userId" => $objUser->getId (), "groupId" => $objGroup->getId ()]);

        if ( $arrResult === false )
        {
            trigger_error ("Unable to get results from db", E_USER_WARNING);
            throw new Exception ("Unable to get results from db");
        }

        if ( empty ($arrResult[0]) )
        {
            return false;
        }

        return true;
    }

}
