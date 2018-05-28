<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPost
 *
 * @author michael.hampton
 */
class UserPost extends BasePostFactory implements PostInterface
{

    /**
     * 
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory)
    {
        parent::__construct ($objPostActionFactory, $objUploadFactory, $objCommentFactory, $objReviewFactory, $objTagUserFactory, $objCommentReplyFactory);
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @param type $blReview
     */
    public function createComment ($comment, User $objUser, \JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, array $imageIds = null)
    {
        
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @param type $usersLocation
     * @param type $messageType
     * @param type $privacyOption
     * @return type
     */
    public function createPost ($comment, User $objUser, JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, array $imageIds = null, $usersLocation = null, $messageType = 3, $privacyOption = null)
    {
        return $this->savePost ($comment, $objUser, $objBadWordFilter, $imageIds, $usersLocation, $messageType, $privacyOption);
    }

    /**
     * 
     * @param User $objUser
     * @param type $sortBy
     * @param type $intPageNo
     * @param type $intNoPerPage
     * @return type
     */
    public function getPostsForUser (User $objUser, $sortBy = null, $intPageNo = null, $intNoPerPage = null, $blShowUsersPosts = true, $blUnread = true)
    {

        $sql = "
                                        SELECT 
                                            u.uid,
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            u.username,
                                            message AS content,
                                            sharers_comment,
                                            u2.username AS original_poster_id,
                                            m.image_id,
                                            m.sharers_username,
                                            m.post_security,
                                            m.like_count AS likes,
                                            m.date_updated,
                                            user_location,
                                            m.created AS date_added,
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
                                            LEFT JOIN comments com ON com.msg_id_fk = m.msg_id
                                        INNER JOIN users u ON u.uid = m.uid_fk
                                        WHERE (
                                                (m.uid_fk = :userId)
                                            OR (m.uid_fk IN (
                                                SELECT friend_one 
                                                FROM friends 
                                                WHERE friend_two = :userId AND status = '2') 
                                            OR m.uid_fk IN (
                                                SELECT friend_two 
                                                FROM friends 
                                                WHERE friend_one = :userId AND status = '2') AND m.post_security = 'friend') OR
                                                m.post_security = 'public'
                                            )
                                            AND msg_id NOT IN (SELECT post_id FROM ignored_posts WHERE user_id = :userId)
                                             AND m.uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                        AND message_type = 3";

        if ( $blShowUsersPosts === false )
        {
            $sql .= " AND m.uid_fk != :userId";
        }

        $sql .= " GROUP BY m.msg_id ";


        $sql .= $sortBy === null ? " ORDER BY m.msg_id DESC" : " ORDER BY com.created DESC";

        if ( $intPageNo !== null && $intNoPerPage !== null )
        {
            $sql .= " LIMIT " . $intPageNo . " ," . $intNoPerPage;
        }

        $arrResults = $this->db->_query ($sql, [':userId' => $objUser->getId (), ':userAdded' => $objUser->getId ()]);



        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        return $arrPosts;
    }

    /**
     * 
     * @param type $array
     * @return type
     */
    private function array_flatten ($array)
    {
        $result = array();

        if ( !is_array ($array) )
        {
            $array = func_get_args ();
        }

        foreach ($array as $key => $value) {
            if ( is_array ($value) )
            {
                $result = array_merge ($result, $this->array_flatten ($value));
            }
            else
            {
                $result = array_merge ($result, array($key => $value));
            }
        }

        return $result;
    }
    
    private function getUnreadPostsForUser(User $objUser, $blFeed = true)
    {
        if($blFeed === true) {
            
        } else {
            
        }
        
    }

    /**
     * Builds posts array for the news feed which is a combination of the users posts, posts from events they are subscribed to, pages they are followers of and groups they are members of
     * @param array $arrPages
     * @param array $arrGroups
     * @param array $arrEvents
     * @param type $objUser
     * @param type $sortBy
     * @param type $intPageNo
     * @param type $intNoPerPage
     * @return boolean
     */
    public function getPostsForNewsFeed (array $arrPages, array $arrGroups, array $arrEvents, $objUser, $blShowGroups = true, $sortBy = null, $intPageNo = null, $intNoPerPage = null, UserSettings $objUserSettings, $blUnread = true )
    {
        $arrPagePosts = [];
        $arrGroupPosts = [];
        $arrEventPosts = [];
        $arrAllPosts = [];

        try {
            if ( !empty ($arrPages) )
            {
                foreach ($arrPages as $objPage) {
                    $objPagePost = (new PagePost ($objPage, $this->objLikes, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory));

                    $pagePost = $objPagePost->getComments ($objUser);

                    if ( $pagePost === false )
                    {
                        continue;
                    }

                    array_push ($arrPagePosts, $pagePost);
                }
            }

            if ( $objUserSettings->getFeedSetting ('page') === true )
            {
                $arrPagePosts = $this->array_flatten ($arrPagePosts);

                $arrAllPosts = array_merge ($arrPagePosts, $arrAllPosts);
            }

            if ( !empty ($arrGroups) && $blShowGroups === true && $objUserSettings->getFeedSetting ('group') === true )
            {
                foreach ($arrGroups as $objGroup) {

                    $objGroupPost = new GroupPost ($objGroup, $this->objLikes, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);

                    $groupPost = $objGroupPost->getComments ($objUser);

                    if ( $groupPost === false )
                    {
                        continue;
                    }

                    array_push ($arrGroupPosts, $groupPost);
                }

                $arrGroupPosts = $this->array_flatten ($arrGroupPosts);
                $arrAllPosts = array_merge ($arrGroupPosts, $arrAllPosts);
            }

            if ( !empty ($arrEvents) )
            {
                foreach ($arrEvents as $objEvent) {

                    $objEventPost = new EventPost ($objEvent, $this->objLikes, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);

                    $eventPost = $objEventPost->getComments ($objUser);

                    if ( $eventPost === false )
                    {
                        continue;
                    }

                    array_push ($arrEventPosts, $eventPost);
                }
            }

            if ( $objUserSettings->getFeedSetting ('event') === true )
            {
                $arrEventPosts = $this->array_flatten ($arrEventPosts);
            }


            //$arrAllPosts = array_merge ($arrEventPosts, $arrAllPosts);

            $arrUserPosts = $this->getPostsForUser ($objUser, $sortBy, $intPageNo, $intNoPerPage, false, $blUnread);

            if ( $arrUserPosts === false )
            {
                return false;
            }

            $arrAllPosts = array_merge ($arrUserPosts, $arrAllPosts);

            return $arrAllPosts;
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
        }
    }

    /**
     * 
     * @param User $objUser
     */
    public function getComments (User $objUser)
    {
        
    }

}
