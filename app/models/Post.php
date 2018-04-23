<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Post
 *
 * @author michael.hampton
 */
class Post
{

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $message;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $likes;

    /**
     *
     * @var type 
     */
    private $created;

    /**
     *
     * @var type 
     */
    private $arrLikes;

    /**
     *
     * @var type 
     */
    private $arrComments;

    /**
     *
     * @var type 
     */
    private $arrImages;

    /**
     *
     * @var type 
     */
    private $imageId;

    /**
     *
     * @var type 
     */
    private $username;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $messageType;

    /**
     *
     * @var type 
     */
    private $author;

    /**
     *
     * @var type 
     */
    private $clonedFrom;

    /**
     *
     * @var type 
     */
    private $clonedFromAuthor;

    /**
     *
     * @var type 
     */
    private $sharersComment;

    /**
     *
     * @var type 
     */
    private $dateUpdated;

    /**
     *
     * @var type 
     */
    private $previousValue;

    /**
     *
     * @var type 
     */
    private $arrTags = [];

    /**
     *
     * @var type 
     */
    private $postSecurity;

    /**
     *
     * @var type 
     */
    private $sharersUsername;

    /**
     *
     * @var type 
     */
    private $wowCount;

    /**
     *
     * @var type 
     */
    private $sadCount;

    /**
     *
     * @var type 
     */
    private $angryCount;

    /**
     *
     * @var type 
     */
    private $loveCount;

    /**
     *
     * @var type 
     */
    private $hahaCount;

    /**
     *
     * @var type 
     */
    private $sharersFullname;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $arrRatings = [];

    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function __construct ($id)
    {
        $this->id = $id;

        $this->db = new Database();

        $this->db->connect ();

        if ( $this->loadObjectFromArray () === false )
        {
            throw new Exception ("Failed to load object");
        }
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return (int) $this->id;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getMessage ()
    {
        return $this->message;
    }

    /**
     * 
     * @return type
     */
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @return type
     */
    public function getLikes ()
    {
        return $this->likes;
    }

    /**
     * 
     * @return type
     */
    public function getCreated ()
    {
        return $this->created;
    }

    /**
     * 
     * @return type
     */
    public function getArrLikes ()
    {
        return $this->arrLikes;
    }

    /**
     * 
     * @return type
     */
    public function getArrComments ()
    {
        return $this->arrComments;
    }

    /**
     * 
     * @return type
     */
    public function getArrImages ()
    {
        return $this->arrImages;
    }

    /**
     * 
     * @param type $arrImages
     */
    public function setArrImages (array $arrImages)
    {
        $this->arrImages = $arrImages;
    }

    /**
     * 
     * @return type
     */
    public function getImageId ()
    {
        return $this->imageId;
    }

    /**
     * 
     * @param type $message
     */
    public function setMessage ($message)
    {
        if ( !isset ($this->previousValue) )
        {
            $this->previousValue = $this->message;
        }

        $this->message = $message;
    }

    /**
     * 
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $likes
     */
    public function setLikes ($likes)
    {
        $this->likes = $likes;
    }

    /**
     * 
     * @param type $created
     */
    public function setCreated ($created)
    {
        $this->created = $created;
    }

    /**
     * 
     * @param array $arrLikes
     */
    public function setArrLikes (array $arrLikes)
    {
        $this->arrLikes = $arrLikes;
    }

    /**
     * 
     * @return type
     */
    public function getArrRatings ()
    {
        return $this->arrRatings;
    }

    /**
     * 
     * @param type $arrRatings
     */
    public function setArrRatings ($arrRatings)
    {
        $this->arrRatings = $arrRatings;
    }

    /**
     * 
     * @param array $arrComments
     */
    public function setArrComments (array $arrComments)
    {
        $this->arrComments = $arrComments;
    }

    /**
     * 
     * @param type $imageId
     */
    public function setImageId ($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * 
     * @return type
     */
    public function getDateUpdated ()
    {
        return $this->dateUpdated;
    }

    /**
     * 
     * @param type $dateUpdated
     */
    public function setDateUpdated ($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * 
     * @return type
     */
    public function getAuthor ()
    {
        return $this->author;
    }

    /**
     * 
     * @return type
     */
    public function getUsername ()
    {
        return $this->username;
    }

    /**
     * 
     * @param type $username
     */
    public function setUsername ($username)
    {
        $this->username = $username;
    }

    /**
     * 
     * @param type $author
     */
    public function setAuthor ($author)
    {
        $this->author = $author;
    }

    /**
     * 
     * @return type
     */
    public function getLocation ()
    {
        return $this->location;
    }

    /**
     * 
     * @param type $location
     */
    public function setLocation ($location)
    {
        $this->location = $location;
    }

    /**
     * 
     * @return type
     */
    public function getMessageType ()
    {
        return $this->messageType;
    }

    /**
     * 
     * @param type $messageType
     */
    public function setMessageType ($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * 
     * @return type
     */
    public function getClonedFrom ()
    {
        return $this->clonedFrom;
    }

    /**
     * 
     * @return type
     */
    public function getClonedFromAuthor ()
    {
        return $this->clonedFromAuthor;
    }

    /**
     * 
     * @param type $clonedFrom
     */
    public function setClonedFrom ($clonedFrom)
    {
        $this->clonedFrom = $clonedFrom;
    }

    public function getWowCount ()
    {
        return $this->wowCount;
    }

    public function getSadCount ()
    {
        return $this->sadCount;
    }

    public function getAngryCount ()
    {
        return $this->angryCount;
    }

    public function getLoveCount ()
    {
        return $this->loveCount;
    }

    public function getHahaCount ()
    {
        return $this->hahaCount;
    }

    /**
     * 
     * @param type $wowCount
     */
    public function setWowCount ($wowCount)
    {
        $this->wowCount = $wowCount;
    }

    /**
     * 
     * @param type $sadCount
     */
    public function setSadCount ($sadCount)
    {
        $this->sadCount = $sadCount;
    }

    /**
     * 
     * @param type $angryCount
     */
    public function setAngryCount ($angryCount)
    {
        $this->angryCount = $angryCount;
    }

    /**
     * 
     * @param type $loveCount
     */
    public function setLoveCount ($loveCount)
    {
        $this->loveCount = $loveCount;
    }

    /**
     * 
     * @param type $hahaCount
     */
    public function setHahaCount ($hahaCount)
    {
        $this->hahaCount = $hahaCount;
    }

    /**
     * 
     * @param type $clonedFromAuthor
     */
    public function setClonedFromAuthor ($clonedFromAuthor)
    {
        $this->clonedFromAuthor = $clonedFromAuthor;
    }

    /**
     * 
     * @return type
     */
    public function getSharersComment ()
    {
        return $this->sharersComment;
    }

    /**
     * 
     * @param type $sharersComment
     */
    public function setSharersComment ($sharersComment)
    {
        $this->sharersComment = $sharersComment;
    }

    /**
     * 
     * @return type
     */
    public function getPostSecurity ()
    {
        return $this->postSecurity;
    }

    /**
     * 
     * @return type
     */
    public function getSharersUsername ()
    {
        return $this->sharersUsername;
    }

    /**
     * 
     * @param type $postSecurity
     */
    public function setPostSecurity ($postSecurity)
    {
        $this->postSecurity = $postSecurity;
    }

    /**
     * 
     * @param type $sharersUsername
     */
    public function setSharersUsername ($sharersUsername)
    {
        $this->sharersUsername = $sharersUsername;
    }

    /**
     * 
     * @return type
     */
    public function getSharersFullname ()
    {
        return $this->sharersFullname;
    }

    /**
     * 
     * @param type $sharersFullname
     */
    public function setSharersFullname ($sharersFullname)
    {
        $this->sharersFullname = $sharersFullname;
    }

    /**
     * 
     * @return type
     */
    public function getArrTags ()
    {
        return $this->arrTags;
    }

    /**
     * 
     * @param type $arrTags
     */
    public function setArrTags ($arrTags)
    {
        $this->arrTags = $arrTags;
    }

    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result2 = $this->db->delete ("message_like", "msg_id_fk = :messageId", [':messageId' => $this->id]);

        if ( $result2 === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }


        $result = $this->db->delete ("messages", "msg_id = :messageId", [':messageId' => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function ignorePost (User $objUser)
    {
        $result = $this->db->create ("ignored_posts", ["user_id" => $objUser->getId (), "post_id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function removeHiddenPost (User $objUser)
    {
        $result = $this->db->delete ("ignored_posts", "user_id = :userId AND post_id = :postId", [":userId" => $objUser->getId (), ":postId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param AuditFactory $objAudit
     * @param User $objUser
     * @return boolean
     */
    public function save (AuditFactory $objAudit, User $objUser)
    {
        $result = $this->db->update ("messages", ["message" => $this->message, "date_updated" => date ("Y-m-d H:i:s")], "msg_id = :messageId", [":messageId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        $blAudit = $objAudit->createAudit ($objUser, $this->previousValue, $this->message, "post", $this->id);

        if ( $blAudit === false )
        {
            trigger_error ("Unable to save audit", E_USER_WARNING);
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function loadObjectFromArray ()
    {
        $sql = "SELECT 
                                            u.uid,
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            CONCAT(u2.fname, ' ' , u2.lname)  AS original_poster,
                                            u.username,
                                            m.*,
                                            u2.username AS original_poster_id,
                                            user_location,
                                             (SELECT  CONCAT(fname, ' ' , lname)
                                                FROM users WHERE username = m.sharers_username) AS sharers_fullname
                                            FROM messages m
                                            LEFT JOIN users u2 ON u2.uid = m.original_poster
                                            LEFT JOIN comments com ON com.msg_id_fk = m.msg_id
                                        INNER JOIN users u ON u.uid = m.uid_fk
                                        WHERE msg_id = :messageId";



        $arrResults = $this->db->_query ($sql, [":messageId" => $this->id]);

        if ( $arrResults === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $this->loveCount = $arrResults[0]['love_count'];
        $this->wowCount = $arrResults[0]['wow_count'];
        $this->sadCount = $arrResults[0]['sad_count'];
        $this->hahaCount = $arrResults[0]['haha_count'];
        $this->angryCount = $arrResults[0]['angry_count'];
        $this->created = $arrResults[0]['created'];
        $this->message = $arrResults[0]['message'];

        $this->likes = $arrResults[0]['like_count'];
        $this->author = $arrResults[0]['author'];
        $this->messageType = $arrResults[0]['message_type'];
        $this->username = $arrResults[0]['username'];
        $this->location = $arrResults[0]['user_location'];
        $this->userId = $arrResults[0]['uid'];
        $this->clonedFromAuthor = $arrResults[0]['original_poster'];
        $this->sharersComment = $arrResults[0]['sharers_comment'];
        $this->dateUpdated = $arrResults[0]['date_updated'];
        $this->clonedFrom = $arrResults[0]['original_poster_id'];
        $this->sharersUsername = $arrResults[0]['sharers_username'];
        $this->postSecurity = $arrResults[0]['post_security'];
        $this->sharersFullname = $arrResults[0]['sharers_fullname'];
        $this->setImageId($arrResults[0]['image_id']);

        return true;
    }

}