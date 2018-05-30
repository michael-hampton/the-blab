<?php

/**
 * Description of PostFactory
 *
 * @author michael.hampton
 */
class BasePostFactory
{

    /**
     *
     * @var type 
     */
    protected $objLikes;

    /**
     *
     * @var type 
     */
    protected $objUploadFactory;

    /**
     *
     * @var type 
     */
    protected $objCommentFactory;

    /**
     *
     * @var type 
     */
    protected $objReviewFactory;

    /**
     *
     * @var type 
     */
    protected $objTagUserFactory;

    /**
     *
     * @var type 
     */
    protected $objCommentReplyFactory;

    /**
     *
     * @var array 
     */
    protected $validationFailures = [];

    /**
     *
     * @var type 
     */
    protected $db;

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

    /**
     * 
     * @return type
     */
    protected function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    /**
     * 
     * @param type $comment
     * @param User $objUser
     * @param array $imageIds
     * @param type $usersLocation
     * @param type $messageType
     * @param type $privacyOption
     * @return \Post|boolean
     */
    protected function savePost ($comment, User $objUser, JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, array $imageIds = null, $usersLocation = null, $messageType = 3, $privacyOption = null)
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
     * @param type $arrResults
     * @param type $blRatings
     * @param User $objUser
     * @return \Post|boolean
     */
    protected function buildPostsObject ($arrResults, $blRatings = false, User $objUser)
    {
        if ( $arrResults === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
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
     * @param type $type
     * @param User $objUser
     * @return boolean
     */
    protected function markPostsAsRead ($type, User $objUser)
    {
        $sql = "INSERT INTO messages_read (user_id, message_id, message_type)";

        $sql .= "SELECT :userId AS user_id, msg_id AS message_id, message_type FROM messages WHERE 1=1 ";

        if ( (int) $type === 3 )
        {
            $sql .= " AND (
                (uid_fk = :userId) 
                OR (
                    uid_fk IN ( 
                        SELECT friend_one FROM friends WHERE friend_two = :userId AND status = '2'
                    )
                ) 
                OR uid_fk IN (
                    SELECT friend_two FROM friends WHERE friend_one = :userId AND status = '2'
                )
            )";
        }

        $sql .= " AND message_type = :messageType AND msg_id NOT IN (SELECT message_id FROM messages_read WHERE user_id = :userId)";

        $result = $this->db->_query ($sql, [":userId" => $objUser->getId (), ":messageType" => $type]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
