<?php

/**
 * Description of PagePost
 *
 * @author michael.hampton
 */
class PagePost extends BasePostFactory implements PostInterface
{

    /**
     *
     * @var type 
     */
    private $objPage;

    /**
     * 
     * @param Page $objPage
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (Page $objPage, PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory)
    {
        $this->objPage = $objPage;
       parent::__construct ($objPostActionFactory, $objUploadFactory, $objCommentFactory, $objReviewFactory, $objTagUserFactory, $objCommentReplyFactory);
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
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

        $postType = 2;

        $comment = $objBadWordFilter->clean ($comment);

        $objPost = $this->savePost ($comment, $objUser, $objBadWordFilter, $imageIds, null, $postType);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("page_comment", ["post_id" => $objPost->getId (), "page_id" => $this->objPage->getId ()]);

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
    public function getComments (User $objUser)
    {
        $arrResults = $this->db->_query ("
                                        SELECT 
                                            pg.page_name AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            pg.url AS username,
                                             sharers_comment,
                                             m.sharers_username,
                                            m.post_security,
                                            message AS content,
                                             m.date_updated,
                                            image_id,
                                            u2.username AS original_poster_id,
                                            m.like_count AS likes,
                                            m.created AS date_added,
                                            m.message_type,
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
                                        INNER JOIN page_comment gc ON gc.post_id = msg_id AND gc.page_id = :pageId
                                        INNER JOIN page pg ON pg.id = gc.page_id
                                        WHERE msg_id NOT IN (SELECT post_id FROM ignored_posts WHERE user_id = :userId)
                                        AND message_type = 2
                                        AND m.uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                        ORDER BY created DESC"
                , [':userId' => $objUser->getId (), ":pageId" => $this->objPage->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        return $arrPosts;
    }

}
