<?php

use Phalcon\Mvc\View;
use Phalcon\Dispatcher;

class PageController extends ControllerBase
{

    /**
     *
     * @var type 
     */
    private $paginationLimit = 1;

    public function reportPageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) || empty ($_POST['group_name']) || empty ($_POST['report_group_data']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $subject = $objUser->getFirstName () . ' ' . $objUser->getLastName () . "just reported page {$_POST['group_name']}";
        $message = $_POST['report_group_data'];

        if ( !mail (EMAIL_ADDRESS, $subject, $message) )
        {
            trigger_error ("Failed to send email reporting page {$_POST['group_name']}", E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function getAllPagesAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_GET['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_GET['userId'];
        $objUser = new User ($userId);
        $objPage = new PageFactory();

        $arrPages = $objPage->getPagesForUser ($objUser);

        if ( $arrPages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrPages = $arrPages;
    }

    public function updatePageImageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['page_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_FILES['profilepic']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPage = new Page ($_POST['page_id']);

        $pageName = $objPage->getName ();

        $imageLocation = '';

        $arrFiles["userImage"] = $_FILES['profilepic'];

        if ( !empty ($_FILES) )
        {
            $imageLocation = $this->singleUploadAction ('page', $pageName, $arrFiles);
        }

        if ( $imageLocation === '' || $imageLocation === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = $objPage->updatePageImage ($imageLocation);

        if ( $blResult === false )
        {

            $this->ajaxresponse ("error", implode ("<br/>", $objPage->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

    public function indexAction ($url)
    {
        if ( trim ($url) === "" )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "url is missing"]
                            ]
            );
        }

        $this->view->url = $url;

        try {
            $objPage = new Page ($url);
        } catch (Exception $ex) {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "url is missing"]
                            ]
            );
        }

        Phalcon\Tag::setTitle ("Page - " . $objPage->getName ());

        if ( empty ($_SESSION['user']['username']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        $fullAddress = $objPage->getAddress () . " " . $objPage->getPostcode ();


        if ( trim ($fullAddress) !== "" )
        {
            $location = (new Location())->getLocationForAddress ($fullAddress);
            $this->view->lat = $location['lat'];
            $this->view->lng = $location['lng'];
        }

        $objUserFactory = new UserFactory();
        $arrUsers = $objUserFactory->getUsers ();

        if ( $arrUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get users"]
                            ]
            );
        }

        $userId = $_SESSION['user']['user_id'];

        $this->view->arrFriendList = $objUserFactory->getFriendList ($arrUsers[$userId]);

        if ( $this->view->arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to build friends list"]
                            ]
            );
        }

        try {
            $objPostFactory = new PagePost ($objPage, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $arrPosts = $objPostFactory->getComments ($arrUsers[$_SESSION['user']['user_id']]);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrPosts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get page posts"]
                            ]
            );
        }

        $this->view->arrPosts = array_slice ($arrPosts, 0, 2);
        $this->view->arrUsers = $arrUsers;

        $this->view->objPage = $objPage;

        $arrPhotos = (new UploadFactory())->getUploadsForPage ($objPage); #

        if ( $arrPhotos === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get page photos"]
                            ]
            );
        }

        $this->view->arrPhotos = $arrPhotos;

        try {
            $objPostFactory = new ReviewPost ($objPage, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $arrReviews = $objPostFactory->getComments (new User ($_SESSION['user']['user_id']));
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrReviews === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get page reviews"]
                            ]
            );
        }

        $this->view->arrReviews = $arrReviews;

        $arrPageLikes = (new PageReactionFactory())->getLikeListForPage ($objPage);

        if ( $arrPageLikes === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get likes for page"]
                            ]
            );


            //$this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrUserLikes = [];

        foreach ($arrPageLikes as $objPageLike) {
            $arrUserLikes[] = $objPageLike->getId ();
        }

        $this->view->arrUserLikes = array_unique ($arrUserLikes);

        $followCount = (new PageReaction())->getFollowerCountForPage ($objPage);

        if ( $followCount === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to follow count for page"]
                            ]
            );
        }

        $this->view->followCount = $followCount;

        $arrPageFollowers = (new PageReactionFactory())->getFollowersForPage ($objPage);

        if ( $arrPageFollowers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to followers for page"]
                            ]
            );
        }

        $arrFollowers = [];

        foreach ($arrPageFollowers as $objUser) {
            $arrFollowers[] = $objUser->getId ();
        }

        $this->view->arrFollowers = $arrFollowers;
    }

    public function createPageAction ()
    {

        $objPageType = new PageTypeFactory();

        $this->view->arrPageTypes = $objPageType->getPageTypes ();

        if ( $this->view->arrPageTypes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function savePageAction ()
    {
        $this->view->disable ();

        $objPageFactory = new PageFactory();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        if ( !isset ($_POST['title']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $url = strtolower (str_replace (" ", "_", $_POST['title']));

        $imageLocation = '';

        if ( !empty ($_FILES) )
        {
            $imageLocation = $this->singleUploadAction ('page', $_POST['title'], $_FILES);
        }

        $blResult = $objPageFactory->createPage (
                $objUser, new PageType ($_POST['pageType']), $_POST['category'], $_POST['title'], $_POST['description'], $url, $_POST['link'], $imageLocation, $_POST['address'], $_POST['postcode'], $_POST['telephoneNo'], $_POST['websiteUrl']
        );

        if ( $blResult === false )
        {

            $this->ajaxresponse ("error", implode ("<br/>", $objPageFactory->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

    public function saveNewReviewAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['review']) || empty ($_POST['pageId']) || !isset ($_POST['rating']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        try {
            $objPage = new Page ($_POST['pageId']);

            $objUser = new User ($_SESSION['user']['user_id']);

            //$objPostFactory = new PostFactory (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());

            $objPostFactory = new ReviewPost ($objPage, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());

            $objPost = $objPostFactory->createComment ($_POST['review'], $objUser, new \JCrowe\BadWordFilter\BadWordFilter ());

            if ( $objPost === false )
            {
                $this->ajaxresponse ("error", "Invalid user");
            }

            if ( trim ($_POST['rating']) !== "" )
            {
                $blResponse = (new ReviewFactory())->createReview ($objPage, $objUser, $objPost, $_POST['rating']);
            }

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
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", "Unable to save review");
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Unable to save rating");
        }

        $arrReviews = (new ReviewFactory())->getReviewsForPost ($objPost);

        if ( $arrReviews === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($arrReviews) )
        {
            $objPost->setArrRatings ($arrReviews);
        }

        $arrPosts[0] = $objPost;

        (new NotificationFactory())->sendNotificationToPageFollowers (new PageReactionFactory (), $objPage, $_SESSION['user']['username'] . ' added a review in ' . $objPage->getName ());

        $this->view->arrPosts = $arrPosts;
        $this->view->partial ("templates/posts");
    }

    public function deletePageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['group_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPage = new Page ($_POST['group_id']);

        $blResponse = $objPage->delete ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        (new NotificationFactory())->sendNotificationToPageFollowers (new PageReactionFactory (), $objPage, $objPage->getName () . ' has been deleted');

        $this->ajaxresponse ("success", "SUCCESS");
    }

    public function postPageCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['pageUrl']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        try {
            $objUser = new User ($userId);

            $objPage = new Page ($_POST['pageUrl']);
            $objPostFactory = new PagePost ($objPage, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
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
            $objUserSettings = new UserSettings ($objUser);

            if ( $objUserSettings->getNotificationSetting ('page') === true )
            {
                (new NotificationFactory())->sendNotificationToPageFollowers (new PageReactionFactory (), $objPage, $_SESSION['user']['username'] . ' just posted in ' . $objPage->getName ());
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
        }

        $this->view->arrPosts = array($objPost);
        $this->view->partial ("templates/posts");
    }

    public function saveUpdatedPageAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['page_name']) || !isset ($_POST['page_id']) || !isset ($_POST['page_description']) || !isset ($_POST['vgroup_websiteUrl']) || !isset ($_POST['vgroup_postcode']) || !isset ($_POST['vgroup_telephone']) || !isset ($_POST['vgroup_address']) || !isset ($_POST['photo_added'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        $objPage = new Page ($_POST['page_id']);

        $imageLocation = '';

        if ( !empty ($_FILES[0]['name']) )
        {
            $arrFiles['userImage'] = $_FILES[0];

            $imageLocation = $this->singleUploadAction ('page', $_POST['page_name'], $arrFiles);

            if ( $imageLocation === '' || $imageLocation === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $blResult = $objPage->updatePageImage ($imageLocation);

            if ( $blResult === false )
            {

                $this->ajaxresponse ("error", implode ("<br/>", $objPage->getValidationFailures ()));
            }
        }

        $objPage->setName ($_POST['page_name']);
        $objPage->setDescription ($_POST['page_description']);
        $objPage->setWebsiteUrl ($_POST['vgroup_websiteUrl']);
        $objPage->setPostcode ($_POST['vgroup_postcode']);
        $objPage->setTelephoneNo ($_POST['vgroup_telephone']);
        $objPage->setAddress ($_POST['vgroup_address']);

        $blResult = $objPage->save ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success", ["id" => $objPage->getId (), "photo" => $imageLocation]);
    }

    public function getFollowsForPageAction ()
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

            $arrFollowers = (new PageReactionFactory())->getFollowersForPage ($objPage);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrFollowers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrFollowers = $arrFollowers;
        $this->view->partial ("templates/followList");
    }

    public function getLikesForPageAction ()
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

            $arrLikes = (new PageReactionFactory())->getLikeListForPage ($objPage);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrLikes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrLikes = $arrLikes;
        $this->view->partial ("templates/likeList");
    }

    public function likePageAction ()
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

            $blResult = (new PageReaction())->likePage (new User ($_SESSION['user']['user_id']), $objPage);

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            (new NotificationFactory())->sendNotificationToPageFollowers (new PageReactionFactory (), $objPage, $_SESSION['user']['username'] . ' just liked ' . $objPage->getName ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function followPageAction ()
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

            $blResult = (new PageReaction())->followPage (new User ($_SESSION['user']['user_id']), $objPage);
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

    public function unlikePageAction ()
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

            $blResult = (new PageReaction())->unlikePage (new User ($_SESSION['user']['user_id']), $objPage);
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

    public function searchPagesAction ()
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
            $objPageFactory = new PageFactory();
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



        $arrPages = $objPageFactory->getAllPages (new PageReactionFactory (), $objUser, null, 0, $this->paginationLimit);

        $totalCount = $objPageFactory->getAllPages (new PageReactionFactory (), $objUser);

        if ( $arrPages === false || $totalCount === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get pages"]
                            ]
            );
        }

        $arrMemberPages = $objPageFactory->getPagesForProfile ($objUser, new PageReactionFactory ());

        if ( $arrMemberPages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get pages"]
                            ]
            );
        }

        $this->view->arrMemberPages = $arrMemberPages;

        $this->view->arrPages = $arrPages;

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

        $arrCategories = (new PageCategoryFactory())->getAllCategories ();

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

        $this->view->arrFriendRequests = $arrFriendRequests;
        $this->view->objUser = $objUser;
        $this->view->arrFriendList = $arrFriendList;
        $this->view->arrPages = $arrPages;
        $this->view->paginationLimit = $this->paginationLimit;
        $this->view->totalCount = count ($totalCount);
    }

    public function pageSearchPaginationAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->ajaxresponse ("error", "invalid user");
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( empty ($_POST['vpb_start']) || !isset ($_POST['searchText']) || !isset ($_POST['pageCategory']) || empty ($_POST['vpb_total_per_load']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $searchText = !empty ($_POST['searchText']) ? $_POST['searchText'] : null;
        $category = !empty ($_POST['pageCategory']) ? new PageCategory ($_POST['pageCategory']) : null;
        $page = $searchText === null && $category === null ? (int) $_POST['vpb_start'] : null;
        $totalToLoad = $searchText === null && $category === null ? (int) $_POST['vpb_total_per_load'] : null;

        $arrPages = (new PageFactory())->getAllPages (new PageReactionFactory (), $objUser, $searchText, $page, $totalToLoad, $category);

        if ( $arrPages === false )
        {
            $this->ajaxresponse ("error", "unable to get groups");
        }

        $this->view->arrPages = $arrPages;
        $this->view->objUser = $objUser;
    }

    public function unfollowPageAction ()
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

        $objPage = new Page ($_POST['pageId']);

        $blResult = (new PageReaction())->removefollowerFromPage (new User ($_SESSION['user']['user_id']), $objPage);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

}
