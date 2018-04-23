<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoCommentFactory
 *
 * @author michael.hampton
 */
class PhotoCommentFactory
{

    /**
     *
     * @var type 
     */
    private $db;

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
     * @param Upload $objUpload
     * @return \PhotoComment|boolean
     */
    public function getCommentsForUpload (Upload $objUpload)
    {
        $results = $this->db->_query ("SELECT c.*,  CONCAT(u.fname, ' ' , u.lname) AS author FROM `photo_comments` c 
                                        INNER JOIN users u on u.uid = c.user_id
                                        WHERE c.`upload_id` = :uploadId", [":uploadId" => $objUpload->getId ()]);

        if ( $results === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrComments = [];

        foreach ($results as $result) {
            $objComment = new PhotoComment ($result['id']);
            $objComment->setAuthor ($result['author']);
            $objComment->setComment ($result['comment']);
            $objComment->setUserId($result['user_id']);
            $objComment->setDateAdded ($result['created']);
            $objComment->setUploadId ($result['upload_id']);

            $arrComments[] = $objComment;
        }

        return $arrComments;

        return $results;
    }

    /**
     * 
     * @param User $objUser
     * @param Upload $objUpload
     * @param type $comment
     * @return boolean
     */
    public function create (User $objUser, Upload $objUpload, $comment)
    {
        $userId = $objUser->getId ();

        if ( trim ($userId) === "" || !is_numeric ($userId) )
        {
            return false;
        }

        $uploadId = $objUpload->getId ();

        if ( trim ($uploadId) === "" || !is_numeric ($uploadId) )
        {
            return false;
        }

        if ( trim ($comment) === "" )
        {
            return false;
        }

        $result = $this->db->create ("photo_comments", [
            "comment" => $comment,
            "user_id" => $userId,
            "created" => date ("Y-m-d H:i:s"),
            "upload_id" => $uploadId
                ]
        );

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
