<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Review
 *
 * @author michael.hampton
 */
class Review
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
    private $id;

    /**
     *
     * @var type 
     */
    private $postId;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $pageId;

    /**
     *
     * @var type 
     */
    private $rating;
    
    /**
     *
     * @var type 
     */
    private $average;

    /**
     *
     * @var type 
     */
    private $dateAdded;

    /**
     * 
     * @param type $id
     */
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
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->db->_query("SELECT *,
                                    (SELECT AVG(rating) AS average FROM rating WHERE post_id = r.post_id) AS average
                                    FROM rating r
                                    WHERE id = :id", [":id" => $this->id]);
        
        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Failed to find database row", E_USER_WARNING);
            return false;
        }

        $this->pageId = $result[0]['page_id'];
        $this->postId = $result[0]['post_id'];
        $this->userId = $result[0]['user_id'];
        $this->rating = $result[0]['rating'];
        $this->dateAdded = $result[0]['date_added'];
        $this->average = $result[0]['average'];

        return true;
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
    public function getAverage ()
    {
        return $this->average;
    }

    /**
     * 
     * @param type $average
     */
    public function setAverage ($average)
    {
        $this->average = $average;
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
    public function getPageId ()
    {
        return $this->pageId;
    }

    /**
     * 
     * @return type
     */
    public function getDateAdded ()
    {
        return $this->dateAdded;
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
     * @param type $pageId
     */
    public function setPageId ($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * 
     * @param type $dateAdded
     */
    public function setDateAdded ($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * 
     * @return type
     */
    public function getRating ()
    {
        return $this->rating;
    }

    /**
     * 
     * @param type $rating
     */
    public function setRating ($rating)
    {
        $this->rating = $rating;
    }

}
