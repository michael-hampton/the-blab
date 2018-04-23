<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostActionFactory
 *
 * @author michael.hampton
 */
class PostActionFactory
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
     * @param Post $objPost
     * @return \User|boolean
     */
    public function getLikeListForPost (Post $objPost)
    {

        $results = $this->db->_query ("SELECT u.uid, u.fname, u.lname, u.username, l.created FROM `message_like` l
                                    INNER JOIN users u ON u.uid = l.uid_fk
                                    WHERE l.msg_id_fk = :comId", [':comId' => $objPost->getId ()]
        );

        if ( $results === FALSE )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrLikes = [];

        foreach ($results as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrLikes[] = $objUser;
        }

        return $arrLikes;
    }

    /**
     * 
     * @param Comment $objComment
     * @return \User|boolean
     */
    public function getLikeListForComment (Comment $objComment)
    {
        $results = $this->db->_query ("SELECT u.uid, u.fname, u.lname, u.username, l.created FROM `comment_like` l
                                    INNER JOIN users u ON u.uid = l.uid_fk
                                    WHERE l.com_id_fk = :comId", [':comId' => $objComment->getId ()]
        );

        if ( $results === FALSE )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrLikes = [];

        foreach ($results as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrLikes[] = $objUser;
        }

        return $arrLikes;
    }

    /**
     * 
     * @param Comment $objComment
     * @return \User|boolean
     */
    public function getLikeListForReply (CommentReply $objCommentReply)
    {
        $results = $this->db->_query ("SELECT u.uid, u.fname, u.lname, u.username, l.created FROM `comment_reply_like` l
                                    INNER JOIN users u ON u.uid = l.uid_fk
                                    WHERE l.com_id_fk = :comId", [':comId' => $objCommentReply->getId ()]
        );

        if ( $results === FALSE )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrLikes = [];

        foreach ($results as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrLikes[] = $objUser;
        }

        return $arrLikes;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function getAllLikesForUser (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT m.msg_id, m.message, l.created, 'message' AS type FROM message_like l
                                        INNER JOIN messages m ON m.msg_id = l.msg_id_fk
                                        WHERE l.uid_fk = :userId
                                        UNION
                                        SELECT c.com_id, c.comment, l2.created, 'comment' AS type FROM comment_like l2
                                        INNER JOIN comments c ON c.com_id = l2.com_id_fk
                                        WHERE l2.uid_fk = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        return $arrResults;
    }

}
