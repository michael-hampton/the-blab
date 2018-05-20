<?php

class PageInbox
{

    /**
     *
     * @var type 
     */
    private $objUser = [];

    /**
     *
     * @var type 
     */
    private $objMessage;

    /**
     *
     * @var type 
     */
    private $messageCount = 0;

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $db;
    
    /**
     *
     * @var type 
     */
    private $isTrash;
    
    /**
     *
     * @var type 
     */
    private $direction;
    
    /**
     *
     * @var type 
     */
    private $hasRead;

    /**
     * 
     */
    public function __construct ($id)
    {
        $this->db = new Database();
        $this->db->connect ();
        $this->id = $id;
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
     * @param User $objUser
     */
    public function setObjUser (User $objUser)
    {
        $this->objUser = $objUser;
    }

    /**
     * 
     * @param Message $objMessage
     */
    public function setObjMessage (Message $objMessage)
    {
        $this->objMessage = $objMessage;
    }

    /**
     * 
     * @return type
     */
    public function getObjUser ()
    {
        return $this->objUser;
    }

    /**
     * 
     * @return type
     */
    public function getObjMessage ()
    {
        return $this->objMessage;
    }

    /**
     * 
     * @return type
     */
    public function getMessageCount ()
    {
        return $this->messageCount;
    }

    /**
     * 
     * @param type $messageCount
     */
    public function setMessageCount ($messageCount)
    {
        $this->messageCount = $messageCount;
    }
    
    /**
     * 
     * @return type
     */
    public function getIsTrash ()
    {
        return $this->isTrash;
    }

    /**
     * 
     * @return type
     */
    public function getDirection ()
    {
        return $this->direction;
    }

    /**
     * 
     * @param type $isTrash
     */
    public function setIsTrash ($isTrash)
    {
        $this->isTrash = $isTrash;
    }

    /**
     * 
     * @param type $direction
     */
    public function setDirection ($direction)
    {
        $this->direction = $direction;
    }
    
    /**
     * 
     * @return type
     */
    public function getHasRead ()
    {
        return $this->hasRead;
    }

    /**
     * 
     * @param type $hasRead
     */
    public function setHasRead ($hasRead)
    {
        $this->hasRead = $hasRead;
    }

    
    
    /**
     * 
     * @param Page $objPage
     * @param User $objUser
     * @return boolean
     */
    public function delete (Page $objPage, User $objUser)
    {
        $result = $this->db->update("page_inbox", ["is_trash" => 1], "page_id = :pageId AND user_id = :userId",  [":pageId" => $objPage->getId (), ":userId" => $objUser->getId ()]);

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
     * @param User $objUser
     * @return boolean
     */
    public function markAsRead (Page $objPage, User $objUser, $hasRead = 1)
    {

        $result = $this->db->update ("page_inbox", ["has_read" => $hasRead], "page_id = :pageId AND user_id = :userId", [":pageId" => $objPage->getId (), ":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
