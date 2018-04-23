<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoTagFactory
 *
 * @author michael.hampton
 */
class PhotoTagFactory
{

    /**
     *
     * @var type 
     */
    private $objDb;

    public function __construct ()
    {
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

    /**
     * 
     * @param Upload $objUpload
     * @return \PhotoTag|boolean
     */
    public function getTagsForPhoto (Upload $objUpload)
    {
        $arrResults = $this->objDb->_select ("photo_tags", "photo_id = :photoId", [":photoId" => $objUpload->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrTags = [];

        foreach ($arrResults as $arrResult) {
            $objPhotoTag = new PhotoTag();
            $objPhotoTag->setDateCreated ($arrResult['date_tagged']);
            $objPhotoTag->setId ($arrResult['id']);
            $objPhotoTag->setPosX ($arrResult['pos_x']);
            $objPhotoTag->setPosY ($arrResult['pos_y']);
            $objPhotoTag->setTitle ($arrResult['tag_text']);
            $objPhotoTag->setUserId ($arrResult['user_id']);
            $objPhotoTag->setPhotoId ($arrResult['photo_id']);

            $arrTags[] = $objPhotoTag;
        }

        return $arrTags;
    }

    /**
     * 
     * @param type $posx
     * @param type $posy
     * @param type $title
     * @param Upload $objUpload
     * @param User $objUser
     * @return boolean
     */
    public function createPhotoTag ($posx, $posy, $title, Upload $objUpload, User $objUser, User $objTaggedUser)
    {
        $result = $this->objDb->create ("photo_tags", [
            "pos_x" => $posx,
            "pos_y" => $posy,
            "tag_text" => $title,
            "user_id" => $objUser->getId (),
            "date_tagged" => date ("Y-m-d H:i:s"),
            "photo_id" => $objUpload->getId (),
            "tagged_user" => $objTaggedUser->getId ()
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
