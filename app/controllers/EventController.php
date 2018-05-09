<?php

use Phalcon\Mvc\View;

class EventController extends ControllerBase
{

    /**
     *
     * @var type 
     */
    private $paginationLimit = 1;

    /**
     * 
     * @param type $id
     */
    public function indexAction ($id)
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

        if ( trim ($id) === "" || !is_numeric ($id) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid event id"]
                            ]
            );
        }

        try {
            $objEvent = new Event ($id);
        } catch (Exception $ex) {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid event id"]
                            ]
            );
        }

        $this->view->objEvent = $objEvent;
        Phalcon\Tag::setTitle ("Event - " . $objEvent->getEventName ());

        $memberEventStatus = (new EventMember ($objEvent))->getStatusForUser (new User ($_SESSION['user']['user_id']));

        if ( $memberEventStatus === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get event status for member"]
                            ]
            );
        }

        $this->view->memberEventStatus = $memberEventStatus;

        try {
            $objPostFactory = new EventPost ($objEvent, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $objUser = new User ($_SESSION['user']['user_id']);
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
                                "params" => ["message" => "unable to get event posts"]
                            ]
            );
        }

        $this->view->arrPosts = array_slice ($arrPosts, 0, $this->postsPerPage);

        $arrMembers = (new EventMemberFactory())->getEventMembers ($objEvent);

        if ( $arrMembers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get members for event"]
                            ]
            );
        }

        $arrMemberIds = [];

        foreach ($arrMembers as $objMember) {
            $arrMemberIds[] = $objMember->getId ();
        }

        $this->view->arrMemberIds = $arrMemberIds;

        $arrAllEvents = (new EventFactory())->getEventsForUser ($objUser);

        if ( $arrAllEvents === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get events for user"]
                            ]
            );
        }

        $this->view->arrEvents = $arrAllEvents;
        $this->view->objUser = $objUser;
        $this->view->arrFriendList = (new UserFactory())->getFriendList ($objUser);

        $arrPhotos = (new UploadFactory())->getUploadsForEvent ($objEvent); #

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

        $arrMembers = (new EventMemberFactory())->getEventMembers ($objEvent);

        if ( $arrMembers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get event members"]
                            ]
            );
        }

        $this->view->arrMembers = $arrMembers;
    }

    public function searchEventsAction ()
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

        $objEventFactory = new EventFactory();
        $arrEvents = $objEventFactory->getAllEvents (null, 0, $this->paginationLimit);
        $totalCount = $objEventFactory->getAllEvents (null, null, null);

        if ( $arrEvents === false || $totalCount === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get groups"]
                            ]
            );
        }

        try {
            $objUserFactory = new UserFactory();
            $objUser = new User ($_SESSION['user']['user_id']);
            $arrFriendList = $objUserFactory->getFriendList ($objUser);
            $arrFriendRequests = $objUserFactory->getFriendRequests ($objUser);
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
        $this->view->arrEvents = $arrEvents;
        $this->view->paginationLimit = $this->paginationLimit;
        $this->view->totalCount = count ($totalCount);
    }

    public function eventSearchPaginationAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->ajaxresponse ("error", "invalid user");
        }

        if ( empty ($_POST['vpb_start']) || !isset ($_POST['searchText']) || empty ($_POST['vpb_total_per_load']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $searchText = !empty ($_POST['searchText']) ? $_POST['searchText'] : null;
        $page = $searchText === null ? (int) $_POST['vpb_start'] : null;
        $totalToLoad = $searchText === null ? (int) $_POST['vpb_total_per_load'] : null;

        $arrEvents = (new EventFactory())->getAllEvents ($searchText, $page, $totalToLoad);

        if ( $arrEvents === false )
        {
            $this->ajaxresponse ("error", "unable to get events");
        }

        $this->view->arrEvents = $arrEvents;
    }

    public function reportEventAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_POST['group_name']) || empty ($_POST['report_group_data']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $subject = $objUser->getFirstName () . ' ' . $objUser->getLastName () . "just reported event {$_POST['group_name']}";
        $message = $_POST['report_group_data'];

        if ( !mail (EMAIL_ADDRESS, $subject, $message) )
        {
            trigger_error ("Failed to send email reporting event {$_POST['group_name']}", E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function getEventStatusListAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['status']) )
        {
            $this->ajaxresponse ("error", "Invalid status");
        }

        $status = (int) $_POST['status'];

        if ( empty ($_POST['eventId']) )
        {
            $this->ajaxresponse ("error", "Invalid event id");
        }

        $objEventMembersFactory = new EventMemberFactory();
        $objEvent = new Event ($_POST['eventId']);

        switch ($status) {
            case 1:
                $this->view->label = "Going";
                $this->view->class = "label-warning";
                $arrList = $objEventMembersFactory->getMembersGoing ($objEvent);
                break;

            case 2:
                $this->view->label = "Not Going";
                $this->view->class = "label-danger";
                $arrList = $objEventMembersFactory->getMembersNotGoing ($objEvent);
                break;

            case 3:
                $this->view->class = "label-success";
                $this->view->label = "Interested";
                $arrList = $objEventMembersFactory->geMembersInterested ($objEvent);
                break;

            case 4:
                $this->view->label = "Pending";
                $this->view->class = "label-primary";
                $arrList = $objEventMembersFactory->getMembersPending ($objEvent);
                break;
        }

        if ( $arrList === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrList = $arrList;
    }

    public function eventUsersAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $objFactory = new EventMemberFactory();

        $arrUsers = $objFactory->getEventMembers (new Event ($_GET['id']));

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;
    }

    public function getAllEventsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_GET['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_GET['userId'];

        $objUser = new User ($userId);
        $objEvent = new EventFactory();

        $arrEvents = $objEvent->getEventsForProfile ($objUser);

        if ( $arrEvents === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrEvents = $arrEvents;
    }

    public function saveEventAction ()
    {
        $this->view->disable ();

        $userId = $_SESSION['user']['user_id'];
        $objUser = new User ($userId);

        $objEventFactory = new EventFactory();

        $objEvent = $objEventFactory->createEvent ($objUser, $_POST['location'], $_POST['eventDate'], $_POST['eventName'], $_POST['eventTime']
        );

        if ( $objEvent === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objEventFactory->getValidationFailures ()));
        }

        $groupIds = $_POST['groupIds'];

        $groupIds[] = $userId;

        if ( !empty ($groupIds) )
        {
            $objGroupMember = new EventMemberFactory();
            $blResponse = $objGroupMember->createEventMembers ($groupIds, $objEvent);

            if ( $blResponse === false )
            {
                $this->ajaxresponse ("error", "Unable to save");
            }
        }

        $this->ajaxresponse ("success", "success");
    }

    public function createEventAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objFriend = new UserFactory();

        $this->view->arrFriendList = $objFriend->getFriendList (new User ($_SESSION['user']['user_id']));

        if ( $this->view->arrFriendList === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function updateEventImageAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['groupId']) || !isset ($_POST['groupName']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objGroup = new Event ($_POST['groupId']);

        $imageLocation = '';

        if ( !empty ($_FILES) )
        {
            $imageLocation = $this->singleUploadAction ('event', $_POST['groupName'], $_FILES);
        }

        if ( $imageLocation === '' || $imageLocation === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        $blResult = $objGroup->updateEventImage ($imageLocation);

        if ( $blResult === false )
        {

            $this->ajaxresponse ("error", implode ("<br/>", $objPageFactory->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

    public function updateEventStatusAction ($status, $eventId)
    {
        $this->view->disable ();

        if ( trim ($status) === "" || !is_numeric ($status) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( trim ($eventId) === "" || !is_numeric ($eventId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objEvent = new EventMember (new Event ($eventId));

        $blResponse = $objEvent->updateEventStatus (new User ($_SESSION['user']['user_id']), $status);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    /**
     * 
     * @param type $eventId
     */
    public function showAllEventInvitationsAction ($eventId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( trim ($eventId) === "" || !is_numeric ($eventId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objEvent = new Event ($eventId);

        $arrMembers = (new EventMemberFactory())->getFriendsNotMembers ($objEvent);

        if ( $arrMembers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrMembers = $arrMembers;
    }

    public function saveInvitationsAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['eventId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['userIds']) || !is_array ($_POST['userIds']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objEventMember = new EventMemberFactory();
            $objEvent = new Event ($_POST['eventId']);

            $blResponse = $objEventMember->createEventMembers ($_POST['userIds'], $objEvent);

            if ( $blResponse === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function postEventCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['eventId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        try {
            $objEvent = new Event ($_POST['eventId']);
            $objUser = new User ($userId);
            $objPostFactory = new EventPost ($objEvent, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = $objPostFactory->createComment ($_POST['comment'], $objUser, new \JCrowe\BadWordFilter\BadWordFilter ());

        if ( $objPost === false )
        {
            $this->ajaxresponse ("error", "Unable to save post");
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
                $blSendResponse = (new NotificationFactory())->sendNotificationToEventMembers (new EventMemberFactory (), $objEvent, $_SESSION['user']['username'] . ' just posted in ' . $objEvent->getEventName ());
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
        }

        $this->view->arrPosts = array($objPost);
        $this->view->partial ("templates/posts");
    }

    public function deleteEventAction ()
    {
        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = (new Event ($_POST['group_id']))->deleteEvent ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

}
