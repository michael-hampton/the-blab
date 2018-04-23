<?php

class TagUserFactory
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
     * @param User $objUser
     * @param Post $objPost
     * @return bool
     */
    public function createTagForPost (User $objUser, Post $objPost)
    {
        $result = $this->objDb->create ("post_tag", ["user_id" => $objUser->getId (), "post_id" => $objPost->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * @param User $objUser
     * @param Post $objPost
     * @return bool
     */
    public function deleteTagForPost (User $objUser, Post $objPost)
    {
        $result = $this->objDb->delete ("post_tag", "user_id = :userId AND post_id = :postId", [":userId" => $objUser->getId (), ":postId" => $objPost->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * @param Post $objPost
     * @return array|bool
     */
    public function getTaggedUsersForPost (Post $objPost)
    {
        $arrResults = $this->objDb->_select ("post_tag", "post_id = :postId", [":postId" => $objPost->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrUsers = [];

        foreach ($arrResults as $arrResult) {
            $arrUsers[] = new User ($arrResult['user_id']);
        }

        return $arrUsers;
    }
}