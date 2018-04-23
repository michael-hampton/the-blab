function vpb_send_request_to_join_groups (group_id, group_manager, username, action)
{
    if (group_id == "" || group_manager == "" || username == "" || action == "")
    {
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        var unknow_request = 0;
        if (action == "join")
        {
            $ ("#groups_join_" + group_id).hide ();
            $ ("#groups_requestsent_" + group_id).show ();
        }
        else if (action == "cancel")
        {
            $ ("#groups_requestsent_" + group_id).hide ();
            $ ("#groups_join_" + group_id).show ();
        }
        else
        {
            unknow_request = 1;
        }


        if (parseInt (unknow_request) == 1)
        {
            $ ("#v-wall-message").html ('The action is not recognized');
            $ ("#v-wall-alert-box").click ();
            return false;
        }
        else
        {
            var dataString = {"group_id": group_id, "group_manager": group_manager, "username": username, "action": action, "page": "request-to-join-group"};

            $.post ('/blab/group/addMemberToGroup', dataString, function (response)
            {
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage (objResponse.message);
                        return false;
                    }

                } catch (error) {
                    showErrorMessage ();
                }


            }).fail (function (error_response)
            {
                setTimeout ("vpb_send_request_to_join_groups('" + parseInt (group_id) + "', '" + group_manager + "', '" + username + "', '" + action + "');", 10000);
            });
        }
    }
}


function handleGroupSubmit ()
{
    var mentions = $ ('.mentions').mentionsInput ('getMentions');

    var blkstr = [];
    $.each (mentions, function (idx2, val2)
    {

        blkstr.push (val2.uid);
    });

    var tags = blkstr.join (", ");

    var form = $ (this);
    if (form.find ('#comment').val () === "")
    {
        showErrorMessage ("You must enter a comment");
        return false;
    }

    var data = {
        "comment": form.find ('#comment').val (),
        "groupId": groupId,
        tags: tags
    };
    postGroupComment (data);
    return false;
}

function postGroupComment (data)
{
    // send the data to the server
    $.ajax ({
        type: 'POST',
        url: '/blab/group/postGroupComment',
        data: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: postGroupSuccess,
        error: postGroupError
    });
}

function postGroupSuccess (data, textStatus, jqXHR)
{
    $ ('#groupcommentform').get (0).reset ();
    load_unseen_notification ();
    $ ("#posts-list").prepend (data);
    return false;
}

// Show all friend requests
//function vpb_show_status_updates ()
//{
//    alert("Mike");
//    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
//    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
//
//    $ ("#g_discussion").removeClass ('group_menu_wrapper');
//    $ ("#g_discussion").addClass ('group_menu_wrapper_active');
//
//    //Process is not running
//    $ ("#vpb_is_process_running").val ('0');
//
//    $ ("#vfrnds_data").val ('');
//
//    $ ("#vpb_display_wall_find_friends").html ('');
//    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page
//
//    //$("#vpb_display_page_owners_friends").html('');
//    $ ("#vasplus_page_owners_friends").hide (); // Hide page owners friends box on the current page
//    $ ("#vasplus_group_page_videos").hide ();
//    $ ("#vasplus_group_page_members").hide ();
//
//    $ ("#vpb_display_wall_friend_requests").html ('');
//    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page
//
//    $ ("#vpb_display_about_page_owner").html ('');
//    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page
//
//    $ ("#vpb_display_notifications").html ('');
//    $ ("#vasplus_notifications").hide (); // Hide notification page
//
//    $ ("#vasplus_wall_status_updates").fadeIn (); // Show the status updates on the current page
//
//    $ ("#view_or_edit_button").show (); // Show the view user details and edit user details buttons
//
//    $ ("#group_vides_left_box").show ();
//
//    if ($ ("#view_all_frnds").css ('display') == "none")
//    {
//        $ ("#view_all_frnds").fadeIn ();
//    }
//    else
//    {
//    }
//
//    $ ('html, body').animate ({
//        //scrollTop: $("#vasplus_wall_status_updates").offset().top-parseInt(60)+'px'
//    }, 1600, 'easeInOutExpo');
//}

// Show report this group box
function vpb_shoq_report_this_group_box (fullname, username, email, group_id, group_name)
{
    $ ("#the_groupID").val (group_id);
    $ ("#the_groupNamed").val (group_name);
    $ ('#report-this-group').modal ('show');
}

// Show all Page Group Members
function vpb_show_group_videos ()
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');

    $ ("#g_videos").removeClass ('group_menu_wrapper');
    $ ("#g_videos").addClass ('group_menu_wrapper_active');

    $ ("#group_vides_left_box").hide ();
    $ ("#view_all_frnds").show ();

    // Hide the notification box since a call to this function can also come from the notification box
    $ ("#v_notifications_box").hide ();
    $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
    $ ("#v_notifications").addClass ('vpb_notifications_icon');

    $ ("#v_message_box").hide ();
    $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon');

    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vpb_display_wall_friend_requests").html (''); // Empty the friend requests pop up box
    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#v_friend_requests_box").hide (); // Then hide the friend requests pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates box on the current page
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the friends requests box on the current page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    //$("#vpb_display_page_owners_friends").html($("#vpb_loading_image_gif").val()); //Show loading image
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_members").hide ();
    $ ("#vasplus_group_page_videos").show ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_group_page_videos").offset ().top - parseInt (120) + 'px'
    }, 1600, 'easeInOutExpo');
}

function vpb_load_group_photos (the_group_name)
{
    
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');

    $ ("#g_videos").removeClass ('group_menu_wrapper');
    $ ("#g_videos").addClass ('group_menu_wrapper_active');

    $ ("#group_vides_left_box").hide ();
    $ ("#view_all_frnds").show ();

    // Hide the notification box since a call to this function can also come from the notification box
    $ ("#v_notifications_box").hide ();
    $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
    $ ("#v_notifications").addClass ('vpb_notifications_icon');

    $ ("#v_message_box").hide ();
    $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon');

    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vpb_display_wall_friend_requests").html (''); // Empty the friend requests pop up box
    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#v_friend_requests_box").hide (); // Then hide the friend requests pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates box on the current page
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the friends requests box on the current page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    //$("#vpb_display_page_owners_friends").html($("#vpb_loading_image_gif").val()); //Show loading image
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_members").hide ();
    $ ("#vasplus_group_page_photos").show ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_group_page_videos").offset ().top - parseInt (120) + 'px'
    }, 1600, 'easeInOutExpo');
}

// Report a post
function vpb_report_the_group (session_fullname, session_username, session_email)
{
    var group_id = $ ("#the_groupID").val ();
    var group_name = $ ("#the_groupNamed").val ();

    var report_group_data = vpb_trim ($ ("#report_group_data").val ());

    if (group_id == "" || session_fullname == "" || session_username == "" || session_email == "" || group_name == "")
    {
        $ ("#report_group_status").html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if (report_group_data == "")
    {
        $ ("#report_group_data").focus ();
        $ ("#report_group_status").html ('<div class="vwarning">' + $ ("#empty_reporting_post_field_text").val () + '</div>');
        return false;
    }
    else if (report_group_data.length < 5)
    {
        $ ("#report_group_data").focus ();
        $ ("#report_group_status").html ('<div class="vwarning">' + $ ("#not_enough_reporting_post_field_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'group_id': group_id, 'group_name': group_name, 'session_fullname': session_fullname, 'session_username': session_username, 'session_email': session_email, 'report_group_data': report_group_data, 'page': 'report-a-group'};

        //$ ("#report_group_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $ ("#report-this-group").removeClass ('enable_this_box');
        $ ("#report-this-group").addClass ('disable_this_box');

        $.post ('/blab/group/reportGroup', dataString, function (response)
        {
            $ ("#report-this-group").removeClass ('disable_this_box');
            $ ("#report-this-group").addClass ('enable_this_box');

//            var response_brougght = response.indexOf ('completed_successfuly');
//            if (response_brougght != -1)
//            {
            $ ("#report_group_status").html ('<div class="vsuccess">' + $ ("#report_send_successfully_message").val () + '</div>');
            $ ("#report_group_data").val ('');
            setTimeout (function ()
            {
                $ ('.modal').modal ('hide');
                $ ("#report_group_status").html ('');
            }, 10000);
            return false;
//            }
//            else
//            {
            $ ("#report_group_status").html (response);
            return false;
            //}

        }).fail (function (error_response)
        {
            setTimeout ("vpb_report_the_group('" + session_fullname + "', '" + session_username + "', '" + session_email + "');", 10000);
        });
    }
}


//// Show all friend requests
//function vpb_show_status_updates ()
//{
//    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
//    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
//
//    $ ("#g_discussion").removeClass ('group_menu_wrapper');
//    $ ("#g_discussion").addClass ('group_menu_wrapper_active');
//
//    //Process is not running
//    $ ("#vpb_is_process_running").val ('0');
//
//    $ ("#vfrnds_data").val ('');
//
//    $ ("#vpb_display_wall_find_friends").html ('');
//    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page
//
//    //$("#vpb_display_page_owners_friends").html('');
//    $ ("#vasplus_page_owners_friends").hide (); // Hide page owners friends box on the current page
//    $ ("#vasplus_group_page_videos").hide ();
//    $ ("#vasplus_group_page_members").hide ();
//
//    $ ("#vpb_display_wall_friend_requests").html ('');
//    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page
//
//    $ ("#vpb_display_about_page_owner").html ('');
//    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page
//
//    $ ("#vpb_display_notifications").html ('');
//    $ ("#vasplus_notifications").hide (); // Hide notification page
//
//    $ ("#vasplus_wall_status_updates").fadeIn (); // Show the status updates on the current page
//
//    $ ("#view_or_edit_button").show (); // Show the view user details and edit user details buttons
//
//    $ ("#group_vides_left_box").show ();
//
//    if ($ ("#view_all_frnds").css ('display') == "none")
//    {
//        $ ("#view_all_frnds").fadeIn ();
//    }
//    else
//    {
//    }
//
//    $ ('html, body').animate ({
//        //scrollTop: $("#vasplus_wall_status_updates").offset().top-parseInt(60)+'px'
//    }, 1600, 'easeInOutExpo');
//}

var loader = '<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>';

// Show all Page Group Members
function vpb_show_group_page_members ()
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');

    $ ("#g_members").removeClass ('group_menu_wrapper');
    $ ("#g_members").addClass ('group_menu_wrapper_active');

    $ ("#view_all_frnds").hide ();
    $ ("#vasplus_wall_status_updates").hide ();

    // Hide the notification box since a call to this function can also come from the notification box
    $ ("#v_notifications_box").hide ();
    $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
    $ ("#v_notifications").addClass ('vpb_notifications_icon');

    $ ("#v_message_box").hide ();
    $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon');

    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vpb_display_wall_friend_requests").html (''); // Empty the friend requests pop up box
    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#v_friend_requests_box").hide (); // Then hide the friend requests pop up box
    $ ("#posts-list").hide (); // Hide the status updates box on the current page
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the friends requests box on the current page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    $ ("#group_vides_left_box").show ();

    //$("#vpb_display_page_owners_friends").html($("#vpb_loading_image_gif").val()); //Show loading image
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_members").show ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_group_page_members").offset ().top - parseInt (120) + 'px'
    }, 1600, 'easeInOutExpo');
}

function postGroupError (jqXHR, textStatus, errorThrown)
{
    // display error
    showErrorMessage (errorThrown);
}

function displayGroupComment (data)
{
    var commentHtml = createComment (data);
    var commentEl = $ (commentHtml);
    commentEl.hide ();
    var postsList = $ ('#posts-list');
    postsList.addClass ('has-comments');
    postsList.prepend (commentEl);
    commentEl.slideDown ();
    $ ("div[id^='vpb_hidden_post_id_']").hide ();
}

function createGroupComment (data)
{
    var data = $.parseJSON (data);

    var html = '<div style="width: 100%; margin: 0px !important; display: none !important;" id="vpb_hidden_post_id_' + data.id + '">' +
            '<div class="modal-content vasplus_a">' +
            '<div class="modal-body vasplus_b">' +
            '<div class="vpb_wall_adjust_c">' +
            '<div class="input-group vpb_wall_b_contents" style="display: inline-block;">' +
            'This post has been hidden&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_unhide_this_wall_item(\'' + data.id + '\', \'michaelhampton\', \'post\');">Unhide</span>&nbsp;&nbsp;·&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_show_hidden_item_intro();">Learn more...</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

    html += '<div class="social-feed-box"  postid="' + data.id + '">' +
            '<div class="pull-right social-action dropdown">' +
            '<button data-toggle="dropdown" class="dropdown-toggle btn-white">' +
            '<i class="fa fa-angle-down"></i>' +
            '</button>' +
            '<ul class="dropdown-menu m-t-xs">';

    if ($.trim (data.username) === $.trim (data.current_user))
    {
        html += '<li><a class="deletePost" deleteid="' + data.id + '" href="#">Delete Post</a></li>';
    }
    else
    {
        html += '<li><a class="ignorePost" deleteid="' + data.id + '" href="#">Remove Post From Feed</a></li>';
    }

    html += '</ul>' +
            '</div>' +
            '<div class="social-avatar">' +
            '<a href="" class="pull-left">' +
            '<img alt="image" src="/blab/public/uploads/profile/' + data.username + '.jpg">' +
            '</a>' +
            '<div class="media-body">' +
            '<a href="#">' +
            data.author +
            '</a>' +
            '<small date="' + data.date_added + '" class="text-muted timeAgo">' + timeSince (data.date_added) + ' ' + usersLocation + '</small>' +
            '</div>' +
            '</div>' +
            '<div id="vpb_editable_status_wrapper_' + data.id + '" class="vpb_editable_status_wrapper" style="display: none;">' +
            '<div class="input-group">' +
            '<textarea id="vpb_wall_status_editable_data_' + data.id + '" class="form-control vpb_textarea_editable_status_update" placeholder="Whats on your mind?">Test</textarea>' +
            '<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">' +
            '<div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' + data.id + '\', \'post\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">' +
            '<div class="btn btn-success btn-wall-post" onclick="vpb_save_status_update(\'' + data.id + '\');">Save</div></span>' +
            '</div>' +
            '<div id="saving_changes_loading_' + data.id + '"></div>' +
            '</div>' +
            '<div class="social-body" id="vpost_' + data.id + '">' +
            '<p>' +
            buildVideos (data.comment) +
            '</p>' +
            '<div class="btn-group">' +
            '<button id="' + data.id + '" class="btn btn-white btn-xs like post"><i class="fa fa-thumbs-up"></i> Like this!</button>' +
            '<a href="#" class="showLikes" type="post">Liked by Mike</a>' +
            '<button class="btn btn-white btn-xs"><i class="fa fa-comments"></i> Comment</button>' +
            '<button class="btn btn-white btn-xs Share" messageId="' + data.id + '"><i class="fa fa-share"></i> Share</button>' +
            '</div>' +
            '</div>' +
            '<div class="social-footer" id="' + data.id + '">' +
            '<div class="social-comment">' +
            '<a href="" class="pull-left">' +
            '<img alt="image" src="/blab/public/img/a3.jpg">' +
            '</a>' +
            '<div class="media-body">' +
            '<textarea comment-id="' + data.id + '" class="form-control reply-comment" placeholder="Write comment..."></textarea>' +
            '<ul class="dropdown-menu comment_smiley_dropdown_menu" id="vpb_the_comment_smiley_box' + data.id + '" style="right: 0px; left: auto; top:auto;border-radius:0px !important;">' +
            '<li class="dropdown-header vpb_wall_li_bottom_border">' +
            '<span class="v_wall_position_info_left">What is your current mood?</span> <span class="v_wall_position_info_right" onclick="vpb_hide_comment_smiley_box(\'' + data.id + '\');">x</span><div style="clear:both;"></div></li>' +
            '<li><span id="vpb_comment_smiley_box_' + data.id + '"></span></li>' +
            '</ul>' +
            '<div class="vpb_textarea_photo_box" id="vpb_preview_comment_photo_' + data.id + '">' +
            '<span id="vpb-display-comment-attachment-preview_' + data.id + '"></span>' +
            '</div>' +
            '<div class="vpb_textarea_photo_box" style="text-align:center !important;min-height: 20px !important;" id="vpb_comment_loading_' + data.id + '">' +
            '<span id="vpb_display_comment_loading_' + data.id + '"></span>' +
            '</div>' +
            '<span class="vpb_no_radius_b">' +
            '<input type="file" id="comment_photo_' + data.id + '" onchange="vpb_add_file_to_comment_clicked(\'' + data.id + '\');vpb_comment_image_preview(this, \'Please click on the continue button below to proceed to your post or click on the Browse photos button above to select a new photo or multiple photos at a time.\', \'Photo Enlargement\', \'' + data.id + '\');" style="display:none;">' +
            '<div id="vpb_comment_bottom_icons_4673" style="margin-bottom:2px !important;">' +
            '<span class="vpb_no_radius_c vasplus-tooltip-attached" style="display:none;" id="remove_comment_photo_' + data.id + '" onclick="remove_comment_photo(\'' + data.id + '\');" original-title="Remove photo"><i class="fa fa-times vfooter_icon"></i></span>' +
            '<span id="add_file_clicked_4673" class="vpb_no_radius_c vasplus-tooltip-attached" onclick="document.getElementById(\'comment_photo_' + data.id + '\').click();" original-title="Attach a photo"><i class="fa fa-camera vfooter_icon"></i></span>' +
            '<span class="vpb_no_radius_c vpb_smiley_buttons vasplus-tooltip-attached" id="vpb_show_smiley_button_' + data.id + '" onclick="vpb_comment_smiley_box(\'' + data.id + '\');" original-title="Add smiley"><i class="fa fa-smile-o vfooter_icon"></i></span>' +
            '</div>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>';
    return html;
}

function readURL (input)
{

    if (input.files && input.files[0])
    {
        var reader = new FileReader ();

        reader.onload = function (e)
        {
            $ ('.uploadedImage').append ('<img src="' + e.target.result + '">');

        };

        reader.readAsDataURL (input.files[0]);
        $ (".currentImage").remove ();
        $ ("#groupImage").submit ();
    }
}

$ (document).ready (function ()
{
    $ ('#groupcommentform').submit (handleGroupSubmit);

    $ (document).off ("click", "#timelineProfilePic");
    $ (document).on ('click', '#timelineProfilePic', function ()
    {
        $ ("#profileimgaa").click ();

    });

    $ (document).off ("click", ".joinGroup");
    $ (document).on ('click', '.joinGroup', function ()
    {
        var userId = $ (this).attr ("userId");
        var groupId = $ (this).attr ("groupId");
        var element = $ (this);

        $.ajax ({
            type: "POST",
            url: "/blab/group/addMemberToGroup",
            data: {group_id: groupId, "action": "accept", userId: userId},
            success: function (response)
            {
                //$ ("#inviteGroupMembers").modal ("toggle");
                element.removeClass ("btn-primary joinGroup").addClass ("btn-danger removeMember").text ("Remove Member");
                formatResponse (response);
            },
            error: function (e)
            {

                alert ("Error");
            }

        });

        return false;

    });
    
    
    $ (".invitationWindow").on ("click", function ()
    {
        var groupId = $ ("#currentGroupId").val ();
        
        if (!groupId)
        {
            showErrorMessage ();
            return false;
        }

        $.ajax ({
            type: "GET",
            url: "/blab/group/showAllInvitations/" + groupId,
            success: function (response)
            {                
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage (objResponse.message);
                    }
                } catch (error) {
                    $ ("#showAllInvitations").find (".modal-body").html (response);
                    $ ("#showAllInvitations").modal ("show");
                }

            },
            error: function (e)
            {

                alert ("Error");
            }

        });
    });

    $ (".showGroupMembers").on ("click", function ()
    {
        var groupId = $ ("#currentGroupId").val ();
        
        if (!groupId)
        {
            showErrorMessage ();
            return false;
        }

        $.ajax ({
            type: "GET",
            url: "/blab/group/showMembers/" + groupId,
            success: function (response)
            {                
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage (objResponse.message);
                    }
                } catch (error) {
                    $ ("#showAllGroupMembers").find (".modal-body").html (response);
                    $ ("#showAllGroupMembers").modal ("show");
                }

            },
            error: function (e)
            {

                alert ("Error");
            }

        });
    });


    $ (".inviteFriends").on ("click", function ()
    {
        $.ajax ({
            type: "GET",
            url: "/blab/group/inviteFriends/" + $ (this).attr ("groupId"),
            success: function (response)
            {
                $ ("#inviteGroupMembers").find (".modal-body").html (response);
                $ ("#inviteGroupMembers").modal ("show");
            },
            error: function (e)
            {

                alert ("Error");
            }

        });
    });

    $ (document).off ("click", ".removeMember");
    $ (document).on ('click', '.removeMember', function ()
    {
        var userId = $ (this).attr ("userId");
        var groupId = $ (this).attr ("groupId");

        var element = $ (this);

        $.ajax ({
            type: "POST",
            url: "/blab/group/addMemberToGroup",
            data: {group_id: groupId, "action": "remove", userId: userId},
            success: function (response)
            {
                //$ ("#inviteGroupMembers").modal ("toggle");
                element.removeClass ("btn-danger removeMember").addClass ("btn-primary joinGroup").text ("Add Member");
                formatResponse (response);
            },
            error: function (e)
            {

                alert ("Error");
            }

        });

        return false;

    });

    $ ('.leaveGroup').on ("click", function ()
    {
        var groupId = $ (this).attr ("groupId");

        swal ({
            title: "Are you sure You want to leave this group?",
            text: "You may be able to re-join at a later date",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function ()
        {
            $.ajax ({
                type: "POST",
                url: "/blab/index/removeMemberFromGroup",
                data: {groupId: groupId},
                success: function (response)
                {

                    swal ("Success!", "You have been removed from the group.", "success");
                },
                error: function (e)
                {

                    alert ("Error");
                }

            });
        });
    });

    $ (document).off ("change", "#profileimgaa");
    $ (document).on ('change', '#profileimgaa', function ()
    {

        readURL (this);
    });

    $ (document).off ("submit", "#groupImage");
    $ (document).on ('submit', '#groupImage', function ()
    {

        //stop submit the form, we will post it manually.
        event.preventDefault ();
        // Get form
        var form = $ ('#groupImage')[0];
        // Create an FormData object
        var data = new FormData (form);
        data.append ("groupId", groupId);
        data.append ("groupName", groupName);

        // If you want to add an extra field for the FormData
        // disabled the submit button
        $.ajax ({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "/blab/group/saveGroupImage",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (response)
            {

                formatResponse (response, 'The image was saved successfully.');
                return false;
            },
            error: function (e)
            {


            }
        });

        return false;
    });
});