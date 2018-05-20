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
     * @return type
     */
    public function getTrashMessages (Page $objPage)
    {
        $arrResults = $this->db->_query ("SELECT *,
                                            (SELECT COUNT(*) FROM page_inbox WHERE user_id = i.user_id ) AS count
                                            FROM `page_inbox` i
                                            WHERE page_id = :pageId AND is_trash = 1
                                            ORDER BY message_id DESC", [":pageId" => $objPage->getId ()]);

        return $this->loadObject ($arrResults);
    }

    /**
     * 
     * @param Page $objPage
     * @return type
     */
    public function getSentMessages (Page $objPage)
    {
        $arrResults = $this->db->_query ("SELECT *,
                                            (SELECT COUNT(*) FROM page_inbox WHERE user_id = i.user_id ) AS count
                                            FROM `page_inbox` i
                                            WHERE direction = 'OUT'
                                            ORDER BY message_id DESC", [":pageId" => $objPage->getId ()]);

        return $this->loadObject ($arrResults);
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

            if ( empty ($objMessage[0]) )
            {
                continue;
            }
            
            $objMessage[0]->setDirection($arrResult['direction']);

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
                                            WHERE page_id = :pageId AND is_trash = 0
                                            ORDER BY message_id DESC", [":pageId" => $objPage->getId ()]);

        return $this->loadObject ($arrResults);
    }

    /**
     * 
     * @param type $arrResults
     * @return boolean|\PageInbox
     */
    private function loadObject ($arrResults)
    {
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

            if ( empty ($arrUsed) || !in_array ($arrResult['user_id'], $arrUsed) )
            {

                try {
                    $objUser = new User ($arrResult['user_id']);
                    $objMessage = new Message ($arrResult['message_id']);
                    $objPageInbox = new PageInbox ($arrResult['id']);
                } catch (Exception $e) {
                    trigger_error ($e->getMessage (), E_USER_WARNING);
                    return false;
                }

                $objPageInbox->setObjMessage ($objMessage);
                $objPageInbox->setObjUser ($objUser);
                $objPageInbox->setMessageCount ($arrResult['count']);
                $objPageInbox->setDirection ($arrResult['direction']);
                $objPageInbox->setIsTrash ($arrResult['is_trash']);
                $objPageInbox->setHasRead($arrResult['has_read']);

                $arrFollowers[] = $objPageInbox;
            }

            $arrUsed[] = $arrResult['user_id'];
        }

        return $arrFollowers;
    }

}
