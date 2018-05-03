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

        $objUser = new User ($_SESSION['user']['user_id']);

        $blResponse = (new FriendRequest())->sendRequest ($objUser, new UserSettings($objUser), $friendId, new NotificationFactory ());

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function confirmFriendAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['friendId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $friendId = $_POST['friendId'];

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $blResponse = (new FriendRequest())->acceptRequest ($objUser, $friendId);

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

        $objFriend = new FriendRequest();

        $objUser = new User ($_SESSION['user']['user_id']);

        switch ($_POST['action']) {
            case "addfriend":
                $objFriend->sendRequest ($objUser, new UserSettings($objUser), $_POST['friend'], new NotificationFactory ());
                break;
            case "cancelrequest":
                $objFriend->cancelRequest ($objUser, $_POST['friend']);
                break;
            case "unfriend":
                $objFriend->cancelRequest ($objUser, $_POST['friend']);
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

        $objUser = new User ($userId);

        $objUserFactory = new UserFactory();

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
