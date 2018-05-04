<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserFactory
 *
 * @author michael.hampton
 */
class UserFactory
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
     * @param type $username
     * @return boolean
     * @throws Exception
     */
    private function checkUsernameAvailiable ($username)
    {
        $result = $this->db->_query ("SELECT * FROM users WHERE LOWER(username) = :username", [":username" => $username]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            throw new Exception ("Query failed");
        }

        if ( !empty ($result[0]) && !empty ($result[0]['username']) )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $email
     * @return boolean
     * @throws Exception
     */
    private function checkEmailAvailiable ($email)
    {
        $result = $this->db->_query ("SELECT * FROM users WHERE LOWER(email) = :email", [":email" => $email]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            throw new Exception ("Query failed");
        }

        if ( !empty ($result[0]) && !empty ($result[0]['email']) )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $username
     * @param type $password
     * @param type $firstname
     * @param type $lastName
     * @param type $email
     */
    public function createUser ($username, $password, $firstname, $lastName, $email)
    {
        if ( trim ($username) === "" || !is_string ($username) )
        {
            $this->validationFailures[] = "Username is a mandatory field";
        }

        if ( trim ($password) === "" || !is_string ($password) )
        {
            $this->validationFailures[] = "Password is a mandatory field";
        }

        if ( trim ($firstname) === "" || !is_string ($firstname) )
        {
            $this->validationFailures[] = "First Name is a mandatory field";
        }

        if ( trim ($lastName) === "" || !is_string ($lastName) )
        {
            $this->validationFailures[] = "Last Name is a mandatory field";
        }

        if ( trim ($email) === "" || !is_string ($email) )
        {
            $this->validationFailures[] = "Email is a mandatory field";
        }

        try {
            if ( $this->checkEmailAvailiable ($email) === false )
            {
                $this->validationFailures[] = "The email address you entered already exists";
            }

            if ( $this->checkUsernameAvailiable ($username) === false )
            {
                $this->validationFailures[] = "The username you entered already exists";
            }
        } catch (Exception $ex) {
            return false;
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }


        $result = $this->db->create ('users', array(
            'fname' => $firstname,
            'lname' => $lastName,
            'email' => $email,
            'password' => $this->blowfishCrypt ($password, 12),
            'username' => $username
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return new User ($result);
    }

    /**
     * 
     * @param type $password
     * @param type $cost
     * @return type
     */
    public function blowfishCrypt ($password, $cost)
    {
        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = sprintf ('$2y$%02d$', $cost);
//For PHP < PHP 5.3.7 use this instead
//    $salt=sprintf('$2a$%02d$',$cost);
        //Create a 22 character salt -edit- 2013.01.15 - replaced rand with mt_rand
        mt_srand ();
        for ($i = 0; $i < 22; $i++) {
            $salt.=$chars[mt_rand (0, 63)];
        }
        return crypt ($password, $salt);
    }

    public function getUser ($userId)
    {
        $arrResults = $this->db->_select ("users", "uid = :userId", [':userId' => $userId], "*", "fname ASC");

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }


        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setEmail ($arrResult['email']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setIsActive ($arrResult['is_active']);
            $objUser->setLastName ($arrResult['lname']);
            $objUser->setPassword ($arrResult['password']);
            $objUser->setUsername ($arrResult['username']);

            $arrUsers[$arrResult['uid']] = $objUser;
        }

        return $arrUsers;
    }

    /**
     * 
     * @param User $objUser
     */
    public function getFriends (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT 0 AS `status`, 
                                            u.uid AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'add' AS friend_status 
                                     FROM   users u 
                                     WHERE  uid NOT IN (SELECT friend_two 
                                                        FROM   friends 
                                                        WHERE  friend_one = :id1) 
                                            AND uid NOT IN (SELECT friend_one 
                                                            FROM   friends 
                                                            WHERE  friend_two = :id1) 
                                     UNION 
                                     SELECT f.status AS `status`, 
                                            friend_two  AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'requested' AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_two 
                                     WHERE  friend_one = :id2 
                                            AND f.status != '2' 
                                            AND has_read = 0 
                                     UNION 
                                     SELECT f.status   AS `status`, 
                                            friend_one AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'pending'  AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_one 
                                     WHERE  friend_two = :id3 
                                            AND f.status = '1' 
                                     UNION 
                                     SELECT f.status   AS `status`, 
                                            friend_one AS user_id, 
                                            username, 
                                            fname, 
                                            lname, 
                                            'friend'   AS friend_status 
                                     FROM   friends f 
                                            INNER JOIN users u 
                                                    ON u.uid = friend_one 
                                     WHERE  friend_two = :id4 
                                            AND f.status = '2' ", [':id1' => $objUser->getId (), ':id2' => $objUser->getId (), ':id3' => $objUser->getId (), ':id4' => $objUser->getId ()]);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return FALSE;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        return $arrResults;
    }

    public function getFriendRequests (User $objUser, $page = null, $limit = 1, $searchText = null)
    {
        $sql = "SELECT u.uid, 
                        u.username, 
                        u.fname, 
                        u.lname 
                 FROM   `friends` f 
                        INNER JOIN users u 
                                ON u.uid = f.friend_two 
                 WHERE  friend_one = :id1 
                        AND status = '1' 
                 UNION 
                 SELECT u.uid, 
                        u.username, 
                        u.fname, 
                        u.lname 
                 FROM   friends 
                        INNER JOIN users u 
                                ON u.uid = friend_one 
                 WHERE  friend_two = :id2 
                        AND status = '1'";

        $arrParams = [':id1' => $objUser->getId (), ':id2' => $objUser->getId ()];

        if ( $searchText !== null )
        {
            $sql .= " AND (u.username LIKE :username OR u.fname LIKE :username)";
            $arrParams[':username'] = '%' . $searchText . '%';
        }

        if ( $page !== null )
        {
            $sql .= " LIMIT " . $page . ", " . $limit;
        }

        $arrResults = $this->db->_query ($sql, $arrParams);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return FALSE;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrUsers[] = $objUser;
        }

        return $arrUsers;
    }

    /**
     * 
     * @param User $objUser
     * @param type $page
     * @param type $limit
     * @param type $searchText
     * @return \User|boolean
     */
    public function getFriendList (User $objUser, $page = null, $limit = 1, $searchText = null)
    {

        $sql = "SELECT u.uid, 
                        u.username, 
                        u.fname, 
                        u.lname 
                 FROM   `friends` f 
                        INNER JOIN users u 
                                ON u.uid = f.friend_two 
                 WHERE  friend_one = :id1 
                        AND status = '2' 
                 UNION 
                 SELECT u.uid, 
                        u.username, 
                        u.fname, 
                        u.lname 
                 FROM   friends 
                        INNER JOIN users u 
                                ON u.uid = friend_one 
                 WHERE  friend_two = :id2 
                        AND status = '2'";

        $arrParams = [':id1' => $objUser->getId (), ':id2' => $objUser->getId ()];

        if ( $searchText !== null )
        {
            $sql .= " AND (u.username LIKE :username OR u.fname LIKE :username)";
            $arrParams[':username'] = '%' . $searchText . '%';
        }

        if ( $page !== null )
        {
            $sql .= " LIMIT " . $page . ", " . $limit;
        }

        $arrResults = $this->db->_query ($sql, $arrParams);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return FALSE;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrUsers[] = $objUser;
        }

        return $arrUsers;
    }

    /**
     * 
     * @param type $username
     * @return \User|boolean
     */
    public function getUsers ($username = NULL, $blActive = true, $blShowBlocked = true)
    {

        $sql = "SELECT * FROM users WHERE 1=1 ";

        if ( $blActive === true )
        {
            $sql .= "AND is_active = 1";
        }

        $arrParameters = [];

        if ( $username !== NULL )
        {
            $sql .= " AND (LOWER(fname) LIKE :username1 OR LOWER(lname) LIKE :username2 OR LOWER(username) LIKE :username3)";
            $arrParameters[':username1'] = '%' . strtolower ($username) . '%';
            $arrParameters[':username2'] = '%' . strtolower ($username) . '%';
            $arrParameters[':username3'] = '%' . strtolower ($username) . '%';
        }

        if ( $blShowBlocked === false )
        {
            $sql .= " AND uid NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)";
            $arrParameters[':userAdded'] = $_SESSION['user']['user_id'];
        }

        $arrResults = $this->db->_query ($sql, $arrParameters);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setEmail ($arrResult['email']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setIsActive ($arrResult['is_active']);
            $objUser->setLastName ($arrResult['lname']);
            $objUser->setPassword ($arrResult['password']);
            $objUser->setUsername ($arrResult['username']);


            $arrUsers[$arrResult['uid']] = $objUser;
        }

        return $arrUsers;
    }

    /**
     * 
     * @param User $objUser
     * @return \User|boolean
     */
    public function getBlockedUsersForUser (User $objUser)
    {
        $arrResults = $this->db->_select ("blocked_friend", "blocked_user = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $arrUsers[] = new User ($arrResult['user_added']);
        }

        return $arrUsers;
    }

    public function getUnBlockedUsersForUser (User $objUser)
    {
        $arrResults = $this->db->_select ("blocked_friend", "user_added = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $arrUsers[] = new User ($arrResult['blocked_user']);
        }

        return $arrUsers;
    }

    /**
     * 
     * @param type $searchText
     * @param User $objUser
     * @return \User|boolean
     */
    public function getChatUsers ($searchText = null, User $objUser)
    {
        $arrParams = [];

        $sql = "SELECT * 
                FROM   (SELECT u.uid, 
                               u.username, 
                               u.fname, 
                               u.lname, 
                               sent_on 
                        FROM   chat c 
                               INNER JOIN users u 
                                       ON u.uid = c.sent_to 
                        WHERE  uid NOT IN (SELECT user_added 
                                           FROM   blocked_friend 
                                           WHERE  blocked_user = :userId) 
                        GROUP  BY u.uid 
                        UNION 
                        SELECT uid, 
                               username, 
                               fname, 
                               lname, 
                               NULL AS sent_on 
                        FROM   users 
                        WHERE  uid NOT IN (SELECT user_added 
                                           FROM   blocked_friend 
                                           WHERE  blocked_user = :userId)) AS u";

        $arrParams[':userId'] = $objUser->getId ();

        if ( $searchText !== null )
        {
            $sql .= " WHERE LOWER(fname) LIKE :username1 OR LOWER(lname) LIKE :username2 OR LOWER(username) LIKE :username3";
            $arrParams[':username1'] = '%' . strtolower ($searchText) . '%';
            $arrParams[':username2'] = '%' . strtolower ($searchText) . '%';
            $arrParams[':username3'] = '%' . strtolower ($searchText) . '%';
        }

        $sql .= " GROUP BY u.uid
                                            ORDER BY u.sent_on DESC";

        $arrResults = $this->db->_query ($sql, $arrParams);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);
            $objUser->setDateCreated ($arrResult['sent_on']);
            $objUser->setId ($arrResult['uid']);

            $arrUsers[] = $objUser;
        }

        return $arrUsers;
    }
    
    /**
     * 
     * @param type $userId
     * @return \User
     */
    public function getUserById($userId)
    {
        return new User($userId);
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

}
