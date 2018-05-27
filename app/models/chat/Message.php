<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author michael.hampton
 */
class Message
{

    use MessageEncrypt;

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $recipient;

    /**
     *
     * @var type 
     */
    private $sender;

    /**
     *
     * @var type 
     */
    private $date;

    /**
     *
     * @var type 
     */
    private $message;

    /**
     *
     * @var type 
     */
    private $author;

    /**
     *
     * @var type 
     */
    private $username;

    /**
     *
     * @var type 
     */
    private $type;

    /**
     *
     * @var type 
     */
    private $groupId;

    /**
     *
     * @var type 
     */
    private $filename;

    /**
     *
     * @var type 
     */
    private $direction;

    /**
     *
     * @var type 
     */
    private $lastLogin;

    /**
     *
     * @var type 
     */
    private $objDb;

    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function __construct ($id)
    {
        $this->id = $id;

        $this->objDb = new Database();
        $this->objDb->connect ();

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
    public function getRecipient ()
    {
        return $this->recipient;
    }

    /**
     * 
     * @return type
     */
    public function getSender ()
    {
        return $this->sender;
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
    public function getDate ()
    {
        return $this->date;
    }

    /**
     * 
     * @return type
     */
    public function getMessage ()
    {
        return $this->encrypt_decrypt ("decrypt", $this->message);
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
     * @param type $recipient
     */
    public function setRecipient ($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * 
     * @param type $sender
     */
    public function setSender ($sender)
    {
        $this->sender = $sender;
    }

    /**
     * 
     * @param type $date
     */
    public function setDate ($date)
    {
        $this->date = $date;
    }

    /**
     * 
     * @param type $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
    }

    /**
     * 
     * @return type
     */
    public function getAuthor ()
    {
        return $this->author;
    }

    /**
     * 
     * @param type $author
     */
    public function setAuthor ($author)
    {
        $this->author = $author;
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
     * @return type
     */
    public function getUsername ()
    {
        return $this->username;
    }

    /**
     * 
     * @param type $username
     */
    public function setUsername ($username)
    {
        $this->username = $username;
    }

    /**
     * 
     * @return type
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * 
     * @return type
     */
    public function getFilename ()
    {
        return $this->filename;
    }

    /**
     * 
     * @param type $type
     */
    public function setType ($type)
    {
        $this->type = $type;
    }

    /**
     * 
     * @return type
     */
    public function getGroupId ()
    {
        return $this->groupId;
    }

    /**
     * 
     * @param type $groupId
     */
    public function setGroupId ($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * 
     * @param type $filename
     */
    public function setFilename ($filename)
    {
        $this->filename = $filename;
    }

    /**
     * 
     * @return type
     */
    public function getLastLogin ()
    {
        return $this->lastLogin;
    }

    /**
     * 
     * @param type $lastLogin
     */
    public function setLastLogin ($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * 
     * @return boolean
     */
    public function delete (User $objUser)
    {
        $userId = (int) $objUser->getId ();

        if ( $userId !== (int) $this->getSender () && $userId !== (int) $this->getRecipient () )
        {
            trigger_error ("User triend to delete chat message that wasnt theirs", E_USER_WARNING);
            return false;
        }

        $result = $this->objDb->delete ("chat", "chat_id = :chatId", [":chatId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("DB QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->objDb->_select ("chat", "chat_id = :id", [":id" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Unable to retrieve record from db", E_USER_WARNING);
            return false;
        }

        $this->date = $result[0]['sent_on'];
        $this->message = $result[0]['message'];
        $this->recipient = $result[0]['sent_to'];
        $this->sender = $result[0]['user_id'];
        $this->type = $result[0]['type'];
        $this->filename = $result[0]['filename'];

        return true;
    }

}
