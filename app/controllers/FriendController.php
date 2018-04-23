<?php

use Phalcon\Mvc\View;

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

        $friendId = $_POST['friendId'];

        if ( !isset ($_SESSION['user']['user_id']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $objUser = new User ($_SESSION['user']['user_id']);

        $blResponse = (new FriendRequest())->sendRequest ($objUser, $friendId, new NotificationFactory ());
        
        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }
        
        $this->ajaxresponse("success", "success");
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
                $objFriend->sendRequest ($objUser, $_POST['friend'], new NotificationFactory ());
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
        $this->view->disable ();

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

        if ( !empty ($arrFriends) )
        {

            echo '<div class="full-height-scroll" style="overflow: auto; width: auto; height: 400px; margin-top: 20px;
    float: left;
    width: 100%;
}">


                                        <ul class="list-group clear-list">';

            foreach ($arrFriends as $arrFriend) {
                echo '<li class="list-group-item fist-item" style="border-bottom: 1px dotted #EEE;">';


                echo '<img style="width:50px; margin-right:10px;" src="/blab/public/uploads/profile/' . $arrFriend->getUsername () . '.jpg">';

                echo $arrFriend->getFirstName () . ' ' . $arrFriend->getLastName ();


                echo '</li>';
            }

            echo '</div>';
            echo '</ul>';
        }
    }

    public function searchFriendsAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['friend']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrUsers = (new UserFactory())->getUsers ($_POST['friend']);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrUsers as $arrUser) {
            echo '<div class="input-group vpb_popup_fb_box">
				<div style="float:left;">
					<div style="display:inline-block;margin-right:10px;"><img src="/blab/uploads/profile/' . $arrUser->getUsername () . '.jpg" border="0" align="absmiddle" onclick="window.location.href=\'/blab/index/profile/' . $arrUser->getUsername () . '\';"></div>
					<div class="vpb_popup_fb_box_c vpb_hover" title="' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '" onclick="window.location.href=\'/blab/index/profile/' . $arrUser->getUsername () . '\';">' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '</div>
					<div style="clear:both;"></div>
				</div>
					<div class="vpb_popup_fb_box_d"><span id="addfriend_' . $arrUser->getId () . '" onclick="vpb_friend_ship(\'' . $arrUser->getId () . '\', \'' . $arrUser->getUsername () . '\', \'addfriend\');" class="cbtn"><i class="fa fa-user-plus"></i> Add Friend</span>
						
						<span style="opacity:0.6;cursor:default;display:none;" id="requestsent_' . $arrUser->getId () . '" class="cbtn"><i class="fa fa-reply"></i> Request Sent</span>
						
						<span style="display:none;" title="Cancel Request" id="cancelrequest_' . $arrUser->getId () . '" onclick="vpb_friend_ship(\'' . $arrUser->getId () . '\', \'' . $arrUser->getUsername () . '\', \'cancelrequest\');" class="cbtn vpb_cbtn"><i class="fa fa-times"></i></span></div>
					<div style="clear:both;"></div>
				</div>';
        }
    }

    public function loadMoreFriendsAction ()
    {
        $this->view->disable ();

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

        foreach ($arrFriends as $arrFriend) {
            echo '<div class="vfriendsPhotos_wraper" style="display:inline-block !important">
                        <a href="/blab/index/profile/' . $arrFriend->getUsername () . '">';


            if ( file_exists ($this->rootPath . '/blab/public/uploads/profile/' . $arrFriend->getUsername () . '.jpg') )
            {
                echo '<img title="' . $arrFriend->getFirstName () . ' ' . $arrFriend->getLastName () . '" src="/blab/public/uploads/profile/' . $arrFriend->getUsername () . '.jpg">';
            }
            else
            {
                echo '<img src="/blab/public/img/avatar.gif">';
            }

            echo '<span>' . $arrFriend->getFirstname () . ' ' . $arrFriend->getLastname () . '</span>
                        </a> 
                    </div>';
        }
    }

    public function suggestFriendsForShareAction ()
    {
        $this->view->disable ();

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

        foreach ($arrFriends as $arrFriend) {
            echo '<li>
					<a onclick="vpb_select_this_friend_for_shares(\'' . $arrFriend->getId () . '\', \'' . $arrFriend->getFirstName () . ' ' . $arrFriend->getLastName () . '\');">
					<span class="vpb_left_tag_box"><img src="/blab/uploads/profile/' . $arrFriend->getUsername () . '.jpg" width="40" height="40" border="0"></span>
					<span class="vpb_left_tag_text_box">' . $arrFriend->getFirstName () . ' ' . $arrFriend->getLastName () . '</span>
					<div style="clear:both;"></div>
					</a>
					</li>';
        }
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

    public function pollFriendListAction ($userId)
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
