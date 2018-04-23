<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Advert
 *
 * @author michael.hampton
 */
class Advert
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
    private $title;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $gender;

    /**
     *
     * @var type 
     */
    private $language;

    /**
     *
     * @var type 
     */
    private $db;
    
    /**
     *
     * @var type 
     */
    private $arrBanners = [];

    public function __construct ($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Failed to populate object");
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
    public function getTitle ()
    {
        return $this->title;
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
     * @return type
     */
    public function getGender ()
    {
        return $this->gender;
    }

    /**
     * 
     * @return type
     */
    public function getLanguage ()
    {
        return $this->language;
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
    public function getUserId ()
    {
        return $this->userId;
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
     * @param type $title
     */
    public function setTitle ($title)
    {
        $this->title = $title;
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
     * @param type $gender
     */
    public function setGender ($gender)
    {
        $this->gender = $gender;
    }

    /**
     * 
     * @param type $language
     */
    public function setLanguage ($language)
    {
        $this->language = $language;
    }
    
    /**
     * 
     * @return type
     */
    public function getArrBanners ()
    {
        return $this->arrBanners;
    }

    /**
     * 
     * @param type $arrBanners
     */
    public function setArrBanners ($arrBanners)
    {
        $this->arrBanners = $arrBanners;
    }

    
    /**
     * 
     * @return boolean
     */
    public function populateObject ()
    {
        $result = $this->db->_select ("advert", "id = :id", [":id" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        $this->title = $result[0]['title'];
        $this->location = $result[0]['location'];
        $this->language = $result[0]['language'];
        $this->gender = $result[0]['gender'];
        $this->userId = $result[0]['user_id'];

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->delete ("advert", "id = :id", [":id" => $this->id]);

        if ( $result === false)
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
