<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Upload
 *
 * @author michael.hampton
 */
class UploadFactory
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
    private $objUpload;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();

        $this->db->connect ();
    }

    public function createUploadForComment (Comment $objComment, User $objUser, $location, $title)
    {
        
    }

    /**
     * 
     * @param User $objUser
     * @param type $location
     * @return boolean
     */
    public function createUpload (User $objUser, $location)
    {

        $result = $this->db->create ('uploads', array(
            'file_location' => $location,
            'user_id' => $objUser->getId (),
            'created' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $objUpload = new Upload ($result);

        return $objUpload;
    }

    public function createUploadForAlbum (PhotoAlbum $objAlbum, User $objUser, $location, $title)
    {
        
    }

    /**
     * 
     * @param Group $objGroup
     * @return boolean|\Upload
     */
    public function getUploadsForGroup (Group $objGroup)
    {
        $arrResults = $this->db->_query ("SELECT image_id FROM `messages` m
                                        INNER JOIN group_comment gc ON gc.post_id = m.msg_id
                                        WHERE `image_id` <> ''
                                        AND gc.group_id = :groupId", [":groupId" => $objGroup->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUploads = [];

        foreach ($arrResults as $arrResult) {
            $arrImageIds = json_decode ($arrResult['image_id']);

            foreach ($arrImageIds as $imageId) {
                $objUpload = new Upload ($imageId);

                $arrUploads[] = $objUpload;
            }
        }

        return $arrUploads;
    }
    
      /**
     * 
     * @param Group $objGroup
     * @return boolean|\Upload
     */
    public function getUploadsForEvent (Event $objEvent)
    {
        $arrResults = $this->db->_query ("SELECT image_id FROM `messages` m
                                        INNER JOIN event_comment gc ON gc.post_id = m.msg_id
                                        WHERE `image_id` <> ''
                                        AND gc.event_id = :eventId", [":eventId" => $objEvent->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUploads = [];

        foreach ($arrResults as $arrResult) {
            $arrImageIds = json_decode ($arrResult['image_id']);

            foreach ($arrImageIds as $imageId) {
                $objUpload = new Upload ($imageId);

                $arrUploads[] = $objUpload;
            }
        }

        return $arrUploads;
    }

    /**
     * 
     * @param Page $objPage
     * @return boolean|\Upload
     */
    public function getUploadsForPage (Page $objPage)
    {
        $arrResults = $this->db->_query ("SELECT image_id FROM `messages` m
                                        INNER JOIN page_comment gc ON gc.post_id = m.msg_id
                                        WHERE `image_id` <> ''
                                        AND gc.page_id = :pageId", [":pageId" => $objPage->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUploads = [];

        foreach ($arrResults as $arrResult) {
            $arrImageIds = json_decode ($arrResult['image_id']);

            foreach ($arrImageIds as $imageId) {
                $objUpload = new Upload ($imageId);

                $arrUploads[] = $objUpload;
            }
        }

        return $arrUploads;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function getUploadaForUser (User $objUser, $limit = 100)
    {
        $arrResults = $this->db->_select ("uploads", "user_id = :userId", [":userId" => $objUser->getId ()], '*', "upload_id ASC", (int) $limit);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUploads = [];

        foreach ($arrResults as $arrResult) {
            $objUpload = new Upload ($arrResult['upload_id']);

            $arrUploads[] = $objUpload;
        }

        return $arrUploads;
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function getUpload ($id)
    {
        $result = $this->db->_select ("uploads", "upload_id = :uploadId", ["uploadId" => $id]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($result[0]) )
        {
            return [];
        }

        return $result;
    }

    /**
     * 
     * @param Upload $objUpload
     * @param User $objUser
     * @return boolean
     */
    public function buildPagination (Upload $objUpload, User $objUser)
    {
        $result = $this->db->_query ("select
                                        (SELECT count(upload_id) FROM uploads WHERE user_id = :userId) AS total,
                                      ( select upload_id
                                           from uploads
                                          where upload_id > t.upload_id ORDER BY upload_id LIMIT 1 ) as next_id
                                     , ( select upload_id
                                           from uploads
                                          where upload_id < t.upload_id ORDER BY upload_id DESC LIMIT 1 ) as prev_id
                                  from uploads as t
                                 where upload_id = :uploadId
                                 AND user_id = :userId
                                                                    ", [':uploadId' => $objUpload->getId (), ':userId' => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($result[0]) )
        {
            return [];
        }

        return $result;
    }

    public function getImagesForComment (Comment $objComment)
    {

        $imageIds = $objComment->getImageIds ();

        if ( !is_array ($imageIds) )
        {
            $imageIds = json_decode ($imageIds, true);
        }

        $arrUploads = [];

        if ( empty ($imageIds) )
        {
            return [];
        }

        foreach ($imageIds as $imageId) {
            $objUpload = new Upload ($imageId);
            $arrUploads[] = $objUpload;
        }

        return $arrUploads;
    }

    /**
     * 
     * @param Product $objProduct
     * @return boolean|\Upload
     */
    public function getImagesForProduct (Product $objProduct)
    {
        $arrResults = $this->db->_query ("SELECT * FROM product_images p
                                            INNER JOIN uploads u ON u.upload_id = p.upload_id
                                            WHERE product_id = :productId", [":productId" => $objProduct->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrUploads = [];

        foreach ($arrResults as $arrResult) {
            $objUpload = new Upload ($arrResult['upload_id']);
            $arrUploads[] = $objUpload;
        }

        return $arrUploads;
    }

    public function getImagesForReply (CommentReply $objCommentReply)
    {

        $imageIds = $objCommentReply->getImageIds ();

        if ( !is_array ($imageIds) )
        {
            $imageIds = json_decode ($imageIds, true);
        }

        $arrUploads = [];

        if ( empty ($imageIds) )
        {
            return [];
        }

        foreach ($imageIds as $imageId) {
            $objUpload = new Upload ($imageId);
            $arrUploads[] = $objUpload;
        }

        return $arrUploads;
    }

    /**
     * 
     * @param Post $objPost
     * @return \Upload
     */
    public function getImagesForPost (Post $objPost)
    {

        $imageIds = $objPost->getImageId ();

        if ( !is_array ($imageIds) )
        {
            $imageIds = json_decode ($imageIds, true);
        }

        $arrUploads = [];

        if ( empty ($imageIds) )
        {
            return [];
        }

        foreach ($imageIds as $imageId) {

            try {
                $objUpload = new Upload ($imageId);
                $arrUploads[] = $objUpload;
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage ());
                continue;
            }
        }

        return $arrUploads;
    }

    /**
     * @param Upload $objUpload
     * @param Album $objAlbum
     * @return bool
     */
    public function addtoAlbum (Upload $objUpload, Album $objAlbum)
    {
        $result = $this->db->create ("photo_album", ["upload_id" => $objUpload->getId (), "album_id" => $objAlbum->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Album $objAlbum
     * @return boolean|\Upload
     */
    public function getPhotosForAlbum (Album $objAlbum = null)
    {
        $arrParameters = [];
        
        $sql = "SELECT u.*, pa.album_id FROM uploads u
                INNER JOIN photo_album pa ON pa.upload_id = u.upload_id";

        if ( $objAlbum !== null )
        {
            $sql .= " WHERE pa.album_id = :albumId";
            $arrParameters = array(":albumId" => $objAlbum->getId ());
        }

        $arrResults = $this->db->_query ($sql, $arrParameters);

        if ( $arrResults === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrPhotos = [];

        foreach ($arrResults as $arrResult) {
            $arrPhotos[$arrResult['album_id']][] = new Upload ($arrResult['upload_id']);
        }

        return $arrPhotos;
    }

}
