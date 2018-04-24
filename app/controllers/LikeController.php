<?php

use Phalcon\Mvc\View;

/**
 * Description of LikeController
 *
 * @author michael.hampton
 */
class LikeController extends ControllerBase
{

    public function likeAction ()
    {
        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $objPostAction = new PostAction();

        if ( $_POST['commentId'] != 'null' && $_POST['commentId'] !== NULL )
        {
            $id = $_POST['commentId'];

            $objComment = new Comment ($id);

            $blResponse = $objPostAction->likeComment ($objComment, $objUser);

            if ( $blResponse === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $likes = $objPostAction->getLikesForComment ($objComment);
        }
        else
        {
            $id = $_POST['id'];
            $objPost = new Post ($id);
            $blResponse = $objPostAction->likePost ($objPost, $objUser);

            if ( $blResponse === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            if ( (int) $objPost->getUserId () !== (int) $objUser->getId () )
            {
                try {
                    $subject = $objUser->getFirstName () . ' ' . $objUser->getLastName () . ' liked your post';
                    $message = 'Post: ' . $objPost->getMessage ();
                    $objNotification = new NotificationFactory();
                    $objOwner = new User ($objPost->getUserId ());
                    $objEmail = new EmailNotification ($objOwner, $subject, $message);
                    $objEmail->sendEmail ();
                    $objNotification->createNotification ($objOwner, $subject);
                } catch (Exception $ex) {
                    trigger_error ($ex->getMessage (), E_USER_WARNING);
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }

            $likes = $objPostAction->getLikesForPost ($objPost);
        }

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "Unable to save");
        }

        if ( $likes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        echo json_encode (array("likes" => $likes));
    }

    public function showLikesAction ()
    {
        $this->view->disable ();

        $objPostAction = new PostActionFactory();

        if ( !isset ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !isset ($_POST['type']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $id = $_POST['id'];

        switch (trim ($_POST['type'])) {
            case "post":
                $objPost = new Post ($id);
                $arrLikes = $objPostAction->getLikeListForPost ($objPost);
                break;

            case "reaction":
                if ( empty ($_POST['reactionType']) )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
                die ("Yes");
                break;

            default:
                $objComment = new Comment ($id);

                $arrLikes = $objPostAction->getLikeListForComment ($objComment);
                break;
        }

        if ( $arrLikes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrLikes = $arrLikes;
        $this->view->partial ("templates/likeList");
    }

    public function getLikeCountAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['post_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPostAction = new PostActionFactory();

        if ( $_POST['type'] == "comment" )
        {
            
        }
        else
        {
            $objPost = new Post ($_POST['post_id']);
            $arrLikes = $objPostAction->getLikeListForPost ($objPost);

            echo '<span id="vpb_system_like_title"><div style="display:inline-block; margin-right:25px; font-family:arial !important; font-size:14px !important;" class="vpb_DefaultColor"><i class="likeIcon_c" onclick="vpb_auto_load_post_likes(\'' . $_POST['post_id'] . '\', \'michaelhampton\', \'Like\');"></i> 1</div></span>';
        }
    }

    public function unlikeAction ()
    {
        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $objPostAction = new PostAction();

        if ( $_POST['commentId'] != 'null' && $_POST['commentId'] !== NULL )
        {
            $id = $_POST['commentId'];

            $objComment = new Comment ($id);

            $blResponse = $objPostAction->unlikeComment ($objComment, $objUser);

            $likes = $objPostAction->getLikesForComment ($objComment);
        }
        else
        {
            $id = $_POST['id'];
            $objPost = new Post ($id);
            $blResponse = $objPostAction->unlikePost ($objPost, $objUser);

            $likes = $objPostAction->getLikesForPost ($objPost);
        }

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "Cant save");
        }

        if ( $likes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        echo json_encode (array("likes" => $likes));
    }

    public function getAllLikesAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_GET['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_GET['userId'];

        $objUser = new User ($userId);

        $objAction = new PostActionFactory();

        $arrLikes = $objAction->getAllLikesForUser ($objUser);

        if ( $arrLikes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrLikes = $arrLikes;
    }

    public function reactionAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['post_id']) || empty ($_POST['type']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            $objFactory = new PostAction();
            $objPost = new Post ($_POST['post_id']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( strtolower (trim ($_POST['type'])) === "like" )
        {
            $blResponse = $objFactory->likePost ($objPost, $objUser);

            if ( $blResponse === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $count = $objFactory->getLikesForPost ($objPost);

            $this->ajaxresponse ("sucess", "SUCCESS", ["count" => $count]);
        }

        $blResult = $objFactory->add ($_POST['type'], $objUser, $objPost);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $count = $objFactory->getReactionCounts ($_POST['type'], $objPost);

        $this->ajaxresponse ("sucess", "SUCCESS", ["count" => $count]);
    }

}
