<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentReplyFactory
 *
 * @author michael.hampton
 */
class CommentReplyFactory
{

    /**
     *
     * @var type 
     */
    private $objDb;

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    public function __construct ()
    {
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

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
     * @param User $objUser
     * @param Comment $objComment
     * @param type $reply
     * @return boolean|\CommentReply
     */
    public function createCommentReply (User $objUser, Comment $objComment, $reply)
    {
        if ( trim ($reply) === "" || !is_string ($reply) )
        {
            $this->validationFailures[] = "Reply cannot be empty";
        }

        $commentId = $objComment->getId ();

        if ( trim ($commentId) === "" || !is_int ($commentId) )
        {
            $this->validationFailures[] = "Invalid comment id";
        }

        $userId = $objUser->getId ();

        if ( trim ($userId) === "" )
        {
            $this->validationFailures[] = "Invalid user id";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
        $reply = $objBadWordFilter->clean ($reply);

        $result = $this->objDb->create ("comment_reply", [
            "comment_id" => $objComment->getId (),
            "reply" => $reply,
            "date_added" => date ("Y-m-d H:i:s"),
            "user_id" => $objUser->getId ()
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Invalid query", E_USER_WARNING);
            return false;
        }

        return new CommentReply ($result);
    }

    /**
     * 
     * @param Comment $objComment
     * @param UploadFactory $objUploadFactory
     * @param PostActionFactory $objLikes
     * @param type $page
     * @param type $limit
     * @return boolean|\CommentReply
     */
    public function getRepliesForComment (Comment $objComment, UploadFactory $objUploadFactory, PostActionFactory $objLikes, $page = 0, $limit = null, User $objUser)
    {

        $sql = "
                                         SELECT 
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            u.username,
                                            user_id,
                                            reply,
                                            date_updated,
                                            likes,
                                            date_added,
                                            id,
                                            image_id,
                                            comment_id
                                            FROM comment_reply r
                                        INNER JOIN users u ON u.uid = r.user_id
                                        WHERE comment_id = :commentId
                                        AND user_id NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                            ORDER BY date_added DESC
                                            ";

        if ( $limit !== null )
        {
            $sql .= " LIMIT 0," . $limit;
        }

        $arrResults = $this->objDb->_query ($sql, [':commentId' => $objComment->getId (), ':userAdded' => $objUser->getId ()]);

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

            $objReply = new CommentReply ($arrResult['id']);
            $objReply->setReply ($arrResult['reply']);
            $objReply->setDateCreated ($arrResult['date_added']);
            $objReply->setLikes ($arrResult['likes']);
            $objReply->setAuthor ($arrResult['author']);
            $objReply->setCommentId ($arrResult['comment_id']);
            $objReply->setUsername ($arrResult['username']);
            $objReply->setUserId ($arrResult['user_id']);
            $objReply->setDateUpdated ($arrResult['date_updated']);

            $arrImages = $objUploadFactory->getImagesForReply ($objReply);

            if ( !empty ($arrImages[0]) )
            {

                $objReply->setArrImages ($arrImages);
            }

            $arrLikes = $objLikes->getLikeListForReply ($objReply);

            if ( $arrLikes !== FALSE )
            {
                $objReply->setArrLikes ($arrLikes);
            }

            $arrComments[] = $objReply;
        }

        return $arrComments;
    }

    /**
     * 
     * @param Comment $objComment
     * @return boolean|\CommentReply
     */
    public function getRepliesByCommentToDelete (Comment $objComment)
    {
        $arrResults = $this->objDb->_select ("comment_reply", "comment_id = :commentId", [":commentId" => $objComment->getId ()]);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrReplies = [];
        foreach ($arrResults as $arrResult) {
            $arrReplies[] = new CommentReply ($arrResult['id']);
        }

        return $arrReplies;
    }

    /**
     * 
     * @param Comment $objComment
     * @param PostAction $objPostAction
     * @return boolean
     * @throws Exception
     */
    public function deleteRepliesForComment (Comment $objComment, PostAction $objPostAction)
    {
        $arrReplies = $this->getRepliesByCommentToDelete ($objComment);

        if ( $arrReplies === false )
        {
            throw new Exception ("Db query fsailed");
        }

        if ( empty ($arrReplies[0]) )
        {
            return true;
        }

        foreach ($arrReplies as $objReply) {

            $blResult1 = $objPostAction->deleteReplyLikes ($objReply);

            if ( $blResult1 === false )
            {
                return false;
            }

            $blResult = $objReply->delete ();

            if ( $blResult === false )
            {
                return false;
            }
        }

        return true;
    }

}
