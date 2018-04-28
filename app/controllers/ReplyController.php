<?php

use Phalcon\Mvc\View;

class ReplyController extends ControllerBase
{

    public function indexAction ($url)
    {
        
    }

    public function saveCommentReplyAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['comment_id']) || empty ($_POST['reply']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = (new CommentReplyFactory())->createCommentReply (new User ($_SESSION['user']['user_id']), new Comment ($_POST['comment_id']), $_POST['reply']);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS", ["id" => $blResult->getId ()]);
    }

    public function uploadCommentReplyPhotoAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objCommentReply = new CommentReply ($_POST['id']);

        $objUpload = new UploadFactory();

        $arrFiles = $_FILES;


        if ( !empty ($arrFiles[0]['name']) )
        {
            $target_dir = $this->rootPath . "/blab/public/uploads/replies/";

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

                $blResult = $objCommentReply->addImageToComment ($objUploaded);

                if ( $blResult === false )
                {
                    $this->ajaxresponse ("error", $this->defaultErrrorMessage);
                }
            }
        }

        echo '<div class="modal-content vasplus_a" style="border-top:0px solid !important;border-bottom:0px solid #E6E6E6 !important;background-color: #f6f7f8 !important;">
                
                <div style="margin-left:55px !important;border-left:4px solid #E1E1FF; clear:both;">
				
				<div class="modal-body vasplus_b" style="background-color: #f6f7f8 !important;">
				<div class="vpb_wall_adjust_c" style="padding-top: 2px !important;">
                
                
				<div class="input-group vpb_wall_b_contents">
				
				<span class="input-group-addon vpb-wall-user-photo" style="cursor:pointer;" onclick="window.location.href=\'/blab/index/profile/' . $objCommentReply->getUsername () . '\';">
				<span class="v_status_pictured_michaelhampton"><img src="/blab/public/uploads/profile/' . $objCommentReply->getUsername () . '.jpg" width="24" height="24" border="0" onmouseover="vpb_get_user_onmouseover_data(\'reply' . $objCommentReply->getId () . 'michaelhampton\', \'Michael hampton\', \'United Kingdom\', \'/blab/uploads/profile/' . $objCommentReply->getUsername () . '.jpg\');" onmouseout="vpb_get_user_mouseout_data(\'reply' . $objCommentReply->getId () . 'michaelhampton\', \'Michael hampton\', \'United Kingdom\', \'/blab/uploads/profile/' . $objCommentReply->getUsername () . '.jpg\');"></span>
				</span>
				<div class="vpb_wrap_post_contents">
				
				<span class="vpb_wrap_post_contents_rr">
				<span class="vpb_wall_reply_fullname" onmouseover="vpb_get_user_onmouseover_data(\'reply' . $objCommentReply->getId () . 'michaelhampton\', \'' . $objCommentReply->getAuthor () . '\', \'United Kingdom\', \'/blab/profile/uploads/profile/' . $objCommentReply->getUsername () . '.jpg\');" onmouseout="vpb_get_user_mouseout_data(\'reply' . $objCommentReply->getId () . 'michaelhampton\', \'' . $objCommentReply->getAuthor () . '\', \'United Kingdom\', \'/blab/public/uploads/profile/' . $objCommentReply->getUsername () . '.jpg\');" onclick="window.location.href=\'/blab/index/profile/' . $objCommentReply->getUsername () . '\';">' . $objCommentReply->getAuthor () . '</span>
               <div style="clear:both;"></div>
               
               <!-- Load User Details Starts -->
				    <div class="dropdown open v_load_user_detail" onmouseover="vpb_get_user_onmouseover_datas();" onmouseout="vpb_get_user_mouseout_datas();" id="vpb_load_user_reply' . $objCommentReply->getId () . 'michaelhampton" style="text-align: left !important; margin: 0px !important; padding: 0px !important; display: none;"> 
    
    <ul class="dropdown-menu bullet" style="border-radius:0px !important; display:block; margin:15px; z-index:9999;text-align:left !important;margin-top:10px;">
    
    <div class="dropdown-header" style="padding:10px !important;text-align:left !important; border:0px solid !important;margin:0px !important;">
    <div class="input-group vpb-wall-load-user-detail-wrap">
    <span class="input-group-addon vpb-wall-load-user-detail-photo" style="cursor:pointer;" onclick="window.location.href=\'/blab/index/profile/' . $objCommentReply->getUsername () . '\';">
    <span id="vpb_load_user_photo_reply' . $objCommentReply->getId () . 'michaelhampton"><img src="/blab/public/uploads/profile/' . $objCommentReply->getUsername () . '.jpg" border="0"></span>
    </span>
    <div class="vpb-wall-load-user-detail-others">
    <span class="vpb-wall-load-user-detail-fullname" onclick="window.location.href=\'/blab/index/profile/' . $objCommentReply->getUsername () . '\';"><span id="vpb_load_user_fullname_reply' . $objCommentReply->getId () . 'michaelhampton">' . $objCommentReply->getAuthor () . '</span></span><br>
   <span style="font-weight:normal !important;" id="vpb_load_user_country_reply' . $objCommentReply->getId () . '' . $objCommentReply->getUsername () . '"><i class="fa fa-map-marker" title="Location"></i>&nbsp;United Kingdom</span>
   
   <input type="hidden" id="vpb_friendship_uid_reply' . $objCommentReply->getId () . '' . $objCommentReply->getUsername () . '" value="' . $objCommentReply->getUsername () . '">
   <input type="hidden" id="vpb_friendship_fid_reply' . $objCommentReply->getId () . '' . $objCommentReply->getUsername () . '" value="' . $objCommentReply->getUsername () . '">
    </div>
    </div>
    </div>
    <div style="clear:both;"></div>
    <div class="modal-footer" style="padding:10px !important; background-color:#F6F6F6; margin:0px;">
    <span id="vpb_load_friendship_reply' . $objCommentReply->getId () . '' . $objCommentReply->getUsername () . '"></span>
    
    <span style="margin-left:16px !important;" class="cbt_friendship" onclick="window.location.href=\'/blab/index/profile/' . $objCommentReply->getUsername () . '\';"><i class="fa fa-user"></i> Profile</span>
    </div>
    </ul>
    </div>
                    <!-- Load User Details Ends -->
               <div class="vpb_wall_comments_description" style="font-size:12px !important;">
				
                <div class="vpb_default_status_wrapper" id="vpb_default_reply_wrapper_' . $objCommentReply->getId () . '">
                <span id="vreplies_' . $objCommentReply->getId () . '">' . $objCommentReply->getReply () . '</span><span id="vreplies_large_' . $objCommentReply->getId () . '" style="display:none !important;"></span>                
                                <br clear="all">                <div class="vpb_photos_wrapper_medium vasplus-tooltip-attached" id="reply_' . $objCommentReply->getId () . '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to enlarge">
					<a style="display:none;" class="v_photo_holders" onclick="vpb_popup_photo_box(\'' . $objCommentReply->getId () . '\', \'1\', \'1\', \'/blab/public/uploads/profile/' . $objCommentReply->getUsername () . '.jpg\');">
					  <img src="/blab/public/uploads/profile/' . $objCommentReply->getUsername () . '.jpg">
					</a>
				</div>
                                
                                </div>
				
				<div style="max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important; display:none !important;" id="vpb_editable_reply_wrapper_' . $objCommentReply->getId () . '" class="vpb_editable_status_wrapper">
				<div class="input-group">
				<textarea id="vpb_wall_reply_editable_data_' . $objCommentReply->getId () . '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a reply...">' . $objCommentReply->getReply () . '</textarea>
				<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">
                <div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' . $objCommentReply->getId () . '\', \'reply\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">
                <div class="btn btn-success btn-wall-post" onclick="vpb_save_reply_update(\'' . $objCommentReply->getId () . '\');">Save</div></span>
				</div>
                <div id="save_reply_changes_loading_' . $objCommentReply->getId () . '"></div>
			   </div>
            </div>
               
                <div style="clear:both;"></div>
				 
				</span>
                <span class="vpb_wrap_post_contents_b">
                                    <div class="dropdown">
                     <i id="menu1' . $objCommentReply->getId () . '" data-toggle="dropdown" data-placement="top" class="fa fa-pencil vpb_wrap_replies_icons vasplus-tooltip-attached" onclick="vpb_hide_popup();" data-toggle="tooltip" data-placement="top" title="" data-original-title="Options"></i>
                      <ul class="dropdown-menu bullet pull-right" role="menu" aria-labelledby="menu1' . $objCommentReply->getId () . '" style="right: -15px; left: auto; top:18px;border-radius:0px !important;">
                                              	<li><a onclick="vpb_show_editable_item(\'' . $objCommentReply->getId () . '\', \'reply\');">Edit Reply</a></li>
                        	                            <li><a onclick="vpb_delete_this_wall_item(\'' . $objCommentReply->getId () . '\', \'reply\');">Delete</a></li>
                                                  </ul>
                    </div>
                                </span>
				<div style="clear:both;"></div>
				
				</div>
				</div><div style="clear:both;"></div>
				
				
				<div class="vpb_wrap_post_contents_e" style=" margin-left:36px; margin-top:-8px;">
                
                <!-- Like button Starts -->
               <span class="vpb_comment_liked" style="display:inline-block;" id="rlike_' . $objCommentReply->getId () . '" onclick="vpb_like_reply_box(\'' . $objCommentReply->getUsername () . '\', \'' . $objCommentReply->getId () . '\', \'like\');" title="Like this comment"><span class="like_text">Like</span></span>
               
               <span class="vpb_comment_liked" style="display:none;" id="runlike_' . $objCommentReply->getId () . '" onclick="vpb_like_reply_box(\'' . $objCommentReply->getUsername () . '\', \'' . $objCommentReply->getId () . '\', \'unlike\');" title="Unlike this comment"><span class="like_text">Unlike</span></span> · 
               
               <!-- Like button Ends -->
               					<span class="vpb_comment_update_bottom_links" title="Leave a reply" onclick="vpb_show_reply_box(\'' . $objCommentReply->getCommentId () . '\');">Reply</span> ·
                                
                <span class="vpb_comment_update_bottom_links vasplus-tooltip-attached" style="display:none;color: #3b5998; text-decoration:none;" id="vpb_rlike_wrapper_' . $objCommentReply->getId () . '" onclick="vpb_load_reply_likes(\'' . $objCommentReply->getId () . '\', \'' . $objCommentReply->getUsername () . '\', \'People Who Like This\');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to see likes">
                
                <i class="fa fa-thumbs-o-up"></i> <span id="vpb_total_rlikes_' . $objCommentReply->getId () . '">0</span>
                 · </span> 
                
                <span class="vpb_comment_update_bottom_links" style="cursor:default;"><span class="vpb_date_time_posted" date="' . $objCommentReply->getDateCreated () . '">' . $objCommentReply->getDateCreated () . '</span></span>
                
                 <span style="display:none;" id="rdotted_id_' . $objCommentReply->getId () . '"> · </span><span id="redited_id_' . $objCommentReply->getId () . '" class="vpb_hover vasplus-tooltip-attached" style="display:none;font-size:12px;" onclick="vpb_load_edited_history(\'' . $objCommentReply->getId () . '\', \'' . $objCommentReply->getUsername () . '\', \'Edit History\', \'reply\');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Show edit history">Edited</span>                
				</div>
                
				<div style="clear:both;"></div>
				</div>
				</div>
				</div>
				</div>';
    }

    /*     * ************************* Replies ************************** */

    public function loadMoreRepliesAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['comment_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objComment = new Comment ($_POST['comment_id']);

            $arrResults = (new CommentReplyFactory())->getRepliesForComment ($objComment, new UploadFactory (), new PostActionFactory (), 0, null, new User($_SESSION['user']['user_id']));
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage ());
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrResults === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrReplies = $arrResults;
        $this->view->comment = $objComment;
    }

    /*     * ************************* Replies ************************** */

    public function likeReplyAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['reply_id']) || empty ($_POST['action']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $objLike = new PostAction();

        $objCommentReply = new CommentReply ($_POST['reply_id']);

        if ( $_POST['action'] === "like" )
        {
            $blResponse = $objLike->likeReply ($objCommentReply, $objUser);
        }
        else
        {
            $blResponse = $objLike->unlikeReply ($objCommentReply, $objUser);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        echo $objLike->getLikesForReply ($objCommentReply);
    }

    public function deleteReplyAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) || empty ($_POST['item_id']) || empty ($_POST['type']) || $_POST['type'] != "reply" )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objReply = new CommentReply ($_POST['item_id']);
        $blResponse = $objReply->delete ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function editReplyAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) || empty ($_POST['reply_id']) || empty ($_POST['reply']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objReply = new CommentReply ($_POST['reply_id']);

        $objReply->setReply ($_POST['reply']);
        $blResult = $objReply->save (new AuditFactory (), new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        echo 'process_completed';
    }

    public function getReplyLikesAction ()
    {
        $this->view->disable ();

        $objPostAction = new PostActionFactory();

        if ( !isset ($_POST['reply_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $id = $_POST['reply_id'];


        $objComment = new CommentReply ($id);

        $arrLikes = $objPostAction->getLikeListForReply ($objComment);


        if ( $arrLikes === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrLikes = $arrLikes;
        $this->view->partial ("templates/likeList");
    }

}
