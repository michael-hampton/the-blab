<?php

use Phalcon\Mvc\View;

/**
 * 
 */
class FriendController extends ControllerBase
{

    public function indexAction ($url)
    {
        
    }

    public function handleBlockedUserAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['friend']) || empty ($_POST['action']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            if ( trim ($_POST['action']) === "unblock" )
            {
                $blResult = $objUser->unBlockUser (new User ($_POST['friend']));
            }
            else
            {
                $blResult = $objUser->blockUser (new User ($_POST['friend']));
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

    public function addFriendAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['friendId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $friendId = $_POST['friendId'];

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objFriend = new User ($friendId);
            $blResponse = (new FriendRequest())->sendRequest ($objUser, new UserSettings ($objUser), new EmailNotificationFactory (), $objFriend, new NotificationFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ('error', $this->defaultErrrorMessage);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function confirmFriendAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['friendId']) || empty($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $friendId = $_POST['friendId'];

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objFriend = new User ($friendId);

            $blResponse = (new FriendRequest())->acceptRequest ($objUser, $objFriend);
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_WARNING);
            $this->ajaxresponse("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function handleFriendRequestAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['action']) || !isset ($_POST['friend']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objFriend = new FriendRequest();
            $objUser = new User ($_SESSION['user']['user_id']);
            $objUserSettings = new UserSettings ($objUser);
            $objNotificationFactory = new NotificationFactory ();
            $objRecipient = new User ($_POST['friend']);
            $objEmailFactory = new EmailNotificationFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        switch ($_POST['action']) {
            case "addfriend":
                $objFriend->sendRequest ($objUser, $objUserSettings, $objEmailFactory, $objRecipient, $objNotificationFactory);
                break;
            case "cancelrequest":
                $objFriend->cancelRequest ($objUser, $objRecipient);
                break;
            case "unfriend":
                $objFriend->cancelRequest ($objUser, $objRecipient);
                break;
        }
    }

    public function friendRequestAction ()
    {
        
    }

    /**
     * 
     */
    public function getAllFriendsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_GET['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_GET['userId'];

        try {
            $objUser = new User ($userId);

            $objUserFactory = new UserFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrFriends = $objUserFactory->getFriendList ($objUser);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrFriends = $arrFriends;
    }

    public function searchFriendsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['friend']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrUsers = (new UserFactory())->getUsers ($_POST['friend']);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;
    }

    public function loadMoreFriendsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['vpb_start']) || empty ($_POST['page_id']) || empty ($_POST['session_uid']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_POST['session_uid']);

        $arrFriends = (new UserFactory())->getFriendList ($objUser, $_POST['vpb_start'], 1);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->rootPath = $this->rootPath;

        $this->view->arrFriends = $arrFriends;
    }

    public function suggestFriendsForShareAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['friend']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objFriends = new UserFactory();

        $arrFriends = $objFriends->getFriendList (new User ($_SESSION['user']['user_id']), null, 1, $_POST['friend']);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrFriends = $arrFriends;
    }

    public function readFriendListAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $objFriend = new FriendRequest();
        $blResponse = $objFriend->readRequests ($objUser);

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function pollFriendListAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $arrAllUsers = (new UserFactory())->getFriends (new User ($userId));

        if ( $arrAllUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $waiting = 0;

        foreach ($arrAllUsers as $arrAllUser) {
            if ( $arrAllUser['friend_status'] == "pending" )
            {
                $waiting++;
            }
        }


        echo json_encode (
                array(
                    "count" => $waiting,
                    "data" => $arrAllUsers
                )
        );
    }

}
