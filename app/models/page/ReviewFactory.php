<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewFactory
 *
 * @author michael.hampton
 */
class ReviewFactory
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
     * @param Post $objPost
     * @return \Review|boolean
     */
    public function getReviewsForPost (Post $objPost)
    {
        $arrResults = $this->db->_select ("rating", "post_id = :postId", [":postId" => $objPost->getId ()], "*", "date_added DESC");

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrReviews = [];

        foreach ($arrResults as $arrResult) {
            $objReview = new Review($arrResult['id']);
            
            $arrReviews[] = $objReview;
        }

        return $arrReviews;
    }

    /**
     * 
     * @param Page $objPage
     * @param User $objUser
     * @param type $review
     * @return boolean
     */
    public function createReview (Page $objPage, User $objUser, Post $objPost, $rating)
    {
        if(  trim ($rating) === "") {
            return false;
        }
        
        $result = $this->db->create ("rating", ["page_id" => $objPage->getId (), "post_id" => $objPost->getId(), "user_id" => $objUser->getId (), "rating" => $rating, "date_added" => date ("Y-m-d H:i:s")]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        return true;
    }

}
