<?php

/**
 * 
 */
class PageInboxFactory
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
     * @param User $objUser
     * @param Message $objMessage
     * @param type $direction
     * @return boolean
     */
    public function createMessage (Page $objPage, User $objUser, Message $objMessage, $direction = "IN")
    {
        $result = $this->db->create ("page_inbox", [
            "message_id" => $objMessage->getId (),
            "page_id" => $objPage->getId (),
            "user_id" => $objUser->getId (),
            "direction" => $direction
                ]
        );

        if ( $result === false )
        {
            trigger_error ("db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param Page $objPage
     * @param MessageFactory $objMessageFactory
     * @param User $objUser
     * @return \Message|boolean
     */
    public function getMessages (Page $objPage, MessageFactory $objMessageFactory, User $objUser)
    {

        $arrResults = $this->db->_select ("page_inbox", "page_id = :pageId AND user_id = :userId", [":pageId" => $objPage->getId (), ":userId" => $objUser->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrMessages = [];

        foreach ($arrResults as $arrResult) {
            $objMessage = $objMessageFactory->getMessageById ($arrResult['message_id']);
            
            $arrMessages[] = $objMessage[0];
        }

        return $arrMessages;
    }

    /**
     * 
     * @param Page $objPage
     * @return boolean|\PageInbox
     */
    public function getUsers (Page $objPage)
    {

        $arrResults = $this->db->_query ("SELECT *,
                                            (SELECT COUNT(*) FROM page_inbox WHERE user_id = i.user_id ) AS count
                                            FROM `page_inbox` i
                                            WHERE page_id = :pageId
                                            ORDER BY message_id DESC", [":pageId" => $objPage->getId ()]);

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
        $arrUsed = [];

        foreach ($arrResults as $arrResult) {

            if ( !in_array ($arrUsed, $arrResult['user_id']) )
            {
                $objUser = new User ($arrResult['user_id']);
                $objMessage = new Message ($arrResult['message_id']);
                $objPageInbox = new PageInbox($arrResult['id']);
                $objPageInbox->setObjMessage ($objMessage);
                $objPageInbox->setObjUser ($objUser);
                $objPageInbox->setMessageCount($arrResult['count']);

                $arrFollowers[] = $objPageInbox;
            }

            $arrUsed = $arrResult['user_id'];
        }

        return $arrFollowers;
    }

}
