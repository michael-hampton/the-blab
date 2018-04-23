<?php

/**
 * Description of GroupMember
 *
 * @author michael.hampton
 */
class GroupMember
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

        $this->db->connect ();
    }

    /**
     * 
     * @param User $objUser
     * @param Group $objGroup
     * @return boolean
     */
    public function removeMemberFromGroup (User $objUser, Group $objGroup)
    {
        $result = $this->db->delete ("group_member", "user_id = :userId AND group_id = :groupId", [":userId" => $objUser->getId (), "groupId" => $objGroup->getId ()]);

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
    public function addMemberToGroup (User $objUser, Group $objGroup)
    {
        $result = $this->db->create ("group_member", ["user_id" => $objUser->getId (), "group_id" => $objGroup->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Unable to get results from db", E_USER_WARNING);
            return false;
        }

        return true;
    }

   /**
    * 
    * @param Group $objGroup
    * @return boolean
    */
    public function delete (Group $objGroup)
    {


        $result2 = $this->db->delete ("group_member", "group_id = :groupId", [":groupId" => $objGroup->getId ()]);

        if ( $result2 === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        $result = $this->db->delete ("groups", "group_id = :groupId", [":groupId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
