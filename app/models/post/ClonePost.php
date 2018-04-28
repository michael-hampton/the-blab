<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClonePost
 *
 * @author michael.hampton
 */
class ClonePost
{

    /**
     *
     * @var type 
     */
    private $db;

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
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
