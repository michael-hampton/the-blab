<?php

use Phalcon\Mvc\View;

class CommentController extends ControllerBase
{

    public function indexAction ($url)
    {
        
    }

    public function uploadCommentPhotoAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objComment = new Comment ($_POST['id']);

        $objUpload = new UploadFactory();

        $arrFiles = $_FILES;

        if ( empty ($arrFiles[0]['name']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            ;
        }

        $target_dir = $this->rootPath . "/blab/public/uploads/comments/";

        $arrIds = [];

        $target_file = $target_dir . basename ($_FILES[0]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

        if ( $_FILES[0]["size"] > 500000 )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

        $check = getimagesize ($_FILES[0]["tmp_name"]);

        if ( $check !== false )
        {
            $uploadOk = 1;
        }
        else
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            $uploadOk = 0;
        }

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        if ( move_uploaded_file ($_FILES[0]["tmp_name"], $target_file) )
        {
            $savedLocation = str_replace ($this->rootPath, "", $target_file);

            $objUploaded = $objUpload->createUpload (new User ($_SESSION['user']['user_id']), $savedLocation);

            if ( $objUploaded === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                return false;
            }

            $blResult = $objComment->addImageToComment ($objUploaded);

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            $this->ajaxresponse ("success", "SUCCESS");
        }
        else
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function deleteCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['item_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objComment = new Comment ($_POST['item_id']);

        $blResult = $objComment->delete (new CommentReplyFactory (), new PostAction ());

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to delete");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function postReplyAction ()
    {
        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) ||
                empty ($_SESSION['user']['user_id']) ||
                empty ($_SESSION['user']['username'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $id = $_POST['id'];

        $objUser = new User ($_SESSION['user']['user_id']);
        $objPost = new Post ($id);


        $blResponse = (new CommentFactory())->createComment ($_POST['comment'], $objPost, $objUser);

        if ( $blResponse === FALSE )
        {
            $this->ajaxresponse ("error", "Unable to save");
        }

        $arrCommentLikes = $blResponse->getArrLikes ();

        $likeCommentClass = 'like';
        $commentLikes = 'Be the first person to like this';
        $likeCommentCount = 0;

        if ( !empty ($arrCommentLikes) )
        {

            $userCommentLikes = array_reduce ($arrCommentLikes, function ($reduced, $current) {

                $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

                $reduced[$current->getUsername ()] = $fullName;
                return $reduced;
            });

            $likeCommentClass = !empty ($userCommentLikes) && array_key_exists ($_SESSION['user']['username'], $userCommentLikes) ? 'unlike' : $likeCommentClass;
            $commentLikes = !empty($userCommentLikes) && $userCommentLikes !== null ? implode (',', array_slice ($userCommentLikes, 0, 2)) : '';
            $likeCommentCount = !empty($userCommentLikes) && $userCommentLikes !== null ? count ($userCommentLikes) - 2 : 0;
        }

        $comment = array(
            "date_added" => date ('Y-m-d H:i:s', strtotime ('- 1 hour')),
            "id" => $_POST['id'],
            "comment" => $_POST['comment'],
            "author" => $objUser->getFirstName () . ' ' . $objUser->getLastName (),
            "username" => $objUser->getUsername (),
            "current_user" => $_SESSION['user']['username'],
            "comment_id" => $blResponse->getId (),
            "likeCount" => $blResponse->getLikes (),
            "likeCommentClass" => $likeCommentClass,
            "commentLikes" => $commentLikes,
            "likeCommentCount" => $likeCommentCount
        );

        echo json_encode ($comment);
        die;
    }

    public function updateCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['comment_id']) || empty ($_POST['comment']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objComment = new Comment ($_POST['comment_id']);
        $objComment->setComment ($_POST['comment']);
        $blResponse = $objComment->save (new AuditFactory (), new User ($_SESSION['user']['user_id']));

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function unhideCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['type']) || empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( $_POST['type'] == "post" )
        {
            $objPost = new Post ($_POST['id']);
            $blResponse = $objPost->removeHiddenPost ($objUser);
        }
        elseif ( $_POST['type'] == "comment" )
        {
            $objComment = new Comment ($_POST['id']);
            $blResponse = $objComment->removeHiddenComment ($objUser);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function ignoreCommentAction ()
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

        $objComment = new Comment ($_POST['id']);

        $blResult = $objComment->ignoreComment (new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }

}
