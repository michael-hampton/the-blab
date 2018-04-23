<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostFactory
 *
 * @author michael.hampton
 */
class PostFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $objLikes;

    /**
     *
     * @var type 
     */
    private $objUploadFactory;

    /**
     *
     * @var type 
     */
    private $objCommentFactory;

    /**
     *
     * @var type 
     */
    private $objReviewFactory;

    /**
     *
     * @var type 
     */
    private $objTagUserFactory;

    /**
     *
     * @var type 
     */
    private $objCommentReplyFactory;

    /**
     * 
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     */
    public function __construct (
    PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory
    )
    {
        $this->db = new Database();
        $this->db->connect ();

        $this->objLikes = $objPostActionFactory;
        $this->objUploadFactory = $objUploadFactory;
        $this->objCommentFactory = $objCommentFactory;
        $this->objReviewFactory = $objReviewFactory;
        $this->objTagUserFactory = $objTagUserFactory;
        $this->objCommentReplyFactory = $objCommentReplyFactory;
    }

    private function buildPostsObject ($arrResults, $blRatings = false, User $objUser)
    {
        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        try {

            $arrPosts = [];

            foreach ($arrResults as $count => $arrResult) {

                $objPost = new Post ($arrResult['id']);
                $objPost->setLoveCount ($arrResult['love_count']);
                $objPost->setWowCount ($arrResult['wow_count']);
                $objPost->setSadCount ($arrResult['sad_count']);
                $objPost->setHahaCount ($arrResult['haha_count']);
                $objPost->setAngryCount ($arrResult['angry_count']);
                $objPost->setCreated ($arrResult['date_added']);
                $objPost->setMessage ($arrResult['content']);
                $objPost->setImageId ($arrResult['image_id']);
                $objPost->setLikes ($arrResult['likes']);
                $objPost->setAuthor ($arrResult['author']);
                $objPost->setMessageType (isset ($arrResult['message_type']) ? $arrResult['message_type'] : '');
                $objPost->setUsername ($arrResult['username']);
                $objPost->setLocation (isset ($arrResult['user_location']) ? $arrResult['user_location'] : '');
                $objPost->setUserId (isset ($arrResult['uid']) ? $arrResult['uid'] : '');
                $objPost->setClonedFromAuthor ($arrResult['original_poster']);
                $objPost->setSharersComment ($arrResult['sharers_comment']);
                $objPost->setDateUpdated ($arrResult['date_updated']);
                $objPost->setClonedFrom ($arrResult['original_poster_id']);
                $objPost->setSharersUsername ($arrResult['sharers_username']);
                $objPost->setPostSecurity ($arrResult['post_security']);
                $objPost->setSharersFullname ($arrResult['sharers_fullname']);

                $arrImages = $this->objUploadFactory->getImagesForPost ($objPost);

                if ( $blRatings === true )
                {
                    $arrRatings = $this->objReviewFactory->getReviewsForPost ($objPost);

                    if ( !empty ($arrRatings) )
                    {
                        $objPost->setArrRatings ($arrRatings);
                    }
                }

                if ( !empty ($arrImages[0]) )
                {
                    $objPost->setArrImages ($arrImages);
                }

                $arrComments = $this->objCommentFactory->getCommentsForPost ($objPost, $this->objUploadFactory, $this->objLikes, $this->objCommentReplyFactory, $objUser);

                if ( $arrComments !== false )
                {
                    $objPost->setArrComments ($arrComments);
                }

                $arrLikes = $this->objLikes->getLikeListForPost ($objPost);

                if ( $arrLikes !== FALSE )
                {
                    $objPost->setArrLikes ($arrLikes);
                }

                $arrTags = $this->objTagUserFactory->getTaggedUsersForPost ($objPost);

                if ( !empty ($arrTags) )
                {

                    $objPost->setArrTags ($arrTags);
                }

                $arrPosts[] = $objPost;
            }

            return $arrPosts;
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }
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
    public function getPostsForNewsFeed (array $arrPages, array $arrGroups, array $arrEvents, $objUser, $blShowGroups = true, $sortBy = null, $intPageNo = null, $intNoPerPage = null, UserSettings $objUserSettings)
    {
        $arrPagePosts = [];
        $arrGroupPosts = [];
        $arrEventPosts = [];
        $arrAllPosts = [];

        if ( !empty ($arrPages) )
        {
            foreach ($arrPages as $objPage) {
                $pagePost = $this->getPostsForPage ($objPage, $objUser);

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
                $groupPost = $this->getPostsForGroup ($objGroup, $objUser);

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
                $eventPost = $this->getPostsForEvent ($objEvent, $objUser);

                if ( $eventPost === false )
                {
                    continue;
                }

                array_push ($arrEventPosts, $eventPost);
            }
        }
        
        if ( $objUserSettings->getFeedSetting ('event') === true ) {
             $arrEventPosts = $this->array_flatten ($arrEventPosts);
        }

       
        //$arrAllPosts = array_merge ($arrEventPosts, $arrAllPosts);

        $arrUserPosts = $this->getPostsForUser ($objUser, $sortBy, $intPageNo, $intNoPerPage, false);

        if ( $arrUserPosts === false )
        {
            return false;
        }

        $arrAllPosts = array_merge ($arrUserPosts, $arrAllPosts);

        return $arrAllPosts;
    }

    /**
     * 
     * @param User $objUser
     * @param type $sortBy
     * @param type $intPageNo
     * @param type $intNoPerPage
     * @return type
     */
    public function getPostsForUser (User $objUser, $sortBy = null, $intPageNo = null, $intNoPerPage = null, $blShowUsersPosts = true)
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

    /**
     * 
     * @param Group $objGroup
     * @param User $objUser
     * @return type
     */
    public function getPostsForEvent (Event $objEvent, User $objUser)
    {

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
                                        ORDER BY created DESC", [":userId" => $objUser->getId (), ":eventId" => $objEvent->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);


        return $arrPosts;
    }

    /**
     * 
     * @param Group $objGroup
     * @param User $objUser
     * @return type
     */
    public function getPostsForGroup (Group $objGroup, User $objUser)
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
                , [':userId' => $objUser->getId (), ":groupId" => $objGroup->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        return $arrPosts;
    }

    /**
     * 
     * @param Page $objPage
     * @param User $objUser
     * @return type
     */
    public function getPostsForPage (Page $objPage, User $objUser)
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
                , [':userId' => $objUser->getId (), ":pageId" => $objPage->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, false, $objUser);

        return $arrPosts;
    }

    /**
     * 
     * @param Page $objPage
     * @param User $objUser
     * @return type
     */
    public function getReviewsForPage (Page $objPage, User $objUser)
    {
        $arrResults = $this->db->_query ("
                                        SELECT 
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            u.username,
                                             sharers_comment,
                                             m.sharers_username,
                                            m.post_security,
                                            message AS content,
                                             m.date_updated,
                                            image_id,
                                            u2.username AS original_poster_id,
                                            like_count AS likes,
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
                                        INNER JOIN page_comment gc ON gc.post_id = msg_id AND gc.page_id = :pageId
                                        WHERE uid_fk = :userId
                                        AND msg_id NOT IN (SELECT post_id FROM ignored_posts WHERE user_id = :userId)
                                        AND message_type = 4
                                        AND m.uid_fk NOT IN (SELECT user_added FROM blocked_friend WHERE blocked_user = :userAdded)
                                        ORDER BY created DESC"
                , [':userId' => $objUser->getId (), ":pageId" => $objPage->getId (), ':userAdded' => $objUser->getId ()]);

        $arrPosts = $this->buildPostsObject ($arrResults, true, $objUser);

        return $arrPosts;
    }

    /**
     * 
     * @param Group $objGroup
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @return \Post|boolean
     */
    public function createGroupComment (Group $objGroup, $comment, User $objUser, array $imageIds = null)
    {
        $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
        $comment = $objBadWordFilter->clean ($comment);

        $objPost = $this->createPost ($comment, $objUser, $imageIds, null, 1);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("group_comment", ["post_id" => $objPost->getId (), "group_id" => $objGroup->getId ()]);

        if ( $result2 === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return $objPost;
    }

    /**
     * 
     * @param Event $objEvent
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @return boolean
     */
    public function createEventComment (Event $objEvent, $comment, User $objUser, array $imageIds = null)
    {
        $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
        $comment = $objBadWordFilter->clean ($comment);

        $objPost = $this->createPost ($comment, $objUser, $imageIds, null, 6);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("event_comment", ["post_id" => $objPost->getId (), "event_id" => $objEvent->getId ()]);

        if ( $result2 === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return $objPost;
    }

    /**
     * 
     * @param Page $objPage
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @return \Post|boolean
     */
    public function createPageComment (Page $objPage, $comment, User $objUser, array $imageIds = null, $blReview = false)
    {
        $postType = $blReview === true ? 4 : 2;

        $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
        $comment = $objBadWordFilter->clean ($comment);

        $objPost = $this->createPost ($comment, $objUser, $imageIds, null, $postType);

        if ( $objPost === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result2 = $this->db->create ("page_comment", ["post_id" => $objPost->getId (), "page_id" => $objPage->getId ()]);

        if ( $result2 === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return $objPost;
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @param type $usersLocation
     * @param type $messageType
     * @return \Post|boolean
     */
    public function createPost ($comment, User $objUser, array $imageIds = null, $usersLocation = null, $messageType = 3, $privacyOption = null)
    {
        if ( trim ($comment) === "" )
        {
            $this->validationFailures[] = "Comment is a mandatory field";
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

        $arrParameters = array(
            'message' => $comment,
            'uid_fk' => $userId,
            'created' => date ("Y-m-d H:i:s"),
            'user_location' => $usersLocation,
            "message_type" => $messageType,
            "post_security" => $privacyOption
        );


        if ( $imageIds !== null )
        {
            $arrParameters['image_id'] = json_encode ($imageIds);
        }

        $result = $this->db->create ('messages', $arrParameters);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $objPost = new Post ($result);

        return $objPost;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @param type $newComment
     * @param type $sharersUsername
     * @param type $postSecurity
     * @return \Post|boolean
     */
    public function clonePost (Post $objPost, User $objUser, $newComment = null, $sharersUsername = null, $postSecurity = null)
    {
        $result = $this->db->create ("messages", [
            "message" => $objPost->getMessage (),
            "uid_fk" => $objUser->getId (),
            "created" => date ("Y-m-d H:i:s"),
            "image_id" => $objPost->getImageId (),
            "message_type" => $objPost->getMessageType (),
            "original_poster" => $objPost->getUserId (),
            "sharers_comment" => $newComment,
            "sharers_username" => $sharersUsername,
            "post_security" => $postSecurity
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Unable to save", E_USER_WARNING);
            return false;
        }

        return new Post ($result);
    }

}
