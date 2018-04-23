<?php

/**
 * Description of PageReaction
 *
 * @author michael.hampton
 */
class PageReaction
{

    /**
     *
     * @var type 
     */
    private $objDatabase;

    public function __construct ()
    {
        $this->objDatabase = new Database();
        $this->objDatabase->connect ();
    }

    /**
     * 
     * @param User $objUser
     * @param Page $objPage
     * @return boolean
     */
    public function unlikePage (User $objUser, Page $objPage)
    {
        $result = $this->objDatabase->_query ("UPDATE page 
                            SET like_count = 
                                if(like_count is not null, like_count - 1, 0) 
                            WHERE id = :pageId", [":pageId" => $objPage->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->objDatabase->delete ("page_like", "user_id = :userId AND page_id = :id", [':userId' => $objUser->getId (), ":id" => $objPage->getId ()]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $followResult = $this->removefollowerFromPage ($objUser, $objPage);

        if ( $followResult === false )
        {
            trigger_error ("Unable to follow page", E_USER_WARNING);
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param Page $objPage
     * @return boolean
     */
    public function followPage (User $objUser, Page $objPage)
    {
        $result = $this->objDatabase->create ("page_follower", ["user_id" => $objUser->getId (), "page_id" => $objPage->getId (), "date_created" => date ("Y-m-d H:i:s")]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Page $objPage
     * @return boolean
     */
    public function getFollowerCountForPage (Page $objPage)
    {
        $result = $this->objDatabase->_query ("SELECT COUNT(*) AS count FROM `page_follower` WHERE page_id = :pageId", [":pageId" => $objPage->getId ()]);

        if ( $result === false || !isset ($result[0]['count']) )
        {
            trigger_error ("Unable to get follow count for page", E_USER_WARNING);
            return false;
        }

        return (int) $result[0]['count'];
    }

    /**
     * 
     * @param User $objUser
     * @param Page $objPage
     * @return boolean
     */
    public function removefollowerFromPage (User $objUser, Page $objPage)
    {
        $result = $this->objDatabase->delete ("page_follower", "user_id = :userId AND page_id = :pageId", [":userId" => $objUser->getId (), ":pageId" => $objPage->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param Page $objPage
     * @return boolean
     */
    public function likePage (User $objUser, Page $objPage)
    {
        $checkResult = $this->objDatabase->_select ("page_like", "page_id = :pageId AND user_id = :userId", [":pageId" => $objPage->getId (), ":userId" => $objUser->getId ()]);

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

        $result = $this->objDatabase->_query ("UPDATE page 
                            SET like_count = 
                                if(like_count is not null, like_count + 1, 1) 
                            WHERE id = :pageId", [":pageId" => $objPage->getId ()], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->objDatabase->create ('page_like', array(
            'page_id' => $objPage->getId (),
            'user_id' => $objUser->getId (),
            'date_added' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $followResult = $this->followPage ($objUser, $objPage);

        if ( $followResult === false )
        {
            trigger_error ("Unable to follow page", E_USER_WARNING);
        }

        return true;
    }

}
