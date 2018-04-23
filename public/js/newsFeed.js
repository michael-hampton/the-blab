var loader = '<div class="sk-spinner sk-spinner-wave"><div class="sk-rect1"></div><div class="sk-rect2"></div><div class="sk-rect3"></div><div class="sk-rect4"></div><div class="sk-rect5"></div></div>';

var allowPolling = false;



$ (function ()
{

    vpb_get_all_the_users_groups ();

    $ ("#geocomplete").geocomplete ()
            .bind ("geocode:result", function (event, result)
            {
                $.log ("Result: " + result.formatted_address);
            })
            .bind ("geocode:error", function (event, status)
            {
                $.log ("ERROR: " + status);
            })
            .bind ("geocode:multiple", function (event, results)
            {
                $.log ("Multiple: " + results.length + " results found");
            });

    $ ("#find").click (function ()
    {
        $ ("#geocomplete").trigger ("geocode");
    });


    $ ("#examples a").click (function ()
    {
        $ ("#geocomplete").val ($ (this).text ()).trigger ("geocode");
        return false;
    });

});


setInterval (function ()
{
    console.log ("UPDATING TIMES");
    liveUpdateDate ();
}, 60 * 1000); // 60 * 1000 milsec

function timeSince (date)
{
    if (typeof date !== 'object')
    {
        date = new Date (date);
    }

    var seconds = Math.floor ((new Date () - date) / 1000);
    var intervalType;

    var interval = Math.floor (seconds / 31536000);
    if (interval >= 1)
    {
        intervalType = 'year';
    }
    else
    {
        interval = Math.floor (seconds / 2592000);
        if (interval >= 1)
        {
            intervalType = 'month';
        }
        else
        {
            interval = Math.floor (seconds / 86400);
            if (interval >= 1)
            {
                intervalType = 'day';
            }
            else
            {
                interval = Math.floor (seconds / 3600);
                if (interval >= 1)
                {
                    intervalType = "hour";
                }
                else
                {
                    interval = Math.floor (seconds / 60);
                    if (interval >= 1)
                    {
                        intervalType = "minute";
                    }
                    else
                    {
                        interval = seconds;
                        intervalType = "second";
                    }
                }
            }
        }
    }

    if (interval > 1 || interval === 0)
    {
        intervalType += 's';
    }

    return interval + ' ' + intervalType;
}

function liveUpdateDate ()
{
    $ (".timeAgo").each (function ()
    {

        var date = $ (this).attr ("date");
        $ (this).html (timeSince (date));
    });
}

function showErrorMessage (message)
{
    message = message || "Somethinh went wrong whilst trying to complete the request";

    swal ({
        title: "Error",
        text: message,
        type: "error",
        showCancelButton: false,
        confirmButtonColor: "#1ab394",
        confirmButtonText: "Ok",
        closeOnConfirm: false,
        html: true

    });
}

// Show and Hide the sorting box
function vpb_sorting_box ()
{
    if ($ ("#vpb_sorting_box").css ('display') == "none")
    {
        $ ("#vpb_sorting_box").show ();
        $ ("#vpb_sorting_text").removeClass ('vpb_sort_text');
        $ ("#vpb_sorting_text").addClass ('vpb_sort_text_active');
    }
    else
    {
        $ ("#vpb_sorting_box").hide ();
        $ ("#vpb_sorting_text").removeClass ('vpb_sort_text_active');
        $ ("#vpb_sorting_text").addClass ('vpb_sort_text');
    }
}

function vpb_sort_the_status_updates (type, text)
{
    $ ("#vpb_sort_option").val (type);
    $ ("#vpb_sorted_option").val (text);

    // Hide the sorting details
    $ ("#vpb_sorting_box").hide ();
    $ ("#vpb_sorting_text").removeClass ('vpb_sort_text_active');
    $ ("#vpb_sorting_text").addClass ('vpb_sort_text');

    $ ("#vpb_sorted_txt").html ($ ("#v_loadings_btn").val ());
    $ (".vsorted_checked").hide ();
    $ ("#" + type + "_").show ();

    setTimeout (function ()
    {
        vpb_load_more_status_updates (type);
    }, 100);
}

function refreshPosts ()
{
    vpb_load_more_status_updates ('refresh');
}

function vpb_get_all_the_user_groups ()
{

}

function vpb_load_more_status_updates (type)
{
    var viewed_post_id = $ ("#viewed_post_id").val ();

    if (type != "auto-load")
    {
        //$ ("#vpb_start").val (0);
    }
    else
    {
    }
    var vpb_start = $ ("#vpb_start").val (); //Where to start loading the updates

    vpb_start = vpb_start == 0 ? 4 : vpb_start;


    var vpb_total_per_load = $ ("#vpb_total_per_load").val (); //Total status updates to load each time
    var session_uid = $ ("#session_uid").val (); //The username of the current logged in user
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner
    var page_id = $ ("#page_id").val (); //The page id which identifies the current page viewed
    var vpb_sort_option = $ ("#vpb_sort_option").val (); //The method to sort the updates

    var dataString = {"vpb_start": vpb_start, "vpb_total_per_load": vpb_total_per_load, "session_uid": session_uid, "vpb_page_owner": vpb_page_owner, "page_id": page_id, "vpb_sort_option": vpb_sort_option, "viewed_post_id": viewed_post_id, "page": "load-the-status-updates"}

    vpb_running_process = true; //prevent further ajax loading

    $ ("#vpb_loading_status_updates").html (loader); /* Show loading image */

    $.post ('/blab/post/sortPosts', dataString, function (response)
    {
        //Hide the loading image after loading data onto the page
        $ ("#vpb_loading_status_updates").html ('');

        $ ("#vpb_start").val (parseInt (vpb_start) + parseInt (vpb_total_per_load));

        // Show sorted Option
        $ ("#vpb_sorted_txt").html ($ ("#vpb_sorted_option").val ());

        // Remove the white background color class from status updates if there are updates to display
        $ ("#vasplus_wall_status_updates").removeClass ('vmiddle_others');

        //Append the received data unto the current page
        if (type != "auto-load")
        {
            $ ("#posts-list").html (response);
        }
        else
        {
            $ ("#posts-list").append (response);
        }
        // When a single post is viewed, remove the top margin from the post displayed
        if (viewed_post_id != "")
        {
            setTimeout (function ()
            {
                $ ("#vpb_wall_wrapper_" + parseInt (viewed_post_id)).removeClass ("v_wall_wrapper_top");
                $ ("#vpb_wall_wrapper_" + parseInt (viewed_post_id)).css ("margin-top", "0px");

                $ ("#vpb_status_update_wraps").hide ();

                $ ('html, body').animate ({
                    scrollTop: $ ("#vasplus_wall_status_updates").offset ().top - parseInt (120) + 'px'
                }, 1600, 'easeInOutExpo');

            }, 10);
        }
        else
        {
        }

        if (type == "start" || type != "auto-load")
        {
            vpb_get_all_the_user_groups ();
        }
        else
        {
        }

        vpb_running_process = false;
    }).fail (function (xhr, ajaxOptions, theError)
    {
        $ ("#vpb_loading_status_updates").html (''); //Hide loading image
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        vpb_running_process = false;
        //return false;
    });
}



// Show About Page Owner Details
function vpb_show_notifications_details (action)
{
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');


    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates box on the current page

    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#friendRequests_bar").hide (); // Then hide the friend requests pop up box

    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page

    $ ("#vpb_display_wall_friend_requests").html ('');
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about page owner page

    // Hide the website menus
    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    $ ("#v_notifications_box").hide ();
    $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
    $ ("#v_notifications").addClass ('vpb_notifications_icon');

    $ ("#v_message_box").hide ();
    $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon');

    //$("#vpb_display_page_owners_friends").html('');
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_members").hide ();

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    var session_uid = $ ("#session_uid").val ();
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner


    //return false;

    //Process is running
    $ ("#vpb_is_process_running").val ('1');
    $ ("#vpb_display_vnotifications").html (''); // Remove the previous pop up notification contents

    $ ("#vpb_display_notifications").html ('');

    $ ("#vpb_display_vnotifications").html (loader); //Show loading image
    $ ("#vasplus_notifications").show ();

    if ($ ("#view_all_frnds").css ('display') == "none")
    {
        $ ("#view_all_frnds").fadeIn ();
    }
    else
    {
    }

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_notifications").offset ().top - parseInt (100) + 'px'
    }, 1600, 'easeInOutExpo');

    var dataString = {'username': session_uid};

    $.post ('/blab/notification/getAllNotifications', dataString, function (response)
    {
        $ ("#vpb_display_vnotifications").html (response);

    }).fail (function (error_response)
    {
        setTimeout ("vpb_show_notifications_details('" + action + "');", 10000);
    });
}

function vpb_get_user_mouseout_data (username, fullname, country, photo)
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);

    vpb_time_out = setTimeout (function ()
    {
        $ (".v_load_user_detail").css ('display', 'none');
    }, parseInt (vpb_user_loader_timer));
}

function vpb_get_user_mouseout_datas ()
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);
    vpb_time_out = setTimeout (function ()
    {
        $ (".v_load_user_detail").css ('display', 'none');
    }, parseInt (vpb_user_loader_timer));
}

function vpb_get_user_onmouseover_datas ()
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);
}

//Friendship function
function vpb_friend_ship (id, friend, action)
{
    if (id == "")
    {

        showErrorMessage ();
        return false;
    }
    else
    {
        var unknow_request = 0;
        if (action == "addfriend")
        {
            $ ("#addfriend_" + parseInt (id)).hide ();
            $ ("#requestsent_" + parseInt (id)).show ();
            $ ("#cancelrequest_" + parseInt (id)).show ();
        }
        else if (action == "cancelrequest")
        {
            $ ("#requestsent_" + parseInt (id)).hide ();
            $ ("#cancelrequest_" + parseInt (id)).hide ();
            $ ("#addfriend_" + parseInt (id)).show ();
        }
        else if (action == "unfriend")
        {
            $ ('span[id^="unfriend_"]').hide ();
            $ ("#addfriend_" + parseInt (id)).show ();
        }
        else if (action == "reject")
        {
            $ ("#reject_" + parseInt (id)).hide ();
            $ ("#accept_" + parseInt (id)).hide ();
            $ ("#addfriend_" + parseInt (id)).show ();
        }
        else if (action == "accept")
        {
            $ ("#reject_" + parseInt (id)).hide ();
            $ ("#accept_" + parseInt (id)).hide ();
            $ ("#unfriend_" + parseInt (id)).show ();
        }
        else
        {
            unknow_request = 1
        }

        if (parseInt (unknow_request) == 1)
        {
            showErrorMessage ();
            return false;
        }
        else
        {
            var dataString = {"friend": id, "action": action};

            $.post ('/blab/friend/handleFriendRequest', dataString, function (response)
            {
                //alert(username+' '+friend+' '+action+' '+response);


                //vpb_load_chat_friends_box ('auto');
            }).fail (function (error_response)
            {
                setTimeout ("vpb_friend_ship('" + parseInt (id) + "', '" + friend + "', '" + action + "');", 10000);
            });
        }
    }

    return false;
}


//Friendship function
function block_user (id, action)
{
    if (id == "")
    {
        showErrorMessage ();
        return false;
    }
    else
    {
        var unknow_request = 0;
        if (action == "block")
        {
            $ ("#block_" + parseInt (id)).hide ();
            $ ("#unblock_" + parseInt (id)).show ();
        }
        else if (action == "unblock")
        {
            $ ("#unblock_" + parseInt (id)).hide ();
            $ ("#block_" + parseInt (id)).show ();
        }
        else
        {
            unknow_request = 1
        }

        if (parseInt (unknow_request) == 1)
        {
            showErrorMessage ();
            return false;
        }
        else
        {
            var dataString = {"friend": id, "action": action};

            $.post ('/blab/friend/handleBlockedUser', dataString, function (response)
            {
                formatResponse (response, 'User has been ' + action + 'ed successfully');
            }).fail (function (error_response)
            {
                showErrorMessage ();
            });
        }
    }
}
// Find new friends
function vpb_show_find_new_friends_box ()
{

    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');


    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box

    $ ("#friendRequests_bar").hide (); // Then hide the friend requests pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates on the current page

    $ ("#vpb_display_wall_friend_requests").html (''); // Remove the previously displayed friends request from the page
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the veiw all friend requests page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    //$("#vpb_display_page_owners_friends").html('');
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_members").hide ();

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    $ ("#vasplus_wall_find_friends").show (); // Show the find friend page

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_wall_find_friends").offset ().top - parseInt (100) + 'px'
    }, 1600, 'easeInOutExpo');

    if ($ ("#view_all_frnds").css ('display') == "none")
    {
        $ ("#view_all_frnds").fadeIn ();
    }
    else
    {
    }
}

// Site searching for friends
function vpb_search_friends ()
{
    var friend = $ ("#vfrnds_data").val ();
    var session_uid = $ ("#session_uid").val ();

    if (friend.length > 0)
    {
        $ ("#vpb_display_wall_find_friends").html (loader); //Show loading image

        var dataString = {'friend': friend, 'username': session_uid, 'page': 'search_for_friends'};

        $.post ('/blab/friend/searchFriends', dataString, function (response)
        {

            if (response == "")
            {
                $ ("#vpb_display_wall_find_friends").html ('');
            }
            else
            {
                $ ("#vpb_display_wall_find_friends").html (response);
            }


            $ ('html, body').animate ({
                scrollTop: $ ("#vasplus_wall_find_friends").offset ().top - parseInt (100) + 'px'
            }, 1600, 'easeInOutExpo');

        }).fail (function (error_response)
        {
            setTimeout ("vpb_search_friends('" + friend + "');", 10000);
        });
    }
    else
    {
        $ ("#vfrnds_data").focus ();
        $ ("#vpb_display_wall_find_friends").html ('');
        return false;
    }
}

// Show all friend requests
function vpb_show_status_updates ()
{
    $ (".event-details").show ();
    $ (".r-sidebar").show ();
    $ (".m-sidebar").hide ();
    $ (".l-sidebar").show ();
    $ ("#group_page_menu span").removeClass ('group_menu_wrapper_active');
    $ ("#group_page_menu span").addClass ('group_menu_wrapper');
    $ ("#vasplus_group_page_reviews").hide ();

    $ ("#g_discussion").removeClass ('group_menu_wrapper');
    $ ("#g_discussion").addClass ('group_menu_wrapper_active');

    //Process is not running
    $ ("#vpb_is_process_running").val ('0');

    $ ("#vfrnds_data").val ('');

    $ ("#vpb_display_wall_find_friends").html ('');
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page

    //$("#vpb_display_page_owners_friends").html('');
    $ ("#vasplus_page_owners_friends").hide (); // Hide page owners friends box on the current page
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_photos").hide ();
    $ ("#vasplus_group_page_members").hide ();

    $ ("#vpb_display_wall_friend_requests").html ('');
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page

    $ ("#vpb_display_about_page_owner").html ('');
    $ ("#vasplus_about_page_owner").hide (); // Hide about the page owner page

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ ("#vasplus_wall_status_updates").fadeIn (); // Show the status updates on the current page

    $ ("#view_or_edit_button").show (); // Show the view user details and edit user details buttons

    $ ("#group_vides_left_box").show ();

    if ($ ("#view_all_frnds").css ('display') == "none")
    {
        $ ("#view_all_frnds").fadeIn ();
    }
    else
    {
    }

    $ ('html, body').animate ({
        //scrollTop: $("#vasplus_wall_status_updates").offset().top-parseInt(60)+'px'
    }, 1600, 'easeInOutExpo');
}


function formatResponse (response, message)
{

    try {
        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            if (objResponse.message !== undefined)
            {
                showErrorMessage (objResponse.message);
            }
        }
        else
        {
            showSuccessMessage (message);
        }
    } catch (error) {
        showErrorMessage ();
    }

}

function showSuccessMessage (message)
{
    message = message || "The action was complete successfully.";

    swal ({
        title: "Success",
        text: message,
        type: "success",
        showCancelButton: false,
        confirmButtonColor: "#1ab394",
        confirmButtonText: "Ok",
        closeOnConfirm: false

    });
}

var selectedChat = null;

//Load system users
function vpb_load_system_users (action)
{
    var searchText = "";

    selectedChat = $ (".selectedChat").attr ("user-id");

    var dataString = {"searchText": searchText};
    $.ajax ({
        type: "POST",
        url: '/blab/chat/searchChatUsers',
        data: dataString,
        beforeSend: function ()
        {
            if (action == "normal")
            {
                $ (".category-list").html (loader);
            }

        },
        success: function (response)
        {
            $ (".category-list").html (response);

            setTimeout (function ()
            {
                $ (".category-list > li > a[user-id=" + selectedChat + "]").addClass ("selectedChat");
                return false;
            }, 10);
        }
    });
}

//Get basename of file
function vpb_basename_only (url)
{
    //return ((url=/(([^\/\\\.#\? ]+)(\.\w+)*)([?#].+)?$/.exec(url))!= null)? url[2]: '';
    return url.replace (/\\/g, '/').replace (/.*\//, '');
}

// Preview Group Photo
function vpb_group_photo_preview (vpb_selector_)
{
    var id = 1, last_id = last_cid = '';
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                document.getElementById ('group_pics').title = '';
                document.getElementById ('group_pic').value = '';
                $ ('#vpb_adding_group_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
                return false;
            }
            else
            {
                //Clear previous previewed files and start again
                $ ('#vpb_adding_group_photo_status').html ('');
                $ ('#vpb-display-group-photo-preview').html ('');
                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-group-photo-preview').append (
                            '<div class="vpb_preview_wrapper"> \
				   <img class="vpb_image_style" src="' + e.target.result + '" title="' + escape (file.name) + '" onClick="vpb_view_this_image(\'Photo Preview\', \'' + e.target.result + '\');" style="cursor:pointer; height:150px;" /><br /> \
				   </div>');
                }
                reader.readAsDataURL (file);
            }
        }
        else
        {
            return false;
        }
    });
}

// Preview Group Photo
function vpb_page_photo_preview (vpb_selector_)
{
    var id = 1, last_id = last_cid = '';
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                document.getElementById ('page_pics').title = '';
                document.getElementById ('page_pic').value = '';
                $ ('#vpb_adding_page_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
                return false;
            }
            else
            {
                //Clear previous previewed files and start again
                $ ('#vpb_adding_page_photo_status').html ('');
                $ ('#vpb-display-page-photo-preview').html ('');
                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-page-photo-preview').append (
                            '<div class="vpb_preview_wrapper"> \
				   <img class="vpb_image_style" src="' + e.target.result + '" title="' + escape (file.name) + '" onClick="vpb_view_this_image(\'Photo Preview\', \'' + e.target.result + '\');" style="cursor:pointer; height:150px;" /><br /> \
				   </div>');
                }
                reader.readAsDataURL (file);
            }
        }
        else
        {
            return false;
        }
    });
}

//this is used to close a popup
function vpb_vchat_close_chat_boxes (id)
{
    for (var iii = 0; iii < popups.length; iii++)
    {
        if (id == popups[iii])
        {
            Array.remove (popups, iii);

            document.getElementById (id).style.display = "none";

            vpb_vchat_position_chat_boxes ();
            vchat_removecookie ('compose_new_message');

            vchat_removecookie ('group_fullname' + id);
            vchat_removecookie ('group_username' + id);
            vchat_removecookie ('group_photo' + id);
            vchat_removecookie ('v_group_manager' + id);

            var group_uid = new Array ();
            group_uid = vchat_getcookie ('group_uid').split (/\,/);
            vchat_setcookie ('group_uid', vchat_remove_data (group_uid, id), 30);
            return;
        }
    }
}


function vpb_show_message_menu ()
{
    $ ("#friendRequests_bar").hide ();
    $ ("#friendRequests_bar").removeClass ('vpb_notifications_icon_active');
    $ ("#friendRequests_bar").addClass ('vpb_notifications_icon');

    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    $ (".dropdown-menu2").hide ();
    $ (".dropdown-menu2").removeClass ('vpb_notifications_icon_active');
    $ (".dropdown-menu2").addClass ('vpb_notifications_icon');

    $ ("#vasplus_notifications").hide ();
    $ ("#vpb_display_messages").html ('');

    $ ("#vpb_display_search_results").hide ();
    $ ("#message_counter").hide ();

    $ ("#vpb_display_messages").html ('<div style="padding:10px;">' + $ ("#v_loading_btn").val () + '</div>');
    $ ("#v_message_box").show ();

    $ ("#v_new_messages").removeClass ('vpb_notifications_icon');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon_active');

    var dataString = {'page': 'get_the_new_messages'};

    $.post ('/blab/chat/getUnreadChatMessages', dataString, function (response)
    {
        if ($.trim (response) === "")
        {
            $ ("#v_no_new_message").show ();
            $ ("#vpb_display_messages").html ('');
        }
        else
        {
            $ ("#v_no_new_message").hide ();
            $ ("#vpb_display_messages").html (response);
        }


    }).fail (function (error_response)
    {
        setTimeout ("vpb_show_message_menu();", 10000);
    });
}

// Update Group Picture
function vpb_update_group_picture (group_uid)
{
    var group_name = vpb_getcookie ('g_name') != "" && vpb_getcookie ('g_name') != null && vpb_getcookie ('g_name') != undefined ? vpb_getcookie ('g_name') : "Group Photo";

    var ext = $ ('#group_pic').val ().split ('.').pop ().toLowerCase ();

    if (group_uid == "" || group_uid == undefined || group_uid == null)
    {
        document.getElementById ('group_pics').title = '';
        document.getElementById ('group_pic').value = '';
        $ ('#vpb_adding_group_photo_status').html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if ($ ("#group_pic").val () == "")
    {
        return false;
    }
    else if ($.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        document.getElementById ('group_pics').title = '';
        //document.getElementById('group_pic').value = '';
        $ ('#vpb_adding_group_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
        return false;
    }
    else
    {
        $ ("#vpb_adding_group_photo_status").html ('');
        $ ('.vpb_progress_outer_bar').show ();

        var formData = new FormData ();
        formData.append ("page", 'update-group-photo');
        formData.append ("group_id", $ ("#currentGroupId").val ());
        formData.append ("profilepic", document.getElementById ('group_pic').files[0]);

        $.ajax ({
            url: '/blab/group/saveGroupImage',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            xhr: function ()
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    var xhr = vpb_this_xhr ();
                    xhr.upload.addEventListener ("progress", function (e_vent)
                    {
                        if (e_vent.lengthComputable)
                        {
                            var vpb_current_progress = e_vent.loaded / e_vent.total;
                            vpb_current_progress = parseInt (vpb_current_progress * 100);
                            var vpb_totalPercentage = parseInt (vpb_current_progress) + '%';
                            vpb_progress_percentage.width (vpb_totalPercentage)
                            vpb_progress_inner_bar.html (vpb_totalPercentage);

                            if (vpb_current_progress === 100)
                            {
                                $ ('.vpb_progress_outer_bar').hide ();
                                $ ("#vpb_adding_group_photo_status").html ('<br>' + $ ("#v_loading_btn").val ());
                            }
                        }
                    }, false);
                    return xhr;
                }
            },
            complete: function (xhr)
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    vpb_progress_percentage.width ("100%");
                    vpb_progress_inner_bar.html ("100%");
                    //xhr.responseText;
                }
            },
            success: function (response)
            {
                var objResponse = $.parseJSON (response);


                if (objResponse.sucess == 1)
                {
                    var upic = response.split ('&');
                    document.getElementById ('group_pics').title = '';
                    document.getElementById ('group_pic').value = '';
                    $ ('#vpb-display-group-photo-preview').html ('');

                    $ ("#vpb_adding_group_photo_status").html ('<div class="vsuccess">' + $ ('#successfully_updated_group_photo_text').val () + '</div>');

                    if (upic[0] != "")
                    {
                        setTimeout (function ()
                        {
                            var currentPic = vpb_site_url + 'vpb-group-photos/' + upic[0];
                            vpb_setcookie ('group_uphoto', currentPic, 30);

                            $ ("#vcoverPic").html ('<div class="gvprofilephoto" style="background-image: url(' + currentPic + ');" onclick="vpb_popup_photo_box(\'' + group_name + '\', 1, 1, \'' + currentPic + '\');"></div>');
                        }, 1000);
                    }

                    setTimeout (function ()
                    {
                        $ ("#vpb_adding_group_photo_status").html ('');
                    }, 4600);
                    setTimeout (function ()
                    {
                        //$('.modal').modal('hide');
                    }, 5000);
                }
                else
                {
                    document.getElementById ('group_pics').title = '';
                    document.getElementById ('group_pic').value = '';
                    $ ('#vpb_adding_group_photo_status').html (response);
                    return false;
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_update_group_picture('" + group_uid + "');", 10000);
        });
    }
}

// Update Group Picture
function vpb_update_page_picture (group_uid)
{
    var group_name = "Group Photo";

    var ext = $ ('#page_pic').val ().split ('.').pop ().toLowerCase ();

    if (group_uid == "" || group_uid == undefined || group_uid == null)
    {
        document.getElementById ('page_pics').title = '';
        document.getElementById ('page_pic').value = '';
        $ ('#vpb_adding_page_photo_status').html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if ($ ("#page_pic").val () == "")
    {
        return false;
    }
    else if ($.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        document.getElementById ('page_pics').title = '';
        //document.getElementById('group_pic').value = '';
        $ ('#vpb_adding_page_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
        return false;
    }
    else
    {
        $ ("#vpb_adding_page_photo_status").html ('');
        $ ('.vpb_progress_outer_bar').show ();

        var formData = new FormData ();
        formData.append ("page", 'update-page-photo');
        formData.append ("page_id", $ ("#currentPageId").val ());
        formData.append ("profilepic", document.getElementById ('page_pic').files[0]);

        $.ajax ({
            url: '/blab/page/updatePageImage',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            xhr: function ()
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    var xhr = vpb_this_xhr ();
                    xhr.upload.addEventListener ("progress", function (e_vent)
                    {
                        if (e_vent.lengthComputable)
                        {
                            var vpb_current_progress = e_vent.loaded / e_vent.total;
                            vpb_current_progress = parseInt (vpb_current_progress * 100);
                            var vpb_totalPercentage = parseInt (vpb_current_progress) + '%';
                            vpb_progress_percentage.width (vpb_totalPercentage)
                            vpb_progress_inner_bar.html (vpb_totalPercentage);

                            if (vpb_current_progress === 100)
                            {
                                $ ('.vpb_progress_outer_bar').hide ();
                                $ ("#vpb_adding_page_photo_status").html ('<br>' + $ ("#v_loading_btn").val ());
                            }
                        }
                    }, false);
                    return xhr;
                }
            },
            complete: function (xhr)
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    vpb_progress_percentage.width ("100%");
                    vpb_progress_inner_bar.html ("100%");
                    //xhr.responseText;
                }
            },
            success: function (response)
            {
                var objResponse = $.parseJSON (response);


                if (objResponse.sucess == 1)
                {
                    var upic = response.split ('&');
                    document.getElementById ('page_pics').title = '';
                    document.getElementById ('page_pic').value = '';
                    $ ('#vpb-display-page-photo-preview').html ('');

                    $ ("#vpb_adding_page_photo_status").html ('<div class="vsuccess">' + $ ('#successfully_updated_group_photo_text').val () + '</div>');

                    if (upic[0] != "")
                    {
                        setTimeout (function ()
                        {
                            var currentPic = vpb_site_url + 'vpb-group-photos/' + upic[0];
                            vpb_setcookie ('page_uphoto', currentPic, 30);

                            $ ("#vcoverPic").html ('<div class="gvprofilephoto" style="background-image: url(' + currentPic + ');" onclick="vpb_popup_photo_box(\'' + group_name + '\', 1, 1, \'' + currentPic + '\');"></div>');
                        }, 1000);
                    }

                    setTimeout (function ()
                    {
                        $ ("#vpb_adding_page_photo_status").html ('');
                    }, 4600);
                    setTimeout (function ()
                    {
                        //$('.modal').modal('hide');
                    }, 5000);
                }
                else
                {
                    document.getElementById ('page_pics').title = '';
                    document.getElementById ('page_pic').value = '';
                    $ ('#vpb_adding_page_photo_status').html (response);
                    return false;
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_update_page_picture('" + group_uid + "');", 10000);
        });
    }
}

// Preview File(s)
function vpb_profile_photo_preview (vpb_selector_)
{
    var id = 1, last_id = last_cid = '';
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                document.getElementById ('profile_pics').title = '';
                document.getElementById ('profile_pic').value = '';
                $ ('#vpb_adding_profile_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
                return false;
            }
            else
            {
                //Clear previous previewed files and start again
                $ ('#vpb_adding_profile_photo_status').html ('');
                $ ('#vpb-display-profile-photo-preview').html ('');
                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-profile-photo-preview').append (
                            '<div class="vpb_preview_wrapper"> \
				   <img class="vpb_image_style" src="' + e.target.result + '" title="' + escape (file.name) + '" onClick="vpb_view_this_image(\'Photo Preview\', \'' + e.target.result + '\');" style="cursor:pointer; height:150px;" /><br /> \
				   </div>');
                }
                reader.readAsDataURL (file);
            }
        }
        else
        {
            return false;
        }
    });
}

function vpb_this_xhr ()
{
    if (window.XMLHttpRequest)
    {
        return new XMLHttpRequest ();
    }
    try {
        return new ActiveXObject ('MSXML2.XMLHTTP.6.0');
    } catch (e) {
        try {
            return new ActiveXObject ('MSXML2.XMLHTTP.3.0');
        } catch (e) {
            return '';
        }
    }
}


// Update Profile Picture
function vpb_update_profile_picture (my_identity)
{
    var vpb_page_owner = $ ("#vpb_page_owner").val ();
    var ext = $ ('#profile_pic').val ().split ('.').pop ().toLowerCase ();

    if (my_identity == "" || my_identity == undefined || my_identity == null)
    {
        document.getElementById ('profile_pics').title = '';
        document.getElementById ('profile_pic').value = '';
        $ ('#vpb_adding_profile_photo_status').html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if ($ ("#profile_pic").val () == "")
    {
        return false;
    }
    else if ($.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        document.getElementById ('profile_pics').title = '';
        //document.getElementById('profile_pic').value = '';
        $ ('#vpb_adding_profile_photo_status').html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
        return false;
    }
    else
    {
        $ ("#vpb_adding_profile_photo_status").html ('');
        $ ('.vpb_progress_outer_bar').show ();

        var formData = new FormData ();
        formData.append ("page", 'update-user-photo');
        formData.append ("userid", my_identity);
        formData.append ("profilepic", document.getElementById ('profile_pic').files[0]);

        $.ajax ({
            url: '/blab/user/uploadProfile',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            xhr: function ()
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    var xhr = vpb_this_xhr ();
                    xhr.upload.addEventListener ("progress", function (e_vent)
                    {
                        if (e_vent.lengthComputable)
                        {
                            var vpb_current_progress = e_vent.loaded / e_vent.total;
                            vpb_current_progress = parseInt (vpb_current_progress * 100);
                            var vpb_totalPercentage = parseInt (vpb_current_progress) + '%';
                            vpb_progress_percentage.width (vpb_totalPercentage)
                            vpb_progress_inner_bar.html (vpb_totalPercentage);

                            if (vpb_current_progress === 100)
                            {
                                $ ('.vpb_progress_outer_bar').hide ();
                                $ ("#vpb_adding_profile_photo_status").html ('<br>' + $ ("#v_loading_btn").val ());
                            }
                        }
                    }, false);
                    return xhr;
                }
            },
            complete: function (xhr)
            {
                var vpb_progress_percentage = $ ('.vpb_progress_inner_bar');
                var vpb_progress_inner_bar = $ ('.vpb_progress_percentage');
                if (vpb_this_xhr () == "")
                {
                    vpb_progress_inner_bar.hide ();
                }
                else
                {
                    vpb_progress_percentage.width ("100%");
                    vpb_progress_inner_bar.html ("100%");
                    //xhr.responseText;
                }
            },
            success: function (response)
            {
                var response_brought = response.indexOf ('completed');
                if (response_brought != -1)
                {
                    var upic = response.split ('&');
                    document.getElementById ('profile_pics').title = '';
                    document.getElementById ('profile_pic').value = '';
                    $ ('#vpb-display-profile-photo-preview').html ('');

                    $ ("#vpb_adding_profile_photo_status").html ('<div class="vsuccess">' + $ ('#successfully_updated_profile_photo_text').val () + '</div>');
                    setTimeout (function ()
                    {
                        $ ("#vp_profile_photo").html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="30" height="26" align="absmiddle">');

                        $ ("#vp_profile_wall_photo").html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="40" height="40" align="absmiddle">');

                        if ($ ("#page_id").val () == "group")
                        {
                            $ ("#vp_user_photo_on_group_page").html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="30" height="26" align="absmiddle">');
                        }
                        else
                        {
                            if (my_identity == vpb_page_owner)
                            {
                                $ ("#updateProfilePic").html ('<div class="profilephoto_wrap"><div class="vprofilephoto" style="background-image: url(' + vpb_site_url + 'photos/' + upic[0] + ');" onclick="vpb_popup_photo_box(\'' + my_identity + '\', 1, 1, \'' + vpb_site_url + 'photos/' + upic[0] + '\');"></div><div class="vprofilephoto_editer" data-backdrop="static" data-toggle="modal" data-target="#add-profile-photo"><i class="fa fa-camera"></i> Update Photo</div></div>');
                            }
                            else
                            {
                            }
                        }

                        $ (".v_status_pics_" + my_identity).html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="45" height="45" align="absmiddle">');
                        $ (".v_status_pic_" + my_identity).html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="34" height="34" align="absmiddle">');

                        $ (".v_status_picture_" + my_identity).html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="35" height="35" align="absmiddle">');

                        $ (".v_status_pictured_" + my_identity).html ('<img src="' + vpb_site_url + 'photos/' + upic[0] + '" border="0" width="24" height="24" align="absmiddle">');

                        // replace the user photos in current chat boxes
                        $ ('.vchat_uphoto_' + my_identity).attr ('src', vpb_site_url + "photos/" + upic[0]);

                        $ ("#vpb_session_pic_" + my_identity).val (vpb_site_url + 'photos/' + upic[0]);
                    }, 1000);
                    setTimeout (function ()
                    {
                        $ ("#vpb_adding_profile_photo_status").html ('');
                    }, 4600);
                    setTimeout (function ()
                    {
                        //$('.modal').modal('hide');
                    }, 5000);
                }
                else
                {
                    document.getElementById ('profile_pics').title = '';
                    document.getElementById ('profile_pic').value = '';
                    $ ('#vpb_adding_profile_photo_status').html (response);
                    return false;
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_update_profile_picture('" + my_identity + "');", 10000);
        });
    }
}


//Add Files to Message
function vpb_add_files_to_message (id, group_id)
{    
    //var username = vpb_trim (vpb_strip_tags ($ ("#session_uid").val ()));

    var group_id = vpb_getcookie ('v_group_id') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_id')));
    //var group_manager = vpb_getcookie ('v_group_manager') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_manager')));
    //var group_username = vpb_getcookie ('group_username') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_username')));
    //var group_fullname = vpb_getcookie ('group_fullname') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_fullname')));

    if (group_id == '')
    {
        var group_id = $ (".selectedChat").attr ("user-id");
        var group_type = 'user';
    }
    else
    {
        var group_type = 'group';
    }

    if (id == "")
    {
        $ ("#v_sending_message_status").html ('');
        $ ("#vpb_bottom_box").removeClass ('disable_this_box');
        $ ("#vpb_bottom_box").addClass ('enable_this_box');
        showErrorMessage ();
        return false;
    }
    else
    {
        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('add_photos').files;
        var vpb_file = document.getElementById ('add_files').files;
        // Create a formdata object and append the files
        var vpb_data = new FormData ();
        var vpb_datas = new FormData ();
        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });
        $.each (vpb_file, function (keys, values)
        {
            vpb_data.append (keys, values);
        });
        var photos = $ ("#add_photos").val ();
        var files = $ ("#add_files").val ();
        if (photos != "")
        {
            vpb_data.append ("message_id", id);
            vpb_data.append ("group_type", group_type);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("page", 'vpb_add_images_to_message');

        }
        else if (files != "")
        {
            vpb_data.append ("message_id", id);
            vpb_data.append ("message_id", id);
            vpb_data.append ("group_type", group_type);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("page", 'vpb_add_files_to_message');
        }
        else
        {
            vpb_data.append ("message_id", id);
            vpb_data.append ("message_id", id);
            vpb_data.append ("group_type", group_type);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("page", 'vpb_add_none_to_message');
        }

        $.ajax ({
            url: '/blab/chat/uploadChatFile',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {},
            success: function (response)
            {
                $ ("#v_sending_message_status").html ('');
                $ ("#vpb_bottom_box").removeClass ('disable_this_box');
                $ ("#vpb_bottom_box").addClass ('enable_this_box');

//                var response_brought = response.indexOf ('message_no_found');
//                if (response_brought != -1)
//                {
//                    $ ("#v-pms-message").html ($ ("#general_system_error").val ());
//                    $ ("#v-pms-alert-box").click ();
//                    return false;
//                }
//                else
//                {
                vpb_load_system_users ('new');

                $ ("#add_a_file_button").show ();
                $ ("#add_a_photo_button").show ();
                $ ("#add_a_video_button").show ();

                document.getElementById ('add_video_url').value = '';
                document.getElementById ('vpb-display-video').innerHTML = '';
                $ ("#added_video_url").val ('');
                $ ("#vpb_video").html ('');
                $ ("#vpb_added_videos").slideUp ();

                $ ('#vpb-display-attachment-preview').html ('');
                $ ('#vpb_photos').html ('');
                $ ('#add_photos').val ('');
                $ ('#add_files').val ('');
                $ ("#vpb_added_photos").hide ();

                $ ("#tag_people_button").removeClass ('vfooter_wraper_active');
                $ ("#tag_people_button").addClass ('vfooter_wraper');

                //vpb_exit_compose_new_message ();

                document.getElementById ("vpb_pms_message_data").placeholder = $ ("#write_a_reply").val ();
                $ ("#vpb_pms_message_data").val ('').val ('').change ();

                setTimeout (function ()
                {
                    $ ('#additional_size').val (0);
                    vpb_set_box_height ();

                    $ ("#vpb_display_group_messages").append (
                            $ (response).hide ().fadeIn ('slow')
                            );


                    setTimeout (function ()
                    {
                        $ ("#vpb_default_conversation").animate ({scrollTop: parseInt ($ ('#vpb_default_conversation').height ()) + 10000}, 1000);
                    }, 100);
                }, 100);

            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_add_files_to_message('" + parseInt (id) + "');", 1000);
        });
    }
}

function vpb_submit_share_status_update ()
{
    var post_id = $ ("#the_post_to_share_id").val ();
    var post_owner_username = $ ("#v_owner_of_post").val ();
    var where_to_share = $ ("#selected_option_shared_").val ();
    var selected_option_shared_privacy = $ ("#selected_option_shared_privacy_").val ();
    var selected_friend_to_share_with = $ ("#selected_friend_to_share_with").val ();
    var vpb_wall_share_post_data = $ ("#vpb_wall_share_post_data").val ();
    var page_id = $ ("#page_id").val ();

    $ ("#sharing_update_status").html ($ ("#v_sending_btn").val ());
    $ ("#v-wall-share-this-post").removeClass ('enable_this_box');
    $ ("#v-wall-share-this-post").addClass ('disable_this_box');

    var dataString = {"post_id": post_id, "where_to_share": where_to_share, "selected_friend_to_share_with": selected_friend_to_share_with, "selected_option_shared_privacy": selected_option_shared_privacy, "page_id": page_id, "vpb_wall_share_post_data": vpb_wall_share_post_data};

    $.post ('/blab/post/clonePost', dataString, function (response)
    {
        $ ("#v-wall-share-this-post").removeClass ('disable_this_box');
        $ ("#v-wall-share-this-post").addClass ('enable_this_box');

        try {
            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                $ ("#v-wall-message").html (response);
                $ ("#v-wall-alert-box").click ();
                return false;
            }
        } catch (error) {

            $ ("#posts-list").prepend (response);
            $ ("#v_cancel_sharing_this_update").click ();

            //vpb_removal_this_friend ();
            setTimeout (function ()
            {
                $ ("#v_share_publicly_button").click (); // Set the privacy settings back to default public
                $ ("#on_my_wall_button").click (); // Reset where to share to my-wall
                $ ("#selected_friend_to_share_with").val (''); // Empty the friend whom to share with field
                $ ("#vpb_wall_share_post_data").val (''); // Empty any text which has just been shared
                $ ("#sharing_update_status").html (''); // Stop the sending status
                $ ("#v-wall-message").html ($ ("#the_status_update_was_successfully_shared_message").val ()); // Show the success shared status
                $ ("#v-wall-alert-box").click (); // Pop up the success box


                setTimeout (function ()
                {
                    $ ("#vclose_info_box_button").click ();
                }, 5000);
            }, 100);
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_submit_share_status_update();", 10000);
    });

    return false;
}

function extracturl ()
{
    var val = document.getElementById ("comment").value;

    if (val != "" && val.indexOf ("://") > -1)
    {
        $ ('#output').text ('loading...');
        $.ajax ({
            type: 'post',
            url: '/blab/index/extractUrl',
            data: {
                link: val
            },
            cache: false,
            success: function (response)
            {
                document.getElementById ("vpb_video").innerHTML = response;
                $ ("#vpb_added_videos").show ();
            }
        });
    }
}

$ (document).off ("click", ".addNewFriend");
$ (document).on ('click', '.addNewFriend', function ()
{
    var friendId = $ (this).attr ("friendid");

    $.ajax ({
        url: "/blab/friend/addFriend",
        type: 'POST',
        data: {friendId: friendId},
        success: function (response)
        {
        }
    });

    return false;
});

$ (document).off ("click", ".Share");
$ (document).on ('click', '.Share', function ()
{
    var messageId = $ (this).attr ("messageId");
    $ ("#v-wall-share-this-post").modal ("show");

    $ ("#the_post_to_share_id").val (parseInt (messageId));
    $ ("#v_share_this_conts").html ('<br>' + $ ("#v_loading_btn").val () + '<br><br>');
    $ ("#v-wall-share-this-alert-box").click ();

    var dataString = {"post_id": messageId};

    $.post ('/blab/post/sharePost', dataString, function (response)
    {
        $ ("#vpb_share_privacy_box").css ('display', 'inline-block'); // Show privacy settings option
        $ ("#v_top_share_this_").show (); // Show where to share the post option box at the top of the popup
        $ ("#v_say_something_about_this_share_box").show (); // Show say something about this option
        $ ("#v_share_now_button").show (); // Show the share button

        $ ("#v_share_this_conts").html (response);
    }).fail (function (error_response)
    {
        //setTimeout("vpb_share_this_status_update_box('"+username+"', '"+post_id+"', '"+post_owner_username+"');", 10000);
    });

//    $ ("#vpb_share_privacy_box").css ('display', 'inline-block'); // Show privacy settings option
//    $ ("#v_top_share_this_").show (); // Show where to share the post option box at the top of the popup
//    $ ("#v_say_something_about_this_share_box").show (); // Show say something about this option
//    $ ("#v_share_now_button").show (); // Show the share button
//
//    $ ("#v_share_this_conts").html ('test');
//
//    alert (messageId);

    return false;
});

$ (document).off ("click", ".SaveShare");
$ (document).on ('click', '.SaveShare', function ()
{

    var messageId = $ (this).attr ("messageId");

    $.ajax ({
        url: '/blab/post/clonePost/' + messageId,
        type: 'GET',
        success: function (response)
        {
            displaySharedComment (response);

        }
    });

    return false;

});

$ (document).off ("keyup", "#chatUserSearchBox");
$ (document).on ('keyup', '#chatUserSearchBox', function ()
{
    var searchText = $ (this).val ();

    $.ajax ({
        url: "/blab/chat/searchChatUsers",
        type: 'POST',
        data: {searchText: searchText},
        success: function (response)
        {
            $ (".panel-chat").hide ();
            $ (".category-list").html (response);
        }
    });

    return false;
});

$ (document).off ("click", ".deletePost");
$ (document).on ('click', '.deletePost', function ()
{
    var deleteId = $ (this).attr ("deleteid");

    if (!deleteId)
    {
        showErrorMessage ();
    }

    $.ajax ({
        type: 'POST',
        url: '/blab/post/deletePost',
        data: {id: deleteId},
        success: function (msg)
        {
            $ (".social-feed-box[postid=" + deleteId + "]").fadeOut (300, function ()
            {
                $ (this).remove ();
            });

        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });



    return false;
});

$ (document).off ("click", ".ignorePost");
$ (document).on ('click', '.ignorePost', function ()
{
    var deleteId = $ (this).attr ("deleteid");

    if (!deleteId)
    {
        showErrorMessage ();
    }


    $.ajax ({
        type: 'POST',
        url: '/blab/post/ignorePost',
        data: {id: deleteId},
        success: function (msg)
        {
            $ (".social-feed-box[postid=" + deleteId + "]").fadeOut (300, function ()
            {
                //$ (this).remove ();
                $ (".social-feed-box[postid=" + parseInt (deleteId) + "]").hide ();
                $ ("#vpb_hidden_post_id_" + parseInt (deleteId)).fadeIn ();
            });

        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });

    return false;
});

$ (document).off ("click", ".ignoreComment");
$ (document).on ('click', '.ignoreComment', function ()
{
    var deleteId = $ (this).attr ("deleteid");

    if (!deleteId)
    {
        showErrorMessage ();
    }
    $.ajax ({
        type: 'POST',
        url: '/blab/comment/ignoreComment',
        data: {id: deleteId},
        success: function (msg)
        {
            $ (".comment-a1[commentid=" + deleteId + "]").fadeOut (300, function ()
            {
                $ (".comment-a1[commentid=" + parseInt (deleteId) + "]").hide ();
                $ ("#vpb_hidden_comment_id_" + parseInt (deleteId)).fadeIn ();
            });

        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });

    return false;
});

$ (document).off ("click", ".deleteComment");
$ (document).on ('click', '.deleteComment', function ()
{
    var deleteId = $ (this).attr ("deleteid");

    if (!deleteId)
    {
        showErrorMessage ();
    }

    swal ({
        title: "Are you sure?",
        text: "You will not be able to recover this comment!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function ()
    {

        $.ajax ({
            type: 'POST',
            url: '/blab/comment/deleteComment',
            data: {id: deleteId},
            success: function (msg)
            {
                $ (".comment-a1[commentid=" + deleteId + "]").fadeOut (300, function ()
                {
                    $ (this).remove ();
                });

                swal ("Deleted!", "Your comment has been deleted.", "success");
            }
            ,
            error: function ()
            {
                showErrorMessage ();
            }
        });
    });

    return false;
});

$ (document).off ("paste", "textarea");
$ (document).on ('paste', 'textarea', function ()
{
    if ($ (this).attr ("id") == "comment")
    {
        extracturl ();
    }
});

$ (document).off ("keyup", "textarea");
$ (document).on ('keyup', 'textarea', function ()
{
    if ($ (this).attr ("id") == "comment")
    {
        extracturl ();
    }
    $ (this).height (30);
    $ (this).height (this.scrollHeight + parseFloat ($ (this).css ("borderTopWidth")) + parseFloat ($ (this).css ("borderBottomWidth")));

});

/**
 * 
 * @param {type} userId
 * @param {type} element
 * @returns {undefined}
 */
function buildFriendList (userId, element)
{
    userId = userId || null;

    $.ajax ({
        url: '/blab/friend/pollFriendList',
        async: true, // by default, it's async, but...
        dataType: 'json', // or the dataType you are working with
        cache: false

    }).done (function (eventList)
    {
        try {
            $ ("#friendRequests > #counter").html (eventList.count);
            var HTML = '';
            $.each (eventList.data, function (key, value)
            {
                HTML += ' <div id="requestsBar1">' +
                        '<img class="img" src="/blab/public/uploads/profile/' + value.username + '.jpg">' +
                        '<div id="info1"><a href="profile?username=' + value.username + '">' + value.fname + ' ' + value.lname + '</a>' +
                        '<div class="buttons">';
                if (value.friend_status == 'add')
                {
                    HTML += '<button friend-id="' + value.user_id + '" type="button" class="btn btn-success btn-xs addAFriend" onclick="addNewFriendRequest(' + value.user_id + ')">Add Friend</button>';
                }
                else if (value.friend_status == 'pending')
                {
                    HTML += '<button friend-id="' + value.user_id + '" type="button" class="btn btn-warning btn-xs doFriendConfirmation" onclick="confirmFriend(' + value.user_id + ')">Accept</button>';

                }
                else if (value.friend_status == 'requested')
                {
                    HTML += '<button friend-id="' + value.user_id + '" type="button" class="btn btn-warning btn-xs">Pending</button>';
                    HTML += '<span title="Cancel Request" id="cancelrequest_' + value.user_id + '" onclick="vpb_friend_ship(\'' + value.user_id + '\', \'vasplusblog\', \'cancelrequest\');" class="cbtn vpb_cbtn"><i class="fa fa-times"></i></span>';
                }
                else if (value.friend_status == 'friend')
                {
                    HTML += '<button friend-id="' + value.user_id + '" type="button" class="btn btn-primary btn-xs">Friend</button>';
                }


                HTML += '</div>';
                HTML += '</div>';
                HTML += '</div>';

            });


            $ (element).html ('<div class="midContent-inner2" style="float:right; width:100%; font-weight:normal !important; font-size:13px !important;">' + HTML + '</div>');
        } catch (error) {

        }
        //var eventList = $.parseJSON (eventList);
    });
}

function retrieveFriendRequests ()
{
    buildFriendList ('', '.view-friendRequest-body');
    $ ("#view-FriendRequests").modal ("show");
}


function pollTask ()
{

    buildFriendList ('', '#midContent');

}

function showPreview (objFileInput)
{
    hideUploadOption ();
    if (objFileInput.files[0])
    {
        var fileReader = new FileReader ();
        fileReader.onload = function (e)
        {
            $ ("#targetOuter").find (".icon-choose-image").hide ();
            $ ("#targetLayer").html ('<img src="' + e.target.result + '" width="400px" height="300px" class="upload-preview" />');
            $ ("#targetLayer").css ('opacity', '0.7');
            $ (".icon-choose-image").css ('opacity', '0.5');
        }
        fileReader.readAsDataURL (objFileInput.files[0]);
    }
}
function showUploadOption ()
{
    $ ("#profile-upload-option").css ('display', 'block');
}

function hideUploadOption ()
{
    $ ("#profile-upload-option").css ('display', 'none');
}

function removeProfilePhoto ()
{
    hideUploadOption ();
    $ ("#userImage").val ('');
    $.ajax ({
        url: "remove",
        type: "POST",
        data: new FormData (this),
        beforeSend: function ()
        {
            $ ("#body-overlay").show ();
        },
        contentType: false,
        processData: false,
        success: function (data)
        {
            $ ("#targetLayer").html ('');
            setInterval (function ()
            {
                $ ("#body-overlay").hide ();
            }, 500);
        },
        error: function ()
        {
        }
    });
}

var usersLocation = '';

function showLocation (position)
{
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    $.ajax ({
        type: 'POST',
        url: '/blab/index/getLocation',
        data: 'latitude=' + latitude + '&longitude=' + longitude,
        success: function (msg)
        {
            if (msg)
            {
                usersLocation = msg;
            }

        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
}

function vpb_trim (s)
{
    return s.replace (/^\s+|\s+$/, '');
}


function vpb_location_suggestions (locationTyped)
{
    if (locationTyped.length > 0)
    {
        $ ("#vpb-tagged-people-in-post-server-response").html ('');

        $ ("#vpb-location-server-response").html ('<div class="dropdown"><ul class="dropdown-menu vpb-hundred-percent_loading" style="top:auto;"><li class="dropdown-header vpb_wall_loading_text">' + $ ("#v_loading_btn").val () + '</li></ul></div>');


        var dataString = {'location': locationTyped};

        $.post ('/blab/index/locationSearch', dataString, function (response)
        {

            if (response == "")
            {
                $ ("#vpb-location-server-response").html ('');
            }
            else
            {
                $ ("#vpb-location-server-response").html ('<div class="dropdown"><ul class="dropdown-menu vpb-hundred-percent v_overflow_this_field" id="location_suggestion_box" style="top:auto;">' + response + '</ul></div>');
            }


        }).fail (function (error_response)
        {
            setTimeout ("vpb_location_suggestions('" + locationTyped + "');", 10000);
        });
    }
    else
    {
        $ ("#vpb-location-server-response").html ('');
        return false;
    }
}

function vpb_selected_location (location)
{
    $ ("#comment").val ('Is In ' + location);
//	$("#the_selected_location").html(location);
//	
//	$("#the_selected_location").data('title', location);
//    $("#the_selected_location").attr('title', location);

    $ ("#vpb-location-server-response").html ('');
    vpb_hide_other_boxes ();

    if (vpb_trim ($ ("#the_selected_location").text ()) != "")
    {
        $ ("#user_selected_this_location").fadeIn ();
        $ ("#user_selected_this_location").css ('display', 'inline-block');
    }
    else
    {
    }
}

// Show site menu
function vpb_show_site_menu ()
{
    $ ("#v_actions_box").hide ();

    if ($ ("#v_site_menu_box").css ('display') == "none")
    {
        $ ("#friendRequests_bar").hide ();
        $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
        $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

        $ (".dropdown-menu2").hide ();
        $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
        $ ("#v_notifications").addClass ('vpb_notifications_icon');

        $ ("#v_message_box").hide ();
        $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
        $ ("#v_new_messages").addClass ('vpb_notifications_icon');


        $ ("#v_site_menu_box").show ();
        $ ("#v_site_menu").removeClass ('vpb_notifications_icon');
        $ ("#v_site_menu").addClass ('vpb_notifications_icon_active');
    }
    else
    {
        $ ("#v_site_menu_box").hide ();
        $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
        $ ("#v_site_menu").addClass ('vpb_notifications_icon');
    }
}

//Add smiley to wall post box when clicked on
function vpb_add_smiley_to_wall_status (smiley)
{
    var old_pms_message = $ ("#comment").val ();
    if (old_pms_message == "")
    {
        $ ("#comment").focus ();
        $ ("#comment").val (smiley);
    }
    else
    {
        $ ("#comment").focus ();
        $ ("#comment").val (old_pms_message + ' ' + smiley);
    }

    $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
    $ ("#add_smile_button").addClass ('vfooter_wraper');
    $ ("#vpb_the_wall_smiley_box").hide ();
}
//Add smiley to wall comment box when clicked on
function vpb_add_smiley_to_comment (smiley)
{
    var post_id = $ ("#smiley_identifier").val ();

    var old_pms_message = $ (".reply-comment[comment-id=" + post_id + "]").val ();
    if (old_pms_message == "")
    {
        $ (".reply-comment[comment-id=" + post_id + "]").click ();
        $ (".reply-comment[comment-id=" + post_id + "]").focus ();
        $ (".reply-comment[comment-id=" + post_id + "]").val (smiley);
    }
    else
    {
        $ (".reply-comment[comment-id=" + post_id + "]").click ();
        $ (".reply-comment[comment-id=" + post_id + "]").focus ();
        $ (".reply-comment[comment-id=" + post_id + "]").val (old_pms_message + ' ' + smiley);
    }

}

function vasplus_wall_special_text (ivtech)
{
    var vpb_new_text = ivtech.replace (/\"/gi, "&quot;");
    vpb_new_text = vpb_new_text.replace (/\</gi, "&lt;");
    vpb_new_text = vpb_new_text.replace (/\>/gi, "&gt;");
    vpb_new_text = vpb_new_text.replace (/\ /gi, "&nbsp;");
    vpb_new_text = vpb_new_text.replace (/\Â¡/gi, "&iexcl;");
    vpb_new_text = vpb_new_text.replace (/\Â¢/gi, "&cent;");
    vpb_new_text = vpb_new_text.replace (/\Â£/gi, "&pound;");
    vpb_new_text = vpb_new_text.replace (/\Â¤/gi, "&curren;");
    vpb_new_text = vpb_new_text.replace (/\Â¥/gi, "&yen;");
    vpb_new_text = vpb_new_text.replace (/\Â¦/gi, "&brvbar;");
    vpb_new_text = vpb_new_text.replace (/\Â§/gi, "&sect;");
    vpb_new_text = vpb_new_text.replace (/\Â¨/gi, "&uml;");
    vpb_new_text = vpb_new_text.replace (/\Â©/gi, "&copy;");
    vpb_new_text = vpb_new_text.replace (/\Âª/gi, "&ordf;");
    vpb_new_text = vpb_new_text.replace (/\Â«/gi, "&laquo;");
    vpb_new_text = vpb_new_text.replace (/\Â¬/gi, "&not;");
    vpb_new_text = vpb_new_text.replace (/\Â®/gi, "&reg;");
    vpb_new_text = vpb_new_text.replace (/\Â°/gi, "&deg;");
    vpb_new_text = vpb_new_text.replace (/\Â±/gi, "&plusmn;");
    vpb_new_text = vpb_new_text.replace (/\Â²/gi, "&sup2;");
    vpb_new_text = vpb_new_text.replace (/\Â³/gi, "&sup3;");
    vpb_new_text = vpb_new_text.replace (/\Â´/gi, "&acute;");
    vpb_new_text = vpb_new_text.replace (/\Âµ/gi, "&micro;");
    vpb_new_text = vpb_new_text.replace (/\Â¶/gi, "&para;");
    vpb_new_text = vpb_new_text.replace (/\Â·/gi, "&middot;");
    vpb_new_text = vpb_new_text.replace (/\Â¹/gi, "&sup1;");
    vpb_new_text = vpb_new_text.replace (/\Âº/gi, "&ordm;");
    vpb_new_text = vpb_new_text.replace (/\Â»/gi, "&raquo;");
    vpb_new_text = vpb_new_text.replace (/\Â¼/gi, "&frac14;");
    vpb_new_text = vpb_new_text.replace (/\Â½/gi, "&frac12;");
    vpb_new_text = vpb_new_text.replace (/\Â¾/gi, "&frac34;");
    vpb_new_text = vpb_new_text.replace (/\Â¿/gi, "&iquest;");
    vpb_new_text = vpb_new_text.replace (/\Ã€/gi, "&Agrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã/gi, "&Aacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã‚/gi, "&Acirc;");
    vpb_new_text = vpb_new_text.replace (/\Ãƒ/gi, "&Atilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã„/gi, "&Auml;");
    vpb_new_text = vpb_new_text.replace (/\Ã…/gi, "&Aring;");
    vpb_new_text = vpb_new_text.replace (/\Ã†/gi, "&AElig;");
    vpb_new_text = vpb_new_text.replace (/\Ã‡/gi, "&Ccedil;");
    vpb_new_text = vpb_new_text.replace (/\Ãˆ/gi, "&Egrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã‰/gi, "&Eacute;");
    vpb_new_text = vpb_new_text.replace (/\ÃŠ/gi, "&Ecirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã‹/gi, "&Euml;");
    vpb_new_text = vpb_new_text.replace (/\ÃŒ/gi, "&Igrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã/gi, "&Iacute;");
    vpb_new_text = vpb_new_text.replace (/\ÃŽ/gi, "&Icirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã/gi, "&Iuml;");
    vpb_new_text = vpb_new_text.replace (/\Ã/gi, "&ETH;");
    vpb_new_text = vpb_new_text.replace (/\Ã‘/gi, "&Ntilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã’/gi, "&Ograve;");
    vpb_new_text = vpb_new_text.replace (/\Ã“/gi, "&Oacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã”/gi, "&Ocirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã•/gi, "&Otilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã–/gi, "&Ouml;");
    vpb_new_text = vpb_new_text.replace (/\Ã—/gi, "&times;");
    vpb_new_text = vpb_new_text.replace (/\Ã˜/gi, "&Oslash;");
    vpb_new_text = vpb_new_text.replace (/\Ã™/gi, "&Ugrave;");
    vpb_new_text = vpb_new_text.replace (/\Ãš/gi, "&Uacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã›/gi, "&Ucirc;");
    vpb_new_text = vpb_new_text.replace (/\Ãœ/gi, "&Uuml;");
    vpb_new_text = vpb_new_text.replace (/\Ã/gi, "&Yacute;");
    vpb_new_text = vpb_new_text.replace (/\Ãž/gi, "&THORN;");
    vpb_new_text = vpb_new_text.replace (/\ÃŸ/gi, "&szlig;");
    vpb_new_text = vpb_new_text.replace (/\Ã /gi, "&agrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã¡/gi, "&aacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã¢/gi, "&acirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã£/gi, "&atilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã¤/gi, "&auml;");
    vpb_new_text = vpb_new_text.replace (/\Ã¥/gi, "&aring;");
    vpb_new_text = vpb_new_text.replace (/\Ã¦/gi, "&aelig;");
    vpb_new_text = vpb_new_text.replace (/\Ã§/gi, "&ccedil;");
    vpb_new_text = vpb_new_text.replace (/\Ã¨/gi, "&egrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã©/gi, "&eacute;");
    vpb_new_text = vpb_new_text.replace (/\Ãª/gi, "&ecirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã«/gi, "&euml;");
    vpb_new_text = vpb_new_text.replace (/\Ã¬/gi, "&igrave;");
    vpb_new_text = vpb_new_text.replace (/\Ã­/gi, "&iacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã®/gi, "&icirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã¯/gi, "&iuml;");
    vpb_new_text = vpb_new_text.replace (/\Ã°/gi, "&eth;");
    vpb_new_text = vpb_new_text.replace (/\Ã±/gi, "&ntilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã²/gi, "&ograve;");
    vpb_new_text = vpb_new_text.replace (/\Ã³/gi, "&oacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã´/gi, "&ocirc;");
    vpb_new_text = vpb_new_text.replace (/\Ãµ/gi, "&otilde;");
    vpb_new_text = vpb_new_text.replace (/\Ã¶/gi, "&ouml;");
    vpb_new_text = vpb_new_text.replace (/\Ã·/gi, "&divide;");
    vpb_new_text = vpb_new_text.replace (/\Ã¸/gi, "&oslash;");
    vpb_new_text = vpb_new_text.replace (/\Ã¹/gi, "&ugrave;");
    vpb_new_text = vpb_new_text.replace (/\Ãº/gi, "&uacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã»/gi, "&ucirc;");
    vpb_new_text = vpb_new_text.replace (/\Ã¼/gi, "&uuml;");
    vpb_new_text = vpb_new_text.replace (/\Ã½/gi, "&yacute;");
    vpb_new_text = vpb_new_text.replace (/\Ã¾/gi, "&thorn;");
    vpb_new_text = vpb_new_text.replace (/\Ã¿/gi, "&yuml;");
    vpb_new_text = vpb_new_text.replace (/\â‚¬/gi, "&euro;");
    return vpb_new_text;
}
/* Add break to contents */function vpb_wall_nl2br (vdata, vxml)
{
    var createNewLines = (vxml || typeof vxml === 'undefined') ? '<br ' + '/>' : '<br>';
    return (vdata + '').replace (/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + createNewLines + '$2');
}/* Add links to chat URLs */
function vpb_wall_create_link (vpb_chat_text)
{
    var vpb_converted_links, vpb_https_http_ftp_links, vpb_www_links, vpb_email_links;
    vpb_https_http_ftp_links = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    vpb_converted_links = vpb_chat_text.replace (vpb_https_http_ftp_links, '<a style="color: blue;" class="hover_text" href="$1" target="_blank">$1</a>');
    pb_www_links = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    vpb_converted_links = vpb_converted_links.replace (vpb_www_links, '$1<a style="color: blue;" class="hover_text" href="http://$2" target="_blank">$2</a>');
    vpb_email_links = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    vpb_converted_links = vpb_converted_links.replace (vpb_email_links, '<a style="color: blue;" class="hover_text" href="mailto:$1" target="_blank">$1</a>');
    return vpb_converted_links;
}/* Add smilies to chat */
function vpb_wall_add_smilies (vpb_chat_text)
{
    var vpb_wall_text_smiles = vpb_chat_text, vpb_a_smilies = [{vpb_smiley_symbol: ':)', vpb_smiley_im: '<img src="/blab/public/img/smileys/smile.png" title="Smile" align="absmiddle">'}, {vpb_smiley_symbol: ':(', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/frown.png" title="Frown, Sad" align="absmiddle">'}, {vpb_smiley_symbol: 'O:)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/angel.png" title="Blushing angel" align="absmiddle">'}, {vpb_smiley_symbol: ':cat-face:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/colonthree.png" title="Cat face" align="absmiddle">'}, {vpb_smiley_symbol: 'o.O', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/confused.png" title="Confused" align="absmiddle">'}, {vpb_smiley_symbol: 'O.o', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/confused.png" title="Confused" align="absmiddle">'}, {vpb_smiley_symbol: ':cry:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/cry.png" title="Cry" align="absmiddle">'}, {vpb_smiley_symbol: ':laughing-devil:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/devil.png" title="Laughing devil" align="absmiddle">'}, {vpb_smiley_symbol: ':O', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/gasp.png" title="Shocked and surprised" align="absmiddle">'}, {vpb_smiley_symbol: 'B)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/glasses.png" title="Glasses" align="absmiddle">'}, {vpb_smiley_symbol: ':D', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/grin.png" title="Grin, Big Smile" align="absmiddle">'}, {vpb_smiley_symbol: ':grumpy:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/grumpy.png" title="Upset and angry" align="absmiddle">'}, {vpb_smiley_symbol: ':heart:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/heart.png" title="Heart" align="absmiddle">'}, {vpb_smiley_symbol: '^_^', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/kiki.png" title="Kekeke happy" align="absmiddle">'}, {vpb_smiley_symbol: ':kiss:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/kiss.png" title="Kiss" align="absmiddle">'}, {vpb_smiley_symbol: ':v', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/pacman.png" title="Pacman" align="absmiddle">'}, {vpb_smiley_symbol: ':penguin:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/penguin.gif" title="Penguin" align="absmiddle">'}, {vpb_smiley_symbol: ':unsure:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/unsure.png" title="Unsure" align="absmiddle">'}, {vpb_smiley_symbol: 'B|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: 'B-|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '8-|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '8|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '-_-', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/squint.png" title="Annoyed, sighing or bored" align="absmiddle">'}, {vpb_smiley_symbol: ':lve:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/red_heart_love.gif" title="Love" align="absmiddle">'}, {vpb_smiley_symbol: ':putnam:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/putnam.gif" title="Christopher Putnam" align="absmiddle">'}, {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/wink.png" title="Wink" align="absmiddle">'}, {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/wink.png" title="Wink" align="absmiddle">'}, {vpb_smiley_symbol: '(off)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/no_idea.gif" title="No idea" align="absmiddle">'}, {vpb_smiley_symbol: '(on)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/got_idea.gif" title="Got an idea" align="absmiddle">'}, {vpb_smiley_symbol: ':tea-cup:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/cup_of_tea.png" title="Cup of tea" align="absmiddle">'}, {vpb_smiley_symbol: '(n)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/no_thumbs_down.gif" title="No, thumb down" align="absmiddle">'}, {vpb_smiley_symbol: '(y)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/yes_thumbs_up.gif" title="Yes, thumb up" align="absmiddle">'}, {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/shark.gif" title="Shark" align="absmiddle">'}, {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}, {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}];
    for (var i = 0; i < vpb_a_smilies.length; i++) {
        vpb_wall_text_smiles = vpb_wall_text_smiles.replace (vpb_a_smilies[i].vpb_smiley_symbol, vpb_a_smilies[i].vpb_smiley_im);
    }
    return vpb_wall_text_smiles;
}

/* Add links to chat URLs */
function vpb_wall_create_link (vpb_chat_text)
{

}


/* Add break to contents */
function vpb_wall_nl2br (vdata, vxml)
{
    var createNewLines = (vxml || typeof vxml === 'undefined') ? '<br ' + '/>' : '<br>';
    return (vdata + '').replace (/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + createNewLines + '$2');
}

/* Add smilies to chat */
function vpb_wall_add_smilies (vpb_chat_text)
{

    vpb_site_url = "/blab/public/img/";

    var vpb_wall_text_smiles = vpb_chat_text, vpb_a_smilies =
            [{vpb_smiley_symbol: ':)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/smile.png" title="Smile" align="absmiddle">'},
                {vpb_smiley_symbol: ':(', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/frown.png" title="Frown, Sad" align="absmiddle">'},
                {vpb_smiley_symbol: 'O:)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/angel.png" title="Blushing angel" align="absmiddle">'},
                {vpb_smiley_symbol: ':cat-face:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/colonthree.png" title="Cat face" align="absmiddle">'},
                {vpb_smiley_symbol: 'o.O', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/confused.png" title="Confused" align="absmiddle">'},
                {vpb_smiley_symbol: 'O.o', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/confused.png" title="Confused" align="absmiddle">'},
                {vpb_smiley_symbol: ':cry:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/cry.png" title="Cry" align="absmiddle">'},
                {vpb_smiley_symbol: ':laughing-devil:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/devil.png" title="Laughing devil" align="absmiddle">'},
                {vpb_smiley_symbol: ':O', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/gasp.png" title="Shocked and surprised" align="absmiddle">'},
                {vpb_smiley_symbol: 'B)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/glasses.png" title="Glasses" align="absmiddle">'},
                {vpb_smiley_symbol: ':D', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/grin.png" title="Grin, Big Smile" align="absmiddle">'},
                {vpb_smiley_symbol: ':grumpy:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/grumpy.png" title="Upset and angry" align="absmiddle">'},
                {vpb_smiley_symbol: ':heart:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/heart.png" title="Heart" align="absmiddle">'},
                {vpb_smiley_symbol: '^_^', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/kiki.png" title="Kekeke happy" align="absmiddle">'},
                {vpb_smiley_symbol: ':kiss:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/kiss.png" title="Kiss" align="absmiddle">'},
                {vpb_smiley_symbol: ':v', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/pacman.png" title="Pacman" align="absmiddle">'},
                {vpb_smiley_symbol: ':penguin:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/penguin.gif" title="Penguin" align="absmiddle">'},
                {vpb_smiley_symbol: ':unsure:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/unsure.png" title="Unsure" align="absmiddle">'},
                {vpb_smiley_symbol: 'B|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'},
                {vpb_smiley_symbol: 'B-|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'},
                {vpb_smiley_symbol: '8-|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'},
                {vpb_smiley_symbol: '8|', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/sunglasses.png" title="Cool" align="absmiddle">'},
                {vpb_smiley_symbol: '-_-', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/squint.png" title="Annoyed, sighing or bored" align="absmiddle">'},
                {vpb_smiley_symbol: ':lve:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/red_heart_love.gif" title="Love" align="absmiddle">'},
                {vpb_smiley_symbol: ':putnam:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/putnam.gif" title="Christopher Putnam" align="absmiddle">'},
                {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/wink.png" title="Wink" align="absmiddle">'},
                {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/wink.png" title="Wink" align="absmiddle">'},
                {vpb_smiley_symbol: '(off)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/no_idea.gif" title="No idea" align="absmiddle">'},
                {vpb_smiley_symbol: '(on)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/got_idea.gif" title="Got an idea" align="absmiddle">'},
                {vpb_smiley_symbol: ':tea-cup:', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/cup_of_tea.png" title="Cup of tea" align="absmiddle">'},
                {vpb_smiley_symbol: '(n)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/no_thumbs_down.gif" title="No, thumb down" align="absmiddle">'},
                {vpb_smiley_symbol: '(y)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/yes_thumbs_up.gif" title="Yes, thumb up" align="absmiddle">'},
                {vpb_smiley_symbol: '(wink)', vpb_smiley_im: '<img src="' + vpb_site_url + 'smileys/shark.gif" title="Shark" align="absmiddle">'},
                {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}, {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}
            ];

    for (var i = 0; i < vpb_a_smilies.length; i++) {
        vpb_wall_text_smiles = vpb_wall_text_smiles.replace (vpb_a_smilies[i].vpb_smiley_symbol, vpb_a_smilies[i].vpb_smiley_im);
    }

    return vpb_wall_text_smiles;
}

// Save edited comment
function vpb_save_comment_update (comment_id)
{
    var comment_post = vpb_trim ($ ("#vpb_wall_comment_editable_data_" + parseInt (comment_id)).val ());

    if (comment_post == "")
    {
        $ ("#v-wall-message").html ($ ("#invalid_comment_update_field").val ());
        showErrorMessage ();
        return false;
    }
    else
    {
        var dataString = {'comment_id': comment_id, 'comment': comment_post};
        $.ajax ({
            type: "POST",
            url: '/blab/comment/updateComment',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#save_changes_loading_" + parseInt (comment_id)).html ($ ("#v_sending_btn").val ());
                $ ("#vpb_editable_comment_wrapper_" + parseInt (comment_id)).removeClass ('enable_this_box');
                $ ("#vpb_editable_comment_wrapper_" + parseInt (comment_id)).addClass ('disable_this_box');
            },
            success: function (response)
            {
                $ ("#save_changes_loading_" + parseInt (comment_id)).html ('');
                $ ("#vpb_editable_comment_wrapper_" + parseInt (comment_id)).removeClass ('disable_this_box');
                $ ("#vpb_editable_comment_wrapper_" + parseInt (comment_id)).addClass ('enable_this_box');


                var len = comment_post.length;
                var comment_post_Left;
                var max_allowed = 300;
                if (len <= parseInt (max_allowed))
                {
                    comment_post_Left = comment_post;
                }
                else if (len >= parseInt (max_allowed))
                {
                    comment_post_trimed = comment_post.substring (0, parseInt (max_allowed));
                    comment_post_Left = comment_post_trimed + '...';
                }

                //$ ("#vcomments_" + parseInt (comment_id)).html (vpb_wall_add_smilies (vpb_wall_nl2br (vpb_wall_create_link (vasplus_wall_special_text (comment_post_Left)))));

                $ ("#vcomments_" + parseInt (comment_id)).html (vpb_wall_add_smilies (vpb_wall_nl2br (vasplus_wall_special_text (comment_post))));

                $ ("#vpb_editable_comment_wrapper_" + parseInt (comment_id)).hide ();
                $ ("#vpb_default_comment_wrapper_" + parseInt (comment_id)).show ();
                $ ("#cedited_id_" + parseInt (comment_id)).show ();
                $ ("#cdotted_id_" + parseInt (comment_id)).show ();
            }


        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_comment_update('" + parseInt (comment_id) + "');", 10000);
        });
    }
}

// Load and display post likes
function vpb_load_edited_history (item_id, username, label, action)
{
    var dataString = {'page': 'load_edited_histories', 'item_id': item_id, 'username': username, 'action': action};
    $.ajax ({
        type: "POST",
        url: '/blab/index/getEditHistory',
        data: dataString,
        beforeSend: function ()
        {
            $ ("#vpb_system_data_title").html (label);
            $ ("#vpb_display_wall_gen_data").html ('<br><br>' + $ ("#v_loading_btn").val ());
            $ ("#v-wall-g-data-alert-box").click ();
        },
        success: function (response)
        {
            $ ("#vpb_display_wall_gen_data").html (response);

            $ ("#v_wall_bottom_left_info").html ($ ("#the_edit_history_info_text").val () + ' ' + action);

        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_edited_history('" + item_id + "', '" + username + "', '" + label + "', '" + action + "');", 10000);
    });
}

// Show editable post, comment or reply boxes
function vpb_show_editable_item (id, type)
{
    if (type == "post") // Show editable post
    {
        $ (".vpb_editable_status_wrapper").hide ();
        $ (".vpb_default_status_wrapper").show ();

        $ ("#vpb_default_status_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_editable_status_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_editable_status_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else if (type == "comment") // Show editable comment
    {
        $ (".vpb_editable_status_wrapper").hide ();
        $ (".vpb_default_status_wrapper").show ();
        $ (".btn-group[id=" + id + "]").hide ();

        $ ("#vpb_default_comment_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_editable_comment_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_editable_comment_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else // Show editable reply
    {
        $ (".vpb_editable_status_wrapper").hide ();
        $ (".vpb_default_status_wrapper").show ();

        $ ("#vpb_default_reply_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_editable_reply_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_editable_reply_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
}

// Hide the editable post, comment or reply boxes
function vpb_cancel_editable_item (id, type)
{
    if (type == "post") // Cancel editable post
    {
        $ ("#vpb_editable_status_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_default_status_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_default_status_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else if (type == "comment") // Cancel editable comment
    {
        $ ("#vpb_editable_comment_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_default_comment_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_default_comment_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else // Cancel editable reply
    {
        $ ("#vpb_editable_reply_wrapper_" + parseInt (id)).hide ();
        $ ("#vpb_default_reply_wrapper_" + parseInt (id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_default_reply_wrapper_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
}

// Update Post
function vpb_save_status_update (post_id)
{
    var post = vpb_trim ($ ("#vpb_wall_status_editable_data_" + parseInt (post_id)).val ());
    var session_uid = $ ("#session_uid").val ();

    if (post == "")
    {
        $ ("#v-wall-message").html ($ ("#invalid_status_update_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        var dataString = {'post_id': post_id, 'session_uid': session_uid, 'page': 'update_post', 'post': post};
        $.ajax ({
            type: "POST",
            url: '/blab/post/updatePost',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#saving_changes_loading_" + parseInt (post_id)).html ($ ("#v_sending_btn").val ());
                $ ("#vpb_editable_status_wrapper_" + parseInt (post_id)).removeClass ('enable_this_box');
                $ ("#vpb_editable_status_wrapper_" + parseInt (post_id)).addClass ('disable_this_box');
            },
            success: function (response)
            {
                $ ("#saving_changes_loading_" + parseInt (post_id)).html ('');
                $ ("#vpb_editable_status_wrapper_" + parseInt (post_id)).removeClass ('disable_this_box');
                $ ("#vpb_editable_status_wrapper_" + parseInt (post_id)).addClass ('enable_this_box');


                var len = post.length;
                var post_Left;
                var max_allowed = 400;
                if (len <= parseInt (max_allowed))
                {
                    post_Left = post;
                }
                else if (len >= parseInt (max_allowed))
                {
                    post_trimed = post.substring (0, parseInt (max_allowed));
                    post_Left = post_trimed + '...';
                }

                $ ("#vpost_" + parseInt (post_id)).find ("p").html (vpb_wall_add_smilies (vpb_wall_nl2br (vasplus_wall_special_text (post))));
                //$ ("#vpost_large_" + parseInt (post_id)).html (vpb_wall_add_smilies (vpb_wall_nl2br (vpb_wall_create_link (vasplus_wall_special_text (post)))));
                $ ("#vpb_editable_status_wrapper_" + parseInt (post_id)).hide ();
                $ ("#vpb_default_status_wrapper_" + parseInt (post_id)).show ();
                $ ("#vedited_id_" + parseInt (post_id)).show ();
                $ ("#vdotted_id_" + parseInt (post_id)).show ();


            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_status_update('" + parseInt (post_id) + "');", 10000);
        });
    }
}

function loadVideos ()
{
    $ (".social-body > div > div > p").each (function ()
    {
        var content = $.trim ($ (this).html ());
        content = buildVideos (content);
        $ (this).html (content);
    });
}

//Join group sending requests function
function vpb_send_request_to_join_group (group_id, group_manager, username, action)
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
            $ ("#group_join_" + group_id).hide ();
            $ ("#group_requestsent_" + group_id).show ();
        }
        else if (action == "cancel")
        {
            $ ("#group_requestsent_" + group_id).hide ();
            $ ("#group_join_" + group_id).show ();
        }
        else
        {
            unknow_request = 1
        }

        if (parseInt (unknow_request) == 1)
        {
            $ ("#v-wall-message").html ($ ("#general_system_error").val ());
            $ ("#v-wall-alert-box").click ();
            return false;
        }
        else
        {
            var dataString = {"group_id": group_id, "group_manager": group_manager, "username": username, "action": action, "page": "request-to-join-group"};

            $.post ('/blab/group/addMemberToGroup', dataString, function (response)
            {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 1)
                {

                }
                else
                {
                    $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                    $ ("#v-wall-alert-box").click ();
                    return false;
                }

            }).fail (function (error_response)
            {
                setTimeout ("vpb_send_request_to_join_group('" + parseInt (group_id) + "', '" + group_manager + "', '" + username + "', '" + action + "');", 10000);
            });
        }
    }
}

// Load user details when mouseover and when mouseout
var vpb_user_loader_timer = 500,
        vpb_time_out = null;

function vpb_get_user_onmouseover_data (username, fullname, country, photo)
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);

    vpb_time_out = setTimeout (function ()
    {
        if (username == "" || fullname == "" || photo == "")
        {
            $ (".v_load_user_detail").css ('display', 'none');
            clearTimeout (vpb_time_out);
            return false;
        }
        else
        {
            username = $.trim (username);
            var username2 = username.replace ('.', '');

            var session_uid = "michael.hampton";
            var vpb_friend_uid = $ ("#vpb_friendship_fid_" + username2).val ();

//            var vpb_session_pic = $ ("#vpb_session_pic_" + session_uid).val ();
//            if (vpb_session_pic != "" && session_uid == vpb_friend_uid)
//            {
//                photo = vpb_session_pic;
//            }
//            else
//            {
//            }

            $ ("#vpb_load_user_photo_" + username2).html ('<img src="' + photo + '" border="0">');
            $ ("#vpb_load_user_fullname_" + username2).html (fullname);
            if (country == "")
            {
                $ ("#vpb_load_user_country_" + username2).html ('');
            }
            else
            {
                $ ("#vpb_load_user_country_" + username2).html ('<i class="fa fa-map-marker" title="Location"></i>&nbsp;' + country);
            }

            $ (".v_load_user_detail").css ('display', 'none');

            console.log ("#vpb_load_user_" + username2);
            $ ("#vpb_load_user_" + username2).show ();


//            var dataString = {'page': 'load_friendship_button', 'session_uid': session_uid, 'friend_uid': vpb_friend_uid};
//            $.ajax ({
//                type: "POST",
//                url: vpb_site_url + 'wall-processor.php',
//                data: dataString,
//                beforeSend: function ()
//                {
//                    $ ("#vpb_load_friendship_" + username).html ($ ("#v_loading_btn").val ());
//                },
//                success: function (response)
//                {
//                    $ ("#vpb_load_friendship_" + username).html (response);
//                }
//            }).fail (function (error_response)
//            {
//                $ ("#vpb_load_friendship_" + username).html ('');
//            });
        }

    }, parseInt (vpb_user_loader_timer));
}

function vpb_get_user_mouseout_datas ()
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);
    vpb_time_out = setTimeout (function ()
    {
        $ (".v_load_user_detail").css ('display', 'none');
    }, parseInt (vpb_user_loader_timer));
}

// Report a post
function vpb_report_the_post (session_fullname, session_username, session_email)
{
    var post_id = $ ("#the_postID").val ();
    var the_pageUsernamed = $ ("#the_pageUsernamed").val ();
    var the_posterFullname = $ ("#the_posterFullname").val ();
    var the_posterUsername = $ ("#the_posterUsername").val ();
    var the_posterEmail = $ ("#the_posterEmail").val ();

    var report_post_data = vpb_trim ($ ("#report_post_data").val ());

    if (post_id == "" || the_posterFullname == "" || the_posterUsername == "" || the_posterEmail == "" || session_fullname == "" || session_username == "" || session_email == "" || the_pageUsernamed == "")
    {
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if (report_post_data == "")
    {
        $ ("#report_post_data").focus ();
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#empty_reporting_post_field_text").val () + '</div>');
        return false;
    }
    else if (report_post_data.length < 5)
    {
        $ ("#report_post_data").focus ();
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#not_enough_reporting_post_field_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'post_id': post_id, 'the_posterFullname': the_posterFullname, 'the_posterUsername': the_posterUsername, 'the_posterEmail': the_posterEmail, 'the_pageUsernamed': the_pageUsernamed, 'session_fullname': session_fullname, 'session_username': session_username, 'session_email': session_email, 'report_post_data': report_post_data, 'page': 'report-an-update'};

        //$ ("#report_post_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $ ("#report-this-post").removeClass ('enable_this_box');
        $ ("#report-this-post").addClass ('disable_this_box');

        $.post ('/blab/post/reportPost', dataString, function (response)
        {
            $ ("#report-this-post").removeClass ('disable_this_box');
            $ ("#report-this-post").addClass ('enable_this_box');

            if ($.trim (response) !== "")
            {
                $ ("#report_post_status").html ('<div class="vsuccess">' + $ ("#report_send_successfully_message").val () + '</div>');
                $ ("#report_post_data").val ('');
                setTimeout (function ()
                {
                    $ ('.modal').modal ('hide');
                    $ ("#report_post_status").html ('');
                }, 10000);
                return false;
            }
            else
            {
                $ ("#report_post_status").html (response);
                return false;
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_report_the_post('" + session_fullname + "', '" + session_username + "', '" + session_email + "');", 10000);
        });
    }
}

// Save the details of the post which a user wanted 
function vpb_save_the_item_detail (post_id, fullname, username, email, pageusername)
{
    $ ("#the_postID").val (post_id);
    $ ("#the_pageUsernamed").val (pageusername);
    $ ("#the_posterFullname").val (fullname);
    $ ("#the_posterUsername").val (username);
    $ ("#the_posterEmail").val (email);
}

// Suggest friends to tag
function vpb_friends_suggestion (friend)
{
    var session_uid = $ ("#session_uid").val ();

    if (friend.length > 0)
    {
        $ ("#vpb-location-server-response").html ('');

        $ ("#vpb-tagged-people-in-post-server-response").html ('<div class="dropdown"><ul class="dropdown-menu vpb-hundred-percent_loading" style="top:auto;"><li class="dropdown-header vpb_wall_loading_text">' + $ ("#v_loading_btn").val () + '</li></ul></div>');


        var dataString = {'friend': friend, 'page': 'tag_people_in_post'};

        $.post ('/blab/post/tagPeopleInPost', dataString, function (response)
        {

            if (response == "")
            {
                $ ("#vpb-tagged-people-in-post-server-response").html ('');
            }
            else
            {
                $ ("#vpb-tagged-people-in-post-server-response").html ('<div class="dropdown"><ul class="dropdown-menu vpb-hundred-percent" id="tag_people_in_post_suggestion_box" style="top:auto;">' + response + '</ul></div>');
            }


        }).fail (function (error_response)
        {
            setTimeout ("vpb_friends_suggestion('" + friend + "');", 10000);
        });
    }
    else
    {
        $ ("#vpb-tagged-people-in-post-server-response").html ('');
        return false;
    }
}

/* Add and Remove Friend From Selected List */
function vpb_tag_this_friend (fullname, username, userId)
{
    $ ("#vpb-tagged-people-in-post-server-response").html ('');
    //Play sound when tagged successfully
//                $ ('<audio id="vasplusAudio"><source src="' + vpb_site_url + 'vpb_sound/shutter.mp3" type="audio/mpeg"></audio>').appendTo ('body');
//                $ ('#vasplusAudio')[0].play ();

    $ ("#new_tags_loading").html ('');

    $ ("#new_tags").fadeIn (2000).append ('<span userid="' + userId + '" id="added_id_' + username + '"><span id="added_id' + username + '" class="selected_friends_in_tagged_list">' + fullname + ' <span class="remove_selected_friends_in_tagged_list" title="Remove" onclick="vpb_comfirm_removal_of_tagged_friend(\'' + userId + '\',\'' + username + '\');">x</span></span></span>');
    $ ("#vpb-tag").val ('').focus ();
    $ (".vpb-tagged-list-wrap").show ();
}

// Conform before removing a friend from the tagged list
function vpb_comfirm_removal_of_tagged_friend (fullname, username)
{
    $ ("#friend-username-to-remove-from-tagged-list").val (username);
    $ ("#friend-fullname-to-remove-from-tagged-list").val (fullname);
    $ ("#remove_friend_message").html ($ ("#v_confirmation_message").val () + ' <b>' + fullname + '</b>?');
    $ ("#v-remove-friend-alert-box").click ();
}



/* This function is called when a user clicks on remove friend from tagged list */
function vpb_remove_this_tagged_friend ()
{


    //var session_uid = $ ("#session_uid").val ();
    var friend_username = $ ("#friend-username-to-remove-from-tagged-list").val ();
    var friend_fullname = $ ("#friend-fullname-to-remove-from-tagged-list").val ();


    $ ("#new_tags").find ("[userid=" + friend_fullname + "]").fadeOut ('slow').remove ();
    //$ ("#tagged_list").css ('display', 'none');
    $ ("#vpb-tag").focus ();
}

// Conform before removing a friend from the tagged list
function vpb_comfirm_removal_of_tagged_friend (fullname, username)
{
    $ ("#friend-username-to-remove-from-tagged-list").val (username);
    $ ("#friend-fullname-to-remove-from-tagged-list").val (fullname);
    $ ("#remove_friend_message").html ($ ("#v_confirmation_message").val () + ' <b>' + fullname + '</b>?');
    $ ("#v-remove-friend-alert-box").click ();
}


// Show/Hide tag people in post box
function vpb_tag_people_in_post ()
{
    $ ("#user_selected_this_location").hide ();
    $ (".v_wall_tag_with").hide ();

    if ($ ("#start_typing_name").css ('display') == "none")
    {
        // Hide the smiley section before showing the tagging section
        $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
        $ ("#add_smile_button").addClass ('vfooter_wraper');
        $ ("#vpb_the_wall_smiley_box").slideUp ();

        // Hide the location section before showing the tagging section
        $ ("#add_location_button").removeClass ('vfooter_wraper_active');
        $ ("#add_location_button").addClass ('vfooter_wraper');
        $ ("#user_is_at_this_location").css ('display', 'none');

        $ ("#url_content_wrapper").css ('display', 'none');

        $ ("#tag_people_button").removeClass ('vfooter_wraper');
        $ ("#tag_people_button").addClass ('vfooter_wraper_active');

        $ ("#start_typing_name").css ('display', 'inline-block');
        $ ("#vpb-tag").focus ();

        // If there are already tagged friends in the list, show them when the user is about to make new tags
        if (vpb_trim ($ ("#the_tagged_friends").text ()) != "")
        {
            $ ("#tagged_list").css ('display', 'inline-block');
        }
        else
        {
        }
    }
    else
    {
        $ ("#vpb-tag").val ('');
        $ ("#tag_people_button").removeClass ('vfooter_wraper_active');
        $ ("#tag_people_button").addClass ('vfooter_wraper');
        $ ("#start_typing_name").css ('display', 'none');
    }
}

var pageLoaded = false;

$ (function ()
{
    // Auto suggest users
    var timers;
    var y;
    $ ("#vpb_wall_members_data").keyup (function ()
    {
        if (y)
        {
            y.abort ();
        } // If there is an existing XHR, abort it.
        clearTimeout (timers); // Clear the timer so we don't end up with dupes.
        timers = setTimeout (function ()
        { // assign timer a new timeout 

            y = vpb_search_get_suggested_user ($ ('#vpb_wall_members_data').val ());
        }, 1000); // 2000ms delay, tweak for faster/slower
    });

    $ (document).on ("click", function (e)
    {
        var $clicked = $ (e.target);
        if (!$clicked.parents ().hasClass ("dropdown-menu") && !$clicked.parents ().hasClass ("the_sorting_button") && !$clicked.parents ().hasClass ("vpb_notifications_icon") && !$clicked.parents ().hasClass ("vpb_notifications_icon_active") && !$clicked.parents ().hasClass ("dropdown") && !$clicked.parents ().hasClass ("input-group-addon-plus") && !$clicked.parents ().hasClass ("form-control-plus") && $clicked.attr ('id') != "vfrnd_data" && $clicked.attr ('id') != "v_suggested_wall_users_box" && $clicked.attr ('id') != "vpb_wall_members_data")
        {
            $ ("#vpb-tagged-people-in-post-server-response").html ('');
            $ ("#vpb-location-server-response").html ('');

            $ (".vpb_wall_post_security_setting_active").addClass ('vpb_wall_post_security_setting');
            $ (".vpb_wall_post_security_setting_active").removeClass ('vpb_wall_post_security_setting_active');

            // Hide the sorting details
            $ ("#vpb_sorting_box").hide ();
            $ ("#vpb_sorting_text").removeClass ('vpb_sort_text_active');
            $ ("#vpb_sorting_text").addClass ('vpb_sort_text');

            // Hide the website menus
            $ ("#v_site_menu_box").hide ();
            $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
            $ ("#v_site_menu").addClass ('vpb_notifications_icon');

            $ ("#friendRequests_bar").hide ();
            $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
            $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

            $ (".dropdown-menu2").hide ();
            $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
            $ ("#v_notifications").addClass ('vpb_notifications_icon');

            $ ("#v_message_box").hide ();
            $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
            $ ("#v_new_messages").addClass ('vpb_notifications_icon');

            // Hide the search box
            $ ("#vpb_display_search_results").hide ();
            $ ("#v_friend_requests_box").hide ();

            $ ("#vpb_wall_members_data").val ('');
            $ ("#v_suggested_wall_users_box").hide ();
        }
    });

    bindhover ();


    $ ("#vpb_display_search_results").on ('mouseenter', function ()
    {
        $ (".vthe_inner").css ('overflow-y', 'auto');
    }).on ('mouseleave', function ()
    {
        $ (".vthe_inner").css ('overflow', 'hidden');
    });

    setTimeout (function ()
    {
        $ ("html, body").animate ({scrollTop: 0}, "slow");
        pageLoaded = true;
    }, 50);

    var vpb_running_process = false; //This will prevents multipal ajax loads

//The total records for this current page viewed
    var total_status_updates = $ ("#vtotal_status_updates").val ();

//The total records for this current page viewed when sorted by latest comments
    var total_status_updates_by_comments = $ ("#vtotal_status_updates_by_comments").val ();


    //To detect the page scroll so as to auto load status updates
    $ (window).scroll (function (e)
    {
        // Check if other processes are running then pause
        if (parseInt ($ ("#vpb_is_process_running").val ()) > 0 || pageLoaded === false)
        {
        }
        else
        {
            //The current user has scrolled to the bottom of the current page
            if ($ (window).scrollTop () + $ (window).height () == $ (document).height () && pageLoaded === true)
            {
                if (parseInt ($ ("#vtotal_status_updates").val ()) > parseInt ($ ("#vpb_total_per_load").val ()) && $ ("#viewed_post_id").val () == "")
                {
                    if ($ ("#vpb_sort_option").val () == "sort_updates_by_latest_comments")
                    {
                        var vpb_start = $ ("#vpb_start").val (); //Where to start loading the updates
                        //There are still more records to load
                        //if (parseInt (vpb_start) <= parseInt (total_status_updates_by_comments) && vpb_running_process == false)
                        //{

                        vpb_load_more_status_updates ('auto-load');
                        //}
                        //else
                        //{
                        //}
                    }
                    else
                    {
                        var vpb_start = $ ("#vpb_start").val (); //Where to start loading the updates
                        //There are still more records to load
                        if (parseInt (vpb_start) <= parseInt (total_status_updates) && vpb_running_process == false)
                        {
                            vpb_load_more_status_updates ('auto-load');
                        }
                        else
                        {
                        }
                    }
                }
            }
        }
    });

    // Auto search for users
    var timer;
    var x;
    $ ('#vfrnd_data').keyup (function ()
    {
        if (x)
        {
            x.abort ()
        } // If there is an existing XHR, abort it.
        clearTimeout (timer); // Clear the timer so we don't end up with dupes.
        timer = setTimeout (function ()
        { // assign timer a new timeout 

            x = vpb_search_for_friends ();
        }, 1000); // 2000ms delay, tweak for faster/slower
    });
});

// When mouse over the search results, add scroller to the result box if its too long in height
$ ("#vpb_display_search_results").on ('mouseenter', function ()
{
    $ (".vthe_inner").css ('overflow-y', 'auto');
}).on ('mouseleave', function ()
{
    $ (".vthe_inner").css ('overflow', 'hidden');
});


if ($ ("#vpb_smiley_box").length > 0)
{
    $ ("#vpb_smiley_box").html ('<div class="vpb_wall_smiley_box_wrapper"><div class="vpb_smiley_inner_box"><span class="smiley_a" title="Smile" onclick="vpb_add_smiley_to_wall_status(\':)\');"></span><span class="smiley_b" title="Frown, Sad" onclick="vpb_add_smiley_to_wall_status(\':(\');"></span><span class="smiley_c" title="Blushing angel" onclick="vpb_add_smiley_to_wall_status(\':blushing-angel:\');"></span><span class="smiley_d" title="Cat face" onclick="vpb_add_smiley_to_wall_status(\':cat-face:\');"></span><span class="smiley_e" title="Confused" onclick="vpb_add_smiley_to_wall_status(\'o.O\');"></span><span class="smiley_f" title="Cry" onclick="vpb_add_smiley_to_wall_status(\':cry:\');"></span><span class="smiley_g" title="Laughing devil" onclick="vpb_add_smiley_to_wall_status(\':laughing-devil:\');"></span><span class="smiley_h" title="Shocked and surprised" onclick="vpb_add_smiley_to_wall_status(\':O\');"></span><span class="smiley_i" title="Glasses" onclick="vpb_add_smiley_to_wall_status(\'B)\');"></span><span class="smiley_j" title="Grin, Big Smile" onclick="vpb_add_smiley_to_wall_status(\':D\');"></span><span class="smiley_k" title="Upset and angry" onclick="vpb_add_smiley_to_wall_status(\':grumpy:\');"></span><span class="smiley_l" title="Heart" onclick="vpb_add_smiley_to_wall_status(\':heart:\');"></span><span class="smiley_m" title="Kekeke happy" onclick="vpb_add_smiley_to_wall_status(\'^_^\');"></span><span class="smiley_n" title="Kiss" onclick="vpb_add_smiley_to_wall_status(\':kiss:\');"></span><span class="smiley_o" title="Pacman" onclick="vpb_add_smiley_to_wall_status(\':v\');"></span><span class="smiley_p" title="Penguin" onclick="vpb_add_smiley_to_wall_status(\':penguin:\');"></span><span class="smiley_q" title="Unsure" onclick="vpb_add_smiley_to_wall_status(\':unsure:\');"></span><span class="smiley_r" title="Cool" onclick="vpb_add_smiley_to_wall_status(\'B|\');"></span><span class="smiley_s" title="Annoyed, sighing or bored" onclick="vpb_add_smiley_to_wall_status(\'-_-\');"></span><span class="smiley_t" title="Love" onclick="vpb_add_smiley_to_wall_status(\':lve:\');"></span><span class="smiley_u" title="Christopher Putnam" onclick="vpb_add_smiley_to_wall_status(\':putnam:\');"></span><span class="smiley_zb" title="Shark" onclick="vpb_add_smiley_to_wall_status(\'(wink)\');"></span><span class="smiley_v" title="Wink" onclick="vpb_add_smiley_to_wall_status(\'(wink)\');"></span><span class="smiley_w" title="No idea" onclick="vpb_add_smiley_to_wall_status(\'(off)\');"></span><span class="smiley_x" title="Got an idea" onclick="vpb_add_smiley_to_wall_status(\'(on)\');"></span><span class="smiley_y" title="Cup of tea" onclick="vpb_add_smiley_to_wall_status(\':tea-cup:\');"></span><span class="smiley_z" title="No, thumb down" onclick="vpb_add_smiley_to_wall_status(\'(n)\');"></span><span class="smiley_za" title="Yes, thumb up" onclick="vpb_add_smiley_to_wall_status(\'(y)\');"></span><div style="clear:both;"></div></div></div><div style="clear: both;"></div>');

}
else
{
}

$.log = function (message)
{
    var $logger = $ ("#logger");
    $logger.html ($logger.html () + "\n * " + message);
};




//pollTask ();

loadVideos ();

if (usersLocation === '')
{
    if (navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition (showLocation);
    }
    else
    {
        $ ('#location').html ('Geolocation is not supported by this browser.');
    }
}

$ (document).off ("click", ".seeAllFriends");
$ (document).on ('click', '.seeAllFriends', function ()
{
    var userId = $ (this).attr ("userid");

    $.ajax ({
        url: '/blab/friend/getAllFriends?userId=' + userId,
        type: 'GET',
        success: function (response)
        {
            $ ("#showAllFriends").find (".modal-body").html (response);
            $ ("#showAllFriends").modal ("show");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });

    return false;
});

$ (".showEvent").on ("click", function ()
{
    var groupId = $ (this).attr ("event-id");

    $.ajax ({
        url: '/blab/event/eventUsers?id=' + groupId,
        type: 'GET',
        success: function (response)
        {
            $ ("#eventUsers > div > div").html (response);
            $ ("#eventUsers").modal ("show");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

$ (".showGroup").on ("click", function ()
{
    var groupId = $ (this).attr ("group-id");

    $.ajax ({
        url: '/blab/group/groupUsers?id=' + groupId,
        type: 'GET',
        success: function (response)
        {
            $ ("#groupUsers > div > div").html (response);
            $ ("#groupUsers").modal ("show");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

$ (document).off ("click", "#createEvent");
$ (document).on ('click', '#createEvent', function ()
{
    $.ajax ({
        url: '/blab/event/createEvent',
        type: 'GET',
        success: function (response)
        {
            $ ("#eventPopup").html (response);
            $ ("#eventPopup").modal ("show");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

$ (document).off ("click", "#createGroup");
$ (document).on ('click', '#createGroup', function ()
{
    $.ajax ({
        url: '/blab/group/createGroup',
        type: 'GET',
        success: function (response)
        {
            $ ("#groupPopup").html (response);
            $ ("#groupPopup").modal ("show");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

$ (document).off ("click", ".chatClose");
$ (document).on ('click', '.chatClose', function ()
{
    $ (this).parent ().parent ().remove ();
});

$ ('.mentions').mentionsInput ({
    source: 'autoSuggest',
    showAtCaret: true
});
// Check if a group session exit or not
function vpb_wall_session_is_created ()
{
    if (vpb_getcookie ('group_unames') != "" && vpb_getcookie ('group_unames') != null && vpb_getcookie ('group_unames') != undefined)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Remove a user from a group conversation
function vpb_remove_a_user_from_group (fullname, username)
{
    if (vpb_wall_session_is_created ())
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();

        vpb_group_users_fname = vpb_getcookie ('group_fnames').split (/\,/);
        vpb_group_users_name = vpb_getcookie ('group_unames').split (/\,/);

        vpb_setcookie ('g_fullnames', vpb_remove_data (vpb_group_users_fname, fullname), 30);
        vpb_setcookie ('g_username', vpb_remove_data (vpb_group_users_name, username), 30);

        vpb_setcookie ('group_fnames', vpb_remove_data (vpb_group_users_fname, fullname), 30);
        vpb_setcookie ('group_unames', vpb_remove_data (vpb_group_users_name, username), 30);

        $ ("#vpb_new_user_in_group_" + username).fadeOut ();

        setTimeout (function ()
        {
            $ ("#vpb_new_user_in_group_" + username).remove ();
        }, 50);

        var group_id = $ ("#group_uid").val () != "" ? $ ("#group_uid").val () : "";
        if (group_id != "" && group_id != undefined)
        {
            var dataString = {"username": username, "group_id": group_id, "page": "vpb_remove_user_from_group"};
            $.ajax ({
                type: "POST",
                url: vpb_site_url + 'wall-processor.php',
                data: dataString,
                beforeSend: function ()
                {},
                success: function (response)
                {}
            });
        }
        else
        {
        }
        vpb_display_the_users_in_group ();
    }
    else
    {
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
}

// Display all newly added users in a group
function vpb_display_the_users_in_group ()
{
    if (vpb_wall_session_is_created ())
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();

        vpb_group_users_fname = vpb_getcookie ('group_fnames').split (/\,/);
        vpb_group_users_name = vpb_getcookie ('group_unames').split (/\,/);

        $ ("#vpb_added_wall_users_box").html ('');

        for (var u = 0; u < vpb_group_users_name.length; u++)
        {
            if (vpb_group_users_name[u] != "")
            {
                var fullname = vpb_group_users_fname[u];
                var username = vpb_group_users_name[u];

                $ ("#vpb_added_wall_users_box").append ('<span id="vpb_new_user_in_group_' + username + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vpb_remove_a_user_from_group(\'' + fullname + '\', \'' + username + '\')"></i></span></span>');
            }
            else
            {
            }
        }
    }
    else
    {
    }
}

//Save group details
function vpb_save_wall_group_details (whois)
{
    //var username = vpb_trim (vpb_strip_tags ($ ("#session_uid").val ()));

    if (whois == "create")
    {
        var group_id = vpb_getcookie ('v_wall_group_id') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_wall_group_id')));
    }
    else
    {
        var group_id = $ ("#currentGroupId").val ();
    }
    var group_username = vpb_getcookie ('group_unames') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_unames')));
    var group_fullname = vpb_getcookie ('group_fnames') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_fnames')));
    var group_name = vpb_trim (vpb_strip_tags ($ ("#vgroup_name").val ()));
    var vgroup_description = vpb_trim (vpb_strip_tags ($ ("#vgroup_description").val ()));

    var ext = $ ('#vgroup_picture').val ().split ('.').pop ().toLowerCase ();
    var vgroup_picture = $ ("#vgroup_picture").val ();

    var photo_added = vgroup_picture == "" ? "no" : "yes";

    $ ("#vplease_wait_loading").html ('');

    if ($ ('#public_group').prop ('checked'))
    {
        var group_type = "Public";
    }
    else if ($ ('#secret_group').prop ('checked'))
    {
        var group_type = "Secret";
    }
    else
    {
        var group_type = "";
    }

    if (group_id == "")
    {
        console.log ("1g");
        $ ("#vpb_display_group_status").html ('');
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();

        return false;
    }
    else if (group_name == "")
    {
        console.log ("1f");
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_name_text").val () + '</div>');
        $ ("#vgroup_name").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (vgroup_description == "")
    {
        console.log ("1e");
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vgroup_description").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (group_username == "")
    {
        console.log ("1d");
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_members_name_text").val () + '</div>');
        $ ("#vpb_wall_members_data").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (whois == "create" && vgroup_picture == "")
    {
        console.log ("1c");
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_photo_text").val () + '</div>');
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (whois == "create" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        console.log ("1b");
        document.getElementById ('vgroup_picture').value = '';
        document.getElementById ('vgrouppicture').title = 'No file is chosen';
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#invalid_file_attachment").val () + '</div>');
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (group_type == "")
    {
        console.log ("1a");
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_type_text").val () + '</div>');
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else
    {
        //Proceed now because a user has selected a file
        var vpb_files = document.getElementById ('vgroup_picture').files;

        // Create a formdata object and append the file
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });

        vpb_data.append ("group_id", group_id);
        vpb_data.append ("group_username", group_username);
        vpb_data.append ("group_type", group_type);
        vpb_data.append ("group_name", group_name);
        vpb_data.append ("photo_added", photo_added);
        vpb_data.append ("vgroup_description", vgroup_description);
        vpb_data.append ("saveType", whois);
        vpb_data.append ("page", 'vpb_save_wall_group_details');

        $.ajax ({
            url: '/blab/group/saveGroup',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#vpb_display_group_status").html ('');
                $ ("#vplease_wait_loading").html ('<div style="padding:10px;padding-bottom:6px;padding-top:0px;">' + $ ("#v_loading_btn").val () + '</div>');
                $ ("#v-create-group").removeClass ('enable_this_box');
                $ ("#v-create-group").addClass ('disable_this_box');
                $ ('#v-create-group').animate ({
                    scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
                }, 1600, 'easeInOutExpo');
            },
            success: function (response)
            {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 1)
                {

                    if (vpb_getcookie ('group_name') || vpb_getcookie ('group_name') != null)
                    {
                        vpb_removecookie ('group_name');
                    }
                    else
                    {
                    }
                    if (vpb_getcookie ('group_pic') || vpb_getcookie ('group_pic') != null)
                    {
                        vpb_removecookie ('group_pic');
                    }
                    else
                    {
                    }
                    var group_pic = objResponse.data.photo;

                    vpb_setcookie ('group_name', group_name, 30);
                    if (group_pic != "")
                    {
                        vpb_setcookie ('group_pic', group_pic, 30);

                        $ ("#vpb_display_wall_group_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#group_photo_title_text").val () + '\', \'' + group_pic + '\');" src="' + group_pic + '" title=' + $ ("#group_photo_title_text").val () + ' border="0">');

                        $ ("#vcoverPic").html ('<div class="gvprofilephoto" style="background-image: url(' + group_pic + ');" onclick="vpb_popup_photo_box(\'' + group_name + '\', 1, 1, \'' + group_pic + '\');"></div>');

                    }
                    else
                    {
                    }

                    $ ("#vpb_display_group_status").html ('<div class="vsuccess">' + $ ('#saved_group_details_successfully_text').val () + '</div>');

                    $ ("#v-create-group").removeClass ('disable_this_box');
                    $ ("#v-create-group").addClass ('enable_this_box');

                    $ ('#v-create-group').animate ({
                        scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
                    }, 1600, 'easeInOutExpo');

                    if (whois == "create")
                    {
                        //vpb_removecookie('group_fnames');
                        //vpb_removecookie('group_unames');
                        //vpb_removecookie('group_uphoto');
                        setTimeout (function ()
                        {
                            $ ("#vpb_display_group_status").html ('');
                            window.location.replace ('/blab/group/index/' + objResponse.data.id);
                        }, 2000);
                    }
                    else
                    {
                        setTimeout (function ()
                        {
                            $ ("#vpb_display_group_status").html ('');
                        }, 5000);
                        $ ("#vplease_wait_loading").html ('');

                        document.getElementById ('vgroup_picture').value = '';
                        document.getElementById ('vgrouppicture').title = 'No file is chosen';

                        vpb_load_more_status_updates ('start');


                        if (group_pic != "")
                        {
                            vpb_setcookie ('group_uphoto', group_pic, 30);
                            vpb_setcookie ('group_pic', group_pic, 30);

                            vpb_store_current_group_details (group_id, group_name, vgroup_description, group_username, group_pic, group_type, group_fullname);
                        }
                        else
                        {
                            vpb_store_current_group_details (group_id, group_name, vgroup_description, group_username, '', group_type, group_fullname);
                        }
                    }
                }
                else
                {
                    $ ("#vplease_wait_loading").html ('');
                    $ ("#vpb_display_group_status").html ('');
                    $ ("#v-create-group").removeClass ('disable_this_box');
                    $ ("#v-create-group").addClass ('enable_this_box');

                    $ ("#v-wall-message").html (response);
                    $ ("#v-wall-alert-box").click ();
                }


                $ ("#vplease_wait_loading").html ('');
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_wall_group_details();", 1000);
        });
    }
}

// sae modify page
//Save group details
function vpb_save_wall_page_details (whois)
{
    //var username = vpb_trim (vpb_strip_tags ($ ("#session_uid").val ()));


    var page_id = $ ("#currentPageId").val ();

    var page_name = vpb_trim (vpb_strip_tags ($ ("#vpage_name").val ()));
    var page_description = vpb_trim (vpb_strip_tags ($ ("#vpage_description").val ()));
    var vgroup_websiteUrl = vpb_trim (vpb_strip_tags ($ ("#vpage_websiteUrl").val ()));
    var vgroup_postcode = vpb_trim (vpb_strip_tags ($ ("#vpage_postcode").val ()));
    var vgroup_telephone = vpb_trim (vpb_strip_tags ($ ("#vpage_telephone").val ()));
    var vgroup_address = vpb_trim (vpb_strip_tags ($ ("#vpage_address").val ()));

    var ext = $ ('#vpage_picture').val ().split ('.').pop ().toLowerCase ();
    var vgroup_picture = $ ("#vpage_picture").val ();

    var photo_added = vgroup_picture == "" ? "no" : "yes";

    $ ("#vplease_wait_loading").html ('');

    if (page_id == "")
    {
        $ ("#vpb_display_group_status").html ('');
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();

        return false;
    }
    else if (page_name == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_name_text").val () + '</div>');
        $ ("#vgroup_name").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (page_description == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vgroup_description").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (vgroup_websiteUrl == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vpage_websiteUrl").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (vgroup_postcode == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vpage_postcode").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (vgroup_telephone == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vpage_telephone").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else if (vgroup_address == "")
    {
        $ ("#vpb_display_group_status").html ('<div class="vwarning">' + $ ("#empty_group_desc_text").val () + '</div>');
        $ ("#vpage_address").focus ();
        $ ('#v-create-group').animate ({
            scrollTop: $ ("#vpb_display_group_status").offset ().top + parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');
        return false;
    }
    else
    {
        //Proceed now because a user has selected a file
        var vpb_files = document.getElementById ('vpage_picture').files;

        // Create a formdata object and append the file
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });

        vpb_data.append ("page_id", page_id);
        vpb_data.append ("page_name", page_name);
        vpb_data.append ("photo_added", photo_added);
        vpb_data.append ("page_description", page_description);
        vpb_data.append ("vgroup_websiteUrl", vgroup_websiteUrl);
        vpb_data.append ("vgroup_postcode", vgroup_postcode);
        vpb_data.append ("vgroup_telephone", vgroup_telephone);
        vpb_data.append ("vgroup_address", vgroup_address);
        vpb_data.append ("saveType", whois);
        vpb_data.append ("page", 'vpb_save_wall_group_details');

        $.ajax ({
            url: '/blab/page/saveUpdatedPage',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#vpb_display_page_status").html ('');
                $ ("#vplease_wait_loading").html ('<div style="padding:10px;padding-bottom:6px;padding-top:0px;">' + $ ("#v_loading_btn").val () + '</div>');
                $ ("#v-create-page").removeClass ('enable_this_box');
                $ ("#v-create-page").addClass ('disable_this_box');
                $ ('#v-create-page').animate ({
                    scrollTop: $ ("#vpb_display_page_status").offset ().top + parseInt (120) + 'px'
                }, 1600, 'easeInOutExpo');
            },
            success: function (response)
            {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 1)
                {


                    var group_pic = objResponse.data.photo;

                    if (group_pic != "")
                    {

                        $ ("#vpb_display_wall_page_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#group_photo_title_text").val () + '\', \'' + group_pic + '\');" src="' + group_pic + '" title=' + $ ("#group_photo_title_text").val () + ' border="0">');

                        $ ("#vcoverPic").html ('<div class="gvprofilephoto" style="background-image: url(' + group_pic + ');" onclick="vpb_popup_photo_box(\'' + page_name + '\', 1, 1, \'' + group_pic + '\');"></div>');

                    }
                    else
                    {
                    }

                    $ ("#vpb_display_page_status").html ('<div class="vsuccess">' + $ ('#saved_page_details_successfully_text').val () + '</div>');

                    $ ("#v-create-page").removeClass ('disable_this_box');
                    $ ("#v-create-page").addClass ('enable_this_box');

                    $ ('#v-create-page').animate ({
                        scrollTop: $ ("#vpb_display_page_status").offset ().top + parseInt (120) + 'px'
                    }, 1600, 'easeInOutExpo');


                    setTimeout (function ()
                    {
                        $ ("#vpb_display_page_status").html ('');
                    }, 5000);
                    $ ("#vplease_wait_loading").html ('');

                    document.getElementById ('vpage_picture').value = '';
                    document.getElementById ('vpagepicture').title = 'No file is chosen';

                    vpb_load_more_status_updates ('start');


                    if (group_pic != "")
                    {
                        vpb_setcookie ('page_uphoto', group_pic, 30);
                        vpb_setcookie ('page_pic', group_pic, 30);

                        //vpb_store_current_group_details (group_id, group_name, vgroup_description, group_username, group_pic, group_type, group_fullname);
                    }
                    else
                    {
                        //vpb_store_current_group_details (group_id, group_name, vgroup_description, group_username, '', group_type, group_fullname);
                    }

                }
                else
                {
                    $ ("#vplease_wait_loading").html ('');
                    $ ("#vpb_display_page_status").html ('');
                    $ ("#v-create-page").removeClass ('disable_this_box');
                    $ ("#v-create-page").addClass ('enable_this_box');

                    $ ("#v-wall-message").html (response);
                    $ ("#v-wall-alert-box").click ();
                }


                $ ("#vplease_wait_loading").html ('');
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_wall_group_details();", 1000);
        });
    }
}

// Keep current group details for display when needed
function vpb_store_current_group_details (g_id, g_name, g_desc, g_members, g_photo, g_privacy, g_fname)
{
    vpb_setcookie ('g_id', g_id, 30);
    vpb_setcookie ('g_name', g_name, 30);
    vpb_setcookie ('g_desc', g_desc, 30);
    if (g_photo != "")
    {
        vpb_setcookie ('group_uphoto', g_photo, 30);
    }
    else
    {
    }
    vpb_setcookie ('g_privacy', g_privacy, 30);
    vpb_setcookie ('g_username', jQuery.parseJSON (g_members), 30);
    vpb_setcookie ('g_fullnames', jQuery.parseJSON (g_fname), 30);
}

// Remove newly added users from group
function vpb_remove_data (arr, itemToRemove)
{
    var j = 0;
    for (var i = 0, l = arr.length; i < l; i++) {
        if (arr[i] !== itemToRemove)
        {
            arr[j++] = arr[i];
        }
    }
    arr.length = j;
    return arr;
}



// Already added user to list
function vpb_uInArray (group_username, user)
{
    var found = false;
    var vpb_group_users_name = new Array ();
    vpb_group_users_name = group_username.split (/\,/);
    for (var u = 0; u < vpb_group_users_name.length; u++) {
        if (vpb_group_users_name[u] == user)
        {
            found = true;
        }
        else
        {
        }
    }
    return found;
}


// Add user to group
function vpb_add_new_wall_user_to_group (fullname, username, photo)
{
    var group_id = vpb_getcookie ('v_wall_group_id') != null ? vpb_getcookie ('v_wall_group_id') : "";
    if (group_id != "" && group_id != undefined)
    {
        if (vpb_wall_session_is_created () && vpb_uInArray (vpb_getcookie ('group_unames'), username))
        {
            // Already in the list
        }
        else
        {

            var group_fullname = new Array ();
            var group_username = new Array ();

            if (vpb_wall_session_is_created ())
            {
                group_fullname = vpb_getcookie ('group_fnames').split (/\,/);
                group_username = vpb_getcookie ('group_unames').split (/\,/);

                group_fullname[group_fullname.length] = fullname;
                group_username[group_username.length] = username;
            }
            else
            {
                group_username[group_username.length] = username;
                group_fullname[group_fullname.length] = fullname;
            }

            vpb_setcookie ('g_fullnames', group_fullname, 30);
            vpb_setcookie ('g_username', group_username, 30);

            vpb_setcookie ('group_fnames', group_fullname, 30);
            vpb_setcookie ('group_unames', group_username, 30);

            $ ("#vpb_user_selected_" + username).hide ();
            $ ("#vpb_wall_members_data").val ('').focus ();

            $ ("#vpb_added_wall_users_box").append ('<span id="vpb_new_user_in_group_' + username + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vpb_remove_a_user_from_group(\'' + fullname + '\', \'' + username + '\')"></i></span></span>');
        }
    }
    else
    {
    }
}

// Get cookie
function vpb_getcookie (name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split (';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring (1, c.length);
        if (c.indexOf (nameEQ) == 0)
            return c.substring (nameEQ.length, c.length);
    }
    return null;
}

// Strip_tags
function vpb_strip_tags (input, allowed)
{
    allowed = (((allowed || '') + '').toLowerCase ().match (/<[a-z][a-z0-9]*>/g) || []).join ('');
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
            commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace (commentsAndPhpTags, '').replace (tags, function ($0, $1)
    {
        return allowed.indexOf ('<' + $1.toLowerCase () + '>') > -1 ? $0 : '';
    });
}

//Del

//Search for suggested users
function vpb_search_get_suggested_user (system_username)
{
    var group_names = vpb_getcookie ('group_fnames') == null ? '' : vpb_getcookie ('group_fnames');
    var formatted_names = group_names == "" ? "" : group_names.split (',').join ("|");

    if (system_username != "")
    {
        var dataString = {"system_username": system_username, "group_names": formatted_names, "page": "search_for_suggested_users"};
        $.ajax ({
            type: "POST",
            url: '/blab/user/searchForSuggestedUser',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#v_suggested_wall_users_box").fadeIn ();
                $ ("#vpb_suggested_users_displayer").html ('<div style="padding:10px; text-align:center;font-size:16px !important;">' + $ ("#v_loading_btn").val () + '</div>');
            },
            success: function (response)
            {
                $ ("#vpb_suggested_users_displayer").html (response);
            }
        });
    }
    else
    {
        $ ("#v_suggested_wall_users_box").fadeOut ();
        //$("#vpb_suggested_users_displayer").html('');
    }
}

function vpb_load_post_like_icons (post_id, username, selected, type)
{
    var dataStringA = {'post_id': post_id, "type": "post", 'username': username, 'selected': selected};
    $.ajax ({
        type: "POST",
        url: '/blab/like/getLikeCount',
        data: dataStringA,
        beforeSend: function ()
        {
            if (type == "auto")
            {
            }
            else
            {
                $ ("#vpb_system_like_title").html ($ ("#v_loading_btn").val ());
            }
        },
        success: function (response)
        {
            $ ("#vpb_system_like_title").html (response);
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_post_like_icons('" + parseInt (post_id) + "', '" + username + "', '" + selected + "', '" + type + "');", 10000);
    });
}


$ (".showLikes").on ("click", function ()
{

    var type = $ (this).attr ("type");
    var id = $ (this).attr ("id");
    $.ajax ({
        url: '/blab/like/showLikes',
        type: 'POST',
        data: {type: type, id: id},
        beforeSend: function ()
        {
            if (type == "comment")
            {
                $ ("#vpb_system_data_title").html ('People who like this');
                $ ("#vpb_display_wall_gen_data").html ($ ("#v_loading_btn").val ());
                $ ("#v-wall-g-data-alert-box").click ();
            }
            else
            {
                vpb_load_post_like_icons (id, "michael.hampton", "like", 'manual');

                $ ("#vpb_display_like_gen_data").html ('<br clear="all" />' + $ ("#v_loading_btn").val ());
                $ ("#v-like-g-data-alert-box").click ();
            }


        },
        success: function (response)
        {

            if (type == "comment")
            {
                $ ("#vpb_display_wall_gen_data").html (response);

            }
            else
            {
                $ ("#vpb_display_like_gen_data").html (response);
            }


        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});
$ ("#uploadForm2").on ('submit', (function (e)
{
    e.preventDefault ();
    $.ajax ({
        url: "/blab/user/uploadProfile",
        type: "POST",
        data: new FormData (this),
        beforeSend: function ()
        {
            $ ("#body-overlay").show ();
        },
        contentType: false,
        processData: false,
        success: function (data)
        {
            $ ("#targetLayer").css ('opacity', '1');
            setInterval (function ()
            {
                $ ("#body-overlay").hide ();
            }, 500);
        },
        error: function ()
        {
            showErrorMessage ();
        }
    });
}));

$ (document).off ("submit", "#uploadForm");
$ (document).on ('submit', '#uploadForm', function ()
{
    //stop submit the form, we will post it manually.
    event.preventDefault ();
    // Get form
    var form = $ ('#uploadForm')[0];
    // Create an FormData object
    var data = new FormData (form);
    // If you want to add an extra field for the FormData

    var mentions = $ ('#uploadComment').mentionsInput ('getMentions');

    var taggedPhotoUsers = [];
    $.each (mentions, function (idx2, val2)
    {
        taggedPhotoUsers.push (val2.uid);
    });

    var tags = taggedPhotoUsers.join (", ");
    data.append ("tags", tags);

    data.append ("selectedImageType", selectedImageType);
    data.append ("selectedImageId", selectedImageId);
    data.append ("uploadComment", vpb_trim ($ ('#uploadComment').val ()));

    $ ("#dvPreview").html ("");
    // disabled the submit button
    $ (".Upload").prop ("disabled", false);
    $.ajax ({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "/blab/index/upload",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function (response)
        {
            try {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    showErrorMessage ('Unable to save post');
                }
            } catch (error) {
                $ ("#posts-list").prepend (response);
                $ (".Upload").prop ("disabled", false);
                $ (".StartUpload").show ();
                $ (".Upload").hide ();
                $ ("#photoUpload").modal ('hide');
            }
        },
        error: function (e)
        {

            $ ("#result").text (e.responseText);
            $ ("#btnSubmit").prop ("disabled", false);
        }
    });
});
$ (".StartUpload").on ("click", function ()
{

    $ ("#fileupload").click ();
    $ ("#v_status_update_box").css ('z-index', 99);
});
$ (document).off ("change", "#fileupload");
$ (document).on ('change', '#fileupload', function ()
{
    $ (".StartUpload").hide ();
    $ ("#photoUpload").modal ("show");
    $ (".Upload").show ();

    if (typeof (FileReader) != "undefined")
    {
        var dvPreview = $ ("#dvPreview");
        dvPreview.html ("");
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
        $ ($ (this)[0].files).each (function ()
        {
            var file = $ (this);
            if (regex.test (file[0].name.toLowerCase ()))
            {
                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    var img = $ ("<img />");
                    img.attr ("style", "width:45%; float:left; height:300px; margin-right: 10px;margin-top: 10px;");
                    img.attr ("src", e.target.result);
                    dvPreview.append (img);
                }
                reader.readAsDataURL (file[0]);
            }
            else
            {
                showErrorMessage (file[0].name + " is not a valid image file.");
                dvPreview.html ("");
                return false;
            }
        });
    }
    else
    {
        showErrorMessage ("This browser does not support HTML5 FileReader.");
    }
});


$ (document).off ("click", ".show-chat");
$ (document).on ('click', '.show-chat', function ()
{

    $ (".show-chat").removeClass ("selectedChat");
    $ (this).addClass ("selectedChat");

    //$ (".chatWindow").show ("slide", {direction: "right"}, 500);
    var userId = $ (this).attr ("user-id");
    var username = $ (this).attr ("username");
    var fullname = $ (this).attr ("fullname");
    var session_user = $ (this).attr ("session-user");
    var group_id = $ (this).attr ("groupid");
    var group_name = $ (this).attr ("groupname");

    if ($ (this).hasClass ("single"))
    {
        $ (this).find ("i").removeClass ("text-danger").addClass ("text-navy");
        var data = {userId: userId};
    }
    else
    {
        var data = {groupId: group_id};
    }

    $.ajax ({
        url: '/blab/chat/chat',
        type: 'POST',
        data: data,
        success: function (response)
        {
            $ (".chatWindow").html (response);

            vpb_start_conversation (session_user, fullname, username, group_id, group_name);

        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });

    //$ (this).parent ().parent ().addClass ("vpb_users_wraper_active");


    return false;
});
$ ("#friendRequests").click (function ()
{
    $.ajax ({
        url: '/blab/friend/readFriendList',
        type: 'GET',
        success: function (response)
        {
            $ (this).css ({"background": "url('images/blab-icons.png') 0 -150px"});
            $ ("#counter").fadeOut ("slow");
            $ ("#v_site_menu_box").hide ();
            $ ('.dropdown-menu2').hide ();
            $ ("#friendRequests_bar").fadeToggle (200);
            $ ("#friendRequests").css ({"background": "url('images/blab-icons.png') 0 -77px"});
            pollTask ();
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });


    return false;
});
$ ("#confirm1").click (function ()
{
    $ (this).hide ();
    $ ("#remove1").hide ();
    $ ("#info1").html ("Successfully added as a friend");
    $ ("#requestsBar1").fadeOut (3000); // 3000 = 3seconds
});
$ ("#remove1").click (function ()
{
    $ ("#requestsBar1").fadeOut ("slow");
});
$ ("#confirm2").click (function ()
{
    $ (this).hide ();
    $ ("#remove2").hide ();
    $ ("#info2").html ("Successfully added as a friend");
    $ ("#requestsBar2").fadeOut (3000);
});
$ ("#remove2").click (function ()
{
    $ ("#requestsBar2").fadeOut ("slow");
});
$ (document).click (function ()
{
    $ ("#friendRequests_bar").fadeOut ("fast");
});
$ ("#friendRequests_bar").click (function ()
{

    return false;
});
$ (document).off ("click", ".loadmore");
$ (document).on ('click', '.loadmore', function ()
{
    $ (this).find (".loading-bar").text ('Loading...');
    var ele = $ (this);
    $.ajax ({
        url: '/blab/index/loadmore',
        type: 'POST',
        data: {
            page: $ (this).data ('page'),
        },
        success: function (response)
        {
            if (response)
            {
                ele.hide ();
                $ ("#posts-list").append (response);
                loadVideos ();
            }
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});
$ ('#commentform').submit (handleSubmit);

$ (document).off ("click", ".dropdown-toggle");
$ (document).on ('click', '.dropdown-toggle', function ()
{
    $ ('.count').html ('');
    load_unseen_notification ('yes');
});

$ (document).off ("click", ".closeNotification");
$ (document).on ('click', '.closeNotification', function ()
{
    $ ('.dropdown-menu2').hide ();
});


if (allowPolling === true)
{
    setInterval (function ()
    {
        load_unseen_notification ();
        ;
    }, 10000);

    load_unseen_notification ();

    pollTask ();

    setTimeout (
            function ()
            {
                pollTask ();
            }, 10000);
}



$ (document).off ("click", ".viewMore");
$ (document).on ("click", ".viewMore", function (e)
{
    var id = $ (this).attr ("id");
    if (!id)
    {
        showErrorMessage ("Invalid id");
        return false;
    }

    var totalToShow = 1;

    shown = $ (".social-footer[id=" + id + "] > .comment-a1:visible").size ();

    var items = $ (".social-footer[id=" + id + "] > .comment-a1").size ();


    $ (this).remove ();

    if (shown <= items)
    {
        totalToShow = shown + totalToShow;

        $ (".social-footer[id=" + id + "] > .comment-a1:lt(" + totalToShow + ")").show ();

        if ((shown + 1) !== items)
        {
            $ (".social-footer[id=" + id + "] > .comment-a1:visible:last").append ('<br><a id="' + id + '" href="#" class="viewMore">View More Comments</a>');
        }
    }
    else
    {
        $ ('.social-footer[id=" + id + "] > .comment-a1:lt(' + items + ')').show ();
    }

    return false;
});

$ (document).off ("click", ".like");
$ (document).on ("click", ".like", function (e)
{
    var getID = $ (this).attr ('id');
    if (!getID)
    {
        showErrorMessage ("Invalid Id");
        return false;
    }

    if ($ (this).hasClass ("comment"))
    {

        var commentId = $ (this).attr ("comment-id");

        if (!commentId)
        {
            showErrorMessage ();
        }
    }
    else
    {
        var commentId = 'null';
    }

    var element = $ (this);
    var element2 = $ (this).parent ().parent ().find (".showLikes");

    $ ("#like-loader-" + getID).html ('<img src="loader.gif" alt="" />');
    $.ajax ({
        type: 'POST',
        url: '/blab/like/like',
        data: {id: getID, commentId: commentId},
        success: function (data)
        {
            var data = $.parseJSON (data);
            if (!data.likes)
            {
                showErrorMessage ("Unable to complete request");
                return false;
            }

            element2.html (data.likes + ' Likes');
            element.removeClass ("like");
            element.addClass ("unlike");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

/// unlike
$ (document).off ("click", ".unlike");
$ (document).on ("click", ".unlike", function (e)
{
    var getID = $ (this).attr ('id');
    if (!getID)
    {
        showErrorMessage ("Invalid Id");
        return false;
    }

    if ($ (this).hasClass ("comment"))
    {

        var commentId = $ (this).attr ("comment-id");

        if (!commentId)
        {
            showErrorMessage ();
        }
    }
    else
    {
        var commentId = 'null';
    }

    var element = $ (this);
    var element2 = $ (this).parent ().parent ().find (".showLikes");

    $ ("#like-loader-" + getID).html ('<img src="loader.gif" alt="" />');
    $.ajax ({
        type: 'POST',
        url: '/blab/like/unlike',
        data: {id: getID, commentId: commentId},
        success: function (data)
        {
            try {
                var data = $.parseJSON (data);
                if (!data.likes)
                {
                    showErrorMessage ("Unable to complete request");
                    return false;
                }

                if (data.likes = '' || data.likes <= 0)
                {
                    element2.html ("Be the first person to like this");
                }

                if (parseInt (data.likes) > 0)
                {
                    element2.html (data.likes + ' Likes');
                }
                else
                {
                    //element.html ('<i class="fa fa-thumbs-up"></i> Like this!>');
                }

                element.removeClass ("unlike");
                element.addClass ("like");
                element.html ('<i class="fa fa-thumbs-up"></i> Like this!');
            } catch (error) {

            }
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
});

$ (document).off ("click", ".SubmitNewComment");
$ (document).on ("click", ".SubmitNewComment", function (e)
{
    var id = $ (this).attr ("comment-id");
    var comment = $ (".reply-comment[comment-id=" + id + "]").val ();

    if ($.trim (comment) === '')
    {
        showErrorMessage ("You must enter a comment");
        return false;
    }

    handleCommentReply (comment, id);
    $ (".SubmitNewComment").prop ("disabled", true);
});

$ (document).off ("keyup", ".reply-comment");
$ (document).on ("keyup", ".reply-comment", function (e)
{
    var comment = $ (this).val ();

    if ($ (this).val ().length > 0)
    {
        $ (".SubmitNewComment").prop ("disabled", false);
    }
    else
    {
        $ (".SubmitNewComment").prop ("disabled", true);
    }


    var id = $ (this).attr ("comment-id");
    var code = e.keyCode ? e.keyCode : e.which;
    if (code == 13)
    {
        if ($.trim (comment) === '')
        {
            showErrorMessage ("You must enter a comment");
            return false;
        }

        $ (".SubmitNewComment").prop ("disabled", true);
        handleCommentReply (comment, id);

    }
});

// Hide profile popups
function vpb_hide_profile_popups ()
{
    // Hide the sorting details
    $ ("#vpb_sorting_box").hide ();
    $ ("#vpb_sorting_text").removeClass ('vpb_sort_text_active');
    $ ("#vpb_sorting_text").addClass ('vpb_sort_text');

    // Hide the website menus
    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    $ ("#friendRequests_bar").hide ();
    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    $ (".dropdown-menu2").hide ();
    $ ("#v_notifications").removeClass ('vpb_notifications_icon_active');
    $ ("#v_notifications").addClass ('vpb_notifications_icon');

    $ ("#v_message_box").hide ();
    $ ("#v_new_messages").removeClass ('vpb_notifications_icon_active');
    $ ("#v_new_messages").addClass ('vpb_notifications_icon');

    // Hide the search box
    $ ("#vpb_display_search_results").hide ();
    $ ("#v_friend_requests_box").hide ();
}

function vpb_get_all_the_users_groups ()
{
    var session_username = $ ("#session_uid").val (); //The username of the current logged in user
    var page_username = $ ("#vpb_page_owner").val (); //The username of the page owner
    var total_per_load = $ ("#vpb_total_friends_per_load").val (); //Total friends to load each time

    var dataString = {"page_username": page_username, "session_username": session_username, "page": "vpb_get_all_the_users_groups"};

    $.ajax ({
        type: "POST",
        url: '/blab/group/getSuggestedGroups',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            $ ("#vpb_get_all_the_users_groups").html ('<div style="padding:10px;padding-bottom:0px;">' + loader + '</div>');
        },
        success: function (response)
        {
            $ ("#vpb_get_all_the_users_groups").html (response);
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_get_all_the_users_groups();", 10000);
    });
}


// Update status clicked
function vpb_update_status_clicked ()
{
    document.getElementById ('comment').click ();
    document.getElementById ('comment').focus ();
}

function vpb_show_post_bg ()
{
    $ ('#vpb_close_post_box').show ();
    $ ('.post_box_bg').show ();
    $ ("#v_status_update_box").css ('z-index', 999999);
    //$ ('#v_status_update_box').addClass ('fixed_post_box');
    //$ ('#vpb_status_update_wraps').addClass ('fixed_post_wraps');
}

function vpb_close_post_box ()
{
    $ ('#vpb_close_post_box').hide ();
    $ ('.post_box_bg').hide ();
    $ ('#vpb_status_update_wraps').removeClass ('fixed_post_wraps');
    $ ('#v_status_update_box').removeClass ('fixed_post_box');
}

// Show added details when user begins to type his or her post
function vpb_show_added_details ()
{
    if (vpb_trim ($ ("#the_tagged_friends").text ()) != "")
    {
        $ (".v_wall_tag_with").show ();
        $ ("#tagged_list").css ('display', 'inline-block');
    }
    else
    {
    }

    if (vpb_trim ($ ("#the_selected_location").text ()) != "")
    {
        $ ("#user_selected_this_location").css ('display', 'inline-block');
    }
    else
    {
    }


    if (vpb_trim ($ ("#vpb_selected_image_num").text ()) != "" && parseInt ($ ("#vpb_selected_image_num").text ()) > 0)
    {
        $ ("#url_content_wrapper").css ('display', 'inline-block');
    }
    else
    {
    }
}



//Delete cookie
function vpb_removecookie (name)
{
    vpb_setcookie (name, "", -1);
}

// Set cookie
function vpb_setcookie (name, value, days)
{
    if (days)
    {
        var date = new Date ();
        date.setTime (date.getTime () + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString ();
    }
    else
        var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

// Update Passwd
function vpb_update_passwd (my_identity)
{
    var oldpasswd = $ ("#oldpasswd").val ();
    var newpasswd = $ ("#newpasswd").val ();
    var verifynewpasswd = $ ("#verifynewpasswd").val ();

    if (my_identity == "" || my_identity == undefined || my_identity == null)
    {
        $ ("#update_passwd_status").html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if (oldpasswd == "" || oldpasswd == "Current Password")
    {
        $ ("#oldpasswd").focus ();
        $ ("#update_passwd_status").html ('<div class="vwarning">' + $ ("#current_user_password_field_blank_text").val () + '</div>');
        return false;
    }
    else if (newpasswd == "" || newpasswd == "New Password")
    {
        $ ("#newpasswd").focus ();
        $ ("#update_passwd_status").html ('<div class="vwarning">' + $ ("#new_user_password_field_blank_text").val () + '</div>');
        return false;
    }
    else if (verifynewpasswd == "" || verifynewpasswd == "Verify new Password")
    {
        $ ("#verifynewpasswd").focus ();
        $ ("#update_passwd_status").html ('<div class="vwarning">' + $ ("#verify_new_user_password_field_blank_text").val () + '</div>');
        return false;
    }
    else if (verifynewpasswd != newpasswd)
    {
        $ ("#verifynewpasswd").focus ();
        $ ("#update_passwd_status").html ('<div class="vwarning">' + $ ("#verify_and_new_user_password_field_not_match_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'my_identity': my_identity, 'oldpasswd': oldpasswd, 'newpasswd': newpasswd, 'verifynewpasswd': verifynewpasswd, 'page': 'update-user-passwd'};

        //$ ("#update_passwd_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $.post ('/blab/user/resetPassword', dataString, function (response)
        {
            var response_brougght = response.indexOf ('changed and saved successfully');
            if (response_brougght != -1)
            {
                $ ("#update_passwd_status").html (response);
                $ ("#oldpasswd").val ('');
                $ ("#newpasswd").val ('');
                $ ("#verifynewpasswd").val ('');
                setTimeout (function ()
                {
                    $ ('.modal').modal ('hide');
                    $ ("#update_passwd_status").html ('');
                }, 8000);
                return false;
            }
            else
            {
                $ ("#update_passwd_status").html (response);
                return false;
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_update_passwd('" + my_identity + "');", 10000);
        });
    }
}


// logout
function vpb_log_user_off (url)
{
    vpb_removecookie ('session_user_data');
    vpb_removecookie ('uep_data');

    vpb_removecookie ('g_fullnames');
    vpb_removecookie ('g_username');

    //End chat session
    vpb_removecookie ('from_username');
    vpb_removecookie ('to_username');
    vpb_removecookie ('to_fullname');

    vpb_removecookie ('vpb_chat_users_array');

    vpb_removecookie ('vpb_ckpoint');

    vpb_removecookie ('csname');
    vpb_removecookie ('csemail');

    setTimeout (function ()
    {
        window.location.replace (url);
    }, 500);
}

function bindhover ()
{
    $ (".like").hover (
            function ()
            {
                var messageid = $ (this).attr ("id");
                $ (".feelings-box[messageid=" + messageid + "]").show ();
            }, function ()
    {
        var messageid = $ (this).attr ("id");

        setTimeout (function ()
        {
            $ (".feelings-box[messageid=" + messageid + "]").hide ();
        }, 4000);



    }
    );
}

function vpb_show_previous_search_results ()
{
    vpb_hide_profile_popups ();
    if (vpb_trim ($ ('#vpb_display_search_results').text ()) == "")
    {
    }
    else
    {
        $ ("#vpb_display_search_results").show ();
        $ ("#v_search_results_box").css ('display', 'block');
    }
}


function addNewFriendRequest (friendId)
{
    var el = $ (".addAFriend[friend-id=" + friendId + "]");
    $.ajax ({
        url: '/blab/friend/addFriend',
        type: 'POST',
        data: {friendId: friendId},
        success: function (response)
        {
            el.removeClass ("btn-success");
            el.removeClass ("addFriend");
            el.addClass ("btn-warning");
            el.text ("Pending");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
}

function confirmFriend (friendId)
{

    var el = $ (".doFriendConfirmation[friend-id=" + friendId + "]");
    $.ajax ({
        url: '/blab/friend/confirmFriend',
        type: 'POST',
        data: {friendId: friendId},
        success: function (response)
        {
            el.removeClass ("btn-warning");
            el.addClass ("btn-primary");
            el.text ("Accepted");
        }
        ,
        error: function ()
        {
            showErrorMessage ();
        }
    });
}

// Preview File(s)
function vpb_image_previews (vpb_selector_, info, prev_label)
{
    var id = 1, last_id = last_cid = '';
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                $ ('#vpb-display-attachment-preview').html ('');
                $ ('#add_photos').val ('');
                $ ('#vpb_photos').html ('');
                $ ("#vpb_added_photos").show ();
                $ ("#add_a_video_button").hide ();
                $ ("#add_a_photo_button").hide ();

                $ ('#vpb-display-attachment-preview').html ("");
                var label = parseInt (id) > 1 ? "files" : "file";
                $ ('#additional_photos').fadeIn ().html (parseInt (id) + " " + label + " added");
                id++;

                $ ('#vRemovePhotos').hide ();
                $ ('#vRemoveFiles').show ();
                $ ('#additional_size').val (parseInt ($ ("#vpb_added_photos").height ()) + 20);
                $("#vpb_submitBox").prop("disabled", false);
                vpb_set_box_height ();
            }
            else
            {
                // Not supported
                if (parseInt (id) > 1)
                {
                }
                else
                {
                    $ ('#add_files').val ('');
                    $ ('#additional_photos').html ("");
                    $ ("#add_a_video_button").show ();
                    $ ("#add_a_photo_button").show ();
                    $ ("#vpb_added_photos").hide ();

                    $ ('#additional_size').val (0);
                    vpb_set_box_height ();
                }
                $ ("#v-pms-message").html ($ ("#wrong_files_added_text").val ());
                $ ("#v-pms-alert-box").click ();
                return false;
            }
        }
        else
        {
            return false;
        }
    });
}

// Remove added files
function vpb_remove_files ()
{
    $ ('#add_files').val ('');
    $ ('#additional_photos').html ("");
    $ ("#add_a_video_button").show ();
    $ ("#add_a_photo_button").show ();
    $ ("#vpb_added_photos").hide ();
    $ ('#vRemoveFiles').hide ();

    $ ('#additional_size').val (0);
    vpb_set_box_height ();
}

// The first seen URL in a message
function vpb_get_url_in_message (message)
{
    var vpb_message_regex_url = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    var vpb_url_in_message = vpb_trim (message).match (vpb_message_regex_url);

    var first_url;
    var vpb_determiner = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;

    if (vpb_url_in_message == "" || vpb_url_in_message == null)
    {
        first_url = "";
    }
    else
    {
        if (vpb_url_in_message.length > 0)
        {
            if (vpb_url_in_message.length == 1)
            {
                first_url = vpb_url_in_message[0];
            }
            else if (vpb_url_in_message.length == 2)
            {
                var check_first = (vpb_url_in_message[0].match (vpb_determiner)) ? RegExp.$1 : false;

                if (check_first === false)
                {
                    first_url = vpb_url_in_message[1];
                }
                else
                {
                    first_url = vpb_url_in_message[0];
                }
            }
            else if (vpb_url_in_message.length == 3)
            {
                var check_first = (vpb_url_in_message[0].match (vpb_determiner)) ? RegExp.$1 : false;
                var check_second = (vpb_url_in_message[1].match (vpb_determiner)) ? RegExp.$1 : false;

                if (check_first === false)
                {
                    if (check_second === false)
                    {
                        first_url = vpb_url_in_message[2];
                    }
                    else
                    {
                        first_url = vpb_url_in_message[1];
                    }
                }
                else
                {
                    first_url = vpb_url_in_message[0];
                }
            }
            else if (vpb_url_in_message.length == 4)
            {
                var check_first = (vpb_url_in_message[0].match (vpb_determiner)) ? RegExp.$1 : false;
                var check_second = (vpb_url_in_message[1].match (vpb_determiner)) ? RegExp.$1 : false;
                var check_third = (vpb_url_in_message[2].match (vpb_determiner)) ? RegExp.$1 : false;

                if (check_first === false)
                {
                    if (check_second === false)
                    {
                        if (check_third === false)
                        {
                            first_url = vpb_url_in_message[3];
                        }
                        else
                        {
                            first_url = vpb_url_in_message[2];
                        }
                    }
                    else
                    {
                        first_url = vpb_url_in_message[1];
                    }
                }
                else
                {
                    first_url = vpb_url_in_message[0];
                }
            }
            else
            {
                first_url = vpb_url_in_message[0];
            }
        }
        else
        {
            first_url = '';
        }
    }
    return first_url;
}


// Set the height of boxes
function vpb_set_box_height ()
{
    var additional = $ ('#additional_size').val ();
    var box_height = parseInt ($ (window).height ()) - 8;
    var box_width = parseInt (window.innerWidth) - 8;
    $ ('#vpb-main-pms-box').css ('height', parseInt (box_height) + 'px');

    if (parseInt (box_width) >= 875)
    {
        $ ('.pms_left_view_inner_bottom').css ('min-height', parseInt (box_height) - 190 + 'px');
        $ ('.pms_left_view_inner_bottom').css ('max-height', parseInt (box_height) - 190 + 'px');
        $ ('.pms_left_view_inner_bottom').css ('height', parseInt (box_height) - 190 + 'px');
    }
    else
    {
        $ ('.pms_left_view_inner_bottom').css ('min-height', 255 + 'px');
        $ ('.pms_left_view_inner_bottom').css ('max-height', 255 + 'px');
        $ ('.pms_left_view_inner_bottom').css ('height', 255 + 'px');
    }
    if (parseInt (box_width) >= 875)
    {
        var addtional = parseInt ($ ('#vpb_pms_message_data').height ()) + 230;
        var input_box_height = parseInt ($ (window).height ()) - parseInt (addtional);

        var dd_total = parseInt (additional) > 1 ? parseInt (input_box_height) - parseInt (additional) : parseInt (input_box_height);

        $ ('.pms_right_view_inner_bottom').css ('min-height', parseInt (dd_total) + 'px');
        $ ('.pms_right_view_inner_bottom').css ('max-height', parseInt (dd_total) + 'px');
        $ ('.pms_right_view_inner_bottom').css ('height', parseInt (dd_total) + 'px');

        $ ('.pms_right_view_inner_top').css ('min-height', parseInt (dd_total) + 'px');
        $ ('.pms_right_view_inner_top').css ('max-height', parseInt (dd_total) + 'px');
        $ ('.pms_right_view_inner_top').css ('height', parseInt (dd_total) + 'px');

        $ ('#vpb_default_compouse_section_height').val (parseInt (dd_total));
    }
    else
    {
        if (parseInt (box_width) <= 480)
        {
            var addtion = parseInt ($ ('#vpb_pms_message_data').height ()) + 526;
            var input_box_height = parseInt (window.screen.height) - parseInt (addtion);

            var the_total = parseInt (additional) > 1 ? parseInt (input_box_height) - parseInt (additional) : parseInt (input_box_height);

            $ ('.pms_right_view_inner_bottom').css ('min-height', parseInt (the_total) + 'px');
            $ ('.pms_right_view_inner_bottom').css ('max-height', parseInt (the_total) + 'px');
            $ ('.pms_right_view_inner_bottom').css ('height', parseInt (the_total) + 'px');

            $ ('.pms_right_view_inner_top').css ('min-height', parseInt (the_total) + 15 + 'px');
            $ ('.pms_right_view_inner_top').css ('max-height', parseInt (the_total) + 15 + 'px');
            $ ('.pms_right_view_inner_top').css ('height', parseInt (the_total) + 15 + 'px');

            $ ('#vpb_default_compouse_section_height').val (parseInt (the_total));
        }
        else
        {
            var addtion = parseInt ($ ('#vpb_pms_message_data').height ()) + 469;
            var input_box_height = parseInt (window.screen.height) - parseInt (addtion);

            var d_total = parseInt (additional) > 1 ? parseInt (input_box_height) - parseInt (additional) : parseInt (input_box_height);

            $ ('.pms_right_view_inner_bottom').css ('min-height', parseInt (d_total) + 'px');
            $ ('.pms_right_view_inner_bottom').css ('max-height', parseInt (d_total) + 'px');
            $ ('.pms_right_view_inner_bottom').css ('height', parseInt (d_total) + 'px');

            $ ('.pms_right_view_inner_top').css ('min-height', parseInt (d_total) + 'px');
            $ ('.pms_right_view_inner_top').css ('max-height', parseInt (d_total) + 'px');
            $ ('.pms_right_view_inner_top').css ('height', parseInt (d_total) + 'px');

            $ ('#vpb_default_compouse_section_height').val (parseInt (d_total));
        }
    }
}

//Get basename of file
function v_basename (url)
{
    //return ((url=/(([^\/\\\.#\? ]+)(\.\w+)*)([?#].+)?$/.exec(url))!= null)? url[2]: '';
    return url.replace (/\\/g, '/').replace (/.*\//, '');
}

//Like COMMENT Box
function vpb_popup_photo_box (post_id, total, current, first_photo_link)
{
    $ (".vholder").html ('<img src="' + first_photo_link + '">');
    $ ("#current_status_id").val (parseInt (post_id));
    $ ("#total_photos_to_scroll").val (parseInt (total));
    $ ("#current_scrolled_photo").val (parseInt (current));

    $ ("#vp_curnt").html (parseInt (current));
    $ ("#vp_total_phtos").html (parseInt (total));
    if (parseInt (current) == 1 && parseInt (total) == 1)
    {
        $ ("#vp_photo_scrol_counter").hide ();
    }
    else
    {
        $ ("#vp_photo_scrol_counter").fadeIn (2000);
    }
    //$("#_popupText").html('Post ID: '+post_id+' Total: '+total+' Current: '+current+' Link: '+first_photo_link);

    if (parseInt (current) == 1 && parseInt (total) > 1 && parseInt (current) != parseInt (total))
    {
        $ ("#vpb_left_b").hide ();
        $ ("#vpb_left_a").show ();

        $ ("#vpb_right_a").hide ();
        $ ("#vpb_right_b").show ();
    }
    else if (parseInt (current) == 1 && parseInt (total) == 1)
    {
        $ ("#vpb_left_b").hide ();
        $ ("#vpb_left_a").show ();

        $ ("#vpb_right_b").hide ();
        $ ("#vpb_right_a").show ();
    }
    else
    {
        $ ("#vpb_left_a").hide ();
        $ ("#vpb_left_b").show ();

        if (parseInt (current) == parseInt (total))
        {
            $ ("#vpb_right_b").hide ();
            $ ("#vpb_right_a").show ();
        }
        else
        {
            $ ("#vpb_right_a").hide ();
            $ ("#vpb_right_b").show ();
        }
    }
    $ ("#vpb_clicked_enlarge_photos").click ();
}

// Scroll to the new photo in photo enlargement
function vpb_scroll_popup_photo_next ()
{
    var post_id = $ ("#current_status_id").val ();
    var total = $ ("#total_photos_to_scroll").val ();
    var current = $ ("#current_scrolled_photo").val ();

    if (parseInt (current) <= parseInt (total))
    {
        var current_now = parseInt (current) == parseInt (total) ? parseInt (total) : parseInt (current) + 1;

        var photo_link = $ ("#hidden_photo_link_" + parseInt (post_id) + "_" + parseInt (current_now)).val ();

        $ (".vholder").html ('<img src="' + photo_link + '">');

        $ ("#current_scrolled_photo").val (parseInt (current_now));
        $ ("#vp_curnt").html (parseInt (current_now));

        var current = $ ("#current_scrolled_photo").val ();

        //$("#_popupText").html('Post ID: '+post_id+' Total: '+total+' Current: '+current+' Link: '+photo_link);

        if (parseInt (current) == parseInt (total))
        {
            $ ("#vpb_right_b").hide ();
            $ ("#vpb_right_a").show ();
        }
        else
        {
            $ ("#vpb_right_a").hide ();
            $ ("#vpb_right_b").show ();
        }
    }
    else
    {
    }

    var current = $ ("#current_scrolled_photo").val ();

    if (parseInt (current) == 1)
    {
        $ ("#vpb_left_b").hide ();
        $ ("#vpb_left_a").show ();
    }
    else if (parseInt (current) > 1)
    {
        $ ("#vpb_left_a").hide ();
        $ ("#vpb_left_b").show ();
    }
    else
    {
    }
}

// Scroll to the prev photo in photo enlargement
function vpb_scroll_popup_photo_prev ()
{
    var post_id = $ ("#current_status_id").val ();
    var total = $ ("#total_photos_to_scroll").val ();
    var current = $ ("#current_scrolled_photo").val ();

    if (parseInt (current) > 1)
    {
        var current_now = parseInt (current) == 1 ? 1 : parseInt (current) - 1;

        var photo_link = $ ("#hidden_photo_link_" + parseInt (post_id) + "_" + parseInt (current_now)).val ();
        $ (".vholder").html ('<img src="' + photo_link + '">');

        $ ("#current_scrolled_photo").val (parseInt (current_now));
        $ ("#vp_curnt").html (parseInt (current_now));

        var current = $ ("#current_scrolled_photo").val ();

        if (parseInt (current) < parseInt (total))
        {
            $ ("#vpb_right_a").hide ();
            $ ("#vpb_right_b").show ();
        }
        else
        {
            $ ("#vpb_right_b").hide ();
            $ ("#vpb_right_a").show ();
        }
    }
    else
    {
        $ ("#vpb_left_b").hide ();
        $ ("#vpb_left_a").show ();
    }

    var current = $ ("#current_scrolled_photo").val ();

    if (parseInt (current) == 1)
    {
        $ ("#vpb_left_b").hide ();
        $ ("#vpb_left_a").show ();
    }
    else if (parseInt (current) > 1)
    {
        $ ("#vpb_left_a").hide ();
        $ ("#vpb_left_b").show ();
    }
    else
    {
    }
}


// Add file to comment clicked
function vpb_add_file_to_comment_clicked (post_id)
{
    var name = v_basename ($ ("#comment_photo_" + parseInt (post_id)).val ());
    if (vpb_trim (name) != "")
    {
        $ ("#add_file_clicked_" + parseInt (post_id)).data ('original-title', name);
        $ ("#add_file_clicked_" + parseInt (post_id)).attr ('data-original-title', name);
        $ ("#add_file_clicked_" + parseInt (post_id)).attr ('title', name);
    }
    else
    {
        return false;
    }
}

// Preview File(s)
function vpb_comment_image_preview (vpb_selector_, info, prev_label, post_id)
{
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                $ ("#v-wall-message").html ($ ("#invalid_file_attachment").val ());
                $ ("#v-wall-alert-box").click ();
                return false;
            }
            else
            {
                $ (".comment_smiley_dropdown_menu").hide ();
                $ (".vpb_smiley_buttons").show ();

                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-comment-attachment-preview_' + parseInt (post_id)).html ('\
					  <div class="vpb_photos_wrapper_medium vasplus-tooltip-attached" title="Click to enlarge ' + escape (file.name) + '">\
						<a class="v_photo_holders" onClick="vpb_view_this_image(\'' + prev_label + '\', \'' + e.target.result + '\');">\
						  <img src="' + e.target.result + '" />\
						</a>\
					  </div>');
                    $ ('#vpb_preview_comment_photo_' + parseInt (post_id)).fadeIn ();
                    $ ('#remove_comment_photo_' + parseInt (post_id)).fadeIn ();
                }
                reader.readAsDataURL (file);
            }
            $ ('#vpb_wall_comment_data_' + parseInt (post_id)).focus ();
        }
        else
        {
            return false;
        }
    });
}

// Remove comment photo before commenting
function remove_comment_photo (post_id)
{
    $ ('#remove_comment_photo_' + parseInt (post_id)).hide ();
    $ ('#comment_photo_' + parseInt (post_id)).val ('');
    $ ('#vpb-display-comment-attachment-preview_' + parseInt (post_id)).html ('');
    $ ('#vpb_preview_comment_photo_' + parseInt (post_id)).fadeOut ();
    $ ("#add_file_clicked_" + parseInt (post_id)).data ('original-title', 'Attach a photo');
    $ ("#add_file_clicked_" + parseInt (post_id)).attr ('data-original-title', 'Attach a photo');
    $ ("#add_file_clicked_" + parseInt (post_id)).attr ('title', 'Attach a photo');
}

// Photo enlargement 
function vpb_view_this_image (title, photo)
{
    $ ("#photo_viewer_box_title").html (title);
    $ ("#photo_viewed_contents").html ('<img class="vpb_image_style" style="max-width:250px !important; width:100%;height:auto;margin:0 auto;" src="' + photo + '" />');
    $ ('#vpb_photo_viewer_display_box').modal ('show');

}

function displaySharedComment (data)
{
    var commentHtml = createSharedComment (data);
    var commentEl = $ (commentHtml);
    commentEl.hide ();
    var postsList = $ ('#posts-list');
    postsList.addClass ('has-comments');
    postsList.prepend (commentEl);
    commentEl.slideDown ();
}

function vpb_wall_location_box (action)
{
    $ ("#user_selected_this_location").hide ();
    $ ("#tagged_list").hide ();

    $ ("#vpb-location").val ('');

    if (action == "remove")
    {
        $ ("#vpb-selected-location").val ('');
        $ ("#the_selected_location").html ('');
    }
    else
    {
    }

    if ($ ("#user_is_at_this_location").css ('display') == "none")
    {
        // Hide the smiley section before showing the tagging section
        $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
        $ ("#add_smile_button").addClass ('vfooter_wraper');
        $ ("#vpb_the_wall_smiley_box").slideUp ();

        // Hide the tagging section before showing the tagging section
        $ ("#tag_people_button").removeClass ('vfooter_wraper_active');
        $ ("#tag_people_button").addClass ('vfooter_wraper');
        $ ("#start_typing_name").css ('display', 'none');

        $ ("#url_content_wrapper").css ('display', 'none');

        $ ("#add_location_button").removeClass ('vfooter_wraper');
        $ ("#add_location_button").addClass ('vfooter_wraper_active');

        $ ("#user_is_at_this_location").css ('display', 'inline-block');
        $ ("#vpb-location").focus ();

    }
    else
    {
        $ ("#vpb-location").val ('');
        $ ("#add_location_button").removeClass ('vfooter_wraper_active');
        $ ("#add_location_button").addClass ('vfooter_wraper');
        $ ("#user_is_at_this_location").css ('display', 'none');
    }

}

function vpb_hide_other_boxes ()
{
    // Hide selected location
    $ ("#user_selected_this_location").hide ();

    // Hide the smiley section
    $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
    $ ("#add_smile_button").addClass ('vfooter_wraper');
    $ ("#vpb_the_wall_smiley_box").slideUp ();

    // Hide the tagging section
    $ ("#tag_people_button").removeClass ('vfooter_wraper_active');
    $ ("#tag_people_button").addClass ('vfooter_wraper');
    $ ("#start_typing_name").css ('display', 'none');

    // Hide the location section
    $ ("#add_location_button").removeClass ('vfooter_wraper_active');
    $ ("#add_location_button").addClass ('vfooter_wraper');
    $ ("#user_is_at_this_location").css ('display', 'none');

    $ ("#url_content_wrapper").css ('display', 'none');
}


// Fetch YouTube or Vimeo video via URL
function vpb_fetch_video ()
{
    var session_uid = $ ("#session_uid").val ();
    var video_url = $ ("#add_video_url").val ();
    var page_identifier = $ ("#vpb_page_identifier").val ();

    if (video_url == "" || page_identifier == "")
    {
        $ ("#vpb-display-video").html ('');
        $ ("#v-wall-message").html ($ ("#no_video_link_message").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        var dataString = {'video_url': video_url};
        $.ajax ({
            type: "POST",
            url: '/blab/index/convertLinkToEmbed',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#vpb-display-video").html ($ ("#v_loading_btn").val ());
                //return false;
            },
            success: function (response)
            {                
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 1)
                {
                    $("#vpb_submitBox").prop("disabled", false);
                    $ ("#vpb-display-video").hide();

                    $ ("#add_a_photo_button").hide ();

                    $ ("#add_video_url").val ('');
                    $ ("#added_video_url").val (objResponse.data.id);

                    if ($ ("#pms_video_action").val () == "chat")
                    {
                        $ ("#chatMsg").val (video_url);
                        $ ("#vpb_added_videos").html (objResponse.data.video).show ();
                    }
                    else
                    {
                        $ ("#comment").val (video_url);
                        $ ("#output").html (objResponse.data.video).show ();
                    }


                    return false;
                }
                else
                {
                    // Unconfirmed
                    $ ("#add_video_url").val ('');
                    $ ("#vpb-display-video").html ('');
                    $ ("#vpb_video").html ('');
                    $ ("#vpb_added_videos").hide ();
                    $ ("#close_video_popup_button").click ();



                    $ ("#add_a_video_button").hide ();

                    //vpb_setcookie ('this_url_has_already_been_fetched', url_realised, 90);
                    $ ("#fetched_url_content").html ($ ("#v_loading_btn").val ());

                }

                vpb_fetch_url_contents (video_url);
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_fetch_video();", 10000);
        });
    }
}

// Remove added video before sending message
function vpb_remove_added_video ()
{

    var dataString = {'page': 'remove_added_video'};
    $.ajax ({
        type: "POST",
        url: '/blab/index/deleteVideo',
        data: dataString,
        beforeSend: function ()
        {
            $ ("#previewed_video").html ($ ("#v_loading_btn").val ());
            //return false;
        },
        success: function (response)
        {
            if ($.trim (response) === "")
            {
                $ ("#add_video_url").val ('');
                $ ("#added_video_url").val ("");
                $ ("#vpb-display-video").html ('');
                $ ("#vpb_video").html ('');
                $ ("#vpb_added_videos").fadeOut ();
                $ ("#add_a_photo_button").show ();
                $ ("#add_a_file_button").show ();
                $ ('#additional_size').val (0);
                vpb_set_box_height ();
            }
            else
            {
                $ ("#add_video_url").val ('');
                $ ("#previewed_video").html ('<span onclick="vpb_remove_added_video();" class="vhover">Remove</span>');

                $ ("#v-pms-message").html ($ ("#no_proper_data_passed").val ());
                setTimeout (function ()
                {
                    $ ("#v-pms-alert-box").click ();
                }, 10);
                return false;
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_remove_added_video();", 1000);
    });

}


//This function does the actual fetching of the submitted URL contents
function vpb_fetch_url_contents (url)
{

    var dataString = {"link": url};

    $.post ("/blab/index/extractUrl", dataString, function (response)
    {

        if ($.trim (response) === "")
        {
            $ ('#vpb_link_to_fetch').val ('');
            $ ("#url_content_wrapper").hide ();
            return false;
        }
        else
        {
            $ ("#comment").val (url);
            $ ("#vpb_video").html (response).show ();
            return false;

        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_fetch_url_contents('" + type + "');", 10000);
    });

}

//Show/Hide Smiley Box
function vpb_wall_smiley_box ()
{
    $ ("#user_selected_this_location").hide ();

    var vpb_box_state = $ ("#vpb_the_wall_smiley_box").css ('display');
    if (vpb_box_state == 'block')
    {
        $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
        $ ("#add_smile_button").addClass ('vfooter_wraper');
        $ ("#vpb_the_wall_smiley_box").slideUp ();
    }
    else
    {

        // Hide the tagging section before showing the smiley section
        $ ("#tag_people_button").removeClass ('vfooter_wraper_active');
        $ ("#tag_people_button").addClass ('vfooter_wraper');
        $ ("#start_typing_name").css ('display', 'none');

        // Hide the location section before showing the tagging section
        $ ("#add_location_button").removeClass ('vfooter_wraper_active');
        $ ("#add_location_button").addClass ('vfooter_wraper');
        $ ("#user_is_at_this_location").css ('display', 'none');

        $ ("#url_content_wrapper").css ('display', 'none');

        $ ("#add_smile_button").removeClass ('vfooter_wraper');
        $ ("#add_smile_button").addClass ('vfooter_wraper_active');
        $ ("#vpb_the_wall_smiley_box").slideDown ();
    }
}


function vpb_comment_smiley_box (post_id)
{
    $ (".reply_smiley_dropdown_menu").hide ();
    $ (".vpb_reply_smiley_buttons").show ();
    $ ("#smiley_identifier").val (parseInt (post_id));

    if ($ ("#vpb_comment_smiley_box_" + parseInt (post_id)).length > 0)
    {
        $ ("#vpb_comment_smiley_box_" + parseInt (post_id)).html ('<div class="vpb_wall_smiley_box_wrapper"><div class="vpb_smiley_inner_box"><span class="smiley_a" title="Smile" onclick="vpb_add_smiley_to_comment(\':)\');"></span><span class="smiley_b" title="Frown, Sad" onclick="vpb_add_smiley_to_comment(\':(\');"></span><span class="smiley_c" title="Blushing angel" onclick="vpb_add_smiley_to_comment(\':blushing-angel:\');"></span><span class="smiley_d" title="Cat face" onclick="vpb_add_smiley_to_comment(\':cat-face:\');"></span><span class="smiley_e" title="Confused" onclick="vpb_add_smiley_to_comment(\'o.O\');"></span><span class="smiley_f" title="Cry" onclick="vpb_add_smiley_to_comment(\':cry:\');"></span><span class="smiley_g" title="Laughing devil" onclick="vpb_add_smiley_to_comment(\':laughing-devil:\');"></span><span class="smiley_h" title="Shocked and surprised" onclick="vpb_add_smiley_to_comment(\':O\');"></span><span class="smiley_i" title="Glasses" onclick="vpb_add_smiley_to_comment(\'B)\');"></span><span class="smiley_j" title="Grin, Big Smile" onclick="vpb_add_smiley_to_comment(\':D\');"></span><span class="smiley_k" title="Upset and angry" onclick="vpb_add_smiley_to_comment(\':grumpy:\');"></span><span class="smiley_l" title="Heart" onclick="vpb_add_smiley_to_comment(\':heart:\');"></span><span class="smiley_m" title="Kekeke happy" onclick="vpb_add_smiley_to_comment(\'^_^\');"></span><span class="smiley_n" title="Kiss" onclick="vpb_add_smiley_to_comment(\':kiss:\');"></span><span class="smiley_o" title="Pacman" onclick="vpb_add_smiley_to_comment(\':v\');"></span><span class="smiley_p" title="Penguin" onclick="vpb_add_smiley_to_comment(\':penguin:\');"></span><span class="smiley_q" title="Unsure" onclick="vpb_add_smiley_to_comment(\':unsure:\');"></span><span class="smiley_r" title="Cool" onclick="vpb_add_smiley_to_comment(\'B|\');"></span><span class="smiley_s" title="Annoyed, sighing or bored" onclick="vpb_add_smiley_to_comment(\'-_-\');"></span><span class="smiley_t" title="Love" onclick="vpb_add_smiley_to_comment(\':lve:\');"></span><span class="smiley_u" title="Christopher Putnam" onclick="vpb_add_smiley_to_comment(\':putnam:\');"></span><span class="smiley_zb" title="Shark" onclick="vpb_add_smiley_to_comment(\'(wink)\');"></span><span class="smiley_v" title="Wink" onclick="vpb_add_smiley_to_comment(\'(wink)\');"></span><span class="smiley_w" title="No idea" onclick="vpb_add_smiley_to_comment(\'(off)\');"></span><span class="smiley_x" title="Got an idea" onclick="vpb_add_smiley_to_comment(\'(on)\');"></span><span class="smiley_y" title="Cup of tea" onclick="vpb_add_smiley_to_comment(\':tea-cup:\');"></span><span class="smiley_z" title="No, thumb down" onclick="vpb_add_smiley_to_comment(\'(n)\');"></span><span class="smiley_za" title="Yes, thumb up" onclick="vpb_add_smiley_to_comment(\'(y)\');"></span><div style="clear:both;"></div></div></div><div style="clear: both;"></div>');
    }
    else
    {
    }

    $ (".comment_smiley_dropdown_menu").hide ();
    $ (".vpb_smiley_buttons").show ();
    $ ("#vpb_the_comment_smiley_box" + parseInt (post_id)).slideDown ();
    $ ("#vpb_show_smiley_button_" + parseInt (post_id)).hide ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vpb_the_comment_smiley_box" + parseInt (post_id)).offset ().top - parseInt (130) + 'px'
    }, 1600, 'easeInOutExpo');
}
// Hide comment smiley box
function vpb_hide_comment_smiley_box (post_id)
{
    $ (".comment_smiley_dropdown_menu").hide ();
    $ (".vpb_smiley_buttons").show ();

    $ (".reply_smiley_dropdown_menu").hide ();
    $ (".vpb_reply_smiley_buttons").show ();

    $ ("#vpb_the_comment_smiley_box" + parseInt (post_id)).hide ();
    $ ("#vpb_show_smiley_button_" + parseInt (post_id)).show ();
}


// Reply smiley box
function vpb_reply_smiley_box (comment_id)
{
    $ (".comment_smiley_dropdown_menu").hide ();
    $ (".vpb_smiley_buttons").show ();
    $ ("#reply_smiley_identifier").val (parseInt (comment_id));

    if ($ ("#vpb_reply_smiley_box_" + parseInt (comment_id)).length > 0)
    {
        $ ("#vpb_reply_smiley_box_" + parseInt (comment_id)).html ('<div class="vpb_wall_smiley_box_wrapper"><div class="vpb_smiley_inner_box"><span class="smiley_a" title="Smile" onclick="vpb_add_smiley_to_reply(\':)\');"></span><span class="smiley_b" title="Frown, Sad" onclick="vpb_add_smiley_to_reply(\':(\');"></span><span class="smiley_c" title="Blushing angel" onclick="vpb_add_smiley_to_reply(\':blushing-angel:\');"></span><span class="smiley_d" title="Cat face" onclick="vpb_add_smiley_to_reply(\':cat-face:\');"></span><span class="smiley_e" title="Confused" onclick="vpb_add_smiley_to_reply(\'o.O\');"></span><span class="smiley_f" title="Cry" onclick="vpb_add_smiley_to_reply(\':cry:\');"></span><span class="smiley_g" title="Laughing devil" onclick="vpb_add_smiley_to_reply(\':laughing-devil:\');"></span><span class="smiley_h" title="Shocked and surprised" onclick="vpb_add_smiley_to_reply(\':O\');"></span><span class="smiley_i" title="Glasses" onclick="vpb_add_smiley_to_reply(\'B)\');"></span><span class="smiley_j" title="Grin, Big Smile" onclick="vpb_add_smiley_to_reply(\':D\');"></span><span class="smiley_k" title="Upset and angry" onclick="vpb_add_smiley_to_reply(\':grumpy:\');"></span><span class="smiley_l" title="Heart" onclick="vpb_add_smiley_to_reply(\':heart:\');"></span><span class="smiley_m" title="Kekeke happy" onclick="vpb_add_smiley_to_reply(\'^_^\');"></span><span class="smiley_n" title="Kiss" onclick="vpb_add_smiley_to_reply(\':kiss:\');"></span><span class="smiley_o" title="Pacman" onclick="vpb_add_smiley_to_reply(\':v\');"></span><span class="smiley_p" title="Penguin" onclick="vpb_add_smiley_to_reply(\':penguin:\');"></span><span class="smiley_q" title="Unsure" onclick="vpb_add_smiley_to_reply(\':unsure:\');"></span><span class="smiley_r" title="Cool" onclick="vpb_add_smiley_to_reply(\'B|\');"></span><span class="smiley_s" title="Annoyed, sighing or bored" onclick="vpb_add_smiley_to_reply(\'-_-\');"></span><span class="smiley_t" title="Love" onclick="vpb_add_smiley_to_reply(\':lve:\');"></span><span class="smiley_u" title="Christopher Putnam" onclick="vpb_add_smiley_to_reply(\':putnam:\');"></span><span class="smiley_zb" title="Shark" onclick="vpb_add_smiley_to_reply(\'(wink)\');"></span><span class="smiley_v" title="Wink" onclick="vpb_add_smiley_to_reply(\'(wink)\');"></span><span class="smiley_w" title="No idea" onclick="vpb_add_smiley_to_reply(\'(off)\');"></span><span class="smiley_x" title="Got an idea" onclick="vpb_add_smiley_to_reply(\'(on)\');"></span><span class="smiley_y" title="Cup of tea" onclick="vpb_add_smiley_to_reply(\':tea-cup:\');"></span><span class="smiley_z" title="No, thumb down" onclick="vpb_add_smiley_to_reply(\'(n)\');"></span><span class="smiley_za" title="Yes, thumb up" onclick="vpb_add_smiley_to_reply(\'(y)\');"></span><div style="clear:both;"></div></div></div><div style="clear: both;"></div>');
    }
    else
    {
    }

    $ (".reply_smiley_dropdown_menu").hide ();
    $ (".vpb_reply_smiley_buttons").show ();
    $ ("#vpb_the_reply_smiley_box" + parseInt (comment_id)).slideDown ();
    $ ("#vpb_show_reply_smiley_button_" + parseInt (comment_id)).hide ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vpb_the_reply_smiley_box" + parseInt (comment_id)).offset ().top - parseInt (130) + 'px'
    }, 1600, 'easeInOutExpo');
}
// Hide reply smiley box
function vpb_hide_reply_smiley_box (comment_id)
{
    $ (".reply_smiley_dropdown_menu").hide ();
    $ (".vpb_reply_smiley_buttons").show ();

    $ (".comment_smiley_dropdown_menu").hide ();
    $ (".vpb_smiley_buttons").show ();

    $ ("#vpb_the_reply_smiley_box" + parseInt (comment_id)).hide ();
    $ ("#vpb_show_reply_smiley_button_" + parseInt (comment_id)).show ();
}

// Continue with the fetched video to post
function vpb_continue_with_video ()
{
    var video_data = $ ("#added_video_url").val ();

    var response_data = video_data.split ('&');
    var video_id = response_data[0];
    var video_type = response_data[1];

    if (video_type == "youtube")
    {
        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="https://www.youtube.com/embed/' + video_id + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else if (video_type == "vimeo")
    {
        var response_sub_data = video_id.split ('|');
        var video_uid = response_sub_data[0];
        var video_option = response_sub_data[1];

        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="https://player.vimeo.com/video/' + video_uid + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else if (video_type == "metacafe")
    {
        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="http://www.metacafe.com/embed/' + video_id + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else if (video_type == "dailymotion")
    {
        //alert('ID '+video_id+' FINALLY: '+video_data);
        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="//www.dailymotion.com/embed/video/' + video_id + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else if (video_type == "flickr")
    {
        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="http://www.flickr.com/apps/video/stewart.swf?photo_id=' + video_id + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else if (video_type == "myspace")
    {
        $ ("#vpb_video").html ('<div class="embed-responsive embed-responsive-16by9">\
		  <iframe allowfullscreen class="embed-responsive-item" src="//media.myspace.com/play/video/' + video_id + '"></iframe>\
		</div>');

        document.getElementById ('add_video_url').value = '';
        document.getElementById ('vpb-display-video').innerHTML = '';
        $ ("#vpb_added_videos").slideDown ();
    }
    else
    {
    }
}

function createSharedComment (data)
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
            '<small>Shared ' + data.original_poster + 's post</small>' +
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
            '<button class="btn btn-white btn-xs"><i class="fa fa-comments"></i> Comment</button>' +
            '<button class="btn btn-white btn-xs Share" messageId="' + data.id + '"><i class="fa fa-share"></i> Share</button>' +
            '<a href="#" class="showLikes" type="post"></a>' +
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

    $ ("#vpb_hidden_post_id_".data.id).hide ("fast");

    return html;
}

//Cancel sharing a post
function vpb_cancel_post_sharing ()
{
    $ ("#v_share_this_conts").html ('');
}

function vpb_resize_this (evnt)
{
//    var observe;
//    if (window.attachEvent)
//    {
//        observe = function (element, event, handler)
//        {
//            element.attachEvent ('on' + event, handler);
//        };
//    }
//    else
//    {
//        observe = function (element, event, handler)
//        {
//            element.addEventListener (event, handler, false);
//        };
//    }
//
//    var text = document.getElementById ('vpb_wall_share_post_data');
//    var prevHeight = text.offsetHeight;//scrollHeight
//
//    function resize ()
//    {
//        text.style.height = 'auto';
//        text.style.height = text.scrollHeight + 'px';
//    }
//    /* 0-timeout to get the already changed text */
//    function delayedResize ()
//    {
//        window.setTimeout (resize, 1);
//    }
//    observe (text, 'change', resize);
//    observe (text, 'cut', delayedResize);
//    observe (text, 'paste', delayedResize);
//    observe (text, 'drop', delayedResize);
//    observe (text, 'keydown', delayedResize);
//
//    resize ();
//    text.focus ();
}

// Set privacy for the post which the user is about to share
function vpb_set_selected_share_option_privacy (name, id, lower_case_selected, icon, post_owner_username)
{
    $ ("#selected_option_shared_privacy_").val (lower_case_selected);

    $ ("#vpb_share_privacy_box").data ('original-title', name);
    $ ("#vpb_share_privacy_box").attr ('data-original-title', name);
    $ ("#vpb_share_privacy_box").attr ('title', name);
    $ ("#selected_privacy_option_share_option").html ('<i class="fa ' + icon + '"></i> ' + name + '&nbsp;&nbsp;<span class="caret"></span>');
    $ (".vasplus_share_privacy_ticker").hide ();
    $ ("#" + id).show ();
}

// Set where the user wants to share the update
function vpb_set_selected_share_option (name, id, lower_case_selected, icon, post_owner_username)
{
    $ ("#selected_option_shared_").val (lower_case_selected);
    $ ("#selected_option_share_option").html ('<i class="fa ' + icon + '"></i> ' + name + '&nbsp;&nbsp;<span class="caret"></span>');
    $ (".vasplus_area_ticker").hide ();
    $ ("#" + id).show ();

    if (lower_case_selected == "mywall")
    {
        // Remove the selected name so as to share on current user wall
        $ ("#vfriends_name_suggested").val ('');
        $ ("#selected_friend_to_share_with").val ('');
        $ ("#vfriends_suggestion_box").hide ();
        $ ("#vpb_share_privacy_box").css ('display', 'inline-block'); // Show privacy settings option
    }
    else
    {
        $ ("#vfriends_name_suggested").val ('');
        $ ("#selected_friend_to_share_with").val ('');
        setTimeout (function ()
        {
            $ ("#vfriends_name_suggested").focus ();
        }, 10);
        $ ("#vfriends_suggestion_box").fadeIn ();
        $ ("#v_share_publicly_button").click ();
        $ ("#vpb_share_privacy_box").css ('display', 'none'); // Show privacy settings option
    }
}

$ (".comment-a1").hover (
        function ()
        {
            console.log ("ENTERED");
            $ (this).find (".vpb_wrap_coms_icons").show ();
        }, function ()
{
    $ (this).find (".vpb_wrap_coms_icons").hide ();
}
);
function handleSubmit ()
{
    var mentions = $ ('.mentions').mentionsInput ('getMentions');

    var blkstr = [];
    $.each (mentions, function (idx2, val2)
    {

        blkstr.push (val2.uid);
    });

    if ($ ("#new_tags > span").length > 0)
    {
        $.each ($ ("#new_tags > span"), function (idx2, val2)
        {
            var userId = $ (this).attr ("userId");

            blkstr.push (userId);
        });
    }

    var tags = blkstr.join (", ");

    var form = $ (this);

    form.find (':submit').prop ('disabled', true);

    if (form.find ('#comment').val () === "")
    {
        showErrorMessage ("You must enter a comment");
        return false;
    }

    $ ("#selected_option_").html ($ ("#v_sending_btn").val ());

    var privacy_option = $ ("#selected_security_option_").val ();


    profileUser = typeof (profileUser) !== "undefined" && profileUser !== null ? profileUser : null;

    var data = {
        "comment": vpb_trim (form.find ('#comment').val ()),
        "profileUser": profileUser,
        "usersLocation": usersLocation,
        "privacyOption": privacy_option,
        tags: tags
    };
    postComment (data);
    return false;
}

function handleCommentReply (comment, id)
{
    if (!id)
    {
        showErrorMessage ("Invalid id");
        return false;
    }

    var ext = $ ("#comment_photo_" + parseInt (id)).val ().split ('.').pop ().toLowerCase ();
    var photos = $ ("#comment_photo_" + parseInt (id)).val ();


    if (comment == "" && vpb_trim (photos) == "")
    {
        $ ("#v-wall-message").html ($ ("#invalid_comment_update_field").val ());
        $ ("#v-wall-alert-box").click ();
        e.stopPropagation ();
        return false;
    }
    else
    {
        if (photos != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
        {
            $ ("#comment_photo_" + parseInt (id)).val ('');
            $ ("#add_file_clicked_" + parseInt (id)).data ('original-title', 'Attach a photo');
            $ ("#add_file_clicked_" + parseInt (id)).attr ('data-original-title', 'Attach a photo');
            $ ("#add_file_clicked_" + parseInt (id)).attr ('title', 'Attach a photo');

            $ ("#v-wall-message").html ($ ("#invalid_file_attachment").val ());
            $ ("#v-wall-alert-box").click ();
            return false;
        }
        else
        {
            var data = {
                "comment": comment,
                "id": id
            };


            $.ajax ({
                type: 'POST',
                url: '/blab/comment/postReply',
                data: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function ()
                {
                    $ ("#vpb_comment_bottom_icons_" + parseInt (id)).hide ();
                    vpb_hide_comment_smiley_box (parseInt (id));
                    $ ("#vpb_preview_comment_photo_" + parseInt (id)).hide ();
                    $ ("#vpb_display_comment_loading_" + parseInt (id)).html ($ ("#v_sending_btn").val ());
                    $ ("#vpb_comment_loading_" + parseInt (id)).show ();

                    $ ("#vpb_comment_box_" + parseInt (id)).removeClass ('enable_this_box');
                    $ ("#vpb_comment_box_" + parseInt (id)).addClass ('disable_this_box');

                    //vpb_security_check_points ();
                },
                success: postReplySuccess,
                error: function ()
                {
                    $ ("#vpb_comment_bottom_icons_" + parseInt (id)).show ();
                    $ ("#vpb_display_comment_loading_" + parseInt (id)).html ('');
                    $ ("#vpb_comment_loading_" + parseInt (id)).hide ();

                    $ ("#vpb_comment_box_" + parseInt (id)).removeClass ('disable_this_box');
                    $ ("#vpb_comment_box_" + parseInt (id)).addClass ('enable_this_box');

                    //$ ("#v-wall-message").html (response);
                    //$ ("#v-wall-alert-box").click ();
                    return false;
                }
            });
        }
    }


}

function postReplySuccess (data)
{
    $ ('.reply-comment').val ('');
    displayReply (data);
    return false;
}

//Add Image File to Comment
function vpb_add_photos_to_comment (id, post_id)
{

    if (parseInt (id) == "" || parseInt (post_id) == "")
    {
        $ ("#vpb_comment_bottom_icons_" + parseInt (post_id)).show ();
        $ ("#vpb_display_comment_loading_" + parseInt (post_id)).html ('');
        $ ("#vpb_comment_loading_" + parseInt (post_id)).hide ();

        $ ("#vpb_comment_box_" + parseInt (post_id)).removeClass ('disable_this_box');
        $ ("#vpb_comment_box_" + parseInt (post_id)).addClass ('enable_this_box');

        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('comment_photo_' + parseInt (post_id)).files;

        // Create a formdata object and append the files
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });
        vpb_data.append ("id", id);
        vpb_data.append ("page", 'vpb_final_comment_update_submission');


        $.ajax ({
            url: '/blab/comment/uploadCommentPhoto',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {},
            success: function (response)
            {
                $ ("#vpb_comment_box_" + parseInt (post_id)).removeClass ('disable_this_box');
                $ ("#vpb_comment_box_" + parseInt (post_id)).addClass ('enable_this_box');

                $ ("#vpb_comment_bottom_icons_" + parseInt (post_id)).show ();
                $ ("#vpb_display_comment_loading_" + parseInt (post_id)).html ('');
                $ ("#vpb_comment_loading_" + parseInt (post_id)).hide ();
                $ ("#remove_comment_photo_" + parseInt (post_id)).hide ();

                var response_brought = response.indexOf ('post_no_found');
                if (response_brought != -1)
                {
                    $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                    $ ("#v-wall-alert-box").click ();
                    return false;
                }
                else
                {
                    $ ("#comment_photo_" + parseInt (post_id)).val ('');
                    $ ("#vpb_wall_comment_data_" + parseInt (post_id)).val ('').change ();

                    $ ("#vpb_comment_updated_" + parseInt (post_id)).append (
                            $ (response).hide ().fadeIn ('slow')
                            );

                    $ ('html, body').animate ({
                        scrollTop: $ ("#vpb_comment_id_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
                    }, 1600, 'easeInOutExpo');
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_add_photos_to_comment('" + parseInt (id) + "', '" + parseInt (post_id) + "');", 10000);
        });
    }
}

function displayReply (data)
{
    var data = $.parseJSON (data);

    if (!data.id)
    {
        showErrorMessage ("Invalid id");
        return false;
    }

    vpb_add_photos_to_comment (parseInt (data.comment_id), parseInt (data.id));
    var commentHtml = createReply (data);
    var commentEl = $ (commentHtml);
    commentEl.hide ();
    var postsList = $ ('.social-footer[id=' + data.id + ']');
    postsList.addClass ('has-comments');
    postsList.prepend (commentEl);
    commentEl.slideDown ();
    $ ("div[id^='vpb_hidden_comment_id_']").hide ();
    $ ("div[id^='vpb_editable_comment_wrapper_']").hide ();
    $ (".vpb_reply_posting_wrapper").hide ();


}

// Un-hide post, comment or reply
function vpb_unhide_this_wall_item (id, username, type)
{

    var dataString = {"id": id, "username": username, "type": type};

    $.post ('/blab/comment/unhideComment', dataString, function (response)
    {

        if (type == "post") // post
        {
            $ (".social-feed-box[postid=" + parseInt (id) + "]").fadeIn ();
            $ ("#vpb_hidden_post_id_" + $.trim (id)).hide ();
        }
        else if (type == "comment") // comment
        {
            $ (".comment-a1[commentid=" + id + "]").fadeIn ();
            $ ("#vpb_hidden_comment_id_" + id).hide ();
        }
        else // reply
        {
            $ ("#vpb_hidden_reply_id_" + parseInt (id)).hide ();
            $ ("#vpb_unhidden_reply_id_" + parseInt (id)).fadeIn ();
        }

    }).fail (function (error_response)
    {
        setTimeout ("vpb_unhide_this_wall_item('" + parseInt (id) + "', '" + username + "', '" + type + "');", 10000);
    });
}

function vpb_show_reply_box (comment_id)
{
    if ($ ("#vpb_reply_box_" + parseInt (comment_id)).css ('display') == "none")
    {
        $ (".vpb_reply_posting_wrapper").hide ();
        $ ("#vpb_reply_box_" + parseInt (comment_id)).show ();
    }
    else
    {
    }
    $ ("#vpb_wall_reply_data_" + parseInt (comment_id)).click ();
    $ ("#vpb_wall_reply_data_" + parseInt (comment_id)).focus ();

    $ ('html, body').animate ({
        scrollTop: $ ("#vpb_wall_reply_data_" + parseInt (comment_id)).offset ().top - parseInt (150) + 'px'
    }, 1600, 'easeInOutExpo');
}


// Remove comment photo before commenting
function remove_reply_photo (comment_id)
{
    $ ('#remove_reply_photo_' + parseInt (comment_id)).hide ();
    $ ('#reply_photo_' + parseInt (comment_id)).val ('');
    $ ('#vpb-display-reply-attachment-preview_' + parseInt (comment_id)).html ('');
    $ ('#vpb_preview_reply_photo_' + parseInt (comment_id)).fadeOut ();
    $ ("#add_reply_file_clicked_" + parseInt (comment_id)).data ('original-title', 'Attach a photo');
    $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('data-original-title', 'Attach a photo');
    $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('title', 'Attach a photo');
}

// Preview Reply photo
function vpb_reply_image_preview (vpb_selector_, info, prev_label, comment_id)
{
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                $ ("#v-wall-message").html ($ ("#invalid_file_attachment").val ());
                $ ("#v-wall-alert-box").click ();
                return false;
            }
            else
            {
                $ (".reply_smiley_dropdown_menu").hide ();
                $ (".vpb_reply_smiley_buttons").show ();

                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-reply-attachment-preview_' + parseInt (comment_id)).html ('\
					  <div class="vpb_photos_wrapper_medium vasplus-tooltip-attached" title="Click to enlarge ' + escape (file.name) + '">\
						<a class="v_photo_holders" onClick="vpb_view_this_image(\'' + prev_label + '\', \'' + e.target.result + '\');">\
						  <img src="' + e.target.result + '" />\
						</a>\
					  </div>');
                    $ ('#vpb_preview_reply_photo_' + parseInt (comment_id)).fadeIn ();
                    $ ('#remove_reply_photo_' + parseInt (comment_id)).fadeIn ();
                }
                reader.readAsDataURL (file);
            }
            $ ('#vpb_wall_reply_data_' + parseInt (comment_id)).focus ();
        }
        else
        {
            return false;
        }
    });
}

//Add Image File to reply
function vpb_add_photos_to_reply (id, comment_id)
{
    var username = $ ("#session_uid").val ();

    if (parseInt (id) == "" || parseInt (comment_id) == "" || username == "")
    {
        $ ("#vpb_reply_bottom_icons_" + parseInt (comment_id)).show ();
        $ ("#vpb_display_reply_loading_" + parseInt (comment_id)).html ('');
        $ ("#vpb_reply_loading_" + parseInt (comment_id)).hide ();

        $ ("#vpb_reply_box_" + parseInt (comment_id)).removeClass ('disable_this_box');
        $ ("#vpb_reply_box_" + parseInt (comment_id)).addClass ('enable_this_box');

        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('reply_photo_' + parseInt (comment_id)).files;

        // Create a formdata object and append the files
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });
        vpb_data.append ("id", id);
        vpb_data.append ("username", username);
        vpb_data.append ("page", 'vpb_final_reply_update_submission');


        $.ajax ({
            url: '/blab/reply/uploadCommentReplyPhoto',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {},
            success: function (response)
            {
                $ ("#vpb_reply_box_" + parseInt (comment_id)).removeClass ('disable_this_box');
                $ ("#vpb_reply_box_" + parseInt (comment_id)).addClass ('enable_this_box');

                $ ("#vpb_reply_bottom_icons_" + parseInt (comment_id)).show ();
                $ ("#vpb_display_reply_loading_" + parseInt (comment_id)).html ('');
                $ ("#vpb_reply_loading_" + parseInt (comment_id)).hide ();
                $ ("#remove_reply_photo_" + parseInt (comment_id)).hide ();

                var response_brought = response.indexOf ('post_no_found');
                if (response_brought != -1)
                {
                    $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                    $ ("#v-wall-alert-box").click ();
                    return false;
                }
                else
                {
                    $ ("#reply_photo_" + parseInt (comment_id)).val ('');
                    $ ("#vpb_wall_reply_data_" + parseInt (comment_id)).val ('').change ();

                    $ ("#vpb_reply_updated_" + parseInt (comment_id)).append (
                            $ (response).hide ().fadeIn ('slow')
                            );

                    $ ('html, body').animate ({
                        scrollTop: $ ("#vpb_reply_id_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
                    }, 1600, 'easeInOutExpo');
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_add_photos_to_reply('" + parseInt (id) + "', '" + parseInt (comment_id) + "');", 10000);
        });
    }
}

// Hide post, comment or reply
function vpb_delete_the_wall_item ()
{
    var item_id = $ ("#v_wall_is_dlt").val ();
    var type = $ ("#v_wall_is_dltype").val ();

    if (type == "post") // post
    {
        $ (".social-feed-box[postid=" + item_id + "]").fadeOut ('slow');
        var strUrl = '/blab/post/deletePost';
    }
    else if (type == "comment") // comment
    {
        $ (".social-comment[commentid=" + item_id + "]").fadeOut ('slow');
        var strUrl = '/blab/comment/deleteComment';
    }
    else // reply
    {
        $ ("#vpb_reply_id_" + parseInt (item_id)).fadeOut ('slow');
        strUrl = '/blab/reply/deleteReply';
    }

    var dataString = {"item_id": item_id, "type": type, "page": "delete-this-wall-item"};

    $.post (strUrl, dataString, function (response)
    {
        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            if (type == "post") // post
            {
                $ (".social-feed-box[postid=" + item_id + "]").fadeIn ('slow');
            }
            else if (type == "comment") // comment
            {
                $ (".social-comment[commentid=" + item_id + "]").fadeIn ('slow');
            }
            else // reply
            {
                $ ("#vpb_reply_id_" + parseInt (item_id)).fadeIn ('slow');
            }
            $ ("#v-wall-message").html ($ ("#general_system_error").val ());
            $ ("#v-wall-alert-box").click ();
            return false;
        }
        else
        {
            if (type == "post") // post
            {
                $ (".social-feed-box[postid=" + item_id + "]").remove ();
            }
            else if (type == "comment") // comment
            {
                $ (".social-comment[commentid=" + item_id + "]").remove ();
            }
            else // reply
            {
                $ ("#vpb_reply_id_" + parseInt (item_id)).remove ();
            }
        }

    }).fail (function (error_response)
    {
        setTimeout ("vpb_delete_the_wall_item();", 10000);
    });
}

// Deletion comfirmation for post, comment or reply
function vpb_delete_this_wall_item (id, type)
{
    $ ("#v_wall_is_dlt").val (parseInt (id));
    $ ("#v_wall_is_dltype").val (type);

    if (type == "post") // post
    {
        $ ("#v_this_wall_item_del_message").html ($ ("#items_del_confirm_text").val () + type + '?');
        $ ("#v-delete-wall-item-alert-box").click ();
    }
    else if (type == "comment") // comment
    {
        $ ("#v_this_wall_item_del_message").html ($ ("#items_del_confirm_text").val () + type + '?');
        $ ("#v-delete-wall-item-alert-box").click ();
    }
    else // reply
    {
        $ ("#v_this_wall_item_del_message").html ($ ("#items_del_confirm_text").val () + type + '?');
        $ ("#v-delete-wall-item-alert-box").click ();
    }
}

// Save edited reply
function vpb_save_reply_update (reply_id)
{
    var reply_post = vpb_trim ($ ("#vpb_wall_reply_editable_data_" + parseInt (reply_id)).val ());
    var session_uid = $ ("#session_uid").val ();

    if (reply_post == "")
    {
        $ ("#v-wall-message").html ($ ("#invalid_comment_update_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        var dataString = {'reply_id': reply_id, 'session_uid': session_uid, 'page': 'update_reply', 'reply': reply_post};
        $.ajax ({
            type: "POST",
            url: '/blab/reply/editReply',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#save_reply_changes_loading_" + parseInt (reply_id)).html ($ ("#v_sending_btn").val ());
                $ ("#vpb_editable_reply_wrapper_" + parseInt (reply_id)).removeClass ('enable_this_box');
                $ ("#vpb_editable_reply_wrapper_" + parseInt (reply_id)).addClass ('disable_this_box');
            },
            success: function (response)
            {
                $ ("#save_reply_changes_loading_" + parseInt (reply_id)).html ('');
                $ ("#vpb_editable_reply_wrapper_" + parseInt (reply_id)).removeClass ('disable_this_box');
                $ ("#vpb_editable_reply_wrapper_" + parseInt (reply_id)).addClass ('enable_this_box');

                var response_brought = response.indexOf ('process_completed');
                if (response_brought != -1)
                {
                    var len = reply_post.length;
                    var reply_post_Left;
                    var max_allowed = 300;
                    if (len <= parseInt (max_allowed))
                    {
                        reply_post_Left = reply_post;
                    }
                    else if (len >= parseInt (max_allowed))
                    {
                        reply_post_trimed = reply_post.substring (0, parseInt (max_allowed));
                        reply_post_Left = reply_post_trimed + '...';
                    }

                    $ ("#vreplies_" + parseInt (reply_id)).html (vpb_wall_add_smilies (vpb_wall_nl2br (reply_post_Left)));

                    $ ("#vreplies_large_" + parseInt (reply_id)).html (vpb_wall_add_smilies (vpb_wall_nl2br (reply_post)));

                    $ ("#vpb_editable_reply_wrapper_" + parseInt (reply_id)).hide ();
                    $ ("#vpb_default_reply_wrapper_" + parseInt (reply_id)).show ();
                    $ ("#redited_id_" + parseInt (reply_id)).show ();
                    $ ("#rdotted_id_" + parseInt (reply_id)).show ();
                }
                else
                {
                    $ ("#v-wall-message").html (response);
                    $ ("#v-wall-alert-box").click ();
                    return false;
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_reply_update('" + parseInt (reply_id) + "');", 10000);
        });
    }
}

//Like COMMENT Box
function vpb_like_reply_box (username, reply_id, action)
{
    if (username == "" || reply_id == "")
    {
        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        if (action == "like")
        {
            $ ("#rlike_" + parseInt (reply_id)).hide ();
            $ ("#runlike_" + parseInt (reply_id)).show ();
        }
        else
        {
            $ ("#runlike_" + parseInt (reply_id)).hide ();
            $ ("#rlike_" + parseInt (reply_id)).show ();
        }
        var dataString = {"username": username, "reply_id": reply_id, "action": action, "page": "like-reply-update"};

        //vpb_security_check_points ();

        $.post ('/blab/reply/likeReply', dataString, function (response)
        {
            var response_brought = response.indexOf ('general_system_error');
            if (response_brought != -1)
            {
                $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                $ ("#v-wall-alert-box").click ();
                return false;
            }
            else
            {
                if (response == 0)
                {
                    $ ("#vpb_total_rlikes_" + parseInt (reply_id)).html (response);
                    $ ("#vpb_rlike_wrapper_" + parseInt (reply_id)).fadeOut ();
                }
                else
                {
                    $ ("#vpb_total_rlikes_" + parseInt (reply_id)).html (response);
                    $ ("#vpb_rlike_wrapper_" + parseInt (reply_id)).fadeIn ();
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_like_reply_box('" + username + "', '" + parseInt (reply_id) + "');", 10000);
        });
    }
}

// Show full post, comment or reply
function vpb_show_full_item (id, type)
{
    if (type == "post")
    {
        $ ("#post_box_a_" + parseInt (id)).hide ();
        $ ("#show_more_" + parseInt (id)).hide ();
        $ ("#post_box_b_" + parseInt (id)).show ();
        $ ('html, body').animate ({
            scrollTop: $ ("#post_box_b_" + parseInt (id)).offset ().top - parseInt (160) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else if (type == "comment")
    {
        $ ("#comment_box_a_" + parseInt (id)).hide ();
        $ ("#comment_box_b_" + parseInt (id)).show ();
        $ ('html, body').animate ({
            scrollTop: $ ("#comment_box_b_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
    else
    {
        $ ("#reply_box_a_" + parseInt (id)).hide ();
        $ ("#reply_box_b_" + parseInt (id)).show ();
        $ ('html, body').animate ({
            scrollTop: $ ("#reply_box_b_" + parseInt (id)).offset ().top - parseInt (130) + 'px'
        }, 1600, 'easeInOutExpo');
    }
}

// Show About Page Owner Details
function vpb_save_profile_details ()
{
    var vpb_page_owner = $ ("#vpb_page_owner").val ();
    var epage_firstname = $ ("#epage_firstname").val ();
    var epage_lastname = $ ("#epage_lastname").val ();
    var epage_email = $ ("#epage_email").val ();

    var eabout_us = $ ("#eabout_us").val ();
    var efavorite_quotes = $ ("#efavorite_quotes").val ();
    var emarital_status = $ ("#emarital_status").val ();
    var eaddress = $ ("#eaddress").val ();
    var ephone = $ ("#ephone").val ();
    var egender = $ ("#egender").val ();
    var einterested_in = $ ("#einterested_in").val ();
    var eday = $ ("#eday").val ();
    var emonth = $ ("#emonth").val ();
    var eyear = $ ("#eyear").val ();
    var ebirth_date_privacy = $ ("#ebirth_date_privacy").val ();
    var ecompany = $ ("#ecompany").val ();
    var ejob_position = $ ("#ejob_position").val ();
    var eprofessional_skill = $ ("#eprofessional_skill").val ();
    var ehigh_school_name = $ ("#ehigh_school_name").val ();

    var hs_started_day = $ ("#hs_started_day").val ();
    var hs_started_month = $ ("#hs_started_month").val ();
    var hs_started_year = $ ("#hs_started_year").val ();
    var started_high_school_from_date = hs_started_day + '-' + hs_started_month + '-' + hs_started_year;

    var hs_ended_day = $ ("#hs_ended_day").val ();
    var hs_ended_month = $ ("#hs_ended_month").val ();
    var hs_ended_year = $ ("#hs_ended_year").val ();
    var ended_high_school_at_date = hs_ended_day + '-' + hs_ended_month + '-' + hs_ended_year;

    var ecollege_field_of_study = $ ("#ecollege_field_of_study").val ();
    var ecollege_name = $ ("#ecollege_name").val ();

    var c_started_day = $ ("#c_started_day").val ();
    var c_started_month = $ ("#c_started_month").val ();
    var c_started_year = $ ("#c_started_year").val ();
    var started_college_from_date = c_started_day + '-' + c_started_month + '-' + c_started_year;

    var c_ended_day = $ ("#c_ended_day").val ();
    var c_ended_month = $ ("#c_ended_month").val ();
    var c_ended_year = $ ("#c_ended_year").val ();
    var ended_college_at_date = c_ended_day + '-' + c_ended_month + '-' + c_ended_year;

    var efrom_city_name = $ ("#efrom_city_name").val ();
    var elives_in_city_name = $ ("#elives_in_city_name").val ();
    var elanguage = $ ("#elanguage").val ();
    var ereligion = $ ("#ereligion").val ();
    var epoliticl_view = $ ("#epoliticl_view").val ();
    var ecountry = $ ("#ecountry").val ();



    if (vpb_trim (epage_firstname) == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#page_fullname_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#empty_fullname_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (vpb_trim (epage_lastname) == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#page_fullname_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#empty_fullname_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (vpb_trim (epage_email) == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#page_email_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#empty_email_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (!vpb_email_is_valid (vpb_trim (epage_email)))
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#page_email_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#invalid_email_field").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (eday != "" && emonth == "" || eday != "" && eyear == "")
    {

        $ ('html, body').animate ({
            scrollTop: $ ("#birth_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#birthday_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (emonth != "" && eday == "" || emonth != "" && eyear == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#birth_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#birthday_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (eyear != "" && eday == "" || eyear != "" && emonth == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#birth_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#birthday_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_started_day != "" && hs_started_month == "" || hs_started_day != "" && hs_started_year == "")
    {

        $ ('html, body').animate ({
            scrollTop: $ ("#started_high_school_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_started_month != "" && hs_started_day == "" || hs_started_month != "" && hs_started_year == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#started_high_school_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_started_year != "" && hs_started_day == "" || hs_started_year != "" && hs_started_month == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#started_high_school_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_ended_day != "" && hs_ended_month == "" || hs_ended_day != "" && hs_ended_year == "")
    {

        $ ('html, body').animate ({
            scrollTop: $ ("#ended_high_school_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_ended_month != "" && hs_ended_day == "" || hs_ended_month != "" && hs_ended_year == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#ended_high_school_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (hs_ended_year != "" && hs_ended_day == "" || hs_ended_year != "" && hs_ended_month == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#ended_high_school_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_high_school_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_started_day != "" && c_started_month == "" || c_started_day != "" && c_started_year == "")
    {

        $ ('html, body').animate ({
            scrollTop: $ ("#started_college_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_started_month != "" && c_started_day == "" || c_started_month != "" && c_started_year == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#started_college_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_started_year != "" && c_started_day == "" || c_started_year != "" && c_started_month == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#started_college_from_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#started_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_ended_day != "" && c_ended_month == "" || c_ended_day != "" && c_ended_year == "")
    {

        $ ('html, body').animate ({
            scrollTop: $ ("#ended_college_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_ended_month != "" && c_ended_day == "" || c_ended_month != "" && c_ended_year == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#ended_college_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else if (c_ended_year != "" && c_ended_day == "" || c_ended_year != "" && c_ended_month == "")
    {
        $ ('html, body').animate ({
            scrollTop: $ ("#ended_college_at_date_html").offset ().top - parseInt (100) + 'px'
        }, 1600, 'easeInOutExpo');
        $ ("#v-wall-message").html ($ ("#ended_college_missing_fields_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    else
    {
        $ ("#update_acct_status").html ($ ("#v_loading_btn").val ());

        $ ("#vpb_display_about_page_owner").removeClass ('enable_this_box');
        $ ("#vpb_display_about_page_owner").addClass ('disable_this_box');

        var dataString = {
            'firstname-form': epage_firstname,
            'lastname-form': epage_lastname,
            'email-form': epage_email,
            'about_us': eabout_us,
            'favorite_quotes': efavorite_quotes,
            'marital_status': emarital_status,
            'address-form': eaddress,
            'telephone1-form': ephone,
            'gender': egender,
            'interested_in': einterested_in,
            'day': eday,
            'month': emonth,
            'year': eyear,
            'birth_date_privacy': ebirth_date_privacy,
            'company': ecompany,
            'occupation-form': ejob_position,
            'professional_skill': eprofessional_skill,
            'high_school_name': ehigh_school_name,
            'started_high_school_from_date': started_high_school_from_date,
            'ended_high_school_at_date': ended_high_school_at_date,
            'college_field_of_study': ecollege_field_of_study,
            'college_name': ecollege_name,
            'started_college_from_date': started_college_from_date,
            'ended_college_at_date': ended_college_at_date,
            'from_city_name': efrom_city_name,
            'town-form': elives_in_city_name,
            'language': elanguage,
            'religion': ereligion,
            'politicl_view': epoliticl_view,
            'country-form': ecountry,
            'page': 'vpb_save_profile_detail'};

        $.post ('/blab/user/updateProfileData', dataString, function (response)
        {
            $ ("#vpb_display_about_page_owner").removeClass ('disable_this_box');
            $ ("#vpb_display_about_page_owner").addClass ('enable_this_box');

            var response_brought = response.indexOf ('VPB:');
            if (response_brought != -1)
            {
                $ (document).attr ('title', epage_fullname);
                $ ("#p_page_name").html (epage_fullname);

                var name_path = epage_fullname.split (' ');
                if (name_path[0] != "")
                {
                    $ ("#p_page_first_name").html (name_path[0]);
                }
                else
                {
                }

                $ ("#o_p_page_first_name").data ('original-title', epage_fullname);
                $ ("#o_p_page_first_name").attr ('data-original-title', epage_fullname);
                $ ("#o_p_page_first_name").attr ('title', epage_fullname);

                $ ("#update_acct_status").html (response.replace ('VPB: ', ''));

                $ ("#update_acct_wait").html ($ ("#v_sending_btn").val ());

                $ ('html, body').animate ({
                    scrollTop: $ ("#update_acct_wait").offset ().top + parseInt (100) + 'px'
                }, 1600, 'easeInOutExpo');

                setTimeout (function ()
                {
                    $ ("#update_acct_status").html ('');
                    vpb_show_about_page_owner_details ('normal');
                }, 8000);
            }
            else
            {
                $ ("#update_acct_status").html (response);
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_profile_details();", 10000);
        });
    }
}

function matchItem (string, data)
{
    var i = 0,
            j = 0,
            html = '',
            regex,
            regexv,
            match,
            matches,
            version;

    for (i = 0; i < data.length; i += 1) {
        regex = new RegExp (data[i].value, 'i');
        match = regex.test (string);
        if (match)
        {
            regexv = new RegExp (data[i].version + '[- /:;]([\d._]+)', 'i');
            matches = string.match (regexv);
            version = '';
            if (matches)
            {
                if (matches[1])
                {
                    matches = matches[1];
                }
            }
            if (matches)
            {
                matches = matches.split (/[._]+/);
                for (j = 0; j < matches.length; j += 1) {
                    if (j === 0)
                    {
                        version += matches[j] + '.';
                    }
                    else
                    {
                        version += matches[j];
                    }
                }
            }
            else
            {
                version = '0';
            }
            return data[i].name; //{
            //name: data[i].name,
            //version: parseFloat(version)
            //};
        }
    }
    return {name: 'unknown', version: 0};
}

var header = [
    navigator.platform,
    navigator.userAgent,
    navigator.appVersion,
    navigator.vendor,
    window.opera
];

var agent = header.join (' ');

var os = [
    {name: 'Windows Phone', value: 'Windows Phone', version: 'OS'},
    {name: 'Windows', value: 'Win', version: 'NT'},
    {name: 'iPhone', value: 'iPhone', version: 'OS'},
    {name: 'iPad', value: 'iPad', version: 'OS'},
    {name: 'Kindle', value: 'Silk', version: 'Silk'},
    {name: 'Android', value: 'Android', version: 'Android'},
    {name: 'PlayBook', value: 'PlayBook', version: 'OS'},
    {name: 'BlackBerry', value: 'BlackBerry', version: '/'},
    {name: 'Macintosh', value: 'Mac', version: 'OS X'},
    {name: 'Linux', value: 'Linux', version: 'rv'},
    {name: 'Palm', value: 'Palm', version: 'PalmOS'}
]

function getOs ()
{
    var os = matchItem (agent, os);

    alert (os);
}

function vpb_set_current_page_details (groupId)
{
    vpb_setcookie ('v_wall_page_id', groupId);

    $ ("#create_page_button").hide ();
    $ ("#save_page_changes_button").show ();

    $ ("#vpage_name").val ($ ("#currentPageName").val ());
    $ ("#vpage_description").val ($ ("#currentPageDescription").val ());
    $ ("#vpage_address").val ($ ("#currentAddress").val ());
    $ ("#vpage_websiteUrl").val ($ ("#currentWebsiteUrl").val ());
    $ ("#vpage_postcode").val ($ ("#currentPostcode").val ());
    $ ("#vpage_telephone").val ($ ("#currentTelephone").val ());

    pagePhoto = $ ("#currentPageImage").val ();

    if (pagePhoto !== "" && pagePhoto !== null && pagePhoto !== undefined)
    {
        $ ("#vpb_display_wall_page_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#page_photo_title_text").val () + '\', \'' + pagePhoto + '\');" src="' + pagePhoto + '#' + vpb_generte_random () + '" title=' + $ ("#page_photo_title_text").val () + ' border="0">');
    }
    else
    {
    }

    var privacy = $ ("#currentPrivacy").val ();


    setTimeout (function ()
    {
        document.getElementById ('v-create-page-box').click ();
    }, 100);
}

function vpb_set_current_group_details (groupId)
{
    vpb_setcookie ('v_wall_group_id', groupId);

    $ ("#create_group_button").hide ();
    $ ("#save_group_changes_button").show ();

    $ ("#vgroup_name").val ($ ("#currentGroupName").val ());
    $ ("#vgroup_description").val ($ ("#currentGroupDescription").val ());

    groupPhoto = $ ("#currentGroupImage").val ();

    if (groupPhoto !== "" && groupPhoto !== null && groupPhoto !== undefined)
    {
        $ ("#vpb_display_wall_group_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#group_photo_title_text").val () + '\', \'' + groupPhoto + '\');" src="' + groupPhoto + '#' + vpb_generte_random () + '" title=' + $ ("#group_photo_title_text").val () + ' border="0">');
    }
    else
    {
    }

    var privacy = $ ("#currentPrivacy").val ();

    if (privacy == "public")
    {
        $ ('#public_group').prop ('checked', true);
    }
    else if (privacy == "secret")
    {
        $ ('#secret_group').prop ('checked', true);
    }
    else
    {
    }

    vpb_setcookie ('group_unames', vpb_getcookie ('g_username'), 30);
    vpb_setcookie ('group_fnames', vpb_getcookie ('g_fullnames'), 30);

    // Display users in group
    var vpb_group_users_fname = new Array ();
    var vpb_group_users_name = new Array ();

    vpb_group_users_fname = vpb_getcookie ('group_fnames').split (/\,/);
    vpb_group_users_name = vpb_getcookie ('group_unames').split (/\,/);

    $ ("#vpb_added_wall_users_box").html ('');

    for (var u = 0; u < vpb_group_users_name.length; u++)
    {
        if (vpb_group_users_name[u] != "")
        {
            var fullname = vpb_group_users_fname[u];
            var username = vpb_group_users_name[u];

            $ ("#vpb_added_wall_users_box").append ('<span id="vpb_new_user_in_group_' + username + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vpb_remove_a_user_from_group(\'' + fullname + '\', \'' + username + '\')"></i></span></span>');
        }
        else
        {
        }
    }
    setTimeout (function ()
    {
        document.getElementById ('v-create-group-box').click ();
    }, 100);
}

var completedCalled = false;

//Like POST Box
function vpb_like_box (username, post_id, type)
{
    if (!completedCalled)
    {
        completedCalled = true;
        if (username == "" || post_id == "")
        {
            $ ("#v-wall-message").html ('Sorry there was an issue whilst trying to process this request');
            $ ("#v-wall-alert-box").click ();
            return false;
        }
        else
        {
            $ (".vpb_likesBox").hide ();
            var labelType;

            var dataString = {"username": username, "post_id": post_id, "type": type, "page": "like-box-update"};

            //vpb_security_check_points ();

            $.post ('/blab/index/reaction', dataString, function (response)
            {
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                        $ ("#v-wall-alert-box").click ();
                        return false;
                    }
                    else
                    {
                        labelType = '<span class="vpb_d_like_status" onclick="vpb_like_box(\'' + username + '\', \'' + parseInt (post_id) + '\', \'' + type + '\');">\
							<i class="' + type.toLowerCase () + 'Icon_c"></i> <span class="vpb_' + type + 'Status like_text">' + type + '(' + objResponse.data.count + ')</span>\
						</span>';

                        $ (".showLikes[id=" + post_id + "]").html (labelType);
                        $ (".feelings-box").hide ();


                        completedCalled = false;
                        return false;
                    }
                } catch (error) {

                }

            }).fail (function (error_response)
            {
                setTimeout ("vpb_like_box('" + username + "', '" + parseInt (post_id) + "', '" + type + "');", 10000);
            });
        }
    }
    else
    {
        completedCalled = false;
        $ ("#v-wall-message").html ($ ("#vchat_bg_process_running_text").val ());
        $ ("#v-wall-alert-box").click ();
        return false;
    }
    return false;
}

// Conform before removing a friend from the tagged list
function vpb_confirm_delete_current_page_details (group_id, group_manager, group_name)
{
    $ ("#dpageid").val (group_id);
    $ ("#dpagemanager").val (group_manager);
    $ ("#dpagename").val (group_name);
    $ ("#delete_page_message_text").html ($ ("#group_delete_message_text").val () + ' <b>' + group_name + '</b>?');
    $ ("#v-delete-this-page-alert-box").click ();
}

function vpb_delete_this_page_now ()
{
    var group_id = $ ("#dpageid").val ();

    var group_manager = $ ("#dpagemanager").val ();
    var group_name = $ ("#dpagename").val ();

    var dataString = {"group_id": group_id, "group_manager": group_manager, "group_name": group_name, "page": "delete_this_group"};

    $.ajax ({
        type: "POST",
        url: '/blab/page/deletePage',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            vpb_show_status_updates ();
            $ ("#vasplus_wall_status_updates").hide ();

            $ ("#not_deleting_page").hide ();
            $ ("#deleting_page").html ($ ("#vpb_loading_image_gif").val ());

            $ ('html, body').animate ({
                scrollTop: $ ("#deleting_page").offset ().top - parseInt (120) + 'px'
            }, 1600, 'easeInOutExpo');
        },
        success: function (response)
        {

            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                $ ("#deleting_page").html ('');
                $ ("#not_deleting_page").show ();
                $ ("#vasplus_wall_status_updates").show ();
                $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                $ ("#v-wall-alert-box").click ();
            }
            else
            {
                // window.location.replace (vpb_site_url + 'wall/' + group_manager);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_delete_this_group_now();", 10000);
    });
}


// Conform before removing a friend from the tagged list
function vpb_confirm_delete_current_group_details (group_id, group_manager, group_name)
{
    $ ("#dgroupid").val (group_id);
    $ ("#dgroupmanager").val (group_manager);
    $ ("#dgroupname").val (group_name);
    $ ("#delete_group_message_text").html ($ ("#group_delete_message_text").val () + ' <b>' + group_name + '</b>?');
    $ ("#v-delete-this-group-alert-box").click ();
}

function vpb_delete_this_group_now ()
{
    var group_id = $ ("#dgroupid").val ();
    var group_manager = $ ("#dgroupmanager").val ();
    var group_name = $ ("#dgroupname").val ();

    var dataString = {"group_id": group_id, "group_manager": group_manager, "group_name": group_name, "page": "delete_this_group"};

    $.ajax ({
        type: "POST",
        url: '/blab/group/deleteGroup',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            vpb_show_status_updates ();
            $ ("#vasplus_wall_status_updates").hide ();

            $ ("#not_deleting_group").hide ();
            $ ("#deleting_group").html ($ ("#vpb_loading_image_gif").val ());

            $ ('html, body').animate ({
                scrollTop: $ ("#deleting_group").offset ().top - parseInt (120) + 'px'
            }, 1600, 'easeInOutExpo');
        },
        success: function (response)
        {

            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                $ ("#deleting_group").html ('');
                $ ("#not_deleting_group").show ();
                $ ("#vasplus_wall_status_updates").show ();
                $ ("#v-wall-message").html ($ ("#general_system_error").val ());
                $ ("#v-wall-alert-box").click ();
            }
            else
            {
                // window.location.replace (vpb_site_url + 'wall/' + group_manager);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_delete_this_group_now();", 10000);
    });
}



function vpb_reset_current_group_details ()
{
    $ ("#save_group_changes_button").hide ();
    $ ("#create_group_button").show ();

    $ ("#vgroup_name").val ('');
    $ ("#vgroup_description").val ('');
    $ ("#vpb_display_wall_group_photo").html ('');
    $ ('#public_group').prop ('checked', false);
    $ ('#secret_group').prop ('checked', false);
    vpb_removecookie ('group_fnames');
    vpb_removecookie ('group_unames');
    $ ("#vpb_added_wall_users_box").html ('');
    vpb_setcookie ('v_wall_group_id', vpb_generte_random (), 30);
    setTimeout (function ()
    {
        document.getElementById ('v-create-group-box').click ();
    }, 100);
}

// Generate Communication data
function vpb_generte_random ()
{
    return Math.random ().toString ().split ("0.").join ("1").toString ().split (".").join ("");
}

// Show previously searched users on clicking the search box if available
function vpb_show_prev_suggested_searched_user ()
{
    if ($ ('#vpb_suggested_users_displayer').text ().length == 0)
    {
    }
    else
    {
        $ ("#v_suggested_wall_users_box").fadeIn ();
    }
}


// Validate email addresses
function vpb_email_is_valid (email)
{
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test (email);
}


// Show all Page Owner friend
function vpb_show_page_owner_friends ()
{
    $ ("#view_all_frnds").hide ();
    $ (".r-sidebar").hide ();
    $ (".m-sidebar").show ();
    $ (".l-sidebar").hide ();

    $ (".vmiddle_other").hide ();

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
    $ ("#friendRequests_bar").hide (); // Then hide the friend requests pop up box
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

    $ ("#vasplus_group_page_members").hide ();
    $ ("#vasplus_group_page_videos").hide ();

    $ ("#vasplus_photos").hide ();

    //$("#vpb_display_page_owners_friends").html($("#vpb_loading_image_gif").val()); //Show loading image
    $ ("#vasplus_page_owners_friends").show (); // Show the page owners friends page

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_page_owners_friends").offset ().top - parseInt (120) + 'px'
    }, 1600, 'easeInOutExpo');
}

function vpb_load_more_page_owner_friends (userId)
{
    var total_friends = $ ("#vtotal_friends").val (); //The total page owner's friends
    var vpb_start = $ ("#vpb_friends_start").val (); //Where to start loading the friends

    var vpb_total_per_load = $ ("#vpb_total_friends_per_load").val (); //Total friends to load each time
    var session_uid = $ ("#session_uid").val (); //The username of the current logged in user
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner
    var page_id = $ ("#page_id").val (); //The page id which identifies the current page viewed

    //There are still more records to load
    if (parseInt (vpb_start) <= parseInt (total_friends))
    {
        var dataString = {"vpb_start": vpb_start, "vpb_total_per_load": vpb_total_per_load, "session_uid": userId, "vpb_page_owner": vpb_page_owner, "page_id": page_id, "page": "load-more-friends"}

        $ ("#vpb_load_more_friends_box").hide ();

        $ ("#vpb_loading_friends").html ($ ("#v_loading_btn").val ()); //Show loading image
        $ ("#vpb_loading_friends_outer").show ();

        $.post ('/blab/friend/loadMoreFriends', dataString, function (response)
        {
            //Hide the loading image after loading data onto the page
            $ ("#vpb_loading_friends_outer").hide ();
            $ ("#vpb_loading_friends").html ('');

            var total_loaded_friends = parseInt (vpb_start) + parseInt (vpb_total_per_load);

            $ ("#vpb_friends_start").val (parseInt (total_loaded_friends));
            $ ("#v_this_friends_totals").html (parseInt (total_loaded_friends));

            //Append the received data unto the current page
            $ ("#vpb_get_all_the_users_friends > div").prepend (
                    $ (response).hide ().fadeIn ('fast')
                    );

            if (parseInt ($ ("#vpb_friends_start").val ()) < parseInt (total_friends))
            {
                $ ("#vpb_load_more_friends_box").show ();
            }
            else
            {
            }

        }).fail (function (xhr, ajaxOptions, theError)
        {
            setTimeout ("vpb_load_more_page_owner_friends();", 10000);
        });
    }
    else
    {
        //Hide load more friends buttons when there is no more friend
        $ ("#vpb_load_more_friends_box").hide ();
    }
}

// Show About Page Owner Details
function vpb_show_about_page_owner_details (action, userId)
{
    $ (".r-sidebar").hide ();
    $ (".m-sidebar").show ();
    $ (".l-sidebar").hide ();
    $ ("#vasplus_photos").hide ();
    $ ("#view_or_edit_button").hide ();
    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates box on the current page
    $ (".vmiddle_other").hide ();

    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#friendRequests_bar").hide (); // Then hide the friend requests pop up box

    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page

    $ ("#vpb_display_wall_friend_requests").html ('');
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page

    // Hide the website menus
    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    //$("#vpb_display_page_owners_friends").html('');
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_members").hide ();

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ ("#vasplus_wall_status_updates").fadeOut ();

    var session_uid = $ ("#session_uid").val ();
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    $ ("#vpb_display_about_page_owner").html (loader); //Show loading image
    $ ("#vasplus_about_page_owner").show (); // Show the vewl all friend requests page

    if ($ ("#view_all_frnds").css ('display') == "none")
    {
        $ ("#view_all_frnds").fadeIn ();
    }
    else
    {
    }

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_about_page_owner").offset ().top - parseInt (100) + 'px'
    }, 1600, 'easeInOutExpo');

    var dataString = {'userId': userId, 'vpb_page_owner': vpb_page_owner, 'action': action, 'page': 'vpb_get_about_page_owner_detail'};

    $.post ('/blab/user/getUserDetails', dataString, function (response)
    {
        $ ("#vpb_display_about_page_owner").html (response);

    }).fail (function (error_response)
    {
        setTimeout ("vpb_show_about_page_owner_details('" + action + "');", 10000);
    });
}

// Show About Page Owner Details
function vpb_show_photos (action, userId)
{
    $ (".r-sidebar").hide ();
    $ (".m-sidebar").show ();
    $ (".l-sidebar").hide ();
    $ ("#view_or_edit_button").hide ();
    $ ("#vpb_display_search_results").hide (); // Hide the search results pop up box
    $ ("#vasplus_wall_status_updates").hide (); // Hide the status updates box on the current page
    $ ("#vasplus_about_page_owner").hide ();
    $ ("#vasplus_page_owners_friends").hide ();


    $ ("#vpb_display_friend_requests").html (''); // Remove the previous displayed friend requests from the friend requests pop up box
    $ ("#friendRequests_bar").hide (); // Then hide the friend requests pop up box

    $ ("#vpb_display_wall_find_friends").html (''); // Remove the found friends
    $ ("#vasplus_wall_find_friends").hide (); // Hide the find friends box on the current page

    $ ("#vpb_display_wall_friend_requests").html ('');
    $ ("#vasplus_wall_friend_requests").hide (); // Hide the vewl all friend requests page

    // Hide the website menus
    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    //$("#vpb_display_page_owners_friends").html('');
    $ ("#vasplus_page_owners_friends").hide ();
    $ ("#vasplus_group_page_videos").hide ();
    $ ("#vasplus_group_page_members").hide ();

    $ ("#v_friend_requests").removeClass ('vpb_notifications_icon_active');
    $ ("#v_friend_requests").addClass ('vpb_notifications_icon');

    $ ("#vpb_display_notifications").html ('');
    $ ("#vasplus_notifications").hide (); // Hide notification page

    $ (".vmiddle_other").hide ();

    $ ("#vasplus_wall_status_updates").fadeOut ();

    var session_uid = $ ("#session_uid").val ();
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner

    //Process is running
    $ ("#vpb_is_process_running").val ('1');

    $ ("#vpb_display_photos").html (loader); //Show loading image
    $ ("#vpb_display_photos").show (); // Show the vewl all friend requests page

    if ($ ("#vasplus_photos").css ('display') == "none")
    {
        $ ("#vasplus_photos").fadeIn ();
    }
    else
    {
    }

    $ ('html, body').animate ({
        scrollTop: $ ("#vasplus_photos").offset ().top - parseInt (100) + 'px'
    }, 1600, 'easeInOutExpo');

    var dataString = {'userId': userId, 'vpb_page_owner': vpb_page_owner, 'action': action, 'page': 'vpb_get_about_page_owner_detail'};

    $.post ('/blab/photo/album?userId=' + userId, dataString, function (response)
    {
        $ ("#vpb_display_photos").html (response);

    }).fail (function (error_response)
    {
        setTimeout ("vpb_show_about_page_owner_details('" + action + "');", 10000);
    });
}

// Load and display reply likes
function vpb_load_reply_likes (reply_id, username, label)
{
    var dataString = {'page': 'load_people_who_liked_reply', 'reply_id': reply_id, 'username': username};
    $.ajax ({
        type: "POST",
        url: '/blab/reply/getReplyLikes',
        data: dataString,
        beforeSend: function ()
        {
            $ ("#vpb_system_data_title").html (label);
            $ ("#vpb_display_wall_gen_data").html ($ ("#v_loading_btn").val ());
            $ ("#v-wall-g-data-alert-box").click ();
        },
        success: function (response)
        {
            $ ("#vpb_display_wall_gen_data").html (response);
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_comment_likes('" + parseInt (reply_id) + "', '" + username + "', '" + label + "');", 10000);
    });
}


// Load more replies
function vpb_load_more_replies (comment_id)
{
    var total_replies = $ ("#vtotal_replies_" + parseInt (comment_id)).val (); //The total replies for the comment
    var vpb_start = $ ("#vpb_replies_start_" + parseInt (comment_id)).val (); //Where to start loading the replies

    var vpb_total_per_load = $ ("#vpb_total_replies_per_load").val (); //Total replies to load each time
    var session_uid = $ ("#session_uid").val (); //The username of the current logged in user
    var vpb_page_owner = $ ("#vpb_page_owner").val (); //The username of the page owner
    var page_id = $ ("#page_id").val (); //The page id which identifies the current page viewed

    //There are still more records to load
    if (parseInt (vpb_start) <= parseInt (total_replies))
    {
        var dataString = {"comment_id": comment_id, "vpb_start": vpb_start, "vpb_total_per_load": vpb_total_per_load, "session_uid": session_uid, "vpb_page_owner": vpb_page_owner, "page_id": page_id, "page": "load-more-replies"}

        $ ("#vpb_load_more_replies_box_" + parseInt (comment_id)).hide ();

        $ ("#vpb_loading_replies_" + parseInt (comment_id)).html ($ ("#v_loading_btn").val ()); //Show loading image
        $ ("#vpb_loading_replies_outer_" + parseInt (comment_id)).show ();

        $ ('html, body').animate ({
            scrollTop: $ ("#vpb_loading_replies_outer_" + parseInt (comment_id)).offset ().top - parseInt (120) + 'px'
        }, 1600, 'easeInOutExpo');

        //return false;


        $.post ('/blab/reply/loadMoreReplies', dataString, function (response)
        {
            //Hide the loading image after loading data onto the page
            $ ("#vpb_loading_replies_outer_" + parseInt (comment_id)).hide ();
            $ ("#vpb_loading_replies_" + parseInt (comment_id)).html ('');

            var total_loaded_replies = parseInt (vpb_start) + parseInt (vpb_total_per_load);

            $ ("#vpb_replies_start_" + parseInt (comment_id)).val (parseInt (total_loaded_replies));
            $ ("#v_this_reply_totals_" + parseInt (comment_id)).html (parseInt (total_loaded_replies));

            //Append the received data unto the current page
            $ ("#vpb_loaded_replies_" + parseInt (comment_id)).prepend (
                    $ (response).hide ().fadeIn ('slow')
                    );

            if (parseInt ($ ("#vpb_replies_start_" + parseInt (comment_id)).val ()) <= parseInt (total_replies))
            {
                $ ("#vpb_load_more_replies_box_" + parseInt (comment_id)).show ();
            }
            else
            {
            }

        }).fail (function (xhr, ajaxOptions, theError)
        {
            setTimeout ("vpb_load_more_replies('" + comment_id + "');", 10000);
        });
    }
    else
    {
        //Hide load more comment buttons when there is no more comment
        $ ("#vpb_load_more_replies" + parseInt (comment_id)).hide ();
    }
}

// Set post securit option before post
function vpb_selected_security_option (name, id, lower_case_selected, icon)
{
    var session_uid = $ ("#session_uid").val ();

    $ ("#selected_security_option_").val (lower_case_selected); // Set current security option selected for any new post

//    var dataString = {'page': 'set_selected_security_option', 'name': lower_case_selected, 'session_uid': session_uid};
//    $.ajax ({
//        type: "POST",
//        url: vpb_site_url + 'wall-processor.php',
//        data: dataString,
//        beforeSend: function ()
//        {
//            $ ("#selected_option_").html ($ ("#v_loading_btn").val ());
//        },
//        success: function (response)
//        {
//            var response_brought = response.indexOf ('completed');
//            if (response_brought != -1)
//            {
    $ (".dropdown-menu2").hide ();
    $ ("#selected_option_with_title").data ('original-title', name);
    $ ("#selected_option_with_title").attr ('data-original-title', name);
    $ ("#selected_option_with_title").attr ('title', name);
    $ ("#selected_option_").html ('<i class="fa ' + icon + '"></i> ' + name + ' <span class="caret"></span>');
    $ ("#v_current_security_setting").val ('<i class="fa ' + icon + '"></i> ' + name + ' <span class="caret"></span>');
    $ (".vasplus_ticker").hide ();
    $ ("#" + id).show ();
//            }
//            else
//            {
//                $ ("#selected_option_").html ($ ("#v_previous_security_setting").val ());
//                $ ("#v_current_security_setting").val ($ ("#v_previous_security_setting").val ());
//
//                $ ("#v-wall-message").html (response);
//                $ ("#v-wall-alert-box").click ();
//                return false;
//            }
//        }
//    }).fail (function (error_response)
//    {
//        setTimeout ("vpb_selected_security_option('" + name + "', '" + id + "', '" + lower_case_selected + "', '" + icon + "');", 10000);
//    });
}

// Submit reply
function vpb_submit_reply (vpb_evnts, reply, comment_id)
{
    var vpb_key_code = (vpb_evnts.keyCode ? vpb_evnts.keyCode : vpb_evnts.which);

    var reply_post = vpb_trim ($ (reply).val ());
    var session_uid = $ ("#session_uid").val ();

    //Do not allow spaces in textarea chat box
    if (vpb_key_code == 13 && !vpb_evnts.shiftKey)
    {
        vpb_evnts.preventDefault ();
    }

    if (vpb_key_code == 13 && vpb_evnts.shiftKey == 0)
    {
        var ext = $ ("#reply_photo_" + parseInt (comment_id)).val ().split ('.').pop ().toLowerCase ();
        var photos = $ ("#reply_photo_" + parseInt (comment_id)).val ();

        if (reply_post == "" && vpb_trim (photos) == "")
        {
            $ ("#v-wall-message").html ($ ("#invalid_reply_update_field").val ());
            $ ("#v-wall-alert-box").click ();
            e.stopPropagation ();
            return false;
        }
        else
        {
            if (photos != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
            {
                $ ("#reply_photo_" + parseInt (comment_id)).val ('')
                $ ("#add_reply_file_clicked_" + parseInt (comment_id)).data ('original-title', 'Attach a photo');
                $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('data-original-title', 'Attach a photo');
                $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('title', 'Attach a photo');

                $ ("#v-wall-message").html ($ ("#invalid_file_attachment").val ());
                $ ("#v-wall-alert-box").click ();
                return false;
            }
            else
            {
                var dataString = {'comment_id': comment_id, 'session_uid': session_uid, 'page': 'sumit_reply', 'reply': reply_post};
                $.ajax ({
                    type: "POST",
                    url: '/blab/reply/saveCommentReply',
                    data: dataString,
                    beforeSend: function ()
                    {
                        $ ("#vpb_reply_bottom_icons_" + parseInt (comment_id)).hide ();
                        vpb_hide_reply_smiley_box (parseInt (comment_id));
                        $ ("#vpb_preview_reply_photo_" + parseInt (comment_id)).hide ();
                        $ ("#vpb_display_reply_loading_" + parseInt (comment_id)).html ($ ("#v_sending_btn").val ());
                        $ ("#vpb_reply_loading_" + parseInt (comment_id)).show ();

                        $ ("#vpb_reply_box_" + parseInt (comment_id)).removeClass ('enable_this_box');
                        $ ("#vpb_reply_box_" + parseInt (comment_id)).addClass ('disable_this_box');

                        //vpb_security_check_points ();
                    },
                    success: function (response)
                    {

                        var objResponse = $.parseJSON (response);

                        if (objResponse.sucess == 0)
                        {
                            $ ("#vpb_reply_bottom_icons_" + parseInt (comment_id)).show ();
                            $ ("#vpb_display_reply_loading_" + parseInt (comment_id)).html ('');
                            $ ("#vpb_reply_loading_" + parseInt (comment_id)).hide ();

                            $ ("#vpb_reply_box_" + parseInt (comment_id)).removeClass ('disable_this_box');
                            $ ("#vpb_reply_box_" + parseInt (comment_id)).addClass ('enable_this_box');

                            showErrorMessage ();
                        }
                        else
                        {
                            var id = objResponse.data.id;

                            vpb_add_photos_to_reply (parseInt (id), parseInt (comment_id));
                        }

                    }
                }).fail (function (error_response)
                {
                    setTimeout ("vpb_submit_reply('" + vpb_evnts + "', '" + reply + "', '" + parseInt (comment_id) + "');", 10000);
                });
            }
        }
    }
}

//Add smiley to wall comment box when clicked on
function vpb_add_smiley_to_reply (smiley)
{
    var post_id = $ ("#reply_smiley_identifier").val ();
    var old_pms_message = $ ("#vpb_wall_reply_data_" + parseInt (post_id)).val ();
    if (old_pms_message == "")
    {
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).click ();
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).focus ();
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).val (smiley);
    }
    else
    {
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).click ();
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).focus ();
        $ ("#vpb_wall_reply_data_" + parseInt (post_id)).val (old_pms_message + ' ' + smiley);
    }

}


// Add file to reply clicked
function vpb_add_file_to_reply_clicked (comment_id)
{
    var name = v_basename ($ ("#reply_photo_" + parseInt (comment_id)).val ());
    if (vpb_trim (name) != "")
    {
        $ ("#add_reply_file_clicked_" + parseInt (comment_id)).data ('original-title', name);
        $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('data-original-title', name);
        $ ("#add_reply_file_clicked_" + parseInt (comment_id)).attr ('title', name);
    }
    else
    {
        return false;
    }
}


function createReply (data)
{
    var id = data.id;

    var html = '<div style="margin:0px !important;display: inline-block;max-width:100% !important;max-height:100% !important;width:100% !important;height:100% !important;display:none !important;" id="vpb_hidden_comment_id_' + data.comment_id + '">' +
            '<div class="modal-content vasplus_a">' +
            '<div class="modal-body vasplus_b" style="background-color: #f6f7f8 !important;">' +
            '<div class="vpb_wall_adjust_c">' +
            '<div class="input-group vpb_wall_b_contents" style="display: inline-block;">' +
            'This comment has been hidden&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_unhide_this_wall_item(\'' + data.comment_id + '\', \'michaelhampton\', \'comment\');">Unhide</span>&nbsp;&nbsp;·&nbsp;&nbsp;<span class="vpb_hover" onclick="vpb_show_hidden_item_intro();">Learn more...</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

    html += '<div class="social-comment comment-a1" commentid="' + id + '">';

    html += '<span class="vpb_wrap_post_contents_b">' +
            '<div class="dropdown">' +
            '<i id="menu' + data.comment_id + '" data-toggle="dropdown" data-placement="top" class="fa fa-pencil vpb_wrap_coms_icons vasplus-tooltip-attached" onclick="vpb_hide_popup();" original-title="Options"></i>' +
            '<ul class="dropdown-menu bullet pull-right" role="menu" aria-labelledby="menu' + data.comment_id + '" style="right: -15px; left: auto; top:18px;border-radius:0px !important;">' +
            '<li><a onclick="vpb_show_editable_item(\'' + data.comment_id + '\', \'comment\');">Edit Comment</a></li>';


    if ($.trim (data.username) === $.trim (data.current_user))
    {
        html += '<li><a class="deleteComment" deleteid="' + data.comment_id + '">Delete Comment</a></li>';
    }
    else
    {
        html += '<li><a class="pull-right ignoreComment" deleteid="' + data.comment_id + '">Remove Comment From feed</a><li>';
    }

    html += '</ul></div></span>';


    html += '<a href="" class="pull-left">' +
            '<img alt="image" src="/blab/public/uploads/profile/' + data.username + '.jpg">' +
            '</a>' +
            '<div class="media-body">' +
            '<a href="#">' +
            data.author +
            '</a><br>' +
            '<small date="' + data.date_added + '" class="text-muted timeAgo">' + timeSince (data.date_added) + '  ' + usersLocation + '</small><br>' +
            '<span style="font-size:14px;" id="vcomments_' + data.comment_id + '">' + data.comment + '</span>' +
            '</div>' +
            '<div class="btn-group" id="' + data.comment_id + '">' +
            '<span class="vpb_comment_update_bottom_links" title="Leave a reply" onclick="vpb_show_reply_box(\'' + data.comment_id + '\');">Reply</span>' +
            '<button comment-id="' + data.comment_id + '" id="' + data.id + '" class="btn btn-white btn-xs ' + data.likeCommentClass + ' comment"><i class="fa fa-thumbs-up"></i> Like this! (' + (data.likeCount > 0 ? data.likeCount : '') + ')</button>' +
            '<br><a href="#" class="showLikes" id="' + data.comment_id + '" type="comment">' + data.commentLikes + ' ' + (data.likeCommentCount > 0 && data.likeCommentCount != null && data.likeCommentCount != 'null' ? ' and ' + data.likeCommentCount + ' others' : ' liked this ') + '</a>' +
            '</div>' +
            '</div>';

    html += '<div style="max-width: 100% !important; max-height: 100% !important; width: 100% !important; height: 100% !important; display: none;" id="vpb_editable_comment_wrapper_' + data.comment_id + '" class="vpb_editable_status_wrapper">' +
            '<div class="input-group">' +
            '<textarea id="vpb_wall_comment_editable_data_' + data.comment_id + '" class="form-control vpb_textarea_editable_status_update" placeholder="Write a comment...">' + data.comment + '</textarea>' +
            '<span class="input-group-addon" style="vertical-align:bottom; background-color:#FFF !important; border-left:0px solid;">' +
            '<div class="cbtn vpb_cbtn" style="margin-bottom:20px;" onclick="vpb_cancel_editable_item(\'' + data.comment_id + '\', \'comment\');" title="Cancel"><i class="fa fa-times"></i></div><br clear="all">' +
            '<div class="btn btn-success btn-wall-post" onclick="vpb_save_comment_update(' + data.comment_id + ');">Save</div></span>' +
            '</div>' +
            '<div id="save_changes_loading_' + data.comment_id + '"></div>' +
            '</div>';

    html += '<div id="vpb_reply_updated_' + data.comment_id + '"></div>';
    html += '<div style="clear:both;"></div>';

    html += '<div id="vpb_reply_box_' + data.comment_id + '" class="vpb_reply_posting_wrapper" style="display: none !important;">' +
            '<span id="replied_' + data.comment_id + '"></span>' +
            '<div class="input-group">' +
            '<span class="input-group-addon vpb_no_radius_reply">' +
            '<span class="v_status_pictured_michaelhampton"><img src="http://www.vasplus.info/photos/1519977600575007907.jpeg" width="24" height="24" border="0"></span>' +
            '</span>' +
            '<div class="vpb_wrap_post_contents_r">' +
            '<textarea id="vpb_wall_reply_data_' + data.comment_id + '" onclick="vpb_hide_reply_smiley_box(\'' + data.comment_id + '\');" class="vpb_reply_textarea" placeholder="Write a reply..." onkeydown="javascript:return vpb_submit_reply(event,this, \'' + data.comment_id + '\');"></textarea>' +
            '<ul class="dropdown-menu reply_smiley_dropdown_menu" id="vpb_the_reply_smiley_box' + data.comment_id + '" style="right: 0px; left: auto; top: auto; border-radius: 0px !important; display: none;">' +
            '<li class="dropdown-header vpb_wall_li_bottom_border">' +
            '<span class="v_wall_position_info_left">What is your current mood?</span> <span class="v_wall_position_info_right" onclick="vpb_hide_reply_smiley_box(\'' + data.comment_id + '\');">x</span><div style="clear:both;"></div></li>' +
            '<li><span id="vpb_reply_smiley_box_' + data.comment_id + '"></span></li>' +
            '</ul>' +
            '<div class="vpb_textarea_photo_box" id="vpb_preview_reply_photo_' + data.comment_id + '">' +
            '<span id="vpb-display-reply-attachment-preview_' + data.comment_id + '"></span>' +
            '</div>' +
            '<div class="vpb_textarea_photo_box" style="text-align:center !important;min-height: 20px !important;" id="vpb_reply_loading_' + data.comment_id + '">' +
            '<span id="vpb_display_reply_loading_' + data.comment_id + '"></span>' +
            '</div>' +
            '</div>' +
            '<span class="vpb_no_radius_bb">' +
            '<input type="file" id="reply_photo_' + data.comment_id + '" onchange="vpb_add_file_to_reply_clicked(\'' + data.comment_id + '\');vpb_reply_image_preview(this, \'Please click on the continue button below to proceed to your post or click on the Browse photos button above to select a new photo or multiple photos at a time.\', \'Photo Enlargement\', \'' + data.comment_id + '\');" style="display:none;">' +
            '<div id="vpb_reply_bottom_icons_' + data.comment_id + '" style="">' +
            '<span class="vpb_no_radius_cc vasplus-tooltip-attached" style="display:none;" id="remove_reply_photo_' + data.comment_id + '" onclick="remove_reply_photo(\'' + data.comment_id + '\');" original-title="Remove photo"><i class="fa fa-times vfooter_icon"></i></span>' +
            '<span id="add_reply_file_clicked_' + data.comment_id + '" class="vpb_no_radius_cc vasplus-tooltip-attached" onclick="document.getElementById(\'reply_photo_' + data.comment_id + '\').click();" original-title="Attach a photo"><i class="fa fa-camera vfooter_icon"></i></span>' +
            '<span class="vpb_no_radius_cc vpb_reply_smiley_buttons vasplus-tooltip-attached" id="vpb_show_reply_smiley_button_' + data.comment_id + '" onclick="vpb_reply_smiley_box(\'' + data.comment_id + '\');" original-title="Add smiley"><i class="fa fa-smile-o vfooter_icon"></i></span>' +
            '</div>' +
            '</span>' +
            '</div>' +
            '<div style="clear:both;"></div>' +
            '</div>';


    return html;
}

function vpb_hide_popup ()
{
    $ (".vpb_wall_post_security_setting_active").addClass ('vpb_wall_post_security_setting');
    $ (".vpb_wall_post_security_setting_active").removeClass ('vpb_wall_post_security_setting_active');
    $ (".dropdown-menu2").hide ();
}

function postReplyError (jqXHR, textStatus, errorThrown)
{

}

function postComment (data)
{

    // send the data to the server
    $.ajax ({
        type: 'POST',
        url: '/blab/post/postComment',
        data: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: postSuccess,
        error: postError
    });
}

// Remove user from tagged post
function vpb_remove_me_from_tagged_post (post_id, poster_username, username)
{
    var dataString = {'page': 'remove_me_from_tagged_post', 'post_id': post_id, 'poster_username': poster_username, 'username': username};
    $.ajax ({
        type: "POST",
        url: '/blab/index/removeTaggedUser',
        data: dataString,
        beforeSend: function ()
        {
            $ ("#post_remove_tag_button_" + parseInt (post_id)).hide ();
            //$ ("#the_tagged_box_" + parseInt (post_id)).html ($ ("#v_loading_btn").val ());
        },
        success: function (response)
        {

            $ ("#the_tagged_box_" + post_id).find (".tag[userid=" + poster_username + "]").remove ();
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_remove_me_from_tagged_post('" + parseInt (post_id) + "', '" + username + "');", 10000);
    });
}

// Set where the user wants to share the update
function vpb_set_selected_share_option (name, id, lower_case_selected, icon, post_owner_username)
{
    $ ("#selected_option_shared_").val (lower_case_selected);
    $ ("#selected_option_share_option").html ('<i class="fa ' + icon + '"></i> ' + name + '&nbsp;&nbsp;<span class="caret"></span>');
    $ (".vasplus_area_ticker").hide ();
    $ ("#" + id).show ();

    if (lower_case_selected == "mywall")
    {
        // Remove the selected name so as to share on current user wall
        $ ("#vfriends_name_suggested").val ('');
        $ ("#selected_friend_to_share_with").val ('');
        $ ("#vfriends_suggestion_box").hide ();
        $ ("#vpb_share_privacy_box").css ('display', 'inline-block'); // Show privacy settings option
    }
    else
    {
        $ ("#vfriends_name_suggested").val ('');
        $ ("#selected_friend_to_share_with").val ('');
        setTimeout (function ()
        {
            $ ("#vfriends_name_suggested").focus ();
        }, 10);
        $ ("#vfriends_suggestion_box").fadeIn ();
        $ ("#v_share_publicly_button").click ();
        $ ("#vpb_share_privacy_box").css ('display', 'none'); // Show privacy settings option
    }
}

function vpb_select_this_friend_for_shares (username, fullname)
{
    $ ("#selected_friend_to_share_with").val (username);
    $ ("#search_to_select_friend_for_shares").html (fullname);
    $ ("#un_selected_for_shares").hide ();
    $ ("#selected_for_shares").show ();
    $ ("#vpb-suggested-friends-for-shares-server-response").html ('');
}

function vpb_removal_this_friend ()
{
    $ ("#selected_friend_to_share_with").val ('');
    $ ("#vfriends_name_suggested").val ('');
    $ ("#selected_for_shares").hide ();
    $ ("#un_selected_for_shares").show ();
    setTimeout (function ()
    {
        $ ("#vfriends_name_suggested").focus ();
    }, 10);
}


// Suggest friends to tag
function vpb_suggest_friends_for_shares (friend)
{
    var session_uid = $ ("#session_uid").val ();

    if (friend.length > 0)
    {
        $ ("#search_to_select_friend_for_shares").html ('');

        $ ("#vpb-suggested-friends-for-shares-server-response").html ('<div class="dropdown v_suggested_share_"><ul class="dropdown-menu vpb-hundred-percent_loading"><li class="dropdown-header vpb_wall_loading_text">' + $ ("#v_loading_btn").val () + '</li></ul></div>');

        var dataString = {'friend': friend, 'username': session_uid, 'page': 'suggest_friends_for_shares'};

        $.post ('/blab/friend/suggestFriendsForShare', dataString, function (response)
        {
            var response_brought = response.indexOf ('VPB:');
            if (response_brought != -1)
            {
                $ ("#vpb-suggested-friends-for-shares-server-response").html ('<div class="dropdown v_suggested_share_"><ul class="dropdown-menu vpb-hundred-percent_loading"><li class="dropdown-header vpb_wall_loading_text">' + response.replace ('VPB: ', '') + '</li></ul></div>');
            }
            else
            {
                if (response == "")
                {
                    $ ("#vpb-suggested-friends-for-shares-server-response").html ('');
                }
                else
                {
                    $ ("#vpb-suggested-friends-for-shares-server-response").html ('<div class="dropdown v_suggested_share_"><ul class="dropdown-menu vpb-hundred-percent" id="suggested_friends_result">' + response + '</ul></div>');
                }
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_suggest_friends_for_shares('" + friend + "');", 10000);
        });
    }
    else
    {
        $ ("#vpb-suggested-friends-for-shares-server-response").html ('');
        return false;
    }
}

/**
 * 
 * @param {type} data
 * @param {type} textStatus
 * @param {type} jqXHR
 * @returns {Boolean}
 */
function postSuccess (data, textStatus, jqXHR)
{
    $ ('#commentform').get (0).reset ();
    //displayComment (data);
    load_unseen_notification ();
    $ ("#new_tags > span").remove ();
    $ ("#selected_option_").html ($ ("#v_current_security_setting").val ());
    $ ('#commentform').find (':submit').prop ('disabled', false);
    $ ("#posts-list").prepend (data);
    bindhover ();
    return false;
}

function postError (jqXHR, textStatus, errorThrown)
{
    // display error
    showErrorMessage (errorThrown);
}

function displayComment (data)
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

function createComment (data)
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
            '<a href="/blab/index/profile/' + data.username + '">' +
            data.author +
            '</a>' +
            '<small date="' + data.date_added + '" class="text-muted timeAgo">' + timeSince (data.date_added) + ' ' + usersLocation + '</small>' +
            '</div>' +
            '</div>' +
            '<div id="vpb_editable_status_wrapper_' + data.id + '" class="vpb_editable_status_wrapper" style="display: none;">' +
            '<div class="input-group">' +
            '<textarea id="vpb_wall_status_editable_data_' + data.id + '" class="form-control vpb_textarea_editable_status_update" placeholder="Whats on your mind?">' + data.comment + '</textarea>' +
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
            '<button class="btn btn-white btn-xs"><i class="fa fa-comments"></i> Comment</button>' +
            '<button class="btn btn-white btn-xs Share" messageId="' + data.id + '"><i class="fa fa-share"></i> Share</button>' +
            '<a href="#" class="showLikes" type="post">Liked by Mike</a>' +
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

function parseDisplayDate (date)
{
    date = (date instanceof Date ? date : new Date (Date.parse (date)));
    var display = date.getDate () + ' ' +
            ['January', 'February', 'March',
                'April', 'May', 'June', 'July',
                'August', 'September', 'October',
                'November', 'December'][date.getMonth ()] + ' ' +
            date.getFullYear ();
    return display;
}

function load_unseen_notification (view)
{

    view = view || "";

    $.ajax ({
        url: "/blab/notification/fetch",
        method: "POST",
        data: {view: view},
        dataType: "json",
        success: function (data)
        {
            try {
                if (view == "yes")
                {
                    $ ("#friendRequests_bar").hide ();
                    $ ('.dropdown-menu2').html (data.notification).show ();
                    init_list ();
                }

                if (data.unseen_notification > 0)
                {
                    $ ('.count').html (data.unseen_notification);
                }
            } catch (error) {

            }

        }
        ,
        error: function (error)
        {
            //showErrorMessage (error);
        }
    });
}

var items_per_page = 5;
function init_list ()
{ // Initialize values and states for first time
    var size_list = $ (".dropdown-menu2 > li:not(.divider)").length;
    $ ('.dropdown-menu2').attr ('data-size', size_list); // save total items in custom attribute
    $ ('.dropdown-menu2 li:not(.divider):eq(0)').addClass ('first_item_selected'); //first element of the list         

    var count = 1;
    $ ('.dropdown-menu2 li:not(.divider)').each (function ()
    { // save position in custom attribute
        $ (this).attr ('data-value', count - 1);
        count++;
    });
    $ ('#prev').click (function ()
    {
        var next_item_selected = parseInt ($ ('.first_item_selected').attr ('data-value')) - items_per_page;
        $ ('.dropdown-menu2 > li:not(.divider)').removeClass ('first_item_selected');
        $ ('.dropdown-menu2 > li:not(.divider):eq(' + next_item_selected + ')').addClass ('first_item_selected');
        event_pagination (); // update buttons and li states
    });
    $ ('#next').click (function ()
    {
        var next_item_selected = parseInt ($ ('.first_item_selected').attr ('data-value')) + items_per_page;
        $ ('.dropdown-menu2 > li:not(.divider)').removeClass ('first_item_selected');
        $ ('.dropdown-menu2 > li:not(.divider):eq(' + next_item_selected + ')').addClass ('first_item_selected');
        event_pagination (); // update buttons and li states
    });
    event_pagination ();
    return false;
}

function event_pagination ()
{

    if (($ ('.first_item_selected').attr ('data-value') - items_per_page) > -1)
    {
        $ ('#prev').show ();
    }
    else
    {
        $ ('#prev').hide ();
    }

    if ($ ('.dropdown-menu2').attr ('data-size') - $ ('.first_item_selected').attr ('data-value') > items_per_page)
    {
        $ ('#next').show ();
    }
    else
    {
        $ ('#next').hide ();
    }

    $ ('.dropdown-menu2:not(.divider) > li').each (function ()
    { // show-hide li from first element of pagination 
        var present_item = parseInt ($ (this).attr ('data-value'));
        var first_item = parseInt ($ ('.first_item_selected').attr ('data-value'));
        if (present_item < first_item)
        {
            $ (this).hide ();
        }
        else
        {
            if ((present_item) < (first_item + items_per_page))
            {
                $ (this).show ();
            }
            else
            {
                $ (this).hide ();
            }
        }

    });
    return false;
}



