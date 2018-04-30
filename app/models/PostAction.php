<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostAction
 *
 * @author michael.hampton
 */
class PostAction
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
    private $validationFailures = [];

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();

        $this->db->connect ();
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
     * @param Comment $objComment
     * @param User $objUser
     * @return boolean
     */
    public function likeComment (Comment $objComment, User $objUser)
    {
        $checkResult = $this->db->_select ("comment_like", "com_id_fk = :commentId AND uid_fk = :userId", [":commentId" => $objComment->getId (), ":userId" => $objUser->getId ()]);

        if ( $checkResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( !empty ($checkResult[0]) )
        {
            trigger_error ("User tried to like something twice", E_USER_WARNING);
            return false;
        }

        $result = $this->db->_query ("UPDATE comments 
                            SET like_count = 
                                if(like_count is not null, like_count + 1, 1) 
                            WHERE com_id = :commentId", [":commentId" => $objComment->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->create ('comment_like', array(
            'com_id_fk' => $objComment->getId (),
            'uid_fk' => $objUser->getId (),
            'created' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function unlikeComment (Comment $objComment, User $objUser)
    {
        $result = $this->db->_query ("UPDATE comments 
                            SET like_count = 
                                if(like_count is not null, like_count - 1, 0) 
                            WHERE com_id = :commentId", [":commentId" => $objComment->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->delete ("comment_like", "uid_fk = :userId", [':userId' => $objUser->getId ()]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function unlikeReply (CommentReply $objReply, User $objUser)
    {
        $result = $this->db->_query ("UPDATE comment_reply 
                            SET likes = 
                                if(likes is not null, likes - 1, 0) 
                            WHERE id = :id", [":id" => $objReply->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->delete ("comment_reply_like", "uid_fk = :userId AND com_id_fk = :id", [':userId' => $objUser->getId (), ":id" => $objReply->getId ()]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function likeReply (CommentReply $objReply, User $objUser)
    {
        $checkResult = $this->db->_select ("comment_reply_like", "com_id_fk = :commentId AND uid_fk = :userId", [":commentId" => $objReply->getId (), ":userId" => $objUser->getId ()]);

        if ( $checkResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( !empty ($checkResult[0]) )
        {
            trigger_error ("User tried to like something twice", E_USER_WARNING);
            return false;
        }

        $result = $this->db->_query ("UPDATE comment_reply 
                            SET likes = 
                                if(likes is not null, likes + 1, 1) 
                            WHERE id = :id", [":id" => $objReply->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->create ('comment_reply_like', array(
            'com_id_fk' => $objReply->getId (),
            'uid_fk' => $objUser->getId (),
            'created' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function likePost (Post $objPost, User $objUser)
    {
        $checkResult = $this->db->_select ("message_like", "msg_id_fk = :postId AND uid_fk = :userId", [":postId" => $objPost->getId (), ":userId" => $objUser->getId ()]);

        if ( $checkResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( !empty ($checkResult[0]) )
        {
            trigger_error ("User tried to like something twice", E_USER_WARNING);
            return false;
        }

        $result = $this->db->_query ("UPDATE messages 
                            SET like_count = 
                                if(like_count is not null, like_count + 1, 1) 
                            WHERE msg_id = :postId", [":postId" => $objPost->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->create ('message_like', array(
            'msg_id_fk' => $objPost->getId (),
            'uid_fk' => $objUser->getId (),
            'created' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Post $objPost
     * @param User $objUser
     * @return boolean
     */
    public function unlikePost (Post $objPost, User $objUser)
    {
        $result = $this->db->_query ("UPDATE messages 
                            SET like_count = 
                                if(like_count is not null, like_count - 1, 0) 
                            WHERE msg_id = :postId", [":postId" => $objPost->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->delete ("message_like", "uid_fk = :userId AND msg_id_fk = :id", [':userId' => $objUser->getId (), ":id" => $objPost->getId ()]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Comment $objComment
     * @return type
     */
    public function getLikesForComment (Comment $objComment)
    {
        $result = $this->db->_select ("comments", "com_id = :commentId", [':commentId' => $objComment->getId ()]);

        if ( empty ($result[0]['like_count']) )
        {
            return [];
        }

        return $result[0]['like_count'];
    }

    /**
     * 
     * @param Comment $objComment
     * @return type
     */
    public function getLikesForReply (CommentReply $objComment)
    {
        $result = $this->db->_select ("comment_reply", "id = :id", [':id' => $objComment->getId ()]);

        if ( empty ($result[0]['likes']) )
        {
            return 0;
        }

        return $result[0]['likes'];
    }

    /**
     * 
     * @param Post $objPost
     * @return type
     */
    public function getLikesForPost (Post $objPost)
    {
        $result = $this->db->_select ("messages", "msg_id = :messageId", [':messageId' => $objPost->getId ()]);

        if ( empty ($result[0]['like_count']) )
        {
            return 0;
        }

        return $result[0]['like_count'];
    }

    /**
     * 
     * @param type $type
     * @param User $objUser
     * @param Post $objPost
     * @return boolean
     */
    public function add ($type, User $objUser, Post $objPost)
    {
        switch ($type) {
            case "Love":
                $columnName = "love_count";
                $type = "love";
                break;

            case "Wow":
                $columnName = "wow_count";
                $type = "wow";

                break;

            case "Haha":
                $columnName = "haha_count";
                $type = "haha";
                break;

            case "Sad":
                $columnName = "sad_count";
                $type = "sad";
                break;

            case "Angry":
                $columnName = "angry_count";
                $type = "angry";
                break;
            default:
                return false;
        }

        $checkResult = $this->db->_select ("message_like", "msg_id_fk = :postId AND uid_fk = :userId", [":postId" => $objPost->getId (), ":userId" => $objUser->getId ()]);

        if ( $checkResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( !empty ($checkResult[0]) )
        {
            $this->validationFailures[] = "It appears that you have already reacted to this post";
            trigger_error ("User tried to like something twice", E_USER_WARNING);
            return false;
        }

        $result = $this->db->_query ("UPDATE messages 
                            SET " . $columnName . " = 
                                if(" . $columnName . " is not null, " . $columnName . " + 1, 1) 
                            WHERE msg_id = :postId", [":postId" => $objPost->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->create ('message_like', array(
            'msg_id_fk' => $objPost->getId (),
            'uid_fk' => $objUser->getId (),
            'created' => date ("Y-m-d H:i:s"),
            'reaction_type' => $type
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $type
     * @param User $objUser
     * @param Post $objPost
     * @return boolean
     */
    public function remove ($type, User $objUser, Post $objPost)
    {
        switch ($type) {
            case "Love":
                $columnName = "love_count";
                $type = "love";
                break;

            case "Wow":
                $columnName = "wow_count";
                $type = "wow";

                break;

            case "Haha":
                $columnName = "haha_count";
                $type = "haha";
                break;

            case "Sad":
                $columnName = "sad_count";
                $type = "sad";
                break;

            case "Angry":
                $columnName = "angry_count";
                $type = "angry";
                break;
            default:
                return false;
        }

        $result = $this->db->_query ("UPDATE messages 
                            SET " . $columnName . " = 
                                if(" . $columnName . " is not null, " . $columnName . " - 1, 0) 
                            WHERE msg_id = :postId", [":postId" => $objPost->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->db->delete ("message_like", "uid_fk = :userId AND msg_id_fk = :id", [':userId' => $objUser->getId (), ":id" => $objPost->getId ()]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $type
     * @param Post $objPost
     * @return boolean
     */
    public function getReactionCounts ($type, Post $objPost)
    {
        $type = strtolower (trim ($type));

        switch ($type) {
            case "love":
                $columnName = "love_count";
                $type = "love";
                break;

            case "wow":
                $columnName = "wow_count";
                $type = "wow";

                break;

            case "haha":
                $columnName = "haha_count";
                $type = "haha";
                break;

            case "sad":
                $columnName = "sad_count";
                $type = "sad";
                break;

            case "angry":
                $columnName = "angry_count";
                $type = "angry";
                break;
            default:
                return false;
        }

        $result = $this->db->_select ("messages", "msg_id = :messageId", [":messageId" => $objPost->getId ()], $columnName);

        if ( !isset ($result[0][$columnName]) )
        {
            return false;
        }

        return (int) $result[0][$columnName];
    }

    /**
     * 
     * @param AuditFactory $objAudit
     * @param User $objUser
     * @param CommentReply $objCommentReply
     * @return boolean
     */
    public function deleteReplyLikes (AuditFactory $objAudit, User $objUser, CommentReply $objCommentReply)
    {
        $result = $this->db->delete ("comment_reply_like", "com_id_fk = :commentId", [":commentId" => $objCommentReply->getId ()]);

        if ( $result === false )
        {
            return false;
        }
        
        $objAudit->createAudit ($objUser, "Reply likes {$objCommentReply->getId ()} deleted", "DELETED", "comment_reply_like", $objCommentReply->getId ());

        return true;
    }

   /**
    * 
    * @param AuditFactory $objAudit
    * @param User $objUser
    * @param Post $objPost
    * @return boolean
    */
    public function deletePostLikes (AuditFactory $objAudit, User $objUser, Post $objPost)
    {
        $result = $this->db->delete ("message_like", "msg_id_fk = :postId", [":postId" => $objPost->getId ()]);

        if ( $result === false )
        {
            return false;
        }
        
        $objAudit->createAudit ($objUser, "Post likes {$objPost->getId ()} deleted", "DELETED", "post_like", $objPost->getId ());

        return true;
    }

    /**
     * 
     * @param AuditFactory $objAudit
     * @param User $objUser
     * @param Comment $objComment
     * @return boolean
     */
    public function deleteCommentLikes (AuditFactory $objAudit, User $objUser, Comment $objComment)
    {
        $result = $this->db->delete ("comment_like", "com_id_fk = :commentId", [":commentId" => $objComment->getId ()]);

        if ( $result === false )
        {
            return false;
        }
        
        $objAudit->createAudit ($objUser, "Comment likes {$objComment->getId ()} deleted", "DELETED", "comment_like", $objComment->getId ());

        return true;
    }

}
