<?php
/**
 * Description of ChatList
 *
 * @author michael.hampton
 */
class ChatList
{

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
     * @param type $groupId
     * @param type $username
     * @return boolean
     */
    public function removeUserFromGroupChat ($groupId, $username)
    {
        $result = $this->db->delete ("group_users", "group_id = :groupId AND username = :username", [":groupId" => $groupId, "username" => $username]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param type $groupId
     * @return int|boolean
     */
    public function getUnreadCount (User $objUser, $groupId = null)
    {
        $arrParams = [];

        $sql = "SELECT COUNT(*) AS count FROM `chat` WHERE has_read = 0 AND `sent_to` = :userId";

        $arrParams[":userId"] = $objUser->getId ();

        if ( $groupId !== null )
        {
            $sql .= " AND group_id = :groupId";
            $arrParams[':groupId'] = $groupId;
        }

        $arrResult = $this->db->_query ($sql, $arrParams);

        if ( $arrResult === false )
        {
            return false;
        }

        if ( empty ($arrResult[0]) )
        {
            return 0;
        }

        return (int) $arrResult[0]['count'];
    }

    /**
     * 
     * @param User $objUser
     * @param User $objUser2
     * @return boolean
     */
    public function deleteConversation (User $objUser, User $objUser2)
    {
        $arrParameters = [':userId1' => $objUser->getId (), ':userId2' => $objUser2->getId ()];

        $result1 = $this->db->delete ("chat", "user_id = :userId1 AND sent_to = :userId2");

        $result2 = $this->db->delete ("chat", "user_id = :userId2 AND sent_to = :userId1", $arrParameters);



        if ( $result1 === false || $result2 === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param GroupMessage $objGroupChat
     * @return boolean
     */
    public function setMessagesToRead (User $objUser, GroupMessage $objGroupChat = null)
    {

        $sqlWhere = 'sent_to = :userId';
        $arrParams[':userId'] = $objUser->getId ();

        if ( $groupId !== null )
        {
            $sqlWhere .= ' AND group_id = :groupId';
            $arrParams[':groupId'] = $objGroupChat->getId ();
        }

        $result = $this->db->update ("chat", ["has_read" => 1], $sqlWhere, $arrParams);

        if ( $result === false )
        {
            trigger_error ("Unable to set chat messages to read", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
