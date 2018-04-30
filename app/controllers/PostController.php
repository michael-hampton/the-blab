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

        $this->view->disable ();

        if ( empty ($_POST['post_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objMessage = new Post ($_POST['post_id']);

        echo '<span id="v_share_this_conts">			<br clear="all">
			<div style="margin:0px !important;max-width:100% !important; width:100% !important; text-align:left;">
            
            <input type="hidden" id="v_owner_of_post" value="">            
			<div class="modal-content vasplus_a" style="border:1px solid #E1E1E1 !important; border-bottom:0px solid !important;box-shadow:none !important;border-radius:0px;max-width:100%; width:100%;padding:0px !important; margin:0px !important; padding:0px !important;">
			
			<div class="modal-body vasplus_b">
			<div class="vpb_wall_adjust_c">
			<div class="input-group vpb_wall_b_contents">
			
			<span class="input-group-addon vpb-wall-user-photo" style="cursor:pointer;">
			<span class="v_status_pics_michaelhampton"><img src="/blab/public/uploads/profile/' . $objMessage->getUsername () . '.jpg" width="45" height="45" border="0"></span>
			</span>
			<div class="vpb_wrap_post_contents">
			<div>
			<span class="vpb_wrap_post_contents_a">
			<span class="vpb_wall_fullname">' . $objMessage->getAuthor () . '</span>
			<span style="color:#9197a3;">
					</span>
			<br clear="all">
			
			 <span class="vpb_date_time_posted">8 hours ago 璺� </span>
			 			 <span style="display:none;" id="vdotted_id_4675"> 璺� </span>			 <div style="display:inline-block;">
			
		   <span class="vpb_wall_post_security_setting_disabled">
			<span title="Shared with: Public" class="vasplus-tooltip-attached"><i class="fa fa-certificate"></i></span>
			</span>
		
			</div>
			</span>
			<span class="vpb_wrap_post_contents_b">
			&nbsp;
			</span>
			<div style="clear:both;"></div>
			</div>
			<div style="clear:both;"></div>
			</div>
			</div><div style="clear:both;"></div>
			<div class="vpb_wall_post_description">
						<!-- Added Photo Box -->
			<div class="vpb_photos_wrapper_large">
                
			</div>
						<div class="vpb_shared_post_desc" style="margin-top:0px !important;">
			' . $objMessage->getMessage () . '			</div>
			
			</div>
			
			<div style="clear:both;"></div>
			
			</div>
			</div>
			</div>
			
			</div>
			
			</span>';
    }

    public function tagPeopleInPostAction ()
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

        echo '<div class="dropdown"><ul class="dropdown-menu vpb-hundred-percent" id="tag_people_in_post_suggestion_box" style="top:auto;">';

        foreach ($arrUsers as $arrUser) {

            echo '<li>
						<a onclick="vpb_tag_this_friend(\'' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '\', \'' . $arrUser->getUsername () . '\', ' . $arrUser->getId () . ');">
						<span class="vpb_left_tag_box"><img src="/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg" width="40" height="40" border="0"></span>
						<span class="vpb_left_tag_text_box">' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '</span>
						<div style="clear:both;"></div>
						</a>
						</li>';
        }

        echo '</ul></div>';
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

        $objUser = new User ($_SESSION['user']['user_id']);

        try {
            $objPostFactory = new UserPost (new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPost = $objPostFactory->createPost ($_POST['comment'], $objUser, new JCrowe\BadWordFilter\BadWordFilter (), null, $_POST['usersLocation'], 3, $_POST['privacyOption']);

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
                $objEmail = new EmailNotification ($objProfileUser, $objUser->getFirstName () . ' ' . $objUser->getLastName () . ' posted on your wall', $_POST['comment']);
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

//       Array
//(
//    [post_id] => 126
//    [the_posterFullname] => Jhoanna Hampton
//    [the_posterUsername] => jhoanna.hampton
//    [the_posterEmail] => uanhampton@yahoo.com
//    [the_pageUsernamed] => Lexieh
//    [session_fullname] => Michael hampton
//    [session_username] => michaelhampton
//    [session_email] => bluetiger_uan@yahoo.com
//    [report_post_data] => test etst test
//    [page] => report-an-update
//)
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
