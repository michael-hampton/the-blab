<?php

/**
 * Description of EventPost
 *
 * @author michael.hampton
 */
class EventPost extends BasePostFactory implements PostInterface
{

    /**
     *
     * @var type 
     */
    private $objEvent;

    /**
     * 
     * @param Event $objEvent
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (Event $objEvent, PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory)
    {
        $this->objEvent = $objEvent;
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

        $objPost = $this->savePost ($comment, $objUser, $objBadWordFilter, $imageIds, null, 6);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("event_comment", ["post_id" => $objPost->getId (), "event_id" => $this->objEvent->getId ()]);

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
    public function getComments (User $objUser, $blRead = true)
    {
#
        $unreadSql = $blRead === false ? ' AND msg_id NOT IN (SELECT message_id FROM messages_read WHERE user_id = :userId) ' : '';

        $arrResults = $this->db->_query ("SELECT  CONCAT(u.fname, ' ' , u.lname) AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            u.username,
                                            message AS content,
                                             m.date_updated,
                                             sharers_comment,
                                             m.sharers_username,
                                            m.post_security,
                                            image_id,
                                            m.like_count AS likes,
                                            m.message_type,
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
                                            INNER JOIN event_comment ec ON ec.post_id = m.msg_id AND ec.event_id = :eventId
                                            INNER JOIN event e ON e.event_id = ec.event_id
                                            INNER JOIN users u ON u.uid = m.uid_fk
                                            LEFT JOIN users u2 ON u2.uid = m.original_poster
                                            WHERE msg_id NOT IN (SELECT post_id FROM ignored_posts WHERE user_id = :userId)
                                        AND message_type = 6
                                         AND m.uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                         {$unreadSql}
                                        ORDER BY created DESC", [":userId" => $objUser->getId (), ":eventId" => $this->objEvent->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        if ( $arrPosts !== false )
        {
            $this->markPostsAsRead (6, $objUser);
        }

        return $arrPosts;
    }

}
