<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageReactionFactory
 *
 * @author michael.hampton
 */
class PageReactionFactory
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
     * @param Page $objPage
     * @return boolean
     */
    public function getFollowersForPage (Page $objPage)
    {
        $arrResults = $this->db->_select ("page_follower", "page_id = :pageId", [":pageId" => $objPage->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrFollowers = [];

        foreach ($arrResults as $arrResult) {
            $objUser = new User ($arrResult['user_id']);

            $arrFollowers[] = $objUser;
        }

        return $arrFollowers;
    }

    /**
     * 
     * @param Page $objPage
     * @return \User|boolean
     */
    public function getLikeListForPage (Page $objPage)
    {
        $results = $this->db->_query ("SELECT u.uid, u.fname, u.lname, u.username, l.date_added FROM `page_like` l
                                    INNER JOIN users u ON u.uid = l.user_id
                                    WHERE l.page_id = :pageId", [':pageId' => $objPage->getId ()]
        );

        if ( $results === FALSE )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($results[0]) )
        {
            return [];
        }

        $arrLikes = [];

        foreach ($results as $arrResult) {
            $objUser = new User ($arrResult['uid']);
            $objUser->setUsername ($arrResult['username']);
            $objUser->setFirstName ($arrResult['fname']);
            $objUser->setLastName ($arrResult['lname']);

            $arrLikes[] = $objUser;
        }

        return $arrLikes;
    }

}
