<?php

use Phalcon\Mvc\View;

/**
 * 
 */
class IndexController extends ControllerBase
{

    /**
     * 
     * @param type $per_page
     * @param type $array
     * @param type $current_page
     * @return type
     */
    private function arrayPagination ($per_page, $array, $current_page)
    {
        $page = !empty ($current_page) ? (int) $current_page : 1;
        $total = count ($array); //total items in array    
        $totalPages = ceil ($total / $per_page); //calculate total pages
        $page = max ($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min ($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $per_page;
        if ( $offset < 0 )
        {
            $offset = 0;
        }

        $yourDataArray = array_slice ($array, $offset, $per_page);

        return $yourDataArray;
    }

    /**
     * 
     * @param type $username
     * @return type
     */
    public function profileAction ($username)
    {
        if ( empty ($username) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

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
            $arrUsers = $objUserFactory->getUsers ();
            $objCurrentUser = new User ($_SESSION['user']['user_id']);
            $objEventFactory = new EventFactory();
            $objMessageFactory = new MessageFactory();
            $objGroupFactory = new GroupFactory();
            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $arrChatUsers = $objMessageFactory->getChatUsers ($objCurrentUser);


            $objUser = $objUserFactory->getUsers ($username);

            if ( $objUser === false )
            {
                return $this->dispatcher->forward (
                                [
                                    "controller" => "issue",
                                    "action" => "handler",
                                    "params" => ["message" => "Invalid user"]
                                ]
                );
            }

            $objUser = reset ($objUser);

            $arrUserSettings = (new UserSettings ($objUser));
            $arrPhotos = (new UploadFactory())->getUploadaForUser ($objUser, 4);
            $arrBanners = (new AdvertFactory())->getProfileBannerForUser ($objUser, new BannerFactory ());
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

        if ( $arrUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get list of users"]
                            ]
            );
        }

        Phalcon\Tag::setTitle ("Profile " . $objUser->getFirstName () . ' ' . $objUser->getLastName ());

        $this->view->arrUser = $objUser;

        $arrFriendList = $objUserFactory->getFriendList ($objUser);

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

        $arrFilteredFriendList = $this->arrayPagination ($this->totalFriendsPerLoad, $arrFriendList, 0);
        $this->view->arrFilteredFriendList = $arrFilteredFriendList;

        $arrSuggestedList = $objUserFactory->getUsersNotFriends ($objUser);

        if ( $arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get suggested friend list"]
                            ]
            );
        }

        $this->view->arrSuggestedList = $arrSuggestedList;

        $this->view->arrFriendList = $arrFriendList;
        $this->view->noPerFriend = 1;

        $arrCurrentUserFreindList = $objUserFactory->getFriendList ($objCurrentUser);

        if ( $arrCurrentUserFreindList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get list of friends"]
                            ]
            );
        }

        $this->view->arrCurrentUserFreindList = $arrCurrentUserFreindList;

        $this->view->arrEvents = $objEventFactory->getEventsForUser ($objUser);

        if ( $this->view->arrEvents === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get events for user"]
                            ]
            );
        }

        $this->view->arrGroups = $objGroupFactory->getGroupsForUser ($objUser);

        if ( $this->view->arrGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get groups for user"]
                            ]
            );
        }

        $this->view->objUser = $objUser;

        $arrPosts = $objPostFactory->getPostsForUser ($objUser, null, 0, 4);

        if ( $arrPosts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get posts"]
                            ]
            );
        }

        if ( $arrPhotos === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get photos"]
                            ]
            );
        }

        $this->view->arrPhotos = $arrPhotos;
        $this->view->arrPosts = $arrPosts;
        $this->view->arrUsers = $arrUsers;

        if ( $arrChatUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get chat users"]
                            ]
            );
        }

        $this->view->arrChatUsers = $arrChatUsers;

        $arrBlockedUsers = $objUserFactory->getBlockedUsersForUser ($objCurrentUser);

        if ( $arrBlockedUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get blocked users"]
                            ]
            );
        }

        $blBlocked = false;

        foreach ($arrBlockedUsers as $objBlockedUser) {
            if ( $objUser->getId () === $objBlockedUser->getId () )
            {
                $blBlocked = true;
            }
        }

        if ( $blBlocked === true )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "You are unable to see this user"]
                            ]
            );
        }

        if ( $arrUserSettings === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get user settings"]
                            ]
            );
        }

        $this->view->arrUserSettings = $arrUserSettings;

        $arrBlockedList = $objUserFactory->getUnBlockedUsersForUser ($objCurrentUser, true);

        if ( $arrBlockedList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get blocked users"]
                            ]
            );
        }

        $arrUnblockedList = [];

        foreach ($arrBlockedList as $objBlockedList) {
            $arrUnblockedList[] = $objBlockedList->getId ();
        }

        $this->view->arrUnblockedList = $arrUnblockedList;

        $arrFriendRequests = $objUserFactory->getFriendRequests ($objCurrentUser);

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
        $this->view->objCurrentUser = $objCurrentUser;

        if ( $arrBanners === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get users profile banner"]
                            ]
            );
        }

        $this->view->arrBanners = $arrBanners;
    }

    public function indexAction ()
    {
        Phalcon\Tag::setTitle ("News Feed");

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $objUserFactory = new UserFactory();
            $objUser = new User ($_SESSION['user']['user_id']);
            $arrUserSettings = (new UserSettings ($objUser));
            $objEventFactory = new EventFactory();
            $objGroupFactory = new GroupFactory();
            $objPageFactory = new PageFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        $arrUsers = $objUserFactory->getUsers ();

        if ( $arrUsers === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get list of users"]
                            ]
            );
        }

        $arrFriendList = $objUserFactory->getFriendList ($objUser);

        if ( $arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get friend list"]
                            ]
            );
        }

        $this->view->arrFriendList = $arrFriendList;

        $arrSuggestedList = $objUserFactory->getUsersNotFriends ($objUser);

        if ( $arrFriendList === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get suggested friend list"]
                            ]
            );
        }

        $this->view->arrSuggestedList = $arrSuggestedList;

        $arrFilteredFriendList = $this->arrayPagination ($this->totalFriendsPerLoad, $arrFriendList, 0);
        $this->view->arrFilteredFriendList = $arrFilteredFriendList;

        $arrEvents = $objEventFactory->getEventsForProfile ($objUser);

        if ( $arrEvents === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get events for user"]
                            ]
            );
        }

        $this->view->arrEvents = $arrEvents;

        $arrGroups = $objGroupFactory->getGroupsForProfile ($objUser);

        if ( $arrGroups === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get groups for user"]
                            ]
            );
        }

        $this->view->arrGroups = $arrGroups;

        $arrPages = $objPageFactory->getPagesForProfile ($objUser, new PageReactionFactory ());

        $arrUserPages = $objPageFactory->getPagesMemberOf ($objUser);

        if ( $arrUserPages === false || $arrPages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get pages for user"]
                            ]
            );
        }

        $this->view->arrPages = $arrPages;


        $arrPosts = $objPostFactory->getPostsForNewsFeed ($arrUserPages, $arrGroups, $arrEvents, $objUser, true, null, null, null, $arrUserSettings);

        if ( $arrPosts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get posts"]
                            ]
            );
        }

        $this->view->totalPosts = count ($arrPosts);

        $this->view->arrPosts = array_slice ($arrPosts, 0, $this->postsPerPage);
        $this->view->arrUsers = $arrUsers;

        $this->view->arrUser = $objUser;

        $arrAdverts = (new AdvertFactory())->getAdvertsForUser (null, true, new BannerFactory ());

        if ( $arrAdverts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "unable to get adverts"]
                            ]
            );
        }

        $this->view->arrAdverts = $arrAdverts;

        $arrFriendRequests = $objUserFactory->getFriendRequests ($objUser);

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

        if ( trim (strtolower ($objUser->getCountry ())) === "philippines" )
        {
            $this->view->blHasGdpr = true;
        }
        else
        {

            try {
                $blHasGdpr = (new GDPR())->checkUser ($objUser);
            } catch (Exception $ex) {
                return $this->dispatcher->forward (
                                [
                                    "controller" => "issue",
                                    "action" => "handler",
                                    "params" => ["message" => "unable to check users gdpr status"]
                                ]
                );
            }

            $this->view->blHasGdpr = $blHasGdpr;
        }
    }

    public function autoSuggestAction ()
    {
        $this->view->disable ();

        if ( !isset ($_GET['term']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUsers = new UserFactory();
        $term = urldecode ($_GET['term']);

        $arrUsers = $objUsers->getUsers ($term);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrTest = [];

        foreach ($arrUsers as $arrUser) {
            $arrTest[] = array(
                'value' => $arrUser->getFirstName () . ' ' . $arrUser->getLastName (),
                'uid' => $arrUser->getId ()
            );
        }


        echo json_encode ($arrTest);
        die;
    }

    public function convertLinkToEmbedAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['video_url']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $videoLink = $_POST['video_url'];

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResponse = (new VideoFactory())->createVideoForUser (new User ($_SESSION['user']['user_id']), $videoLink);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        $width = 300;
        $height = 300;
        $embed = '';

        if ( preg_match ('/https:\/\/(?:www.)?(youtube).com\/watch\\?v=(.*?)/', $videoLink) )
        {
            $embed = '<div class="embed-responsive preview_video_size">';
            $embed .= preg_replace ("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe class=\"embed-responsive-item\" width=\"" . $width . "\" height=\"" . $height . "\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $videoLink);
            $embed .= '</div>';
        }

        if ( preg_match ('/https:\/\/vimeo.com\/(\\d+)/', $videoLink, $regs) )
        {
            $embed = '<div class="embed-responsive preview_video_size">';
            $embed .= '<iframe class="embed-responsive-item" src="http://player.vimeo.com/video/' . $regs[1] . '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
            $embed .= '</div>';
        }

        $embed .= '<div class="preview_video_action" id="previewed_video"><span onclick="vpb_remove_added_video();" class="vhover">Remove</span></div>';

        $this->ajaxresponse ("success", "SUCCESS", ["id" => $blResponse->getId (), "video" => $embed]);
    }

    public function deleteVideoAction ()
    {
        $this->view->disable ();
    }

    public function extractUrlAction ()
    {
        $this->view->disable ();

        if ( isset ($_POST["link"]) )
        {

            $main_url = $_POST["link"];


            preg_match_all ('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $main_url, $match);

            if ( empty ($match[0][0]) )
            {
                return false;
            }

            $main_url = $match[0][0];

            @$str = file_get_contents ($main_url);


            // This Code Block is used to extract title
            if ( strlen ($str) > 0 )
            {
                $str = trim (preg_replace ('/\s+/', ' ', $str)); // supports line breaks inside <title>
                preg_match ("/\<title\>(.*)\<\/title\>/i", $str, $title);
            }


            // This Code block is used to extract description 
            $b = $main_url;
            @$url = parse_url ($b);
            @$tags = get_meta_tags ($url['scheme'] . '://' . $url['host']);


            // This Code Block is used to extract any image 1st image of the webpage
            $dom = new domDocument;
            @$dom->loadHTML ($str);
            $images = $dom->getElementsByTagName ('img');
            foreach ($images as $image) {
                $l1 = @parse_url ($image->getAttribute ('src'));
                if ( isset ($l1['scheme']) && $l1['scheme'] )
                {
                    $img[] = $image->getAttribute ('src');
                }
                else
                {
                    
                }
            }


            // This Code Block is used to extract og:image which blab extracts from webpage it is also considered 
            // the default image of the webpage
            $d = new DomDocument();
            @$d->loadHTML ($str);
            $xp = new domxpath ($d);
            foreach ($xp->query ("//meta[@property='og:image']") as $el) {
                $l2 = parse_url ($el->getAttribute ("content"));
                if ( $l2['scheme'] )
                {
                    $img2[] = $el->getAttribute ("content");
                }
                else
                {
                    
                }
            }


            echo '<div class="col-lg-6" style="background-color: #EEE">
        <a href="<?php echo $main_url; ?>" target="_blank">';

            echo "<p id='title'>" . $title[1] . "</p>";

            echo '<br>';

            if ( isset ($img2) && $img2 )
            {
                echo "<img style='max-width:150px;' src='" . $img2[0] . "'><br>";
            }
            else
            {
                echo "<img style='max-width:300px;' src='" . $img[0] . "'><br>";
            }
            echo "<p id='desc'>" . $tags['description'] . "</p>";

            echo '</a>
    </div>';


            exit ();
        }
    }

    public function getLocationAction ()
    {
        $this->view->disable ();

//if latitude and longitude are submitted
        if ( empty ($_POST['latitude']) || empty ($_POST['longitude']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $location = (new Location())->getLocation ($_POST['longitude'], $_POST['latitude']);

        echo $location;
    }

    public function loadMoreAction ()
    {

        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) || empty ($_SESSION['user']['user_id']) )
        {
            die ("You are not logged in");
        }

        $userId = $_SESSION['user']['user_id'];

        try {
            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($userId);
        $arrComments = $objPostFactory->getPostsForUser ($objUser);

        if ( $arrComments === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['page']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $page = $_POST['page'];

        $page = !empty ($page) ? (int) $page : 1;
        $nextPage = $page + 1;
        $total = count ($arrComments); //total items in array    
        $limit = 2; //per page    
        $totalPages = ceil ($total / $limit); //calculate total pages
        $page = max ($page, 1); //get 1 page when $_GET['page'] <= 0
        $page = min ($page, $totalPages); //get last page when $_GET['page'] > $totalPages
        $offset = ($page - 1) * $limit;

        if ( $offset < 0 )
            $offset = 0;

        $this->view->yourDataArray = array_slice ($arrComments, $offset, $limit);
    }

    public function removeTaggedUserAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['post_id']) || empty ($_POST['poster_username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResponse = (new TagUserFactory())->deleteTagForPost (new User ($_POST['poster_username']), new Post ($_POST['post_id']));

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function profileImageAction ()
    {
        
    }

    /**
     * 
     */
    public function getPageCategoriesAction ($typeId)
    {
        $this->view->disable ();

        $objCategories = new PageCategoryFactory();
        $arrCategories = $objCategories->getCategoriesForPageType (new PageType ($typeId));

        if ( $arrCategories === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $html = '';

        foreach ($arrCategories as $arrCategory) {
            $html .= '<option value="' . $arrCategory->getId () . '">' . $arrCategory->getName () . '</option>';
        }

        echo $html;
    }

    public function searchAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $objUserFactory = new UserFactory();
        $objUser = new User ($_SESSION['user']['user_id']);

        $arrFriendRequests = $objUserFactory->getFriendRequests ($objUser);

        if ( $arrFriendRequests === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrFriends = $objUserFactory->getFriendList ($objUser);

        if ( $arrFriends === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrFriendIds = [];
        $requestIds = [];

        foreach ($arrFriendRequests as $objFriendRequest) {
            $requestIds[] = (int) $objFriendRequest->getId ();
        }

        foreach ($arrFriends as $objFriend) {

            $arrFriendIds[] = (int) $objFriend->getId ();
        }

        $this->view->arrFriendIds = $arrFriendIds;
        $this->view->requestIds = $requestIds;

        if ( empty ($_POST['friend']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $searchText = $_POST['friend'];

        $arrUsers = $objUserFactory->getUsers ($searchText, true, false);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrUsers = $arrUsers;

        $arrPages = (new PageFactory())->getAllPages (new PageReactionFactory (), $objUser, $searchText);

        if ( $arrPages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrPages = $arrPages;

        $arrGroups = (new GroupFactory())->getAllGroups ($objUser, new GroupRequestFactory (), $searchText);

        if ( $arrGroups === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrGroups = $arrGroups;
    }

    public function uploadAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['uploadComment']) || empty ($_POST['privacy']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['selectedImageType']) || !isset ($_POST['selectedImageId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['addToStory']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blAddToStory = (bool) $_POST['addToStory'];

        $uploadType = $_POST['selectedImageType'];
        $uploadId = $_POST['selectedImageId'];
        $comment = $_POST['uploadComment'];
        $privacy = $_POST['privacy'];

        $target_dir = $this->rootPath . "/blab/public/uploads/" . $_SESSION['user']['username'] . '/';

        try {

            $objUser = new User ($_SESSION['user']['user_id']);
            $objUserSettings = new UserSettings ($objUser);
            $objUploadFactory = new UploadFactory();
            $objTagUserFactory = new TagUserFactory();
            $objPostUpload = new PostUpload (
                    new PostActionFactory (), $objUploadFactory, new CommentFactory (), new ReviewFactory (), $objTagUserFactory, new CommentReplyFactory (), new JCrowe\BadWordFilter\BadWordFilter (), $objUser, new BannerFactory (), new AdvertFactory (), new NotificationFactory (), new EmailNotificationFactory (), $uploadType, $uploadId);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrTags = !empty ($_POST['tags']) ? $_POST['tags'] : null;

        $blResult = $objPostUpload->multipleUploadValidation ($this->rootPath, $_FILES, $target_dir, $comment, $arrTags, $objUserSettings, $privacy, $blAddToStory);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = $objPostUpload->getObjPost ();

        $arrImges = $objUploadFactory->getImagesForPost ($objPost);

        if ( empty ($arrImges) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrTags = $objTagUserFactory->getTaggedUsersForPost ($objPost);

        if ( !empty ($arrTags) )
        {
            $objPost->setArrTags ($arrTags);
        }

        $objPost->setArrImages ($arrImges);
        $arrPosts[0] = $objPost;
        $this->view->arrPosts = $arrPosts;

        $this->view->partial ("templates/posts");
    }

    public function getEditHistoryAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['item_id']) || empty ($_POST['action']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $action = $_POST['action'] == "reply" ? "comment_reply" : $_POST['action'];

        $arrAudits = (new AuditFactory())->getAudits ($action, $_POST['item_id']);

        if ( $arrAudits === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrAudits as $arrAudit) {
            $objUser = new User ($arrAudit->getUserId ());

            echo '<div class="vpb_popup_tagged_box" style="padding-top:10px !important; padding-bottom:10px !important;">
						<div class="vpb_popup_tagged_box_a">
						<div style="float:left;display:inline-block;width:46px;vertical-align:top;">
						<img src="/blab/uploads/profile/' . $objUser->getUsername () . '.jpg" style="max-width:40px !important; max-height:40px !important; width:auto; height:auto;cursor:pointer;" border="0" align="absmiddle">
						</div>
						
						<div style="float:left;display:inline-block;width:100%;max-width:85%;word-break: break-all;vertical-align:top;">
						
						<div class="vpb_popup_tagged_box_c vpb_hover">' . $objUser->getFirstName () . ' ' . $objUser->getLastName () . '</div><br>
						<div style="font-family:arial; font-size:13px;color:#444;">' . $arrAudit->getAfter () . '</div>
						
						</div>
						<div style="clear:both;"></div>
						</div>
						
						
						<div style="clear:both;"></div>
						<div class="vpb_wrap_post_contents_e">
						<div class="vpb_comment_update_bottom_links" style="cursor:default; text-align:left; margin-left:46px;">
						<span class="vpb_date_time_posted" title="' . $arrAudit->getDateCreated () . '">' . $arrAudit->getDateCreated () . '</span>
						</div>
						</div>
					</div>';
        }
    }

    public function videoUploadAction ()
    {
        $this->view->disable ();

        echo "completed 1";
    }

    public function locationSearchAction ($event = false)
    {
        $this->view->disable ();

        if ( !isset ($_POST['location']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrResults = (new Location())->locationSearch ($_POST['location']);

        if ( $arrResults === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $function = $event === 'true' ? 'buildLocationResults' : 'vpb_selected_location';

        foreach ($arrResults as $arrResult) {
            echo '<li>
					<a onclick="' . $function . '(\'' . $arrResult['IL_NAME'] . '\');">
					<span class="vpb_left_tag_text_box">' . $arrResult['IL_NAME'] . '</span>
					<div style="clear:both;"></div>
					</a>
					</li>';
        }
    }

    public function testAction ()
    {
        
    }

    public function testPostAction ()
    {
        $this->view->disable ();

        if ( !empty ($_FILES) )
        {
            $arrFiles = $_FILES;

            foreach ($arrFiles['files']['name'] as $key => $name) {

                if ( trim ($name) === "" )
                {
                    continue;
                }

                $type = $arrFiles['files']['type'][$key];
                $tempName = $arrFiles['files']['tmp_name'][$key];
                $error = $arrFiles['files']['error'][$key];
                $size = $arrFiles['files']['size'][$key];

                $objFileUpload = new FileUploadTest ($target_dir, 50000, array("gif", "jpeg", "jpg", "png"));
                $objFileUpload->setName($name);
                $objFileUpload->setSize($size);
                $objFileUpload->setTempName($tempName);
                $objFileUpload->setType($type);

                if ( $objFileUpload->validateUpload () === false )
                {
                    print_r ($objFileUpload->getValidationFailures ());
                    die;
                }
            }
        }

        echo '<pre>';
        print_r ($_POST);
        print_r ($_FILES);
        print_r (json_decode ($_POST['attendees'], true));
        die;
    }

    public function getRssAction ()
    {
        $this->view->disable ();
//get the q parameter from URL
        $q = $_GET["q"];

//find out which feed was selected
        if ( $q == "Google" )
        {
            $xml = ("http://www1.cbn.com/rss-cbn-articles-cbnnews.xml");
        }
        elseif ( $q == "NBC" )
        {
            $xml = ("http://rss.msnbc.msn.com/id/3032091/device/rss/rss.xml");
        }

        $xmlDoc = new DOMDocument();
        $xmlDoc->load ($xml);

//get elements from "<channel>"
        $channel = $xmlDoc->getElementsByTagName ('channel')->item (0);
        $channel_title = $channel->getElementsByTagName ('title')
                        ->item (0)->childNodes->item (0)->nodeValue;
        $channel_link = $channel->getElementsByTagName ('link')
                        ->item (0)->childNodes->item (0)->nodeValue;
        $channel_desc = $channel->getElementsByTagName ('description')
                        ->item (0)->childNodes->item (0)->nodeValue;

//output elements from "<channel>"
        echo("<p><a href='" . $channel_link
        . "'>" . $channel_title . "</a>");
        echo("<br>");
        echo($channel_desc . "</p>");

//get and output "<item>" elements
        $x = $xmlDoc->getElementsByTagName ('item');
        for ($i = 0; $i <= 2; $i++) {
            $item_title = $x->item ($i)->getElementsByTagName ('title')
                            ->item (0)->childNodes->item (0)->nodeValue;
            $item_link = $x->item ($i)->getElementsByTagName ('link')
                            ->item (0)->childNodes->item (0)->nodeValue;
            $item_desc = $x->item ($i)->getElementsByTagName ('description')
                            ->item (0)->childNodes->item (0)->nodeValue;
            echo ("<p><a href='" . $item_link
            . "'>" . $item_title . "</a>");
            echo ("<br>");
            echo ($item_desc . "</p>");
        }
    }

}
