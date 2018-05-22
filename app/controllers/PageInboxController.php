<?php

/**
 * Description of PageInboxController
 *
 * @author michael.hampton
 */
use Phalcon\Mvc\View;
use Phalcon\Dispatcher;

class PageInboxController extends ControllerBase
{

    public function markAsReadAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['action']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $action = trim ($_POST['action']) == "read" ? 1 : 0;

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['group_ids']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrGroupIds = explode ("|", $_POST['group_ids']);


        try {
            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInbox();


            foreach ($arrGroupIds as $userId) {
                $objUser = new User ($userId);

                $blResult = $objMessage->markAsRead ($objPage, $objUser, $action);

                if ( $blResult === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function deleteMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['group_ids']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInbox();

            $arrUserIds = explode ("|", $_POST['group_ids']);

            foreach ($arrUserIds as $userId) {
                $objUser = new User ($userId);
                $blResult = $objMessage->delete ($objPage, $objUser);

                if ( $blResult === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function getMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInboxFactory();

            $userId = $_POST['group_id'];

            $objUser = new User ($userId);
            $arrMessages = $objMessage->getMessages ($objPage, new MessageFactory (), $objUser);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $tgis->defaultErrrorMessage);
        }

        $message = $this->view->getPartial (
                "pageInbox/getMessages", [
            "arrMessages" => $arrMessages,
            "user_id" => $userId
                ]
        );


        echo json_encode (array("message" => $message, "pagination" => "", "total" => ''));
    }

    public function getInboxUsersAction ()
    {

        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPage = new Page ($_POST['pageId']);
        } catch (Exception $ex) {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            trigger_error ($ex->getMessage (), E_USER_WARNING);
        }

        $this->view->objPage = $objPage;
    }

    public function getInboxAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInboxFactory();

            $userId = isset ($_POST['user_id']) && !empty ($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user']['user_id'];

            $arrUsers = $objMessage->getUsers ($objPage);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;

        $message = $this->view->getPartial (
                "pageInbox/getInbox", [
            "arrUsers" => $arrUsers,
                ]
        );

        echo json_encode (array("message" => $message, "pagination" => "", "total" => ''));
    }

    public function getTrashAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInboxFactory();

            $arrUsers = $objMessage->getTrashMessages ($objPage);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;

        $message = $this->view->getPartial (
                "pageInbox/getInbox", [
            "arrUsers" => $arrUsers,
                ]
        );

        echo json_encode (array("message" => $message, "pagination" => "", "total" => ''));
    }

    public function getSentAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objPage = new Page ($_POST['pageId']);

            $objMessage = new PageInboxFactory();

            $arrUsers = $objMessage->getSentMessages ($objPage);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;

        $message = $this->view->getPartial (
                "pageInbox/getInbox", [
            "arrUsers" => $arrUsers,
                ]
        );

        echo json_encode (array("message" => $message, "pagination" => "", "total" => ''));
    }

    public function sendMessageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['message']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objPage = new Page ($_POST['pageId']);

            $objMessageFactory = new MessageFactory();

            $userId = isset ($_POST['user_id']) && !empty ($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user']['user_id'];

            $objUser = new User ($userId);

            if ( !empty ($_POST['user_id']) )
            {
                $objMessage = $objMessageFactory->sendMessage ($_POST['message'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), $objUser, new User ($objPage->getUserId ()), "", "text");
                $strDir = 'OUT';
            }
            else
            {
                $objMessage = $objMessageFactory->sendMessage ($_POST['message'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($objPage->getUserId ()), $objUser, "", "text");
                $strDir = 'IN';
            }

            if ( $objMessage === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $blResult = (new PageInboxFactory())->createMessage ($objPage, $objUser, $objMessage, $strDir);


            $arrMessages = (new PageInboxFactory())->getMessages ($objPage, $objMessageFactory, $objUser);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $message = $this->view->getPartial (
                "pageInbox/getMessages", [
            "arrMessages" => $arrMessages,
            "show_reply_box" => false,
            "user_id" => $userId
                ]
        );

        echo json_encode (array("reply" => $message, "pagination" => "", "total" => ''));
    }

    /**
     * 
     * @param type $pageId
     */
    public function inboxAction ($pageId)
    {

        if ( empty ($pageId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->pageId = $pageId;

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

}
