//function vpb_send_request_to_join_groups (group_id, group_manager, username, action)
//{
//    if (group_id == "" || group_manager == "" || username == "" || action == "")
//    {
//        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
//        showErrorMessage ();
//        return false;
//    }
//    else
//    {
//        var unknow_request = 0;
//        if (action == "join")
//        {
//            $ ("#groups_join_" + group_id).hide ();
//            $ (".groups_requestsent").show ();
//        }
//        else if (action == "cancel")
//        {
//            $ (".groups_requestsent").hide ();
//            $ ("#groups_join_" + group_id).show ();
//        }
//        else
//        {
//            unknow_request = 1
//        }
//
//        if (parseInt (unknow_request) == 1)
//        {
//            $ ("#v-wall-message").html ($ ("#general_system_error").val ());
//            $ ("#v-wall-alert-box").click ();
//            return false;
//        }
//        else
//        {
//            var dataString = {"group_id": group_id, "group_manager": group_manager, "username": username, "action": action, "page": "request-to-join-group"};
//
//            $.post ('/blab/index/addMemberToGroup', dataString, function (response)
//            {
////                if ($.trim (response) !== "")
////                {
////                    showErrorMessage();
////                    return false;
////                }
//            }).fail (function (error_response)
//            {
//                setTimeout ("vpb_send_request_to_join_groups('" + parseInt (group_id) + "', '" + group_manager + "', '" + username + "', '" + action + "');", 10000);
//            });
//        }
//    }
//}


// Show report this group box
function vpb_shoq_report_this_page_box (fullname, username, email, group_id, group_name)
{
    $ ("#the_groupID").val (group_id);
    $ ("#the_groupNamed").val (group_name);
    $ ('#report-this-group').modal ('show');
}

// Show all Page Group Members
function vpb_show_page_videos ()
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');

    $ ("#g_videos").removeClass ('group_menu_wrapper');
    $ ("#g_videos").addClass ('group_menu_wrapper_active');
    $ ("#vasplus_group_page_photos").hide ();
    $ ("#vasplus_group_page_reviews").hide ();


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

function vpb_load_page_photos (the_group_name)
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_reviews").hide ();

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

function vpb_load_page_reviews (pageId)
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
    $ ("#vasplus_group_page_videos").hide ();

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
    $ ("#vasplus_group_page_reviews").show ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_group_page_videos").offset ().top - parseInt (120) + 'px'
    }, 1600, 'easeInOutExpo');
}

function likePage (pageId)
{
    if (!pageId)
    {
        showErrorMessage ('Invalid page id');
        return false;
    }

    $.post ('/blab/page/likePage', {"pageId": pageId}, function (response)
    {

        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            showErrorMessage (objResponse.message);
        }
        else
        {
            $ (".likePage").removeClass ("likePage").addClass ("unlikePage").html ('Unlike Page');
            $ (".unlikePage").attr ("onclick", "unlikePage('" + pageId + "')");
            var likeCount = parseInt ($ (".likeCount").html ());
            var newCount = likeCount + 1;
            $ (".likeCount").html (newCount);
        }

    });
}

function followPage (pageId)
{
    if (!pageId)
    {
        showErrorMessage ('Invalid page id');
        return false;
    }

    $.post ('/blab/page/followPage', {"pageId": pageId}, function (response)
    {

        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            showErrorMessage (objResponse.message);
        }
        else
        {
            $ (".followPage").removeClass ("followPage").addClass ("unfollowPage").html ('Unfollow Page');
            $ (".unfollowPage").attr ("onclick", "unfollowPage('" + pageId + "')");
            var likeCount = parseInt ($ (".followCount").html ());
            var newCount = likeCount + 1;
            $ (".followCount").html (newCount);
        }

    });
}

function unlikePage (pageId)
{
    if (!pageId)
    {
        showErrorMessage ('Invalid page id');
        return false;
    }

    $.post ('/blab/page/unlikePage', {"pageId": pageId}, function (response)
    {

        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            showErrorMessage (objResponse.message);
        }
        else
        {
            $ (".unlikePage").removeClass ("unlikePage").addClass ("likePage").html ('Like Page');
            $ (".likePage").attr ("onclick", "likePage('" + pageId + "')");
            var likeCount = parseInt ($ (".likeCount").html ());
            var newCount = likeCount === 0 ? 0 : likeCount - 1;
            $ (".likeCount").html (newCount);
        }

    });
}

function unfollowPage (pageId)
{
    if (!pageId)
    {
        showErrorMessage ('Invalid page id');
        return false;
    }

    $.post ('/blab/page/unfollowPage', {"pageId": pageId}, function (response)
    {

        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            showErrorMessage (objResponse.message);
        }
        else
        {
            $ (".unfollowPage").removeClass ("unfollowPage").addClass ("followPage").html ('Follow Page');
            $ (".followPage").attr ("onclick", "followPage('" + pageId + "')");
            var likeCount = parseInt ($ (".followCount").html ());
            var newCount = likeCount === 0 ? 0 : likeCount - 1;
            $ (".followCount").html (newCount);
        }

    });
}

$ (".followCount").on ("click", function ()
{
    pageId = $ ("#currentPageId").val ();

    if (!pageId)
    {
        showErrorMessage ();
        return false;
    }

    $.post ('/blab/page/getFollowsForPage', {"pageId": pageId}, function (response)
    {
        try {
            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                showErrorMessage (objResponse.message);
            }

        } catch (error) {
            $ ("#v-like-gdata").modal ("show");
            $ ("#vpb_display_like_gen_data").html (response);
        }
    });

});

$ (".likeCount").on ("click", function ()
{
    pageId = $ ("#currentPageId").val ();

    if (!pageId)
    {
        showErrorMessage ();
        return false;
    }

    $.post ('/blab/page/getLikesForPage', {"pageId": pageId}, function (response)
    {
        try {
            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                showErrorMessage (objResponse.message);
            }

        } catch (error) {
            $ ("#v-like-gdata").modal ("show");
            $ ("#vpb_display_like_gen_data").html (response);
        }
    });

});

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

        $.post ('/blab/page/reportPage', dataString, function (response)
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


// Show all friend requests
//function vpb_show_status_updates ()
//{
//    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
//    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
//
//    $ ("#g_discussion").removeClass ('group_menu_wrapper');
//    $ ("#g_discussion").addClass ('group_menu_wrapper_active');
//
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

function set_votes (widget, val)
{

    //var avg = $ (widget).data ('fsr').whole_avg;
    //var votes = $ (widget).data ('fsr').number_votes;
    //var exact = $ (widget).data ('fsr').dec_avg;

    $ (widget).find ('.star_' + val).prevAll ().andSelf ().addClass ('ratings_vote');
    $ (widget).find ('.star_' + val).nextAll ().removeClass ('ratings_vote');
    //$ (widget).find ('.total_votes').text (votes + ' votes recorded (' + exact + ' rating)');
}

var rating = null;

$ (document).ready (function ()
{

    $ ('.ratings_stars').bind ('click', function ()
    {
        var star = this;
        var widget = $ (this).parent ();

        var test = $ (star).attr ('class');
        var part1 = test.split (' ')[0];
        var part2 = part1.split ('_')[1];

        rating = part2;

        set_votes (widget, part2);

//        var clicked_data = {
//            clicked_on: $ (star).attr ('class'),
//            widget_id: widget.attr ('id')
//        };
//        $.post (
//                'ratings.php',
//                clicked_data,
//                function (INFO)
//                {
//                    widget.data ('fsr', INFO);
//                    set_votes (widget);
//                },
//                'json'
//                );
    });

    $ (".SaveReview").on ("click", function ()
    {
        var dataString = {"review": $ ("#theReview").val (), "pageId": $ ("#currentPageId").val (), "rating": rating};

        $.post ('/blab/page/saveNewReview', dataString, function (response)
        {
            try {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    showErrorMessage (objResponse.message);
                    return false;
                }
            } catch (error) {
                $ (".reviewPosts").prepend (response);
                $ ("#theReview").val ("");
            }

        });
    });

    $ ('.ratings_stars').hover (
            // Handles the mouseover
                    function ()
                    {
                        $ (this).prevAll ().andSelf ().addClass ('ratings_over');
                        $ (this).nextAll ().removeClass ('ratings_vote');
                    },
                    // Handles the mouseout
                            function ()
                            {
                                $ (this).prevAll ().andSelf ().removeClass ('ratings_over');
                                //set_votes ($ (this).parent ());
                            }
                    );

                    $ ('.rate_widget').each (function (i)
                    {
//                         {
//            "widget_id"     : "r1",
//            "number_votes"  : 129,
//            "total_points"  : 344,
//            "dec_avg"       : 2.7,
//            "whole_avg"     : 3
//        }

//                        var widget = this;
//                        var out_data = {
//                            widget_id: $ (widget).attr ('id'),
//                            fetch: 1
//                        };
//                        $.post (
//                                'ratings.php',
//                                out_data,
//                                function (INFO)
//                                {
//                                    $ (widget).data ('fsr', INFO);
//                                    set_votes (widget);
//                                },
//                                'json'
//                                );
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