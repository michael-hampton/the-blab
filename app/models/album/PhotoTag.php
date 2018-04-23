<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoTag
 *
 * @author michael.hampton
 */
class PhotoTag
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
    private $posX;
        
    /**
     *
     * @var type 
     */
    private $posY;
    
    /**
     *
     * @var type 
     */
    private $photoId;
    
    /**
     *
     * @var type 
     */
    private $title;
    
    /**
     *
     * @var type 
     */
    private $dateCreated;
    
    /**
     *
     * @var type 
     */
    private $userId;
    
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
    public function getPosX ()
    {
        return $this->posX;
    }

    /**
     * 
     * @return type
     */
    public function getPosY ()
    {
        return $this->posY;
    }

    /**
     * 
     * @return type
     */
    public function getPhotoId ()
    {
        return $this->photoId;
    }

    /**
     * 
     * @return type
     */
    public function getTitle ()
    {
        return $this->title;
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
    public function getUserId ()
    {
        return $this->userId;
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
     * @param type $posX
     */
    public function setPosX ($posX)
    {
        $this->posX = $posX;
    }

    /**
     * 
     * @param type $posY
     */
    public function setPosY ($posY)
    {
        $this->posY = $posY;
    }

    /**
     * 
     * @param type $photoId
     */
    public function setPhotoId ($photoId)
    {
        $this->photoId = $photoId;
    }

    /**
     * 
     * @param type $title
     */
    public function setTitle ($title)
    {
        $this->title = $title;
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
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }


}
