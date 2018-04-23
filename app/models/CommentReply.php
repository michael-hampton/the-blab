<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentReply
 *
 * @author michael.hampton
 */
class CommentReply
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
    private $db;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $commentId;

    /**
     *
     * @var type 
     */
    private $dateCreated;

    /**
     *
     * @var type 
     */
    private $dateUpdated;

    /**
     *
     * @var type 
     */
    private $author;

    /**
     *
     * @var type 
     */
    private $reply;

    /**
     *
     * @var type 
     */
    private $imageIds = [];

    /**
     *
     * @var type 
     */
    private $arrImages = [];

    /**
     *
     * @var type 
     */
    private $arrLikes = [];

    /**
     *
     * @var type 
     */
    private $likes;

    /**
     *
     * @var type 
     */
    private $beforeValue;

    /**
     *
     * @var type 
     */
    private $username;
    
    /**
     *
     * @var type 
     */
    private $totalCount;

    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->id = $id;

        $this->db = new Database();
        $this->db->connect ();

        if ( $this->loadObjectFromArray () === false )
        {
            throw new Exception ("Unable to populate object");
        }
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return $this->id;
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
    public function getCommentId ()
    {
        return $this->commentId;
    }

    /**
     * 
     * @return type
     */
    public function getDateCreated ()
    {
        return $this->dateCreated;
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
    public function getReply ()
    {
        return $this->reply;
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
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $commentId
     */
    public function setCommentId ($commentId)
    {
        $this->commentId = $commentId;
    }

    /**
     * 
     * @param type $dateCreated
     */
    public function setDateCreated ($dateCreated)
    {
        $this->dateCreated = $dateCreated;
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
     * @param type $author
     */
    public function setAuthor ($author)
    {
        $this->author = $author;
    }

    /**
     * 
     * @param type $reply
     */
    public function setReply ($reply)
    {
        if ( !isset ($this->beforeValue) )
        {
            $this->beforeValue = $this->reply;
        }

        $this->reply = $reply;
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
     * @param type $likes
     */
    public function setLikes ($likes)
    {
        $this->likes = $likes;
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
    public function setArrImages ($arrImages)
    {
        $this->arrImages = $arrImages;
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
     * @param type $arrLikes
     */
    public function setArrLikes ($arrLikes)
    {
        $this->arrLikes = $arrLikes;
    }

    /**
     * 
     * @return type
     */
    public function getImageIds ()
    {
        return $this->imageIds;
    }

    /**
     * 
     * @param type $imageIds
     */
    public function setImageIds ($imageIds)
    {
        $this->imageIds = $imageIds;
    }
    
    /**
     * 
     * @return type
     */
    public function getTotalCount ()
    {
        return $this->totalCount;
    }

    /**
     * 
     * @param type $totalCount
     */
    public function setTotalCount ($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    
    /**
     * 
     * @param type $comment
     * @return boolean
     */
    public function save (AuditFactory $objAudit, User $objUser)
    {
        $result = $this->db->update ("comment_reply", ["reply" => $this->reply, "date_updated" => date ("Y-m-d H:i:s")], "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        $blAudit = $objAudit->createAudit ($objUser, $this->beforeValue, $this->reply, "comment_reply", $this->id);

        if ( $blAudit === false )
        {
            trigger_error ("Unable to save audit", E_USER_WARNING);
        }

        return true;
    }

    /**
     * 
     * @param Upload $objUpload
     * @return boolean
     */
    public function addImageToComment (Upload $objUpload)
    {
        if ( !is_array ($this->imageIds) )
        {
            $this->imageIds = json_decode ($this->imageIds, true);
        }

        $this->imageIds[] = $objUpload->getId ();

        $result = $this->db->update ("comment_reply", ["image_id" => json_encode ($this->imageIds)], "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->delete ("comment_reply", "id = :id", [':id' => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function loadObjectFromArray ()
    {

        $result = $this->db->_query ("
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
                                        WHERE id = :id
                                            ORDER BY date_added DESC
                                            ", [':id' => $this->id]);
        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Unable to find record", E_USER_WARNING);
            return false;
        }

        $this->id = $result[0]['id'];
        $this->commentId = $result[0]['comment_id'];
        $this->dateCreated = $result[0]['date_added'];
        $this->reply = $result[0]['reply'];
        $this->imageIds = $result[0]['image_id'];
        $this->userId = $result[0]['user_id'];
        $this->username = $result[0]['username'];
        $this->author = $result[0]['author'];

        return true;
    }

}
