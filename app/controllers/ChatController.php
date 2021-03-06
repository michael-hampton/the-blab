<?php

use Phalcon\Mvc\View;

class ChatController extends ControllerBase
{

    public function countOnlineFriendsAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid user");
        }

        $arrUsers = (new UserFactory())->getUsers ();

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", "Failed to get count");
        }

        echo count ($arrUsers);
    }

    public function checknewMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid user");
        }

        $blResponse = (new ChatList())->getUnreadCount (new User ($_SESSION['user']['user_id']));

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Failed to get count");
        }


        echo $blResponse;
    }

    public function getChatFriendsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $searchText = isset ($_POST['searchText']) && trim ($_POST['searchText']) !== "" ? $_POST['searchText'] : null;

        $arrFriends = (new UserFactory())->getFriendList ($objUser, 0, 50, $searchText);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", "Unable to get friends");
        }

        $this->view->arrFriends = $arrFriends;
    }

    public function loadMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_POST['vpb_total_per_load']) || !isset ($_POST['vpb_start']) )
        {
            $this->ajaxresponse ("error", "Missing Values");
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid user");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $arrMessages = (new MessageFactory())->getMessagesNew ($objUser, new User ($_POST['group_id']));

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", "Unable to get messages");
        }

        $message = $this->view->getPartial (
                "chat/viewMessage", [
            "arrMessages" => $arrMessages,
                ]
        );


        $this->ajaxresponse ("success", "success", ["message_id" => 1, "message" => $message]);
    }

    public function messengerAction ()
    {
        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objMessageFactory = new MessageFactory();
            $objGroupMessageFactory = new GroupMessageFactory();
            $arrChatUsers = $objMessageFactory->getChatUsers ($objUser);
            $arrPages = (new PageFactory())->getAllPages (new PageReactionFactory ());
            $arrPageCategories = (new PageCategoryFactory())->getAllCategories ();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrChatGroups = $objGroupMessageFactory->getGroupChats ($objUser);

        if ( $arrChatGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get chat groups"]
                            ]
            );
        }

        $arrGroups = $objGroupMessageFactory->getGroups ();

        if ( $arrGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get chat groups"]
                            ]
            );
        }

        if ( $arrChatUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get friends list"]
                            ]
            );
        }

        if ( $arrPages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get pages"]
                            ]
            );
        }

        if ( $arrPageCategories === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get pages"]
                            ]
            );
        }

        $this->view->arrMessages = $arrChatUsers;
        $this->view->arrPageCategories = $arrPageCategories;
        $this->view->arrPages = $arrPages;
        $this->view->arrChatGroups = $arrChatGroups;
        $this->view->arrGroups = $arrGroups;
    }

    public function uploadFileAction ()
    {
        $this->view->disable ();

        if ( empty ($_FILES[0]) )
        {
            $this->ajaxresponse ("error", "No file was uploaded");
        }

        if ( empty ($_POST['group_username']) )
        {
            $this->ajaxresponse ("error", "Missing User Id");
        }

        if ( empty ($_POST['page']) )
        {
            $this->ajaxresponse ("error", "Page is missing");
        }

        $type = trim ($_POST['page']) == "vpb_add_images_to_message" ? 'img' : 'file';

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( !isset ($_FILES['0']['name']) )
        {
            $this->ajaxresponse ("error", "Invalid file");
        }


        $file = file_get_contents ($_FILES['0']['tmp_name']);

        $extension = $ext = end ((explode (".", $_FILES[0]['name'])));

        if ( $extension !== false )
        {
            $fileName = rand (10000, 65000) . "." . $extension;
            $location = $this->rootPath . "/blab/public/uploads/chat/" . $fileName;


            if ( !move_uploaded_file ($_FILES[0]['tmp_name'], $location) )
            {
                $this->ajaxresponse ("error", "Cant upload file");
            }
        }

        try {
            $blResponse = (new MessageFactory())->sendMessage ('New file uploaded', new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($_POST['group_username']), $objUser, $fileName, $type, null);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Cant save message");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function fetchVideoAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['video_url']) )
        {
            $this->ajaxresponse ("error", "No url given");
        }

        $test = preg_match ("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $_POST['video_url'], $matches);

        if ( empty ($matches[0]) )
        {
            $this->ajaxresponse ("error", "No id");
        }

        $this->ajaxresponse ("success", "success", ["id" => $matches[0], "type" => "youtube"]);
    }

    public function saveMessageAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( empty ($_POST['group_username']) )
        {
            $this->ajaxresponse ("error", "Missing User Id");
        }

        if ( empty ($_POST['message']) )
        {
            $this->ajaxresponse ("error", "No message entered");
        }

        try {
            $objMessage = new MessageFactory();

            $blResult = $objMessage->sendMessage ($_POST['message'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($_POST['group_username']), $objUser, '', 'text', null);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to save message");
        }

        if ( !empty ($_POST['tags']) )
        {

            $objNotification = new NotificationFactory();

            $tags = explode (",", $_POST['tags']);

            $username = $_SESSION['user']['username'];

            $message = $username . " Tagged you in a comment";

            foreach ($tags as $taggedUser) {

                try {
                    $objNewUser = new User ($taggedUser);

                    $objNotification->createNotification ($objNewUser, $message);

                    $objEmail = new EmailNotification ($objNewUser, $message, $_POST['comment']);
                    $objEmail->sendEmail ();
                } catch (Exception $ex) {
                    trigger_error ($ex->getMessage (), E_USER_WARNING);
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        }

        $this->ajaxresponse ("success", "success");
    }

    // here
    public function getUnreadChatMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            $objMessageFactory = new MessageFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrMessages = $objMessageFactory->getUnreadMessagesForUser ($objUser);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        (new ChatList())->setMessagesToRead ($objUser, null);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrMessages as $arrMessage) {
            echo '<div><img style="width: 25px; margin-right: 12px;" title="' . $arrMessage->getUsername () . '" src="/blab/public/uploads/profile/' . $arrMessage->getUsername () . '.jpg"><div class="vpb_popup_fb_box_c vpb_hover" title="Jhoanna Hampton">' . $arrMessage->getMessage () . '</div>
					<div style="clear:both;"></div>
				</div>';
        }
    }

    public function chatUploadAction ()
    {
        $this->view->disable ();

        if ( !isset ($_FILES['file']['name']) )
        {
            $this->ajaxresponse ("error", "Invalid file");
        }

        if ( isset ($_FILES['file']) )
        {
            $file = file_get_contents ($_FILES['file']['tmp_name']);

            $mime_type = getimagesize ($_FILES['file']['tmp_name'])['mime'];

            if ( $mime_type == '' )
            {
                $mime_type = 'audio/x-wav';
            }


            $supported_types = array(
                "audio/x-wav" => "wav",
                "image/png" => "png",
                "image/jpeg" => "jpg",
                "image/pjpeg" => "jpg",
                "image/gif" => "gif"
            );

            $extension = isset ($supported_types[$mime_type]) ? $supported_types[$mime_type] : false;
            if ( $extension !== false )
            {
                $fileName = rand (10000, 65000) . "." . $extension;
                $location = $this->rootPath . "/blab/public/uploads/chat/" . $fileName;

                move_uploaded_file ($_FILES['file']['tmp_name'], $location);
                echo $fileName;
            }
        }
    }

    public function uploadChatFileAction ()
    {
        $this->view->disable ();

        if ( empty ($_FILES[0]) )
        {
            $this->ajaxresponse ("error", "No file was uploaded");
        }

        if ( empty ($_POST['group_id']) || !is_numeric ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", "Missing User Id");
        }

        if ( empty ($_POST['page']) )
        {
            $this->ajaxresponse ("error", "Page is missing");
        }

        if ( empty ($_POST['group_type']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $type = trim ($_POST['page']) == "vpb_add_files_to_message" ? 'file' : 'img';

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( !isset ($_FILES['0']['name']) )
        {
            $this->ajaxresponse ("error", "Invalid file");
        }

        $file = file_get_contents ($_FILES['0']['tmp_name']);

        $extension = $ext = end ((explode (".", $_FILES[0]['name'])));

        if ( $extension !== false )
        {
            $fileName = rand (10000, 65000) . "." . $extension;
            $location = $this->rootPath . "/blab/public/uploads/chat/" . $fileName;


            if ( !move_uploaded_file ($_FILES[0]['tmp_name'], $location) )
            {
                $this->ajaxresponse ("error", "Cant upload file");
            }
        }

        if ( $_POST['group_type'] == "user" )
        {
            try {
                $blResponse = (new MessageFactory())->sendMessage ('New file uploaded', new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($_POST['group_id']), $objUser, $fileName, $type, null);
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage (), E_USER_WARNING);
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        }


        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Cant save message");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function addPageMessageAction ($pageId)
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "You dont have permission to do this");
        }

        if ( trim ($pageId) === "" )
        {
            $this->ajaxresponse ("error", "Invalid Page");
        }

        try {

            $objPage = new Page ($pageId);

            $objUser = new User ($_SESSION['user']['user_id']);

            $objMessage = (new MessageFactory())->sendMessage ($_POST['msg'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($objPage->getUserId ()), $objUser, $_POST['filename'], "page", null);

            if ( $objMessage === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $blPageResult = (new PageInboxFactory())->createMessage ($objPage, $objUser, $objMessage);

            if ( $blPageResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($_POST['tags']) )
        {

            $objNotification = new NotificationFactory();

            $tags = explode (",", $_POST['tags']);

            $username = $_SESSION['user']['username'];

            $message = $username . " Tagged you in a chat message";

            foreach ($tags as $taggedUser) {

                try {
                    $objNewUser = new User ($taggedUser);

                    $objNotification->createNotification ($objNewUser, $message);

                    $objEmail = new EmailNotification ($objNewUser, $message, $_POST['msg']);
                    $objEmail->sendEmail ();
                } catch (Exception $ex) {
                    trigger_error ($ex->getMessage (), E_USER_WARNING);
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        }

        echo $objMessage->getId ();
    }

    public function addMessageAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "You dont have permission to do this");
        }

        if ( empty ($_POST['userId']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            $groupId = isset ($_POST['group_id']) && trim ($_POST['group_id']) !== "" && is_numeric ($_POST['group_id']) ? $_POST['group_id'] : null;

            $blResponse = (new MessageFactory())->sendMessage ($_POST['msg'], new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), new User ($_POST['userId']), $objUser, $_POST['filename'], $_POST['type'], $groupId);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }

        if ( !empty ($_POST['tags']) )
        {

            $objNotification = new NotificationFactory();

            $tags = explode (",", $_POST['tags']);

            $username = $_SESSION['user']['username'];

            $message = $username . " Tagged you in a chat message";

            foreach ($tags as $taggedUser) {

                try {
                    $objNewUser = new User ($taggedUser);

                    $objNotification->createNotification ($objNewUser, $message);

                    $objEmail = new EmailNotification ($objNewUser, $message, $_POST['msg']);
                    $objEmail->sendEmail ();
                } catch (Exception $ex) {
                    trigger_error ($ex->getMessage (), E_USER_WARNING);
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        }

        echo $blResponse->getId ();
    }

    /* here */

    public function searchNewChatUsersAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['system_username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrUsers = (new UserFactory())->getUsers ($_POST['system_username']);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrUsers as $arrUser) {
            echo '<div id="vpb_user_selected_' . $arrUser->getFirstName () . '" class="vpb_users_wraper" onclick="vpb_add_new_user_to_group(\'' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '\', \'' . $arrUser->getUsername () . '\', \'/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg\');">
            
					<div class="vpb_users_wraper_left" style="width:100% !important;">
					
					<div class="vpb_users_wraper_photos">
					<img src="/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg" border="0">
					</div>
					
					<div class="vpb_users_wraper_name">
					<span class="vpb_users_wraper_name_left" style="margin-bottom:4px !important;">
					' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '
					</span>
					 <span class="vpb_users_wraper_name_right">' . $arrUser->getTown () . '</span>
					</div>
					</div>
					</div>';
        }
    }

    public function addUserToGroupChatAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['username']) || empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            $blResult = (new GroupMessageFactory())->addUserToGroupChat ($objUser, $_POST['group_id'], $_POST['username']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCESS", ["id" => $blResult->getId ()]);
    }

    public function reportChatUserAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['group_id']) ||
                !isset ($_POST['reportFullname']) ||
                !isset ($_POST['reportUsername']) ||
                !isset ($_POST['report_pm_data'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResponse = (new ChatUserLog())->createLog (new GroupMessage ($_POST['group_id']), $_POST['reportFullname'], $_POST['reportUsername'], $_POST['report_pm_data']);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function updateGroupChatNameAction ()
    {
        $this->view->disable ();

        $arrFiles = $_FILES;

        if ( empty ($_POST['group_name']) || empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroupChat = new GroupMessage ($_POST['group_id']);

        if ( empty ($arrFiles[0]['name']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $target_dir = $this->rootPath . "/blab/public/uploads/group_conversations/";

        $target_file = $target_dir . basename ($_FILES[0]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

        if ( $_FILES[0]["size"] > 500000 )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

// Allow certain file formats
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

        $check = getimagesize ($_FILES[0]["tmp_name"]);

        if ( $check !== false )
        {
            $uploadOk = 1;
        }
        else
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        if ( move_uploaded_file ($_FILES[0]["tmp_name"], $target_file) )
        {
            $savedLocation = str_replace ($this->rootPath, "", $target_file);

            $objGroupChat->setImageUrl ($savedLocation);
            $objGroupChat->setGroupName ($_POST['group_name']);

            $blResult = $objGroupChat->update ();

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $this->ajaxresponse ("success", "SUCCESS");
        }
        else
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function deleteConversationAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_type']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $_POST['group_type'] == "group" )
        {
            $blResult = (new GroupMessage ($_POST['group_id']))->delete ();

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        }
        else
        {
            $blResult = (new ChatList())->deleteConversation (new User ($_POST['group_id']), new User ($_SESSION['user']['user_id']));

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        }
    }

    public function setMessagesToReadAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrorMessage);
        }

        $objGroupChat = !empty ($_POST['group_id']) ? new GroupMessage ($_POST['group_id']) : null;

        $blResult = (new ChatList())->setMessagesToRead (new User ($_SESSION['user']['user_id']), $objGroupChat);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function getGroupMessageCountAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) || empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = (new ChatList())->getUnreadCount (new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            return 0;
        }

        echo $blResult;
    }

    public function removeUserFromGroupChatAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['username']) || empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResponse = (new ChatList())->removeUserFromGroupChat ($_POST['group_id'], $_POST['username']);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function searchChatUsersAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_POST['searchText']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objMessageFactory = new MessageFactory();
            $objGroupMessageFactory = new GroupMessageFactory();
            $arrPages = (new PageFactory())->getAllPages (new PageReactionFactory ());
            $arrPageCategories = (new PageCategoryFactory())->getAllCategories ();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrChatGroups = $objGroupMessageFactory->getGroupChats ($objUser);

        if ( $arrChatGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get chat groups"]
                            ]
            );
        }

        $arrGroups = $objGroupMessageFactory->getGroups ();

        if ( $arrGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get chat groups"]
                            ]
            );
        }

        $arrMessages = $objMessageFactory->getChatUsers ($objUser);

        if ( $arrMessages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get friends list"]
                            ]
            );
        }

        if ( $arrPages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get pages"]
                            ]
            );
        }

        if ( $arrPageCategories === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get pages"]
                            ]
            );
        }

        $this->view->arrMessages = $arrMessages;
        $this->view->arrPageCategories = $arrPageCategories;
        $this->view->arrPages = $arrPages;
        $this->view->arrChatGroups = $arrChatGroups;
        $this->view->arrGroups = $arrGroups;
        $this->view->searchText = !empty ($_POST['searchText']) && trim ($_POST['searchText']) !== "" ? $_POST['searchText'] : '';
    }

    public function deleteChatMessageAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['id']) || !is_numeric ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objMessage = new Message ($_POST['id']);

        $blResponse = $objMessage->delete (new User ($_SESSION['user']['user_id']));

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function getGroupMessagesAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['groupId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $groupId = $_POST['groupId'];
        $this->view->groupId = $groupId;

        try {
            $objMessage = new GroupMessageFactory();
            $userId = $_SESSION['user']['user_id'];
            $arrUser[$userId] = (new User ($userId));
            $objGroupChat = new GroupMessage ($groupId);
            $arrChatUsers = (new UserFactory())->getGroupChatUsers ($objGroupChat);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrUser === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrChatUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrMessages = $objMessage->getMessagesForGroup ($objGroupChat);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/chat", ["arrMessages" => $arrMessages, "arrUser" => $arrUser, "userId" => $userId, "objUser" => null, "arrGroupUsers" => $arrChatUsers]);
    }

    public function chatAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objMessage = new MessageFactory();
            $userId = $_POST['userId'];
            $objUser = new User ($_SESSION['user']['user_id']);

            $objRecipient = new User ($_POST['userId']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrMessages = $objMessage->getMessagesNew ($objUser, $objRecipient);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrMessages = $arrMessages;

        $this->view->arrUser = $objRecipient;
        $this->view->userId = $userId;
        $this->view->objUser = $objRecipient;

        $this->view->groupId = '';

        if ( $this->view->arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function removeSelfFromGroupAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['username']) || empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = (new ChatList())->removeUserFromGroupChat ($_POST['group_id'], $_SESSION['user']['username']);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    /**
     * 
     * @param type $groupId
     */
    public function getGroupMessageAction ($groupId)
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objMessage = new GroupMessageFactory();
            $objGroupChat = new GroupMessage ($groupId);
            $arrMessages = $objMessage->getMessagesForGroup ($objGroupChat);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/viewMessage", ["arrMessages" => $arrMessages]);
    }

    /**
     * 
     * @param type $pageId
     */
    public function getSinglePageMessageAction ($pageId)
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objMessage = new PageInboxFactory();
            $objMessageFactory = new MessageFactory();
            $objUser = new User ($_SESSION['user']['user_id']);
            $objPage = new Page ($pageId);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrMessages = $objMessage->getMessages ($objPage, $objMessageFactory, $objUser);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/viewMessage", ["arrMessages" => $arrMessages, "objPage" => $objPage]);
    }

    /**
     * 
     * @param type $userId
     */
    public function getUserMessageAction ($userId)
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objMessage = new MessageFactory();
            $objRecipient = new User ($userId);
            $objUser = new User ($_SESSION['user']['user_id']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrMessages = $objMessage->getMessagesNew ($objUser, $objRecipient);

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/viewMessage", ["arrMessages" => $arrMessages, "objUser" => $objRecipient]);
    }

    /**
     * 
     * @param type $messageId
     */
    public function getUsersToForwardAction ($messageId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        if ( !is_numeric ($messageId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $arrFriends = (new UserFactory())->getFriendList ($objUser);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", "Unable to get friends");
        }

        $this->view->arrFriends = $arrFriends;
        $this->view->messageId = $messageId;
    }

    public function sendMessageForwardAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['messageId']) || empty ($_POST['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        try {
            $objMessage = new Message ($_POST['messageId']);
            $objMessageFactory = new MessageFactory();
            $objRecipient = new User ($_POST['userId']);
            $objUser = new User ($_SESSION['user']['user_id']);
            $blResult = $objMessageFactory->cloneMessage ($objUser, $objMessage, new \JCrowe\BadWordFilter\BadWordFilter (), new EmailNotificationFactory (), $objRecipient, '');
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

    public function getPageMessagesAction ()
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

            $objUser = new User ($_SESSION['user']['user_id']);
            $arrMessages = $objMessage->getMessages ($objPage, new MessageFactory (), $objUser);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrMessages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/chat", ["arrMessages" => $arrMessages, "objPage" => $objPage]);
    }

    public function filterDiscoveryAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['categoryId']) || !isset ($_POST['searchText']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $searchText = trim ($_POST['searchText']) !== "" ? $_POST['searchText'] : null;
        $objPageCategory = trim ($_POST['categoryId']) !== "" && is_numeric ($_POST['categoryId']) ? new PageCategory ($_POST['categoryId']) : null;

        try {
            $objPageCategoryFactory = new PageCategoryFactory();
            $arrPages = (new PageFactory())->getAllPages (new PageReactionFactory (), null, $searchText, null, null, $objPageCategory);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrPageCategories = $objPageCategoryFactory->getAllCategories ();

        if ( $arrPageCategories === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrPages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("chat/discovery", ["arrPages" => $arrPages, "arrPageCategories" => $arrPageCategories]);
    }

}
