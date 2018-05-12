<?php

use Phalcon\Mvc\View;

class GroupController extends ControllerBase
{

    /**
     *
     * @var type 
     */
    private $paginationLimit = 1;

    public function groupUsersAction ()
    {
        $this->view->disable ();

        $objFactory = new GroupMemberFactory();

        $arrUsers = $objFactory->getGroupMembers (new Group ($_GET['id']));

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($arrUsers) )
        {

            echo '<div class="full-height-scroll" style="overflow: auto; width: auto; height: 400px;">

                                        <strong>Likes</strong>

                                        <ul class="list-group clear-list">';

            foreach ($arrUsers as $arrUser) {
                echo '<li class="list-group-item fist-item">';


                echo '<img style="width:50px;" src="/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg">';

                echo $arrUser->getFirstName () . ' ' . $arrUser->getLastName ();


                echo '</li>';
            }

            echo '</div>';
            echo '</ul>';
        }
    }

    public function postGroupCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['groupId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        try {
            $objUser = new User ($userId);
            $objGroup = new Group ($_POST['groupId']);
            $objPostFactory = new GroupPost ($objGroup, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $objPost = $objPostFactory->createComment ($_POST['comment'], $objUser, new \JCrowe\BadWordFilter\BadWordFilter ());

            if ( $objPost === false )
            {
                $this->ajaxresponse ("error", "Unable to save post");
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $arrTags = (new TagUserFactory())->getTaggedUsersForPost ($objPost);

            if ( !empty ($arrTags) )
            {
                $objPost->setArrTags ($arrTags);
            }

            $arrImages = (new UploadFactory())->getImagesForPost ($objPost);

            if ( !empty ($arrImages) )
            {
                $objPost->setArrImages ($arrImages);
            }


            $objUserSettings = new UserSettings ($objUser);

            if ( $objUserSettings->getNotificationSetting ('group') === true )
            {
                $blSendResponse = (new NotificationFactory())->sendNotificationToGroupMembers (new GroupMemberFactory (), $objGroup, $_SESSION['user']['username'] . " posted a comment in {$objGroup->getGroupName ()}");
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
        }

        if ( $blSendResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrPosts = array($objPost);
        $this->view->partial ("templates/posts");
    }

    public function reportGroupAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_POST['group_name']) || empty ($_POST['report_group_data']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $subject = $objUser->getFirstName () . ' ' . $objUser->getLastName () . "just reported group {$_POST['group_name']}";
        $message = $_POST['report_group_data'];

        if ( !mail (EMAIL_ADDRESS, $subject, $message) )
        {
            trigger_error ("Failed to send email reporting group {$_POST['group_name']}", E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    /**
     * 
     * @param type $groupId
     */
    public function indexAction ($groupId)
    {

        if ( empty ($_SESSION['user']['username']) || empty ($_SESSION['user']['user_id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        if ( trim ($groupId) === "" || !is_numeric ($groupId) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid group id"]
                            ]
            );
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $this->view->arrGroups = (new GroupFactory())->getGroupsForUser ($objUser);

        if ( $this->view->arrGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid groups for user"]
                            ]
            );
        }

        $objUserFactory = new UserFactory();
        $arrUsers = $objUserFactory->getUsers ();

        if ( $arrUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get users list"]
                            ]
            );
        }



        $objGroup = new Group ($groupId);

        Phalcon\Tag::setTitle ("Group - " . $objGroup->getGroupName ());

        $this->view->hasPermission = true;

        if ( $objGroup->checkUserAccess ($objUser) === false )
        {
            $this->view->hasPermission = false;
        }

        $this->view->arrFriendList = $objUserFactory->getFriendList ($objUser);

        if ( $this->view->arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get friend list"]
                            ]
            );
        }

        try {
            $objPostFactory = new GroupPost ($objGroup, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrPosts = $objPostFactory->getComments ($objUser);

        if ( $arrPosts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get group posts"]
                            ]
            );
        }

        $this->view->arrPosts = array_slice ($arrPosts, 0, $this->postsPerPage);
        $this->view->arrUsers = $arrUsers;
        $this->view->objGroup = $objGroup;
        $this->view->groupId = $groupId;

        try {
            $blRequestSent = (new GroupRequest())->checkRequestSentForUser ($objUser, $objGroup);
        } catch (Exception $ex) {
            
        }

        $this->view->blRequestSent = $blRequestSent;

        $this->view->objUser = $objUser;

        $arrGroupMembers = (new GroupMemberFactory())->getGroupMembers ($objGroup);

        if ( $arrGroupMembers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get group members"]
                            ]
            );
        }

        $arrMemberIds = [];

        if ( !empty ($arrGroupMembers) )
        {

            foreach ($arrGroupMembers as $objGroupMember) {
                $arrMemberIds[] = $objGroupMember->getId ();
            }
        }

        $this->view->arrMemberIds = $arrMemberIds;

        $this->view->arrGroupMembers = $arrGroupMembers;

        $arrPhotos = (new UploadFactory())->getUploadsForGroup ($objGroup); #

        if ( $arrPhotos === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get group photos"]
                            ]
            );
        }

        $this->view->arrPhotos = $arrPhotos;
    }

    public function searchGroupsAction ()
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
            $objUserFactory = new UserFactory();
            $objUser = new User ($_SESSION['user']['user_id']);
            $arrFriendList = $objUserFactory->getFriendList ($objUser);
            $arrFriendRequests = $objUserFactory->getFriendRequests ($objUser);
            $objGroupFactory = new GroupFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);

            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $this->defaultErrrorMessage]
                            ]
            );
        }



        $arrGroups = $objGroupFactory->getAllGroups ($objUser, new GroupRequestFactory(), null, 0, $this->paginationLimit);
        $totalCount = $objGroupFactory->getAllGroups ($objUser, new GroupRequestFactory(), null, null, null);

        if ( $arrGroups === false || $totalCount === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get groups"]
                            ]
            );
        }

        $arrMemberGroups = $objGroupFactory->getGroupsForProfile ($objUser);

        if ( $arrMemberGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get groups"]
                            ]
            );
        }

        $this->view->arrMemberGroups = $arrMemberGroups;

        $arrCategories = (new GroupCategoryFactory())->getAllCategories ();

        if ( $arrCategories === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get page categories"]
                            ]
            );
        }

        $this->view->arrCategories = $arrCategories;



        $this->view->arrGroups = $arrGroups;

        if ( $arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get list of friends"]
                            ]
            );
        }

        if ( $arrFriendRequests === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get friend requests"]
                            ]
            );
        }

        $this->view->arrFriendRequests = $arrFriendRequests;
        $this->view->objUser = $objUser;
        $this->view->arrFriendList = $arrFriendList;
        $this->view->arrGroups = $arrGroups;
        $this->view->paginationLimit = $this->paginationLimit;
        $this->view->totalCount = count ($totalCount);
    }

    public function groupSearchPaginationAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->ajaxresponse ("error", "invalid user");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( empty ($_POST['vpb_start']) || !isset ($_POST['searchText']) || empty ($_POST['vpb_total_per_load']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $searchText = !empty ($_POST['searchText']) ? $_POST['searchText'] : null;
        $category = !empty ($_POST['pageCategory']) ? new GroupCategory ($_POST['pageCategory']) : null;
        $page = $searchText === null && $category === null ? (int)$_POST['vpb_start'] : null;
        $totalToLoad = $searchText === null ? (int)$_POST['vpb_total_per_load'] : null;

        $arrGroups = (new GroupFactory())->getAllGroups ($objUser, new GroupRequestFactory(), $searchText, $page, $totalToLoad, $category);

        if ( $arrGroups === false )
        {
            $this->ajaxresponse ("error", "unable to get groups");
        }

        $this->view->arrGroups = $arrGroups;
        $this->view->objUser = $objUser;
    }

    public function createGroupAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUserFactory = new UserFactory();

        $this->view->arrFriendList = $objUserFactory->getFriendList (new User ($_SESSION['user']['user_id']));

        if ( $this->view->arrFriendList === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function saveGroupAction ()
    {
        $this->view->disable ();

        $userId = $_SESSION['user']['user_id'];
        $objUser = new User ($userId);

        if ( !isset ($_POST['group_username']) ||
                !isset ($_POST['group_type']) ||
                !isset ($_POST['group_name']) ||
                !isset ($_POST['photo_added']) ||
                !isset ($_POST['vgroup_description']) ||
                !isset ($_POST['groupCategory'])
 
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( isset ($_POST['saveType']) && $_POST['saveType'] == "save" )
        {
            if ( empty ($_POST['group_id']) )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
           
            $objGroup = new Group ($_POST['group_id']);

            $objGroup->setDescription ($_POST['vgroup_description']);
            $objGroup->setGroupName ($_POST['group_name']);
            $objGroup->setGroupType ($_POST['group_type']);
            $objGroup->setGroupCategory ($_POST['groupCategory']);

            $blResult = $objGroup->save ();

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        }
        else
        {            
            // create group
            $objGroupFactory = new GroupFactory();

            $objGroup = $objGroupFactory->createGroup ($objUser, $_POST['group_name'], $_POST['vgroup_description'], $_POST['group_type'], $_POST['groupCategory']);

            if ( $objGroup === false )
            {
                $this->ajaxresponse ("error", implode ("<br/>", $objGroupFactory->getValidationFailures ()));
            }
        }

        // add group image
        $imageLocation = '';

        if ( !empty ($_FILES) )
        {
            $arrFiles["userImage"] = $_FILES[0];

            $imageLocation = $this->singleUploadAction ('group', $_POST['group_name'], $arrFiles);

            if ( $imageLocation === '' || $imageLocation === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $blResult = $objGroup->updateGroupImage ($imageLocation);

            if ( $blResult === false )
            {

                $this->ajaxresponse ("error", "Unable to save image");
            }
        }


        if ( !empty ($_POST['group_username']) && $_POST['group_username'] !== "null" )
        {
            // add roup members
            $groupIds = explode (",", $_POST['group_username']);

            $groupIds[] = $userId;

            if ( !empty ($groupIds) )
            {
                $objGroupMember = new GroupMemberFactory();
                $blResponse = $objGroupMember->createGroupMembers ($groupIds, $objGroup);

                if ( $blResponse === false )
                {
                    $this->ajaxresponse ("error", "Cant save");
                }
            }
        }

        $this->ajaxresponse ("success", "success", ["id" => $objGroup->getId (), "photo" => $imageLocation]);
    }

    public function saveGroupImageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_FILES['profilepic']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Group ($_POST['group_id']);

        $groupName = $objGroup->getGroupName ();

        $arrFiles['userImage'] = $_FILES['profilepic'];

        $imageLocation = '';

        if ( !empty ($_FILES) )
        {
            $imageLocation = $this->singleUploadAction ('group', $groupName, $arrFiles);
        }

        if ( $imageLocation === '' || $imageLocation === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        $blResult = $objGroup->updateGroupImage ($imageLocation);

        if ( $blResult === false )
        {

            $this->ajaxresponse ("error", implode ("<br/>", $objPageFactory->getValidationFailures ()));
            return false;
        }

        $this->ajaxresponse ("success", "success");
    }

    public function getSuggestedGroupsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $arrGroups = (new GroupFactory())->getAllGroups (new User ($_SESSION['user']['user_id']), new GroupRequestFactory(), null, 0, 5);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrGroups === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrGroups = $arrGroups;
    }

    public function getAllGroupsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_GET['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_GET['userId'];

        $objUser = new User ($userId);

        $objGroup = new GroupFactory();

        $arrGroups = $objGroup->getGroupsForProfile ($objUser);


        if ( $arrGroups === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrGroups = $arrGroups;
    }

    /**
     * 
     * @param type $id
     */
    public function inviteFriendsAction ($id)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( trim ($id) === "" )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Group ($id);

        $objGroupFactory = new GroupMemberFactory();

        $arrMembers = $objGroupFactory->getGroupMembers ($objGroup);

        if ( $arrMembers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrRequests = (new GroupRequestFactory())->getAllGroupRequests ($objGroup);

        if ( $arrRequests === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrAllMembers = [];

        foreach ($arrMembers as $arrMember) {
            $arrAllMembers[] = $arrMember->getId ();
        }

        $arrAllUsers = (new UserFactory())->getUsers ();

        if ( $arrAllUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrAllRequests = [];

        foreach ($arrRequests as $arrRequest) {
            $objUser = $arrRequest->getArrUser ();

            $arrAllRequests[] = $objUser->getId ();
        }

        $this->view->arrAllRequests = $arrAllRequests;
        $this->view->arrAllUsers = $arrAllUsers;
        $this->view->arrAllMembers = $arrAllMembers;
        $this->view->id = $id;
    }

    public function deleteGroupAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Group ($_POST['group_id']);

        $blResponse = (new GroupMember())->delete ($objGroup);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }

    public function addMemberToGroupAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_POST['action']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        switch ($_POST['action']) {
            case "join":
                $blResult = (new GroupRequest())->addGroupRequest ($objUser, new Group ($_POST['group_id']));
                break;
            case "cancel":
                $blResult = (new GroupRequest())->deleteRequest ($objUser, new Group ($_POST['group_id']));
                break;
            case "accept":

                if ( empty ($_POST['userId']) )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }

                $objGroup = new Group ($_POST['group_id']);
                $objGroupMember = new GroupMember();
                $objUser = new User ($_POST['userId']);

                $blResponse2 = (new GroupRequest())->deleteRequest ($objUser, $objGroup);

                if ( $blResponse2 === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }

                $blResult = $objGroupMember->addMemberToGroup ($objUser, $objGroup);

                break;

            case "remove":

                if ( empty ($_POST['userId']) )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }

                $objGroup = new Group ($_POST['group_id']);
                $objUser = new User ($_POST['userId']);
                $objGroupMember = new GroupMember();

                $blResult = $objGroupMember->removeMemberFromGroup ($objUser, $objGroup);

                break;

            default:
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                break;
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }

    /**
     * 
     * @param type $groupId
     */
    public function showAllInvitationsAction ($groupId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( trim ($groupId) === "" || !is_numeric ($groupId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Group ($groupId);

        $arrMembers = (new GroupMemberFactory())->getFriendsNotMembers ($objGroup);

        if ( $arrMembers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrMembers = $arrMembers;
    }

    public function saveInvitationsAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['groupId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['userIds']) || !is_array ($_POST['userIds']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objGroupRequest = new GroupRequest();
            $objGroup = new Group ($_POST['groupId']);
            $objNotification = new NotificationFactory();

            foreach ($_POST['userIds'] as $userId) {

                $objUser = new User ($userId);

                $blResponse = $objGroupRequest->addGroupRequest ($objUser, $objGroup);

                if ( $blResponse === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }

                $blResponse2 = $objNotification->createNotification ($objUser, $_SESSION['user']['username'] . " has requested you join " . $objGroup->getGroupName ());

                if ( $blResponse2 === false )
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

    /**
     * 
     * @param type $groupId
     */
    public function showMembersAction ($groupId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( trim ($groupId) === "" || !is_numeric ($groupId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Group ($groupId);

        $arrMembers = (new GroupMemberFactory())->getGroupMembers ($objGroup);

        if ( $arrMembers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrMembers = $arrMembers;
    }

}