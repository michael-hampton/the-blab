<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Comment
 *
 * @author michael.hampton
 */
class Comment
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
    private $comment;

    /**
     *
     * @var type 
     */
    private $likes;

    /**
     *
     * @var type 
     */
    private $arrLikes;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $created;

    /**
     *
     * @var type 
     */
    private $msgId;

    /**
     *
     * @var type 
     */
    private $arrImages;

    /**
     *
     * @var type 
     */
    private $imageIds;

    /**
     *
     * @var type 
     */
    private $author;

    /**
     *
     * @var type 
     */
    private $username;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $dateUpdated;

    /**
     *
     * @var type 
     */
    private $arrReplies = [];

    /**
     *
     * @var type 
     */
    private $previousValue;

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
    public function getComment ()
    {
        return $this->comment;
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
    public function getArrLikes ()
    {
        return $this->arrLikes;
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
    public function getCreated ()
    {
        return $this->created;
    }

    /**
     * 
     * @return type
     */
    public function getMsgId ()
    {
        return $this->msgId;
    }

    /**
     * 
     * @param type $comment
     */
    public function setComment ($comment)
    {
        if ( !isset ($this->previousValue) )
        {
            $this->previousValue = $this->comment;
        }

        $this->comment = $comment;
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
     * @param array $arrLikes
     */
    public function setArrLikes (array $arrLikes)
    {
        $this->arrLikes = $arrLikes;
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
     * @param type $created
     */
    public function setCreated ($created)
    {
        $this->created = $created;
    }

    /**
     * 
     * @param type $msgId
     */
    public function setMsgId ($msgId)
    {
        $this->msgId = $msgId;
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
     * @return type
     */
    public function getImageIds ()
    {
        return $this->imageIds;
    }

    /**
     * 
     * @param array $arrImages
     */
    public function setArrImages (array $arrImages)
    {
        $this->arrImages = $arrImages;
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
    public function getArrReplies ()
    {
        return $this->arrReplies;
    }

    /**
     * 
     * @param type $arrReplies
     */
    public function setArrReplies ($arrReplies)
    {
        $this->arrReplies = $arrReplies;
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
    * @param AuditFactory $objAudit
    * @param User $objUser
    * @param CommentReplyFactory $objCommentReplyFactory
    * @param Comment $objComment
    * @param PostAction $objPostAction
    * @return boolean
    */
   private function deleteCommentReplies(AuditFactory $objAudit, User $objUser, CommentReplyFactory $objCommentReplyFactory, Comment $objComment, PostAction $objPostAction)
   {
       $arrReplies = $objCommentReplyFactory->getRepliesByCommentToDelete ($objComment);

        if ( $arrReplies === false )
        {
            return false;
        }

        if ( empty ($arrReplies[0]) )
        {
            return true;
        }

        foreach ($arrReplies as $objReply) {

            $blResult = $objReply->delete($objAudit, $objUser, $objPostAction);

            if ( $blResult === false )
            {
                return false;
            }
        }

        return true;
   }

   /**
    * 
    * @param Audit $objAudit
    * @param User $objUser
    * @param CommentReplyFactory $objCommentReplyFactory
    * @param PostAction $objPostAction
    * @return boolean
    */
    public function delete (AuditFactory $objAudit, User $objUser, CommentReplyFactory $objCommentReplyFactory, PostAction $objPostAction)
    {
        $blResult = $this->deleteCommentReplies($objAudit, $objUser, $objCommentReplyFactory, $this, $objPostAction);

        if ( $blResult === false )
        {
            return false;
        }

        $blResult2 = $objPostAction->deleteCommentLikes ($objAudit, $objUser, $this);

        if ( $blResult2 === false )
        {
            return false;
        }

        $result = $this->db->delete ("comments", "com_id = :commentId", [':commentId' => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }
        
        $objAudit->createAudit ($objUser, "Comment {$this->id} deleted", "COMMENT DELETED", "comment", $this->id);

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function ignoreComment (User $objUser)
    {
        $result = $this->db->create ("ignored_comments", ["user_id" => $objUser->getId (), "post_id" => $this->id]);

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
    public function removeHiddenComment (User $objUser)
    {
        $result = $this->db->delete ("ignored_comments", "user_id = :userId AND post_id = :postId", [":userId" => $objUser->getId (), ":postId" => $this->id]);

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
        $result = $this->db->update ("comments", ["comment" => $this->comment, "date_updated" => date ("Y-m-d H:i:s")], "com_id = :commentId", [":commentId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        $blAudit = $objAudit->createAudit ($objUser, $this->previousValue, $this->comment, "comment", $this->id);

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

        $result = $this->db->update ("comments", ["image_id" => json_encode ($this->imageIds)], "com_id = :commentId", [":commentId" => $this->id]);

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
    private function loadObjectFromArray ()
    {

        $result = $this->db->_query ("SELECT 
                                            CONCAT(u.fname, ' ' , u.lname)  AS author,
                                            u.username,
                                            u.uid,
                                            m.*
                                            FROM comments m
                                        INNER JOIN users u ON u.uid = m.uid_fk
                                        WHERE com_id = :commentId", [":commentId" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Unable to find record", E_USER_WARNING);
            return false;
        }

        $this->id = $result[0]['com_id'];
        $this->comment = $result[0]['comment'];
        $this->created = $result[0]['created'];
        $this->msgId = $result[0]['msg_id_fk'];
        $this->imageIds = $result[0]['image_id'];
        $this->userId = $result[0]['uid_fk'];
        $this->author = $result[0]['author'];
        $this->username = $result[0]['username'];

        return true;
    }

}