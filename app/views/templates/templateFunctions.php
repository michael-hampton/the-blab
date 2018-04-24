<?php

function parseSmiley ($text)
{
// Smiley to image
    $smileys = array(
        ':)' => 'smile.png',
        ':(' => 'frown.png',
        ':-(' => 'bad.png'
    );

// Now you need find and replace
    foreach ($smileys as $smiley => $img) {
        $text = str_replace (
                $smiley, "<img src='/blab/public/img/{$img}' />", $text
        );
    }

// Now only return it
    return $text;
}

function blab_time_ago ($timestamp)
{
    $time_ago = strtotime ($timestamp);
    $current_time = time ();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round ($seconds / 60);           // value 60 is seconds  
    $hours = round ($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
    $days = round ($seconds / 86400);          //86400 = 24 * 60 * 60;  
    $weeks = round ($seconds / 604800);          // 7*24*60*60;  
    $months = round ($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
    $years = round ($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
    if ( $seconds <= 60 )
    {
        return "Just Now";
    }
    else if ( $minutes <= 60 )
    {
        if ( $minutes == 1 )
        {
            return "one minute ago";
        }
        else
        {
            return "$minutes minutes ago";
        }
    }
    else if ( $hours <= 24 )
    {
        if ( $hours == 1 )
        {
            return "an hour ago";
        }
        else
        {
            return "$hours hrs ago";
        }
    }
    else if ( $days <= 7 )
    {
        if ( $days == 1 )
        {
            return "yesterday";
        }
        else
        {
            return "$days days ago";
        }
    }
    else if ( $weeks <= 4.3 ) //4.3 == 52/12  
    {
        if ( $weeks == 1 )
        {
            return "a week ago";
        }
        else
        {
            return "$weeks weeks ago";
        }
    }
    else if ( $months <= 12 )
    {
        if ( $months == 1 )
        {
            return "a month ago";
        }
        else
        {
            return "$months months ago";
        }
    }
    else
    {
        if ( $years == 1 )
        {
            return "one year ago";
        }
        else
        {
            return "$years years ago";
        }
    }
}

/**
 * 
 * @param type $arrComment
 */
function buildTags ($arrComment)
{
    $arrTags = $arrComment->getArrTags ();

    if ( !empty ($arrTags) )
    {
        echo '<span style="color:#9197a3 !important;">
	<span class="vpb_wall_tagged_friends">
        <span id="the_tagged_box_' . $arrComment->getId () . '">';

        foreach ($arrTags as $key => $arrTag) {


            if ( $key === 0 )
            {
                echo 'with ';
            }
            else
            {
                echo ' and ';
            }

            echo '<span userid="' . $arrTag->getId () . '" class="tag vpb_hover" onclick="vpb_load_tagged_friends(\'' . $arrComment->getId () . '\', \'' . $arrTag->getUsername () . '\', \'' . $arrTag->getUsername () . '\', \'People\');"';
            echo 'onmouseover="vpb_get_user_onmouseover_data(\'taggedfriend' . $arrComment->getId () . $arrTag->getUsername () . '\', \'' . $arrTag->getFirstname () . ' ' . $arrTag->getLastname () . '\', \'Malaysia\', \'/blab/public/uploads/' . $arrTag->getUsername () . '.jpg\');" ';

            echo 'onmouseout="vpb_get_user_mouseout_data(\'taggedfriend4787victor\', \'' . $arrTag->getFirstname () . ' ' . $arrTag->getLastname () . '\', \'Malaysia\', \'/blab/public/uploads/' . $arrTag->getUsername () . '.jpg\');">';

            echo $arrTag->getFirstname () . ' ' . $arrTag->getLastname () . '</span>';

            if ( $_SESSION['user']['user_id'] === $arrTag->getId () || $_SESSION['user']['user_id'] == 1 )
            {
                echo '<span class="tag" userid="' . $arrTag->getId () . '" style="font-size:26px;" onclick="vpb_remove_me_from_tagged_post(\'' . $arrComment->getId () . '\', \'' . $arrTag->getId () . '\', \'username\') ">x</span>';
            }
        }

        echo ' </span>
            </span> 
        	</span><br>';
    }
}

function buildReactions ($arrComment)
{
    $usersLikes = [];
    $likeCount = 0;
    $likeClass = 'like';
    $likes = '';

    $wowCount = $arrComment->getWowCount ();
    $sadCount = $arrComment->getSadCount ();
    $angryCount = $arrComment->getAngryCount ();
    $loveCount = $arrComment->getLoveCount ();
    $hahaCount = $arrComment->getHahaCount ();

    $arrLikes = $arrComment->getArrLikes ();

    if ( !empty ($arrLikes) )
    {
        $usersLikes = array_reduce ($arrLikes, function ($reduced, $current) {

            $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

            $reduced[$current->getUsername ()] = $fullName;
            return $reduced;
        });

        $likes = implode (',', array_slice ($usersLikes, 0, 2));
        $likeCount = count ($usersLikes) - 2;
        $likeClass = !empty ($usersLikes) && array_key_exists ($_SESSION['user']['username'], $usersLikes) ? 'unlike' : $likeClass;
    }


    $likeStr = "<br>";

    if ( $wowCount > 0 )
    {
        $likeStr .= '<span id="' . $arrComment->getId () . '" class="showLikes vpb_d_like_status" type="reaction" reactiontype="wow">							
            <i class="wowIcon_c"></i> <span class="vpb_WowStatus like_text">Wow(' . $wowCount . ')</span>						
            </span>';
    }

    if ( $angryCount > 0 )
    {
        $likeStr .= '<span id="' . $arrComment->getId () . '" class="showLikes vpb_d_like_status" type="reaction" reactiontype="angry">							
            <i class="angryIcon_c"></i> <span class="vpb_AngryStatus like_text">Angry(' . $angryCount . ')</span>						
            </span>';
    }

    if ( $sadCount > 0 )
    {
        $likeStr .= '<span id="' . $arrComment->getId () . '" class="showLikes vpb_d_like_status" type="reaction" reactiontype="sad">							
            <i class="sadIcon_c"></i> <span class="vpb_SadStatus like_text">Sad(' . $sadCount . ')</span>						
            </span>';
    }

    if ( $hahaCount > 0 )
    {
        $likeStr .= '<span id="' . $arrComment->getId () . '" class="showLikes vpb_d_like_status" type="reaction" reactiontype="haha">							
            <i class="hahaIcon_c"></i> <span class="vpb_HahaStatus like_text">Haha(' . $hahaCount . ')</span>						
            </span>';
    }

    if ( $loveCount > 0 )
    {
        $likeStr .= '<span id="' . $arrComment->getId () . '" class="showLikes vpb_d_like_status" type="reaction" reactiontype="love">							
            <i class="loveIcon_c"></i> <span class="vpb_LoveStatus like_text">Love(' . $loveCount . ')</span>						
            </span>';
    }

    echo '<div class="input-group vpb-like-post-wrap" style="display:inline-block;">';

    echo $likeStr;

    $likeStr = '';

    if ( trim ($likes) !== "" )
    {
        $likeStr .= $likes;
    }

    if ( $likeCount > 0 )
    {
        $likeStr .= 'and ' . $likeCount . ' others';
    }


    echo '<a href="#" id="' . $arrComment->getId () . '" class="showLikes" type="post">' . ((int) $arrComment->getLikes () > 0 ? '(' . $arrComment->getLikes () . ')' : '');


    if ( trim ($likeStr) !== "" )
    {

        $likeStr .= ' Liked this';

        echo $likeStr;
    }
    echo '</a>';
    echo '</div>';
}

function generateVideoEmbedUrl ($url)
{
    //This is a general function for generating an embed link of an FB/Vimeo/Youtube Video.
    $finalUrl = '';
    if ( strpos ($url, 'blab.com/') !== false )
    {
        //it is FB video
        $finalUrl.='https://www.blab.com/plugins/video.php?href=' . rawurlencode ($url) . '&show_text=1&width=200';
    }
    else if ( strpos ($url, 'vimeo.com/') !== false )
    {
        //it is Vimeo video
        $videoId = explode ("vimeo.com/", $url)[1];
        if ( strpos ($videoId, '&') !== false )
        {
            $videoId = explode ("&", $videoId)[0];
        }
        $finalUrl.='https://player.vimeo.com/video/' . $videoId;
    }
    else if ( strpos ($url, 'youtube.com/') !== false )
    {
        //it is Youtube video
        $videoId = explode ("v=", $url)[1];
        if ( strpos ($videoId, '&') !== false )
        {
            $videoId = explode ("&", $videoId)[0];
        }
        $finalUrl.='https://www.youtube.com/embed/' . $videoId;
    }
    else if ( strpos ($url, 'youtu.be/') !== false )
    {
        //it is Youtube video
        $videoId = explode ("youtu.be/", $url)[1];
        if ( strpos ($videoId, '&') !== false )
        {
            $videoId = explode ("&", $videoId)[0];
        }
        $finalUrl.='https://www.youtube.com/embed/' . $videoId;
    }
    else
    {
        return $url;
    }

    if ( $finalUrl != "" )
    {
        echo '<div class="input-group vpb_video_link_a"><div class="embed-responsive" style="overflow:inherit !important;"><iframe id="ytplayer" class="embed-responsive-item" type="text/html" width="50" height="50"
src="' . $finalUrl . '"frameborder="0" allowfullscreen></iframe></div></div>';
    }
}

/**
 * 
 * @param type $arrComment
 */
function buildSharedComment ($arrComment)
{
    if ( trim ($arrComment->getSharersComment ()) !== "" )
    {
        echo '<div class="media-body" style="margin-top:12px;">' . parseSmiley ($arrComment->getSharersComment ()) . '</div></div></div>
                <div style="margin-left:20px; border-top: 1px dotted #CCC;" class="social-avatar">
                                    <a href="" class="pull-left">
                                        <img alt="image" src="/blab/public/uploads/profile/' . $arrComment->getClonedFrom () . '.jpg">
                                    </a>
                                    <div class="media-body">
                                    
                                    <a onmouseover="vpb_get_user_onmouseover_data(\'' . $arrComment->getClonedFrom () . '\', \'' . $arrComment->getClonedFromAuthor () . '\', \'United Kingdom\', \'/blab/uploads/profile/' . $arrComment->getClonedFrom () . '.jpg\');" href="/blab/index/profile/' . $arrComment->getUsername () . '">
                                            ' . $arrComment->getClonedFromAuthor () . '
                                        </a>
                                        
                                            <small date="' . $arrComment->getCreated () . '" class="text-muted timeAgo">' . blab_time_ago ($arrComment->getCreated ()) . '</small>

                                    <div class="dropdown open v_load_user_detail" onmouseover="vpb_get_user_onmouseover_datas();" onmouseout="vpb_get_user_mouseout_datas();" id="vpb_load_user_' . $arrComment->getClonedFrom () . '" style="text-align: left !important; margin: 0px !important; padding: 0px !important; display: none;"> 
                                    <ul class="dropdown-menu bullet" style="border-radius:0px !important; display:block; margin:15px; z-index:9999;text-align:left !important;margin-top:10px;">
    
                <div class="dropdown-header" style="padding:10px !important;text-align:left !important; border:0px solid !important;margin:0px !important;">
                    <div class="input-group vpb-wall-load-user-detail-wrap">
                        <span class="input-group-addon vpb-wall-load-user-detail-photo" style="cursor:pointer;">
                            <span id="vpb_load_user_photo_page_ownermichael.hampton"></span>
                        </span>
    
                        <div class="vpb-wall-load-user-detail-others">
                            <span class="vpb-wall-load-user-detail-fullname"><span id="vpb_load_user_fullname_page_ownermichael.hampton"></span></span><br>
                            <span style="font-weight:normal !important;" id="vpb_load_user_country_page_ownermichael.hampton"></span>
   
                            <input type="hidden" id="vpb_friendship_uid_page_ownermichael.hampton" value="michael.hampton">
                            <input type="hidden" id="vpb_friendship_fid_page_ownermichael.hampton" value="michael.hampton">
                        </div>
                    </div>
                </div>
            </ul></div>';

//echo '<br><span style="color:#FF0000;">' . $arrComment->getSharersComment () . '</span><br>';
    }
}

/**
 * 
 * @param type $comment
 */
function buildReplies ($comment)
{
    $arrReplies = $comment->getArrReplies ();

    if ( !empty ($arrReplies) )
    {




        echo '<!-- Loading Comments Gif -->
					<div class="input-group vpb-the-post-wrap" style="display:none;padding:7px 10px !important;border:0px solid !important;border-top:1px solid #e5e5e5 !important;border-bottom:1px solid #e5e5e5 !important;background-color: #F9F9F9 !important; margin-bottom:8px;" id="vpb_loading_replies_outer_' . $comment->getId () . '">
					<div id="vpb_loading_replies_' . $comment->getId () . '"></div>
					</div>';


        echo '<div id="vpb_loaded_replies_' . $comment->getId () . '"></div>';


        foreach ($arrReplies as $key => $arrReply) {
//
//
//
            if ( $key == 0 )
            {
                echo '<div style="max-width:100% !important;max-height:100% !important;width:100% !important;margin:0px !important;background-color: #f6f7f8 !important;">
                    
                    <div class="ml55" style="border-left:4px solid #E1E1FF; clear:both; margin-top: 22px; float: left; width: 92%;">
                    
					<div class="input-group vpb-the-post-wrap" style="border:0px solid !important;border-top:1px solid #e5e5e5 !important;border-bottom:1px solid #e5e5e5 !important;background-color: #F9F9F9 !important; margin-bottom:8px;" id="vpb_load_more_replies_box_' . $comment->getId () . '">';

                echo '<div style="float:left;"><span class="vpb_hover" style="color:#3b5998;" onclick="vpb_load_more_replies(\'' . $comment->getId () . '\');">View previous replies</span></div>';


                echo '
					<div style="clear:both;"></div>
					</div></div></div>';
            }



            echo '<div class="v_wall_wrapper v_hide_show_reply" style="margin:0px !important; float:left;" id="vpb_reply_id_' . $arrReply->getId () . '">
                
                <div style="margin:0px !important;display: inline-block; max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important;display:none !important;" id="vpb_hidden_reply_id_' . $arrReply->getId () . '">
				<div class="vasplus_a">
				<div class="vasplus_b" style="background-color: #f6f7f8 !important;">
				<div class="vpb_wall_adjust_c">
				<div class="input-group vpb_wall_b_contents" style="display: inline-block; padding-left:45px !important;">
                This reply has been hidden&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_unhide_this_wall_item(\'' . $arrReply->getId () . '\', \'michaelhampton\', \'reply\');">Unhide</span>&nbsp;&nbsp;·&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_show_hidden_item_intro();">Learn more...</span>
                
                </div>
                </div>
                </div>
                </div>
                </div>';

            echo '<style> .vpb_wrap_replies_icons { display:none; font-size:11px !important; font-weight:normal !important; vertical-align: top !important;text-align:right !important; opacity:0.4; cursor:pointer; } .vpb_wrap_replies_icons:hover { opacity:1; } #vpb_unhidden_reply_id_' . $arrReply->getId () . ':hover .vpb_wrap_replies_icons { display:inline-block !important;} </style>
                
                
				<div style="max-width:100% !important;max-height:100% !important;width:100% !important;margin:0px !important;display:inline-block !important;" id="vpb_unhidden_reply_id_' . $arrReply->getId () . '">';

            echo '<div class="vasplus_a" style="border-top:0px solid !important;border-bottom:0px solid #E6E6E6 !important;background-color: #f6f7f8 !important;">
                
                <div class="ml55" style="border-left:4px solid #E1E1FF; clear:both;">
				
				<div class="vasplus_b" style="background-color: #f6f7f8 !important;">
				<div class="vpb_wall_adjust_c" style="padding-top: 2px !important;">';




            echo '<div class="input-group vpb_wall_b_contents">
				
				<span class="input-group-addon vpb-wall-user-photo" style="cursor:pointer;" onclick="window.location.href=\'/blab/uploads/profile/' . $arrReply->getUsername () . '.jpg\';">
				<span class="v_status_pictured_michaelhampton"><img src="/blab/uploads/profile/' . $arrReply->getUsername () . '.jpg" width="24" height="24" border="0" onmouseover="vpb_get_user_onmouseover_data(\'reply' . $arrReply->getId () . 'michaelhampton\', \'Michael hampton\', \'United Kingdom\', \'/blab/uploads/profile/' . $arrReply->getUsername () . '\');" onmouseout="vpb_get_user_mouseout_data(\'reply' . $arrReply->getId () . 'michaelhampton\', \'Michael hampton\', \'United Kingdom\', \'/blab/uploads/profile/' . $arrReply->getUsername () . '.jpg\');"></span>
				</span>
				<div class="vpb_wrap_post_contents">';


            echo '<span class="vpb_wrap_post_contents_rr" style="width:100%;">
				<span class="vpb_wall_reply_fullname" onmouseover="vpb_get_user_onmouseover_data(\'reply' . $arrReply->getId () . 'michaelhampton\', \'' . $arrReply->getAuthor () . '\', \'United Kingdom\', \'/blab/uploads/profile/' . $arrReply->getUsername () . '.jpg\');" onmouseout="vpb_get_user_mouseout_data(\'reply' . $arrReply->getId () . 'michaelhampton\', \'' . $arrReply->getAuthor () . '\', \'United Kingdom\', \'/blab/uploads/profile/' . $arrReply->getUsername () . '.jpg\');" onclick="window.location.href=\'http://www.vasplus.info/wall/michaelhampton\';">' . $arrReply->getAuthor () . '</span>
               <div style="clear:both;"></div>
               
               <!-- Load User Details Starts -->
				    <div class="dropdown open v_load_user_detail" onmouseover="vpb_get_user_onmouseover_datas();" onmouseout="vpb_get_user_mouseout_datas();" id="vpb_load_user_reply' . $arrReply->getId () . 'michaelhampton" style="text-align: left !important; margin: 0px !important; padding: 0px !important; display: none;"> 
    
    <ul class="dropdown-menu bullet" style="border-radius:0px !important; display:block; margin:15px; z-index:9999;text-align:left !important;margin-top:10px;">
    
    <div class="dropdown-header" style="padding:10px !important;text-align:left !important; border:0px solid !important;margin:0px !important;">
    <div class="input-group vpb-wall-load-user-detail-wrap">
    <span class="input-group-addon vpb-wall-load-user-detail-photo" style="cursor:pointer;" onclick="window.location.href=\'/blab/index/profile/' . $arrReply->getUsername () . '\';">
    <span id="vpb_load_user_photo_reply' . $arrReply->getId () . 'michaelhampton"><img src="/blab/public/uploads/profile/' . $arrReply->getUsername () . '.jpg" border="0"></span>
    </span>
    <div class="vpb-wall-load-user-detail-others">
    <span class="vpb-wall-load-user-detail-fullname" onclick="window.location.href=\'/blab/index/profile/' . $arrReply->getUsername () . '\';"><span id="vpb_load_user_fullname_reply' . $arrReply->getId () . 'michaelhampton">' . $arrReply->getAuthor () . '</span></span><br>
   <span style="font-weight:normal !important;" id="vpb_load_user_country_reply' . $arrReply->getId () . '' . $arrReply->getUsername () . '"><i class="fa fa-map-marker" title="Location"></i>&nbsp;United Kingdom</span>
   
   <input type="hidden" id="vpb_friendship_uid_reply' . $arrReply->getId () . '' . $arrReply->getUsername () . '" value="' . $arrReply->getUsername () . '">
   <input type="hidden" id="vpb_friendship_fid_reply' . $arrReply->getId () . '' . $arrReply->getUsername () . '" value="' . $arrReply->getUsername () . '">
    </div>
    </div>
    </div>
    <div style="clear:both;"></div>
    <div class="" style="padding:10px !important; background-color:#F6F6F6; margin:0px;">
    <span id="vpb_load_friendship_reply' . $arrReply->getId () . '' . $arrReply->getUsername () . '"></span>
    
    <span style="margin-left:16px !important;" class="cbt_friendship" onclick="window.location.href=\'/blab/index/profile/' . $arrReply->getUsername () . '\';"><i class="fa fa-user"></i> Profile</span>
    </div>
    </ul>
    </div>';

            //                    <!-- Load User Details Ends -->

            echo '<div class="vpb_wall_comments_description" style="font-size:12px !important;">
				
                <div class="vpb_default_status_wrapper" id="vpb_default_reply_wrapper_' . $arrReply->getId () . '">
                <span id="vreplies_' . $arrReply->getId () . '">' . $arrReply->getReply () . '</span><span id="vreplies_large_' . $arrReply->getId () . '" style="display:none !important;"></span>                
                                <br clear="all">                
                                <div class="vpb_photos_wrapper_medium vasplus-tooltip-attached" id="reply_' . $arrReply->getId () . '" original-title="Click to enlarge">
					<a style="display:none;" class="v_photo_holders" onclick="vpb_popup_photo_box(\'' . $arrReply->getId () . '\', \'1\', \'1\', \'/blab/public/uploads/profile/' . $arrReply->getUsername () . '.jpg\');">
					  <img src="/blab/public/uploads/profile/' . $arrReply->getUsername () . '.jpg">
					</a>
				</div>
                                
                                </div>
<div style="max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important; display:none !important;" id="vpb_editable_reply_wrapper_' . $arrReply->getId () . '" class="vpb_editable_status_wrapper">
				
<div class="input-group">
				<textarea id="vpb_wall_reply_editable_data_' . $arrReply->getId () . '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a reply...">test 12</textarea>
				<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">
                <div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' . $arrReply->getId () . '\', \'reply\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">
                <div class="btn btn-success btn-wall-post" onclick="vpb_save_reply_update(\'' . $arrReply->getId () . '\');">Save</div></span>
				</div> <div id="save_reply_changes_loading_' . $arrReply->getId () . '"></div>
</div>                                

';

            echo '</div>';

            echo '<span class="vpb_wrap_post_contents_b">
                                    <div class="dropdown">
                     <i id="menu1' . $arrReply->getId () . '" data-toggle="dropdown" data-placement="top" class="fa fa-pencil vpb_wrap_replies_icons vasplus-tooltip-attached" onclick="vpb_hide_popup();" original-title="Options"></i>
                      <ul class="dropdown-menu bullet pull-right" role="menu" aria-labelledby="menu1' . $arrReply->getId () . '" style="right: -15px; left: auto; top:18px;border-radius:0px !important;">
                                              	<li><a onclick="vpb_show_editable_item(\'' . $arrReply->getId () . '\', \'reply\');">Edit Reply</a></li>
                        	                            <li><a onclick="vpb_delete_this_wall_item(\'' . $arrReply->getId () . '\', \'reply\');">Delete</a></li>
                                                  </ul>
                    </div></span>';

            echo '<div style="clear:both;"></div>';

            echo '<div class="vpb_wrap_post_contents_e" style=" margin-left:36px; margin-top:-8px;">';

            $userCommentLikes = [];
//$likeCommentCount = 0;
//$commentLikes = '';
            $likeCommentReplyClass = 'inline-block';

            $arrCommentLikes = $arrReply->getArrLikes ();

            if ( !empty ($arrCommentLikes) )
            {

                $userCommentLikes = array_reduce ($arrCommentLikes, function ($reduced, $current) {

                    $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

                    $reduced[$current->getUsername ()] = $fullName;
                    return $reduced;
                });

                $likeCommentReplyClass = !empty ($userCommentLikes) && array_key_exists ($_SESSION['user']['username'], $userCommentLikes) ? 'none' : $likeCommentReplyClass;
//$commentLikes = implode (',', array_slice ($userCommentLikes, 0, 2));
//$likeCommentCount = count ($usersLikes) - 2;
            }

            $unlikeDisplay = $likeCommentReplyClass == 'none' ? 'block' : 'none';

            $likeCommentReplyText = trim ($likeCommentReplyClass) === "unlike" ? 'Unlike' : "Like";

            echo '<span class="vpb_comment_liked pull-left" style="display:' . $likeCommentReplyClass . ';" id="rlike_' . $arrReply->getId () . '" onclick="vpb_like_reply_box(\'' . $arrReply->getUsername () . '\', \'' . $arrReply->getId () . '\', \'like\');" title="Like this comment"><span class="like_text">Like</span></span>';

            echo '<span class="vpb_comment_liked pull-left" style="display:' . $unlikeDisplay . ';" id="runlike_' . $arrReply->getId () . '" onclick="vpb_like_reply_box(\'' . $arrReply->getUsername () . '\', \'' . $arrReply->getId () . '\', \'unlike\');" title="Unlike this comment"><span class="like_text">Unlike</span></span>';

            if ( count ($arrCommentLikes) > 0 )
            {
                echo '<span class="vpb_comment_update_bottom_links vasplus-tooltip-attached" style="color: rgb(59, 89, 152); text-decoration: none;" id="vpb_rlike_wrapper_421" onclick="vpb_load_reply_likes(\'' . $arrReply->getId () . '\', \'michaelhampton\', \'People Who Like This\');" original-title="Click to see likes">
                
                <i class="fa fa-thumbs-o-up"></i> <span id="vpb_total_rlikes_' . $arrReply->getId () . '">' . count ($arrCommentLikes) . '</span>
                 · </span>';
            }

            echo '<span class="vpb_comment_update_bottom_links pull-left" title="Leave a reply" onclick="vpb_show_reply_box(\'' . $arrReply->getCommentId () . '\');">Reply</span> ·
                                
                <span class="vpb_comment_update_bottom_links vasplus-tooltip-attached" style="display:none;color: #3b5998; text-decoration:none;" id="vpb_rlike_wrapper_' . $arrReply->getId () . '" onclick="vpb_load_reply_likes(\'' . $arrReply->getId () . '\', \'' . $arrReply->getUsername () . '\', \'People Who Like This\');" original-title="Click to see likes">
                
                <i class="fa fa-thumbs-o-up"></i> <span id="vpb_total_rlikes_' . $arrReply->getId () . '">0</span>
                 · </span> 
                
				<div style="clear:both;"></div>';


            echo '</div>';


            echo '</span>';

            // herre 2
            echo '</div>';

            echo '</div>';


            //here
            echo '</div>';

            echo '</div>';

            echo '</div>';

            echo '</div>';

            echo '</div>';



            echo '</div>';

            echo '<div style="max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important; display:none !important;" id="vpb_editable_reply_wrapper_' . $arrReply->getId () . '" class="vpb_editable_status_wrapper">
				<div class="input-group">
				<textarea id="vpb_wall_reply_editable_data_' . $arrReply->getId () . '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a reply...">' . generateVideoEmbedUrl ($arrReply->getReply ()) . '</textarea>
				<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">
                <div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' . $arrReply->getId () . '\', \'reply\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">
                <div class="btn btn-success btn-wall-post" onclick="vpb_save_reply_update(\'' . $arrReply->getId () . '\');">Save</div></span>
				</div>
                <div id="save_reply_changes_loading_' . $arrReply->getId () . '"></div>
			   </div>';





//				
//				
//				<div class="vpb_wrap_post_contents_e" style=" margin-left:36px; margin-top:-8px;">';
//
//            $userCommentLikes = [];
////$likeCommentCount = 0;
////$commentLikes = '';
//            $likeCommentReplyClass = 'inline-block';
//
//            $arrCommentLikes = $arrReply->getArrLikes ();
//
//            if ( !empty ($arrCommentLikes) )
//            {
//
//                $userCommentLikes = array_reduce ($arrCommentLikes, function ($reduced, $current) {
//
//                    $fullName = $current->getFirstName () . ' ' . $current->getLastName ();
//
//                    $reduced[$current->getUsername ()] = $fullName;
//                    return $reduced;
//                });
//
//                $likeCommentReplyClass = !empty ($userCommentLikes) && array_key_exists ($_SESSION['user']['username'], $userCommentLikes) ? 'none' : $likeCommentReplyClass;
////$commentLikes = implode (',', array_slice ($userCommentLikes, 0, 2));
////$likeCommentCount = count ($usersLikes) - 2;
//            }
//
//            $unlikeDisplay = $likeCommentReplyClass == 'none' ? 'block' : 'none';
//
//            $likeCommentReplyText = trim ($likeCommentReplyClass) === "unlike" ? 'Unlike' : "Like";
//
//            echo '<span class="vpb_comment_liked pull-left" style="display:' . $likeCommentReplyClass . ';" id="rlike_' . $arrReply->getId () . '" onclick="vpb_like_reply_box(\'' . $arrReply->getUsername () . '\', \'' . $arrReply->getId () . '\', \'like\');" title="Like this comment"><span class="like_text">Like</span></span>';
//
//            echo '<span class="vpb_comment_liked pull-left" style="display:' . $unlikeDisplay . ';" id="runlike_' . $arrReply->getId () . '" onclick="vpb_like_reply_box(\'' . $arrReply->getUsername () . '\', \'' . $arrReply->getId () . '\', \'unlike\');" title="Unlike this comment"><span class="like_text">Unlike</span></span>';
//
//            if ( count ($arrCommentLikes) > 0 )
//            {
//                echo '<span class="vpb_comment_update_bottom_links vasplus-tooltip-attached" style="color: rgb(59, 89, 152); text-decoration: none;" id="vpb_rlike_wrapper_421" onclick="vpb_load_reply_likes(\'' . $arrReply->getId () . '\', \'michaelhampton\', \'People Who Like This\');" original-title="Click to see likes">
//                
//                <i class="fa fa-thumbs-o-up"></i> <span id="vpb_total_rlikes_' . $arrReply->getId () . '">' . count ($arrCommentLikes) . '</span>
//                 · </span>';
//            }
//
//
//            echo '<span class="vpb_comment_update_bottom_links pull-left" title="Leave a reply" onclick="vpb_show_reply_box(\'' . $arrReply->getCommentId () . '\');">Reply</span> ·
//                                
//                <span class="vpb_comment_update_bottom_links vasplus-tooltip-attached" style="display:none;color: #3b5998; text-decoration:none;" id="vpb_rlike_wrapper_' . $arrReply->getId () . '" onclick="vpb_load_reply_likes(\'' . $arrReply->getId () . '\', \'' . $arrReply->getUsername () . '\', \'People Who Like This\');" original-title="Click to see likes">
//                
//                <i class="fa fa-thumbs-o-up"></i> <span id="vpb_total_rlikes_' . $arrReply->getId () . '">0</span>
//                 · </span> 
//                </div>
//                
//				<div style="clear:both;"></div>
//				</div>
//				</div>
//				</div>
//				</div>';
//
//            echo '<div style="max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important; display:none !important;" id="vpb_editable_reply_wrapper_' . $arrReply->getId () . '" class="vpb_editable_status_wrapper">
//				<div class="input-group">
//				<textarea id="vpb_wall_reply_editable_data_' . $arrReply->getId () . '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a reply...">' . $arrReply->getReply () . '</textarea>
//				<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">
//                <div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' . $arrReply->getId () . '\', \'reply\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">
//                <div class="btn btn-success btn-wall-post" onclick="vpb_save_reply_update(\'' . $arrReply->getId () . '\');">Save</div></span>
//				</div>
//                <div id="save_reply_changes_loading_' . $arrReply->getId () . '"></div>
//			   </div>
//            </div>';
        }
    }
}

function buildComments ($arrComment, $totalCommentsToDisplay)
{
    $comments = $arrComment->getArrComments ();

    $likeCommentClass = 'like';

    if ( count ($comments) > 0 ):


        foreach ($comments as $count => $comment):

            $class = $count >= $totalCommentsToDisplay ? 'd-none' : '';

            $userCommentLikes = [];
            $likeCommentCount = 0;
            $commentLikes = '';
            $likeCommentClass = 'like';

            $arrCommentLikes = $comment->getArrLikes ();

            if ( !empty ($arrCommentLikes) )
            {

                $userCommentLikes = array_reduce ($arrCommentLikes, function ($reduced, $current) {

                    $fullName = $current->getFirstName () . ' ' . $current->getLastName ();

                    $reduced[$current->getUsername ()] = $fullName;
                    return $reduced;
                });

                $likeCommentClass = !empty ($userCommentLikes) && array_key_exists ($_SESSION['user']['username'], $userCommentLikes) ? 'unlike' : $likeCommentClass;
                $commentLikes = implode (',', array_slice ($userCommentLikes, 0, 2));
                $likeCommentCount = count ($userCommentLikes) - 2;
            }

            $likeCommentText = trim ($likeCommentClass) === "unlike" ? 'Unlike' : "Like";

            if ( $count === $totalCommentsToDisplay )
            {
                echo '<a id="' . $arrComment->getId () . '" href="#" class="viewMore">View More Comments</a>';
            }

            echo '<div style="margin:0px !important;display: inline-block;max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important;display:none !important;" id="vpb_hidden_comment_id_' . $comment->getId () . '">
            <div class="vasplus_a">
            <div class="vasplus_b" style="background-color: #f6f7f8 !important;">
            <div class="vpb_wall_adjust_c">
            <div class="input-group vpb_wall_b_contents" style="display: inline-block;">
            This comment has been hidden&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_unhide_this_wall_item(\'' . $comment->getId () . '\', \'michaelhampton\', \'comment\');">Unhide</span>&nbsp;&nbsp;·&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_show_hidden_item_intro();">Learn more...</span>
            </div>
            </div>
            </div>
            </div>
            </div>';

            echo '<div class="social-comment comment-a1 ' . $class . '" commentid="' . $comment->getId () . '">';


            echo '<span class="vpb_wrap_post_contents_b">
                                    
                    <div class="dropdown">
                     <i id="menu' . $comment->getId () . '" data-toggle="dropdown" data-placement="top" class="fa fa-pencil vpb_wrap_coms_icons vasplus-tooltip-attached" onclick="vpb_hide_popup();" original-title="Options"></i>
                      <ul class="dropdown-menu bullet pull-right" role="menu" aria-labelledby="menu' . $comment->getId () . '" style="right: -15px; left: auto; top:18px;border-radius:0px !important;">
                                              	<li><a onclick="vpb_show_editable_item(\'' . $comment->getId () . '\', \'comment\');">Edit Comment</a></li>';

            if ( $comment->getUserId () == $_SESSION['user']['user_id'] )
            {
                echo '<li><a class="pull-right" onclick="vpb_delete_this_wall_item(\'' . $comment->getId () . '\', \'comment\');">Delete Comment</a></li>';
            }
            else
            {
                echo '<li><a class="pull-right ignoreComment" deleteid="' . $comment->getId () . '">Remove Comment From Feed</a></li>';
            }
            echo '</ul>
                    </div>
                                </span>';

            echo '<a href="" class="pull-left">
                                                    <img alt="image" src="/blab/public/uploads/profile/' . $comment->getUsername () . '.jpg">
                                                </a>';

            $formattedUsername = $comment->getId () . trim (str_replace (".", "", $comment->getUsername ()));
            echo '<div class="media-body">
                
                                                <span class="vpb_wall_comment_fullname" onmouseover="vpb_get_user_onmouseover_data(\'comment' . $formattedUsername . '\', \'' . $comment->getAuthor () . '\', \'United Kingdom\', \'/blab/public/uploads/profile/' . $comment->getUsername () . '.jpg\');" onmouseout="vpb_get_user_mouseout_data(\'comment' . $formattedUsername . '\', \'' . $comment->getAuthor () . '\', \'United Kingdom\', \'/blab/public/uploads/profile/' . $comment->getUsername () . '.jpg\');" onclick="window.location.href=\'/blab/index/profile/' . $comment->getUsername () . '\';">' . $comment->getAuthor () . '</span>
                                                    <small date="' . $comment->getCreated () . '" class="text-muted timeAgo">' . blab_time_ago ($comment->getCreated ()) . '</small> <br>';

            if ( trim ($comment->getDateUpdated ()) !== "" && trim ($comment->getDateUpdated ()) !== "0000-00-00 00:00:00" )
            {
                echo "<small date='" . $comment->getDateUpdated () . "'>Updated this comment " . blab_time_ago ($comment->getDateUpdated ()) . "</small><br>";
            }

            $commentText = $comment->getComment ();

            $len = strlen ($commentText);
            $post_Left = '';
            $max_allowed = 400;

            if ( $len <= (int) $max_allowed )
            {
                $post_Left = $commentText;
                $showMore = 'none';
            }
            else if ( $len >= (int) $max_allowed )
            {
                $post_trimed = substr ($commentText, 0, (int) $max_allowed);
                $post_Left = $post_trimed . '...';
                $showMore = 'inline-block';
            }



            echo '<div class="" id="vpb_default_comment_wrapper_' . $comment->getId () . '">';
            echo '<div id="comment_box_a_' . $comment->getId () . '">';
            echo '<span style="font-size:14px;" id="vcomments_' . $comment->getId () . '">
                                                   ' . parseSmiley ($post_Left) . '
                                                       </span>
                                                       <span id="show_more_' . $arrComment->getId () . '" style="display:' . $showMore . ';" class="vpb_hover" onclick="vpb_show_full_item(\'' . $comment->getId () . '\',\'comment\');">See more</span>
                                                           </div>
                                                           <div style="display:none;" id="comment_box_b_' . $comment->getId () . '">
                                                       <span id="vcomments_large_' . $comment->getId () . '">' . $commentText . '</span>
                                                           </div>
                                                       </div>
                                                    
                                                </div>';
//
            echo '<div style="max-width: 100% !important; max-height: 100% !important; width: 100% !important; height: 100% !important; display: none;" id="vpb_editable_comment_wrapper_' . $comment->getId () . '" class="vpb_editable_status_wrapper">
				<div class="input-group">
				<textarea id="vpb_wall_comment_editable_data_' . $comment->getId () . '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a comment...">' . parseSmiley ($comment->getComment ()) . '</textarea>
				<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">
                <div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' . $comment->getId () . '\', \'comment\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">
                <div class="btn btn-success btn-wall-post" onclick="vpb_save_comment_update(' . $comment->getId () . ');">Save</div></span>
				</div>
                <div id="save_changes_loading_' . $comment->getId () . '"></div>
			   </div>';

            echo '<div class="dropdown open v_load_user_detail" onmouseover="vpb_get_user_onmouseover_datas();" onmouseout="vpb_get_user_mouseout_datas();" id="vpb_load_user_comment' . $formattedUsername . '" style="text-align: left !important; margin: 0px !important; padding: 0px !important; display: none;"> 
    
    <ul class="dropdown-menu bullet" style="border-radius:0px !important; display:block; margin:15px; z-index:9999;text-align:left !important;margin-top:10px;">
    
    <div class="dropdown-header" style="padding:10px !important;text-align:left !important; border:0px solid !important;margin:0px !important;">
    <div class="input-group vpb-wall-load-user-detail-wrap">
    <span class="input-group-addon vpb-wall-load-user-detail-photo" style="cursor:pointer;" onclick="window.location.href=\'/blab/index/profile/' . $comment->getUsername () . '\';">
    <span id="vpb_load_user_photo_comment' . $formattedUsername . '"><img src="/blab/public/uploads/profile/' . $comment->getUsername () . '" border="0"></span>
    </span>
    <div class="vpb-wall-load-user-detail-others">
    <span class="vpb-wall-load-user-detail-fullname" onclick="window.location.href=\'/blab/index/profile/' . $comment->getUsername () . '\';"><span id="vpb_load_user_fullname_comment' . $formattedUsername . '">' . $comment->getAuthor () . '</span></span><br>
   <span style="font-weight:normal !important;" id="vpb_load_user_country_comment' . $formattedUsername . '"><i class="fa fa-map-marker" title="Location"></i>&nbsp;United Kingdom</span>
   
   <input type="hidden" id="vpb_friendship_uid_comment' . $formattedUsername . '" value="' . $comment->getUsername () . '">
   <input type="hidden" id="vpb_friendship_fid_comment' . $formattedUsername . '" value="' . $comment->getUsername () . '">
    </div>
    </div>
    </div>
    <div style="clear:both;"></div>
    <div class="modal-footer" style="padding:10px !important; background-color:#F6F6F6; margin:0px;">
    <span id="vpb_load_friendship_comment' . $formattedUsername . '"></span>
    
    <span style="margin-left:16px !important;" class="cbt_friendship" onclick="window.location.href=\'/blab/index/profile/' . $comment->getUsername () . '\';"><i class="fa fa-user"></i> Profile</span>
    </div>
    </ul>
    </div>';

            $arrImages = $comment->getArrImages ();

            if ( !empty ($arrImages) )
            {
                echo '<div class="photos-frame">';

                foreach ($arrImages as $key => $arrImage) {

                    echo '<input type="hidden" id="hidden_photo_link_' . $comment->getId () . '_' . ($key + 1) . '" value="' . $arrImage->getFileLocation () . '">';

                    echo '<a class="v_photo_holders" onclick="vpb_popup_photo_box(\'' . $comment->getId () . '\', \'' . count ($arrImages) . '\', \'' . ($key + 1) . '\', \'' . $arrImage->getFileLocation () . '\');">';


                    echo '<img style="width:100%;" src="' . $arrImage->getFileLocation () . '" class="img-responsive">';

                    echo '</a>';
                }

                echo '</div>';
            }


            echo '<div class="btn-group vpb_wrap_post_contents_e" style="margin-left:45px; margin-top:8px;" id="' . $comment->getId () . '">
                    <span class="vpb_comment_update_bottom_links" title="Leave a reply" onclick="vpb_show_reply_box(\'' . $comment->getId () . '\');">Reply</span>
                                      <button comment-id="' . $comment->getId () . '" id="' . $arrComment->getId () . '" class="btn btn-white btn-xs ' . $likeCommentClass . ' comment"><i class="fa fa-thumbs-up"></i> ' . $likeCommentText . '</button>
                                            <br><a href="#" class="showLikes pull-left col-lg-12" id="' . $comment->getId () . '" type="comment">' . (!empty ($commentLikes) && $commentLikes !== null ? $commentLikes : '') . ' ' . ($likeCommentCount > 0 ? ' and ' . $likeCommentCount . ' others' : ' ');
            if ( $likeCommentCount > 0 || trim ($commentLikes) !== '' && trim ($commentLikes) != 'null' && $commentLikes !== null )
            {
                echo ' liked this';
            }

            echo '</a>
                                  </div>';
//                                            </div>';
//
            echo '<input type="hidden" id="vtotal_replies_' . $comment->getId () . '" value="11">';
            echo '<input type="hidden" id="vpb_replies_start_' . $comment->getId () . '" value="4">';


            buildReplies ($comment);



            echo '<div id="vpb_reply_updated_' . $comment->getId () . '"></div>';
            echo '<div style="clear:both;"></div>';

            echo '<div id="vpb_reply_box_' . $comment->getId () . '" class="vpb_reply_posting_wrapper" style="display: none;">
                <span id="replied_' . $comment->getId () . '"></span>
               
                <div class="input-group">
                
                <span class="input-group-addon vpb_no_radius_reply">
                <span class="v_status_pictured_michaelhampton"><img src="/blab/uploads/profile/' . $comment->getUsername () . '.jpg" width="24" height="24" border="0"></span>
                </span>
                <div class="vpb_wrap_post_contents_r">
                <textarea id="vpb_wall_reply_data_' . $comment->getId () . '" onclick="vpb_hide_reply_smiley_box(\'' . $comment->getId () . '\');" class="vpb_reply_textarea" placeholder="Write a reply..." onkeydown="javascript:return vpb_submit_reply(event,this, \'' . $comment->getId () . '\');"></textarea>';

            echo '<ul class="dropdown-menu reply_smiley_dropdown_menu" id="vpb_the_reply_smiley_box' . $comment->getId () . '" style="right: 0px; left: auto; top: auto; border-radius: 0px !important; display: none;">
                <li class="dropdown-header vpb_wall_li_bottom_border">
                <span class="v_wall_position_info_left">What is your current mood?</span> <span class="v_wall_position_info_right" onclick="vpb_hide_reply_smiley_box(\'' . $comment->getId () . '\');">x</span><div style="clear:both;"></div></li>
                <li><span id="vpb_reply_smiley_box_' . $comment->getId () . '"></span></li>
                </ul>
            
                            <div class="vpb_textarea_photo_box" id="vpb_preview_reply_photo_' . $comment->getId () . '">
                <span id="vpb-display-reply-attachment-preview_' . $comment->getId () . '"></span>
                </div>
                
                
                <div class="vpb_textarea_photo_box" style="text-align:center !important;min-height: 20px !important;" id="vpb_reply_loading_' . $comment->getId () . '">
                <span id="vpb_display_reply_loading_' . $comment->getId () . '"></span>
                </div>';



            echo '</div>';

            echo '<span class="vpb_no_radius_b">
                <input type="file" id="reply_photo_' . $comment->getId () . '" onchange="vpb_add_file_to_reply_clicked(\'' . $comment->getId () . '\');vpb_reply_image_preview(this, \'Please click on the continue button below to proceed to your post or click on the Browse photos button above to select a new photo or multiple photos at a time.\', \'Photo Enlargement\', \'' . $comment->getId () . '\');" style="display:none;">
                
                <div id="vpb_reply_bottom_icons_' . $comment->getId () . '" style="">
                
                <span class="vpb_no_radius_cc vasplus-tooltip-attached" style="display:none;" id="remove_reply_photo_' . $comment->getId () . '" onclick="remove_reply_photo(\'' . $comment->getId () . '\');" original-title="Remove photo"><i class="fa fa-times vfooter_icon"></i></span>
                
                <span id="add_reply_file_clicked_' . $comment->getId () . '" class="vpb_no_radius_cc vasplus-tooltip-attached" onclick="document.getElementById(\'reply_photo_' . $comment->getId () . '\').click();" original-title="Attach a photo"><i class="fa fa-camera vfooter_icon"></i></span>
                
                <span class="vpb_no_radius_cc vpb_reply_smiley_buttons vasplus-tooltip-attached" id="vpb_show_reply_smiley_button_' . $comment->getId () . '" onclick="vpb_reply_smiley_box(\'' . $comment->getId () . '\');" original-title="Add smiley"><i class="fa fa-smile-o vfooter_icon"></i></span>
               
               </div>
                
                </span>';



            echo '</span>'; //input group

            echo '</div>'; //reply box

            echo '</div>';


            //here
            echo '</div>';
        endforeach;

    endif;
}
