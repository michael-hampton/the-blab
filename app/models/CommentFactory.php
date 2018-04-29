<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentFactory
 *
 * @author michael.hampton
 */
class CommentFactory
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

        $logFile = $_SERVER['DOCUMENT_ROOT'] . "/blab/app/logs";
    }

    /**
     * 
     * @param Post $objPost
     * @return \Comment|boolean
     */
    public function getCommentsByPostForDelete (Post $objPost)
    {
        $arrResults = $this->db->_select ("comments", "msg_id_fk = :postId", [":postId" => $objPost->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrComments = [];

        foreach ($arrResults as $arrResult) {
            $arrComments[] = new Comment ($arrResult['com_id']);
        }

        return $arrComments;
    }

    /**
     * 
     * @param Post $objPost
     * @param UploadFactory $objUploadFactory
     * @param PostActionFactory $objLikes
     * @param CommentReplyFactory $objCommentReplyFactory
     * @return \Comment|boolean
     */
    public function getCommentsForPost (Post $objPost, UploadFactory $objUploadFactory, PostActionFactory $objLikes, CommentReplyFactory $objCommentReplyFactory, User $objUser)
    {

        $arrResults = $this->db->_query ("
                                        SELECT 
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            u.username,
                                            u.uid,
                                            comment,
                                            m.date_updated,
                                            like_count AS likes,
                                            created AS date_added,
                                            com_id AS id,
                                            msg_id_fk
                                            FROM comments m
                                        INNER JOIN users u ON u.uid = m.uid_fk
                                        WHERE msg_id_fk = :postId
                                         AND uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userId)
                                         AND com_id NOT IN (SELECT post_id FROM ignored_comments WHERE user_id = :userId)
                                            ORDER BY created DESC
                                            ", [':postId' => $objPost->getId (), ':userId' => $objUser->getId ()]);

        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrComments = [];

        foreach ($arrResults as $arrResult) {

            $objComment = new Comment ($arrResult['id']);
            $objComment->setComment ($arrResult['comment']);
            $objComment->setCreated ($arrResult['date_added']);
            $objComment->setLikes ($arrResult['likes']);
            $objComment->setAuthor ($arrResult['author']);
            $objComment->setMsgId ($arrResult['msg_id_fk']);
            $objComment->setUsername ($arrResult['username']);
            $objComment->setUserId ($arrResult['uid']);
            $objComment->setDateUpdated ($arrResult['date_updated']);

            $arrImages = $objUploadFactory->getImagesForComment ($objComment);

            if ( !empty ($arrImages[0]) )
            {

                $objComment->setArrImages ($arrImages);
            }

            $arrLikes = $objLikes->getLikeListForComment ($objComment);

            if ( $arrLikes !== FALSE )
            {
                $objComment->setArrLikes ($arrLikes);
            }

            $arrReplies = $objCommentReplyFactory->getRepliesForComment ($objComment, $objUploadFactory, $objLikes, 0, 1, $objUser);

            $objComment->setArrReplies ($arrReplies);

            $arrComments[] = $objComment;
        }

        return $arrComments;
    }

    /**
     * 
     * @param type $comment
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function createComment ($comment, Post $objPost, User $objUser)
    {
        if ( trim ($comment) === "" )
        {
            $this->validationFailures[] = "Comment is a mandatory field";
        }

        $postId = $objPost->getId ();

        if ( !is_int ($postId) === "" )
        {
            $this->validationFailures[] = "Post id is a mandatory field";
        }

        $userId = $objUser->getId ();

        if ( !is_int ($userId) === "" )
        {
            $this->validationFailures[] = "User is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
        $comment = $objBadWordFilter->clean ($comment);

        $result = $this->db->create ('comments', array(
            'comment' => $comment,
            'uid_fk' => $userId,
            'msg_id_fk' => $postId,
            'created' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED - unable to create comment", E_USER_WARNING);
            return false;
        }

        return new Comment ($result);
    }

    /**
     * 
     * @param Post $objPost
     * @param CommentReplyFactory $objCommentReplyFactory
     * @param PostAction $objPostAction
     * @return boolean
     * @throws Exception
     */
    public function deleteCommentsForPost (Post $objPost, CommentReplyFactory $objCommentReplyFactory, PostAction $objPostAction)
    {

        $arrComments = $this->getCommentsByPostForDelete ($objPost);

        if ( $arrComments === false )
        {
            throw new Exception ("Db query failed");
        }

        if ( empty ($arrComments) )
        {
            return true;
        }

        foreach ($arrComments as $objComment) {
            $blResult = $objCommentReplyFactory->deleteRepliesForComment ($objComment, $objPostAction);

            if ( $blResult === false )
            {
                return false;
            }

            $blResult2 = $objPostAction->deleteCommentLikes ($objComment);

            if ( $blResult2 === false )
            {
                return false;
            }

            $blResult3 = $objComment->delete ();

            if ( $blResult3 === false )
            {
                return false;
            }
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

}
