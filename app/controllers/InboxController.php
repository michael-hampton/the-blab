<?php

/**
 * Description of PageInboxController
 *
 * @author michael.hampton
 */
use Phalcon\Mvc\View;
use Phalcon\Dispatcher;

class InboxController extends ControllerBase
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
                "inbox/getMessages", [
                    "arrMessages" => $arrMessages,
                    "user_id" => $userId,
                     "objPage" => $objPage
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
                "inbox/getInbox", [
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
                "inbox/getInbox", [
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
                "inbox/getInbox", [
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

        if ( empty ($_POST['message']) && empty ($_FILES) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objPage = new Page ($_POST['pageId']);

            $objMessageFactory = new MessageFactory();

            $objFileUpload = new FileUpload ($this->rootPath . "/blab/public/uploads/chat/");

            $userId = isset ($_POST['user_id']) && !empty ($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user']['user_id'];

            $objUser = new User ($userId);

            $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();

            $objEmailNotificationFactory = new EmailNotificationFactory();

            $objPageInboxFactory = new PageInboxFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($_POST['message']) )
        {
            if ( !empty ($_POST['user_id']) )
            {
                $objRecipient = new User ($objPage->getUserId ());
                $objUser2 = $objUser;

                $objMessage = $objMessageFactory->sendMessage ($_POST['message'], $objBadWordFilter, $objEmailNotificationFactory, $objUser2, $objRecipient, "", "text");
                $strDir = 'OUT';
            }
            else
            {
                $objRecipient = $objUser;
                $objUser2 = new User ($objPage->getUserId ());
                $objMessage = $objMessageFactory->sendMessage ($_POST['message'], $objBadWordFilter, $objEmailNotificationFactory, $objUser2, $objRecipient, "", "text");
                $strDir = 'IN';
            }

            if ( $objMessage === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        }

        $blResult = $objPageInboxFactory->createMessage ($objPage, $objUser, $objMessage, $strDir);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($_FILES) && count ($_FILES) > 0 )
        {

            if ( empty ($strDir) )
            {
                $strDir = "OUT";
            }

            foreach ($_FILES as $arrFile) {
                $objFileUpload->prepareUpload ($arrFile);

                if ( $objFileUpload->validateUpload () === false )
                {
                    $this->ajaxresponse ("error", implode ("<br/>", $objFileUpload->getValidationFailures ()));
                }

                if ( $objFileUpload->saveUpload () === false )
                {
                    $this->ajaxresponse ("error", "Unable to upload file");
                }

                $targetFile = $objFileUpload->getTargetFile ();
                $objUploadedMessage = $objMessageFactory->sendMessage ('New file uploaded', $objBadWordFilter, $objEmailNotificationFactory, $objUser2, $objRecipient, $targetFile, 'img', null);

                if ( $objUploadedMessage === false )
                {
                    continue;
                }

                $blUploadResult = $objPageInboxFactory->createMessage ($objPage, $objUser, $objUploadedMessage, $strDir);
            }
        }

        $arrMessages = $objPageInboxFactory->getMessages ($objPage, $objMessageFactory, $objUser);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $message = $this->view->getPartial (
                "inbox/getMessages", [
                    "arrMessages" => $arrMessages,
                    "show_reply_box" => false,
                     "user_id" => $userId,
                     "objPage" => $objPage
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

    public function autoSuggestAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_POST['system_username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUsers = new UserFactory();
        $term = urldecode ($_POST['system_username']);

        $arrUsers = $objUsers->getUsers ($term);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;
    }

    public function sendNewMessageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['subject']) || empty ($_POST['message']) || empty ($_POST['recipients']) || empty ($_POST['pageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userIds = explode (",", $_POST['recipients']);

        try {

            $objPage = new Page ($_POST['pageId']);
            $objMessageFactory = new MessageFactory();

            foreach ($userIds as $userId) {

                $objUser = new User ($userId);

                $objMessage = $objMessageFactory->sendMessage ($_POST['message'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), $objUser, new User ($objPage->getUserId ()), "", "text");

                if ( $objMessage === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }

                $blResult = (new PageInboxFactory())->createMessage ($objPage, $objUser, $objMessage, "OUT");
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

}