<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupPost
 *
 * @author michael.hampton
 */
class GroupPost extends BasePostFactory implements PostInterface
{

    /**
     *
     * @var type 
     */
    private $objGroup;


    /**
     * 
     * @param Group $objGroup
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (Group $objGroup, PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory)
    {
        $this->objGroup = $objGroup;
        parent::__construct ($objPostActionFactory, $objUploadFactory, $objCommentFactory, $objReviewFactory, $objTagUserFactory, $objCommentReplyFactory);
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter
     * @param array $imageIds
     * @return boolean
     */
    public function createComment ($comment, User $objUser, \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, array $imageIds = null)
    {
        if ( trim ($comment) === "" || !is_string ($comment) )
        {
            $this->validationFailures[] = "Comment is missing";
            return false;
        }

        $comment = $objBadWordFilter->clean ($comment);

        $objPost = $this->savePost ($comment, $objUser, $objBadWordFilter, $imageIds, null, 1);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("group_comment", ["post_id" => $objPost->getId (), "group_id" => $this->objGroup->getId ()]);

        if ( $result2 === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return $objPost;
    }

    /**
     * 
     * @param User $objUser
     * @return type
     */
    public function getComments (User $objUser, $blUnread = true)
    {
        $arrResults = $this->db->_query ("
                                        SELECT 
                                            CONCAT(CONCAT(u.fname, ' ' , u.lname), ' posted in ', gp.name) AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            u.username,
                                            message AS content,
                                             m.date_updated,
                                             sharers_comment,
                                             m.sharers_username,
                                             m.message_type,
                                            m.post_security,
                                            image_id,
                                            like_count AS likes,
                                            u2.username AS original_poster_id,
                                            created AS date_added,
                                             m.love_count,
                                            m.wow_count,
                                            m.sad_count,
                                            m.haha_count,
                                            m.angry_count,
                                            msg_id AS id,
                                            (SELECT  CONCAT(fname, ' ' , lname)
                                                FROM users WHERE username = m.sharers_username) AS sharers_fullname
                                            FROM messages m
                                            LEFT JOIN users u2 ON u2.uid = m.original_poster
                                        INNER JOIN users u ON u.uid = m.uid_fk
                                        INNER JOIN group_comment gc ON gc.post_id = msg_id AND gc.group_id = :groupId
                                        INNER JOIN groups gp ON gp.group_id = gc.group_id
                                        WHERE msg_id NOT IN (SELECT post_id FROM ignored_posts WHERE user_id = :userId)
                                        AND message_type = 1
                                        AND m.uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                        ORDER BY created DESC"
                , [':userId' => $objUser->getId (), ":groupId" => $this->objGroup->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        return $arrPosts;
    }

}
