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
    
    public function __construct ($id)
    {
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
    public function setObjUser(User $objUser)
    {
        $this->objUser = $objUser;
    }

    /**
     * 
     * @param Message $objMessage
     */
    public function setObjMessage(Message $objMessage)
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



}