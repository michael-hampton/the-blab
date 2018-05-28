<?php

use Phalcon\Mvc\View;

class PostController extends ControllerBase
{

    public function indexAction ($url)
    {
        
    }

    public function ignorePostAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['id']) || !is_numeric ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = new Post ($_POST['id']);

        $blResult = $objPost->ignorePost (new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }
    
    public function getUnreadPostsAction()
    {
        $this->view->disable();
        
        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $objUser = new User ($_SESSION['user']['user_id']);
        
        switch(true)
        {
            case ($pos = strpos($field, 'profile')) >= 0:
                $arrPosts = $objPostFactory->getPostsForUser ($objUser, null, 0, 4);
            break;
                
            case ($pos = strpos($field, 'index/index')) >= 0:
                $arrPosts = $objPostFactory->getPostsForNewsFeed ($arrUserPages, $arrGroups, $arrEvents, $objUser, true, null, null, null, $arrUserSettings);
            break;
        }
        
        if($arrPosts === false) {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
            
    }

    /**
     * 
     * @param type $id
     */
    public function clonePostAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['post_id']) ||
                trim ($_POST['post_id']) === "" ||
                !is_numeric ($_POST['post_id']) ||
                empty ($_POST['selected_option_shared_privacy'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = new Post ($_POST['post_id']);

        if ( !empty ($_POST['selected_friend_to_share_with']) && trim ($_POST['selected_friend_to_share_with']) !== "" && is_numeric ($_POST['selected_friend_to_share_with']) )
        {
            $objUser = new User ($_POST['selected_friend_to_share_with']);
            $sharedUsername = $_SESSION['user']['username'];
        }
        else
        {
            $objUser = new User ($_SESSION['user']['user_id']);
            $sharedUsername = $_SESSION['user']['username'];
        }

        try {
            $objPostFactory = new ClonePost ();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objNewPost = $objPostFactory->clonePost ($objPost, $objUser, $_POST['vpb_wall_share_post_data'], $sharedUsername, $_POST['selected_option_shared_privacy']);

        if ( $objNewPost === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrPosts[0] = $objPost;
        $this->view->totalCommentsToDisplay = 1;
        $this->view->arrPosts = $arrPosts;

        $this->view->partial ("templates/posts");
    }

    public function sortPostsAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( !isset ($_POST['vpb_sort_option']) || trim ($_POST['vpb_sort_option']) === "" )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $sortBy = trim ($_POST['vpb_sort_option']) === "sort_updates_by_latest_comments" ? "comments" : null;

        $start = $_POST['vpb_start'];

        if ( (int) $start !== 0 )
        {
            $perPage = $_POST['vpb_total_per_load'];
            $start = $start + $perPage;
        }

        try {
            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrPosts = $objPostFactory->getPostsForUser ($objUser, $sortBy, $start, $perPage);

        if ( $arrPosts === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['vpb_start']) || !isset ($_POST['vpb_total_per_load']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrPosts = $arrPosts;

        if ( count ($this->view->arrPosts) < 1 )
        {
            return false;
        }

        $this->view->partial ("templates/posts");
    }

    public function deletePostAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['item_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPost = new Post ($_POST['item_id']);

            $blResult = $objPost->delete (
                    new AuditFactory (), new User ($_SESSION['user']['user_id']), new CommentFactory (), new CommentReplyFactory (), new PostAction ()
            );
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to delete");
        }
        $this->ajaxresponse ("success", "success");
    }

    public function sharePostAction ()
    {

        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['post_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objPost = new Post ($_POST['post_id']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->objPost = $objPost;
    }

    public function tagPeopleInPostAction ()
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

    public function postCommentAction ()
    {
        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) ||
                empty ($_SESSION['user']['user_id']) ||
                empty ($_SESSION['user']['username'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objUserSettings = new UserSettings ($objUser);

            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
            $objPost = $objPostFactory->createPost ($_POST['comment'], $objUser, new JCrowe\BadWordFilter\BadWordFilter (), null, $_POST['usersLocation'], 3, $_POST['privacyOption']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        if ( $objPost === FALSE )
        {
            $this->ajaxresponse ("error", "Unable to save");
        }

        if ( !empty ($_POST['tags']) )
        {

            $objNotification = new NotificationFactory();

            $tags = explode (",", $_POST['tags']);

            $username = $_SESSION['user']['username'];

            $message = $username . " Tagged you in a comment";

            foreach ($tags as $taggedUser) {

                $objNewUser = new User ($taggedUser);

                $blResult = $objNotification->createNotification ($objNewUser, $message);

                $blResult2 = (new TagUserFactory())->createTagForPost ($objNewUser, $objPost);
                $objEmail = new EmailNotification ($objNewUser, $message, $_POST['comment']);
                $objEmail->sendEmail ();
            }

            $arrTags = (new TagUserFactory())->getTaggedUsersForPost ($objPost);

            if ( !empty ($arrTags) )
            {
                $objPost->setArrTags ($arrTags);
            }
        }

        $arrImages = (new UploadFactory())->getImagesForPost ($objPost);

        if ( !empty ($arrImages) )
        {
            $objPost->setArrImages ($arrImages);
        }

        if ( isset ($_POST['profileUser']) && trim ($_POST['profileUser']) !== "" && is_numeric ($_POST['profileUser']) && (int) $_POST['profileUser'] !== $objUser->getId () )
        {

            try {
                $objProfileUser = new User ((int) $_POST['profileUser']);

                if ( $objUserSettings->getEmailSetting ("post") === true )
                {
                    $objEmail = new EmailNotification ($objProfileUser, $objUser->getFirstName () . ' ' . $objUser->getLastName () . ' posted on your wall', $_POST['comment']);
                    $objEmail->sendEmail ();
                }


                (new NotificationFactory())->createNotification ($objProfileUser, $objUser->getFirstName () . ' ' . $objUser->getLastName () . ' posted on your wall ' . $_POST['comment']);
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage (), E_USER_WARNING);
            }
        }

        $arrPosts[0] = $objPost;
        $this->view->arrPosts = $arrPosts;

        $this->view->partial ("templates/posts");
    }

    public function reportPostAction ()
    {
        $this->view->disable ();

        mail (EMAIL_ADDRESS, "A post {$_POST['post_id']} has been reported by {$_POST['session_fullname']}", $_POST['report_post_data']);
    }

    public function updatePostAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['post_id']) || empty ($_POST['post']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = new Post ($_POST['post_id']);

        if ( $objPost->getUserId () !== $_SESSION['user']['user_id'] )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost->setMessage ($_POST['post']);
        $blResult = $objPost->save (new AuditFactory (), new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }        
    }

}
