scrollToBottom ();

$ (function ()
{
    $ ("#vpb_display_all_users_in_group").hide ();

    // $('[data-toggle="tooltip"]').tooltip();  // nw | n | ne | w | e | sw | s | se
//    $ ('.pms_tooltip_bottom').tipsy ({fade: true, gravity: 'se'});
//    $ ('.pms_tooltip_left').tipsy ({fade: true, gravity: 'e'});
//    $ ('.vasplus_tooltip_icons').tipsy ({fade: true, gravity: 'sw'});
//    $ ('.vasplus_tooltip_photo_e').tipsy ({fade: true, gravity: 'e'});

//    $ (document).on ("click", function (e)
//    {
//        var $clicked = $ (e.target);
//        if (!$clicked.parents ().hasClass ("dropdown-menu") && !$clicked.parents ().hasClass ("pms_left_view_inner_right") && !$clicked.parents ().hasClass ("pms_message_icon") && !$clicked.parents ().hasClass ("vpb_pms_social_n_btn") && !$clicked.parents ().hasClass ("dropdown") && !$clicked.parents ().hasClass ("input-group-addon-plus") && !$clicked.parents ().hasClass ("form-control-plus") && $clicked.attr ('id') != "vfrnds_data" && $clicked.attr ('id') != "pms_actions_icon" && $clicked.attr ('id') != "v_suggested_users_box" && $clicked.attr ('id') != "vpb_pms_to_data")
//        {
//
//            // Hide the website menus
//            $ ("#v_site_menu_box").hide ();
//            $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
//            $ ("#v_site_menu").addClass ('vpb_notifications_icon');
//
//            $ ("#v_messages_box").hide ();
//            $ ("#v_actions_box").hide ();
//
//            $ ("#v_suggested_users_box").hide ();
//        }
//    });


    // Keep the drop-down box open when clicked on the header
//    $ ("li.dropdown-header").on ("click", function (e)
//    {
//        e.stopPropagation ();
//        return false;
//    });

    //$ ("textarea").expanding ();

    //$ ('#vas-scroll-to-top').hide ();

    // Auto Load Conversations on scroll to top of the conversations div
    if (parseInt ($ ("#vtotal_group_messages").val ()) < 1)
    {
    }
    else
    {
        //vpb_start_and_load_more_messages ('start', 'not-applicable');
    }

    //To detect the div scroll so as to auto load conversations
    $ ('#vpb_default_conversation').scroll (function (e)
    {
        // Check if other processes are running then pause
        if (parseInt ($ ("#vpb_is_process_running").val ()) > 0)
        {
        }
        else
        {
            //The current user has scrolled to the top of the current div
            if (parseInt ($ (this).scrollTop ()) == 0)
            {
                if (parseInt ($ ("#vtotal_group_messages").val ()) > parseInt ($ ("#vpb_total_per_load").val ()))
                {
                    var vpb_start = $ ("#vpb_start").val (); //Where to start loading the updates
                    //There are still more records to load
                    if (parseInt (vpb_start) <= parseInt ($ ("#vtotal_group_messages").val ()) && vpb_running_process == false)
                    {
                        // vpb_start_and_load_more_messages ('auto-load', 'not-applicable');
                    }
                    else
                    {
                    }
                }
                else
                {
                }
            }
            else
            {
            }

            var theHeight = parseInt (document.getElementById ("vpb_default_conversation").scrollHeight) - parseInt (document.getElementById ("vpb_default_conversation").scrollTop)

            if (parseInt (theHeight) < parseInt ($ (this).height ()) + 10)
            {
                $ ("#seen_message_notification_box").fadeIn ('slow');
            }
            else
            {
                $ ("#seen_message_notification_box").hide ();
            }
        }
    });
//    $ (window).scroll (function (e)
//    {
//        if ($ (this).scrollTop () > 100)
//        {
//            $ ('#vas-scroll-to-top').fadeIn ();
//        }
//        else
//        {
//            $ ('#vas-scroll-to-top').fadeOut ();
//        }
//
//    });
//
//    $ ('#vas-scroll-to-top').click (function (event)
//    {
//        var $anchor = $ (this);
//        $ ('html, body').stop ().animate ({
//            scrollTop: 0
//        }, 1500, 'easeInOutExpo');
//        //event.preventDefault();
//        setTimeout (function ()
//        {
//            $anchor.fadeOut ();
//        }, 1000);
//    });



//    // When mouse over the search results, add scroller to the result box if its too long in height
//    $ ("#vpb_display_messages").on ('mouseenter', function ()
//    {
//        $ (".vfriend_inner").css ('overflow-y', 'auto');
//    }).on ('mouseleave', function ()
//    {
//        $ (".vfriend_inner").css ('overflow', 'hidden');
//    });
//
//    setTimeout (function ()
//    {
//        vpb_set_box_height (); // Set PMS heights
//    }, 20);
//
//
//    //vpb_setcookie('to_username', 'uttam21rabadiya', 7);
    //vpb_load_system_users ('normal'); // Load system users on first page load

    vpb_display_new_users_in_group (); // Display users newly added in a group conversation
    vpb_display_users_in_group (); // Display users in current conversation group

    vpb_show_the_remove_button (); // Display add or remove users from a group button
    var timer;
    var x;
    $ ('#vfrnds_data').keyup (function ()
    {
        if (x)
        {
            x.abort ()
        } // If there is an existing XHR, abort it.
        clearTimeout (timer); // Clear the timer so we don't end up with dupes.
        timer = setTimeout (function ()
        { // assign timer a new timeout 

            x = vpb_search_for_this_user ($ ('#vfrnds_data').val ());
        }, 1000); // 2000ms delay, tweak for faster/slower
    });


    // Auto suggest users
    var timers;
    var y;
    $ ("#vpb_pms_to_data").keyup (function ()
    {
        if (y)
        {
            y.abort ()
        } // If there is an existing XHR, abort it.
        clearTimeout (timers); // Clear the timer so we don't end up with dupes.
        timers = setTimeout (function ()
        { // assign timer a new timeout 

            y = vpb_search_for_suggested_user ($ ('#vpb_pms_to_data').val ());
        }, 1000); // 2000ms delay, tweak for faster/slower
    });


//    if (!vpb_session_is_created ())
//    {
//        setTimeout (function ()
//        {
//            vpb_compose_new_message ();
//        }, 500);
//    }
//    else if (vpb_getcookie ('compose_new_message') == "yes")
//    {
//        setTimeout (function ()
//        {
//            vpb_show_compose_new_message_box ();
//        }, 500);
//    }
//    else
//    {
//    }
//
//    setTimeout (function ()
//    {
//        //Show and hide the send message button
//        $ ("input#vpb_press_enter_for_submit").on ('click', function ()
//        {
//            if ($ ('input#vpb_press_enter_for_submit').is (':checked'))
//            {
//                $ ("#vpb_submitBox").hide ();
//                vpb_setcookie ('press_enter_to_submit', $ ("#session_uid").val (), 7);
//            }
//            else
//            {
//                $ ("#vpb_submitBox").fadeIn ();
//                vpb_removecookie ('press_enter_to_submit');
//            }
//        });
//
//        $ ("input#vpb_press_enter_for_submits").on ('click', function ()
//        {
//            if ($ ('input#vpb_press_enter_for_submits').is (':checked'))
//            {
//                $ ("#vpb_submitBox").hide ();
//                vpb_setcookie ('press_enter_to_submit', $ ("#session_uid").val (), 7);
//            }
//            else
//            {
//                $ ("#vpb_submitBox").fadeIn ();
//                vpb_removecookie ('press_enter_to_submit');
//            }
//        });
//
//        if (vpb_getcookie ('press_enter_to_submit') && vpb_getcookie ('press_enter_to_submit') != "")
//        {
//            $ ('input#vpb_press_enter_for_submit').attr ('checked', true);
//            $ ('input#vpb_press_enter_for_submits').attr ('checked', true);
//            $ ("#vpb_submitBox").hide ();
//        }
//        else
//        {
//            $ ('input#vpb_press_enter_for_submit').attr ('checked', false);
//            $ ('input#vpb_press_enter_for_submits').attr ('checked', false);
//            $ ("#vpb_submitBox").fadeIn ();
//        }
//    }, 300);
//
//
//    //Enable and Disable Press enter to send messages
//    $ ('textarea#vpb_pms_message_data').on ("keydown", function (vpb_event)
//    {
//        var vpb_keycode = (vpb_event.keyCode ? vpb_event.keyCode : vpb_event.which);
//
//        if (vpb_event.keyCode == 13 && vpb_event.shiftKey == 0)
//        {
//            if (vpb_getcookie ('press_enter_to_submit') && vpb_session_is_created () || $ ('input#vpb_press_enter_for_submit').is (':checked') && vpb_session_is_created ())
//            {
//                if (vpb_trim ($ ("textarea#vpb_pms_message_data").val ()) == "" || $ ("textarea#vpb_pms_message_data").val () == "Write a reply..." || $ ("textarea#vpb_pms_message_data").val () == "Write your message...")
//                {
//                    $ ("textarea#vpb_pms_message_data").val ("").animate ({
//                        "min-height": "40px"
//                    }, "fast");
//                    vpb_event.preventDefault ();
//                    return false;
//                }
//                else
//                {
//                    vpb_send_message (); //Call the message sender function to send the message
//                }vpb_get_user_onmouseover_data 
//            }
//            else
//            {
//                //Enable the enter key and do not submit for on enter key press 
//            }
//        }
//        else
//        {
//            //Do nothing man
//        }
//    });

});


// Show previously searched users on clicking the search box if available
function vpb_show_suggested_searched_user ()
{
    if ($ ('#vpb_suggested_users_displayer').text ().length == 0)
    {
    }
    else
    {
        $ ("#v_suggested_users_box").fadeIn ();
    }
}

// Search in conversation or toggle search in conversation box
function vpb_search_or_toggle_search_box (which)
{
    if (which == "normal")
    {
        if ($ ("#vtop_search_box").css ('display') == "none")
        {
            $ ("#vtop_pm_links").hide ();
            $ ("#vpm_data").val ('');
            $ ("#vtop_search_box").fadeIn ();
        }
        else
        {
            $ ("#vtop_search_box").hide ();
            $ ("#vtop_pm_links").fadeIn ();
        }
    }
    else if (which == "cancel")
    {
        $ ("#vtop_search_box").hide ();
        $ ("#vtop_pm_links").fadeIn ();

        $ ("#vSearched_messages").hide ();
        $ ("#vDefault_messages").show ();
        $ ("#vpb_loading_searched_messages").html ('');
        $ ("#vpb_is_process_running").val ('0');
    }
    else
    {
        var vpm_data = vpb_trim (vpb_strip_tags ($ ("#vpm_data").val ()));
        if (vpm_data == "")
        {
            $ ("#vtop_search_box").hide ();
            $ ("#vtop_pm_links").fadeIn ();
        }
        else
        {
            $ ("#vDefault_messages").hide ();
            $ ("#vSearched_messages").show ();
            $ ("#vpb_loading_searched_messages").html ('');
            vpb_start_and_load_more_messages ('search-in-conversation', vpm_data);

            $ ("#seen_message_notification").hide ();
            $ ("#vpb_is_process_running").val ('1');
        }
    }
}


// New message notification
function vpb_get_new_message_notification ()
{
    var session_username = $ ("#session_uid").val ();

    if (session_username != "")
    {
        var dataString = {"username": session_username, "page": "check_for_notifications"};
        $.ajax ({
            type: "POST",
            url: vpb_site_url + 'pms-processor.php',
            data: dataString,
            beforeSend: function ()
            {},
            success: function (response)
            {
                if (parseInt (response) > 0)
                {
                    $ ("#totalNotifi").html (parseInt (response));
                    $ ("#pms_counter").fadeIn ();
                }
                else
                {
                    $ ("#pms_counter").fadeOut ();
                }
            }
        });
    }
    else
    {
    }
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
//Delete cookie
function vpb_removecookie (name)
{
    vpb_setcookie (name, "", -1);
}

// Compose new message
function vpb_compose_new_message ()
{
    if (vpb_getcookie ('v_group_id') == "" || vpb_getcookie ('v_group_id') == null || vpb_getcookie ('v_group_id') == undefined)
    {
    }
    else
    {
        $ ("#vpb_user_selected" + vpb_getcookie ('v_group_id')).removeClass ('vpb_users_wraper_active');
        $ ("#vpb_user_selected" + vpb_getcookie ('v_group_id')).addClass ('vpb_users_wraper');
    }

    vpb_removecookie ('group_fullname');
    vpb_removecookie ('group_username');
    vpb_removecookie ('group_photo');
    vpb_removecookie ('v_group_id');
    vpb_removecookie ('v_group_manager');
    vpb_removecookie ('v_last_loaded_message_id');
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

    $ ("#vpb_display_group_messages").html ('');

    $ (".over-body").html ('');
    $ ("#vpb_added_users_box").html ('');
    //document.getElementById("chatMsg").placeholder = $("#write_your_message").val();

    $ ("#vpb_add_people_in_group").hide ();
    $ ("#vpb_default_message").hide ();
    $ ("#vpb_new_message").show ();
    $ (".v_added_u_sers").show ();


    $ ("#vpb_default_conversation").hide ();
    $ ("#vpb_new_conversation").show ();


    vpb_setcookie ('compose_new_message', 'yes', 30);

    vpb_setcookie ('v_group_id', vpb_gen_random (), 30);

    $ ("#seen_message_notification_box").hide ();

}

// Generate Communication data
function vpb_gen_random ()
{
    return Math.random ().toString ().split ("0.").join ("1").toString ().split (".").join ("");
}

// Show Compose new message Box when necessary
function vpb_show_compose_new_message_box ()
{
    //document.getElementById ("vpb_pms_message_data").placeholder = $ ("#write_your_message").val ();

    $ ("#vpb_add_people_in_group").hide ();
    $ ("#vpb_default_message").hide ();
    $ ("#vpb_new_message").show ();

    $ ("#vpb_default_conversation").hide ();
    $ ("#vpb_new_conversation").show ();

    vpb_setcookie ('compose_new_message', 'yes', 30);

    if (vpb_getcookie ('v_group_id') == "" || vpb_getcookie ('v_group_id') == null || vpb_getcookie ('v_group_id') == undefined)
    {
    }
    else
    {
        $ ("#vpb_user_selected" + vpb_getcookie ('v_group_id')).removeClass ('vpb_users_wraper_active');
        $ ("#vpb_user_selected" + vpb_getcookie ('v_group_id')).addClass ('vpb_users_wraper');
    }
    vpb_setcookie ('v_group_id', vpb_gen_random (), 30);
    vpb_setcookie ('v_group_manager', $ ("#session_uid").val (), 30);
}

function vpb_add_more_poeple_in_group ()
{
    $ ("#seen_message_notification_box").hide ();
    $ ("#vpb_default_message").hide ();
    $ ("#vpb_new_message").hide ();
    $ ("#vpb_add_people_in_group").show ();

    $ ("#vpb_default_conversation").hide ();
    $ ("#vpb_new_conversation").show ();
    $ (".v_added_u_sers").show ();
    setTimeout (function ()
    {
        $ ("#vpb_pms_to_data").val ('').focus ();
    }, 100);
}


function vpb_search_for_suggested_user (system_username)
{

    if (system_username != "" && system_username != "Name")
    {
        var dataString = {"system_username": system_username};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/searchNewChatUsers',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#v_suggested_users_box").fadeIn ();
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
        $ ("#v_suggested_users_box").fadeOut ();
        //$("#vpb_suggested_users_displayer").html('');
    }
}

// Auto suggest users
var timers;
var y;
$ ("#vpb_pms_to_data").keyup (function ()
{
    if (y)
    {
        y.abort ()
    } // If there is an existing XHR, abort it.
    clearTimeout (timers); // Clear the timer so we don't end up with dupes.
    timers = setTimeout (function ()
    { // assign timer a new timeout 

        y = vpb_search_for_suggested_user ($ ('#vpb_pms_to_data').val ());
    }, 1000); // 2000ms delay, tweak for faster/slower
});

// Already added user to list
function vpb_isInArray (group_username, user)
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
// Check if a group session exit or not
function vpb_session_is_created ()
{

    if (vpb_getcookie ('group_username') != "" && vpb_getcookie ('group_username') != null && vpb_getcookie ('group_username') != undefined)
    {
        return true;
    }
    else
    {
        return false;
    }
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

// Add user to group
function vpb_add_new_user_to_group (fullname, username, photo)
{
    if (vpb_session_is_created () && vpb_isInArray (vpb_getcookie ('group_username'), username))
    {
        // Already in the list
    }
    else
    {

        var group_fullname = new Array ();
        var group_username = new Array ();
        var group_photo = new Array ();
        if (vpb_session_is_created ())
        {
            group_fullname = vpb_getcookie ('group_fullname').split (/\,/);

            group_username = vpb_getcookie ('group_username').split (/\,/);
            //group_photo = vpb_getcookie ('group_photo').split (/\,/);
            group_fullname[group_fullname.length] = fullname;
            group_username[group_username.length] = username;
            group_photo[group_photo.length] = photo + '#' + username;
        }
        else
        {
            group_username[group_username.length] = username;
            group_fullname[group_fullname.length] = fullname;
            //group_photo[group_photo.length] = photo + '#' + username;
        }

        vpb_setcookie ('group_fullname', group_fullname, 30);
        vpb_setcookie ('group_username', group_username, 30);
        vpb_setcookie ('group_photo', group_photo, 30);
        $ ("#vpb_user_selected_" + username).hide ();
        $ ("#vpb_pms_to_data").val ('').focus ();
        $ ("#vpb_added_users_box").append ('<span id="vpb_new_user_in_group_' + username + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vpb_remove_new_user_from_group(\'' + fullname + '\', \'' + username + '\', \'' + photo + '\')"></i></span></span>');
        setTimeout (function ()
        {
            $ ("#v_added_u_sers").animate ({scrollTop: parseInt ($ ('#v_added_u_sers').height ()) + 10000}, 1000);
        }, 10);
        vpb_display_users_in_group ();

        var newMessage = vpb_getcookie ("compose_new_message");

        var group_id = vpb_getcookie ('v_group_id') != null ? vpb_getcookie ('v_group_id') : "";
        if (group_id != "" && group_id != undefined)
        {
            var dataString = {"username": username, "newMessage": newMessage, "group_fullname": group_fullname, "group_id": group_id, "page": "vpb_add_removed_user_in_group_again"};
            $.ajax ({
                type: "POST",
                url: '/blab/chat/addUserToGroupChat',
                data: dataString,
                beforeSend: function ()
                {},
                success: function (response)
                {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 1)
                    {
                        vpb_setcookie ('v_group_id', objResponse.data.id, 30);
                        vpb_load_system_users ('new');
                    }
                }
            });
        }
        else
        {
        }
    }
}

var vpb_user_loader_timer = 600,
        vpb_time_out = null;

function vpb_get_user_onmouseover_data (username, fullname, country, photo, type)
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);
    $ (".v_load_user_detail").fadeIn ();
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

// Remove self from group conversation
function vpb_remove_self_from_group (username)
{
    var group_manager = vpb_getcookie ('v_group_manager') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_manager')));

    if (vpb_session_is_created () && group_manager != "" && vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
    {
        var group_id = vpb_getcookie ('v_group_id');
        var group_username = vpb_getcookie ('group_username');

        var dataString = {"username": username, "group_id": group_id, "group_manager": group_manager, "group_username": group_username, "page": "vpb_remove_user_from_group"};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/removeSelfFromGroup',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#v_remval_text").html ($ ("#vpb_loading_image_gif").val ());
            },
            success: function (response)
            {
                vpb_compose_new_message ();
                vpb_load_system_users ('new');

                $ ("#seen_message_notification").hide ();
                $ ('.modal').modal ('hide');
                $ ("#v_remval_text").html ($ ("#leave_group_conversation_text").val ());
            }
        });
    }
    else
    {
        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }
}

// Confirm message deletion before the actual deletion
function vpb_confirm_delete_multiple_messages (action)
{
    $ ("#seen_message_notification_box").hide ();

    if (action == "auto")
    {
        $ ("#vpb_entire_messages").val ('1');
        $ ("#v-message-deletion-text").html ($ ("#display_message_for_entire_message_deletion_text").val ());
        $ ("#v-delete-messages-box").click ();
        return false;
    }
    else
    {
        var d_selected_items, display_message;

        if ($ ('.vpb_ms_deletion_option_box').is (':checked') && $ ('.vpb_ms_deletion_option_box').val () != null)
        {
            d_selected_items = new Array ();

            $ ('.vpb_ms_deletion_option_box').each (function ()
            {
                if ($ (this).is (':checked'))
                {
                    d_selected_items.push (parseInt ($ (this).val ()));
                }
                else
                {
                }
            });
        }
        else
        {
            d_selected_items = "";
        }

        if (d_selected_items == "")
        {
            $ ("#v-pms-message").html ($ ("#no_conversation_selected_for_deletion_text").val ());
            $ ("#v-pms-alert-box").click ();
            return false;
        }
        else
        {
            if (d_selected_items.length == parseInt (1))
            {
                display_message = $ ("#display_message_for_single_item_deletion_text").val ();
            }
            else
            {
                display_message = $ ("#display_message_for_multiple_item_deletion_text").val ();
            }

            $ ("#v-message-deletion-text").html (display_message);
            $ ("#v-delete-messages-box").click ();
        }
    }
}

// Perform actual message deletion because it has been confirmed
function vpb_proceed_with_pm_deletion ()
{
    var vpb_entire_messages = $ ("#vpb_entire_messages").val ();

    vpb_entire_messages = 1;

    var userId = $ (".selectedChat").attr ("user-id");

    if (vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
    {
        var group_id = vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_id')));
        var group_type = "group";
    }
    else if (userId != "" && userId != null && userId != undefined)
    {
        var group_id = vpb_trim (vpb_strip_tags (userId));
        var group_type = "user";
    }
    else
    {
        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }

    if (vpb_entire_messages == 1)
    {
        $ ("#seen_message_notification").hide ();

        //$ (".deletingmessages").html ('<img src="' + vpb_site_url + 'img/loading.gif" align="absmiddle" title="Deleting..." />');
        $ (".vpb_ms_wraper").delay (1000).fadeOut (1000, function ()
        {});

        // DISABLED
        $ ("#vpb_entire_messages").val ('0');
        $ ("#vpb_start").val (parseInt ($ ("#vtotal_group_messages").val ()));

        $ ("#vpb_message_deletion_option_selected").val ('0');
        $ (".vpb_ms_wraper_photo").css ('min-width', 'auto');
        $ (".vpb_ms_deletion_option_box").css ('display', 'none');
        $ ("#vpb_message_deletion_button").hide ();
        $ ("#vpb_message_sending_button").fadeIn ();
//            $ ("#v-pms-message").html ('Sorry, this feature has been disabled in the demo version.<br>Thank you.');
//            $ ("#v-pms-alert-box").click ();
//            return false;

        var dataString = {"group_id": group_id, "group_type": group_type};

        $.post ('/blab/chat/deleteConversation', dataString, function (response)
        {
            $ ("#vpb_entire_messages").val ('0');
            $ ("#vpb_start").val (parseInt ($ ("#vtotal_group_messages").val ()));

            $ ("#vpb_message_deletion_option_selected").val ('0');
            $ (".vpb_ms_wraper_photo").css ('min-width', 'auto');
            $ (".vpb_ms_deletion_option_box").css ('display', 'none');
            $ ("#vpb_message_deletion_button").hide ();
            $ ("#vpb_message_sending_button").fadeIn ();

            vpb_load_system_users ('new');
        });
    }
    else
    {
        var d_selected_items = new Array ();
        var id;
        $ ('.vpb_ms_deletion_option_box').each (function ()
        {
            if ($ (this).is (':checked'))
            {
                id = parseInt ($ (this).val ());
                d_selected_items.push (parseInt (id));

                $ ("#vdeletion_loading_" + parseInt (id)).html (' <img src="' + vpb_site_url + 'img/loading.gif" align="absmiddle" title="Deleting..." />');
                $ ("#delete_row" + parseInt (id)).delay (1000).fadeOut (4000, function ()
                { });
                $ ("#delete_row" + parseInt (id)).remove ();

            }
            else
            {
            }
        });

        // DISABLED
        $ ("#vpb_entire_messages").val ('0');
        $ ("#vpb_start").val (parseInt ($ ("#vtotal_group_messages").val ()));

//            $ ("#v-pms-message").html ('Sorry, this feature has been disabled in the demo version.<br>Thank you.');
//            $ ("#v-pms-alert-box").click ();
//            return false;


        var the_selected_items = d_selected_items.join ("|");
        var dataString = {"id": the_selected_items, "group_id": group_id};

        $.post (vpb_site_url + 'pms-processor.php', dataString, function (response)
        {
            var responsebrought = response.indexOf ('finished');
            if (responsebrought != -1)
            {
                // Remove the message deletion button and show the sending message box since there is no more message to delete
                $ ("#vpb_message_deletion_option_selected").val ('0');
                $ (".vpb_ms_wraper_photo").css ('min-width', 'auto');
                $ (".vpb_ms_deletion_option_box").css ('display', 'none');
                $ ("#vpb_message_deletion_button").hide ();
                $ ("#vpb_message_sending_button").fadeIn ();
            }
            else
            {
                var response_brought = response.indexOf ('VPB_ERROR');
                if (response_brought != -1)
                {
                    var uid;
                    $ ('.vpb_ms_deletion_option_box').each (function ()
                    {
                        if ($ (this).is (':checked'))
                        {
                            uid = parseInt ($ (this).val ());
                            $ ("#vdeletion_loading_" + parseInt (uid)).html ('');
                            $ ("#delete_row" + parseInt (uid)).delay (1000).fadeIn (2000, function ()
                            {
                                $ ("#delete_row" + parseInt (uid)).show ();
                            });
                        }
                        else
                        {
                        }
                    });
                    setTimeout (function ()
                    {
                        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
                        $ ("#v-pms-alert-box").click ();
                    }, 5000);
                    return false;
                }
                else
                {
                    var old_pms_start = parseInt ($ ("#vpb_start").val ());
                    //if(parseInt(old_pms_start) <= parseInt(5)) { $("#vpb_start").val(parseInt(5)); }
                    //else { $("#vpb_start").val(parseInt(old_pms_start)-parseInt(d_selected_items.length)); }
                }
            }
            vpb_load_system_users ('new');
        });
    }

}

// Show the Actions menu
function vpb_show_actions_menu ()
{
    $ ("#pms_counter").hide ();
    $ ("#v_messages_box").hide ();

    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');

    if ($ ("#v_actions_box").css ('display') == "none")
    {
        $ ("#v_actions_box").show ();
    }
    else
    {
        $ ("#v_actions_box").hide ();
    }
}

// Confirm self removal from group conversation
function vpb_confirm_remove_self_from_group (username)
{
    if (vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
    {
        $ ("#v_remval_text").html ($ ("#leave_group_conversation_text").val ());
        $ ("#remove_self_from_groups").modal ("show");
    }
    else
    {
        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }
}

function vpb_get_user_onmouseover_datas ()
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);
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


// Display all newly added users in a group
function vpb_display_users_in_group ()
{
    if (vpb_session_is_created ())
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();
        var vpb_group_users_photo = new Array ();
        vpb_group_users_fname = vpb_getcookie ('group_fullname').split (/\,/);
        vpb_group_users_name = vpb_getcookie ('group_username').split (/\,/);
        //vpb_group_users_photo = vpb_getcookie ('group_photo').split (/\,/);
        var total_display = 0;
        var total_users = parseInt (vpb_group_users_name.length);

        $ ("#vpb-current-group-name").html ('');
        $ ("#vpb_display_all_users_in_group").html ('');
        $ ("#vpb_display_all_users_in_group_for_report").html ('');
        for (var u = 0; u < parseInt (total_users); u++)
        {
            if (vpb_group_users_name[u] != "")
            {
                var fullname = vpb_group_users_fname[u];
                var username = vpb_group_users_name[u];

                var photo = vpb_group_users_photo[u];
                var nameData = fullname.split (' ');
                var firstname = nameData[0];

                $ ("#vpb_display_all_users_in_group").append ('<div style="clear:both;"></div>\
                                <div class="vpb_msg_wraper">\
                                <div class="vpb_msg_wraper_photo">\
                                <img src="' + photo + '" border="0">\
                                </div>\
\
                                <div class="vpb_msg_wraper_name">\
                                <span class="vpb_msg_wraper_name_left">\
                                <span>' + fullname + '</span>\
                                </span>\
                                \
                                <div style="clear:both;"></div>\
                                </div>\
                                </div>');
                $ ("#vpb_display_all_users_in_group_for_report").append ('<div style="clear:both;"></div>\
                                <div class="vpb_msg_wraper">\
                                <div class="vpb_msg_wraper_photo">\
                                <img src="' + photo + '" border="0">\
                                </div>\
\
                                <div class="vpb_msg_wraper_name">\
                                <span class="vpb_msg_wraper_name_left">\
                                <span>' + fullname + '</span>\
                                </span>\
                                \
                                <div style="clear:both;"></div>\
                                </div>\
                                <div class="vpb_users_wraper_right" align="right">\
                                        <span class="cbtnb" style=" margin-top:0px; cursor:pointer; float:right; margin-right:0px;" onclick="vpb_report_this(\'' + fullname + '\', \'' + username + '\');">' + $ ("#report_abuse_submission_label").val () + '</span><div style="clear:both;"></div>\
                                        </div>\
                                </div>');
                if (vpb_getcookie ('group_name') == "" || vpb_getcookie ('group_name') == null || vpb_getcookie ('group_name') == undefined)
                {
                    if (parseInt (total_users) == 1)
                    {
                        $ ("#vpb-current-group-name").append ('<span   class="vpb_added_users_names">' + fullname + '</span>');
                    }
                    else if (parseInt (total_users) == 2)
                    {
                        if (parseInt (total_display) == 0)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                        else
                        {
                            $ ("#vpb-current-group-name").append (' and <span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                    }
                    else if (parseInt (total_users) == 3)
                    {
                        if (parseInt (total_display) == 0)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>, ');
                        }
                        else if (parseInt (total_display) == 1)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                        else
                        {
                            $ ("#vpb-current-group-name").append (' and <span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                    }
                    else if (parseInt (total_users) == 4)
                    {
                        if (parseInt (total_display) == 0)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>, ');
                        }
                        else if (parseInt (total_display) == 1)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>, ');
                        }
                        else if (parseInt (total_display) == 2)
                        {
                            $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                        else
                        {
                            $ ("#vpb-current-group-name").append (' and <span class="vpb_added_users_names">' + firstname + '</span>');
                        }
                    }
                    else
                    {
                        if (parseInt (total_display) <= 3)
                        {
                            if (parseInt (total_display) == 0)
                            {
                                $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>, ');
                            }
                            else if (parseInt (total_display) == 1)
                            {
                                $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>, ');
                            }
                            else if (parseInt (total_display) == 2)
                            {
                                $ ("#vpb-current-group-name").append ('<span class="vpb_added_users_names">' + firstname + '</span>');
                            }
                            else
                            {
                                var total_others = parseInt (total_users) - 3;
                                var others_label = parseInt (total_others) > 1 ? "others" : "person";
                                $ ("#vpb-current-group-name").append (' and ' + parseInt (total_others) + ' <span class="vpb_added_users_names">' + others_label + '</span>');
                            }
                        }
                        else
                        {
                        }
                    }
                    total_display++;
                }
                else // User the saved group name
                {
                    var others_label = parseInt (total_users) > 1 ? "people" : "person";
                    $ ("#vpb-current-group-name").html ('<span   class="vpb_added_users_names">' + vpb_getcookie ('group_name') + '</span> (<span class="vpb_added_users_names">' + parseInt (total_users) + ' ' + others_label + '</span>)');
                }
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

//this variable represents the total number of popups can be displayed according to the viewport width
var total_popups = 0;

//arrays of popups ids
var popups = [];

// Already added user to list
function vpb_vchat_isInArray (group_username, user)
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

//calculate the total number of popups suitable and then populate the toatal_popups variable.
function vpb_vchat_position_chat_boxes ()
{
    var width = window.innerWidth - 202;
    if (parseInt (width) < 520)
    {
        total_popups = 0;
        //$ ("#vpb_chat_main_box").hide ();
        $ ("#vchat_hidden_boxes_status").val ('1');
        $ ("#vpb_vchat_hidden_boxes").hide ();
        $ ("#vpb_vchat_hidden_boxes").css ('display', 'none');

    }
    else
    {
        width = parseInt (width) - 230;
        //280 is width of a single popup box
        total_popups = parseInt (width / 280);

        $ ("#vpb_chat_main_box").show ();
        //$("#vchat_hidden_boxes_status").val('0')
        $ ("#vpb_vchat_hidden_boxes").show ();
    }
    vchat_display_chat_box ();
}

function vchat_display_chat_box ()
{
    var right = 230;

    var iii = 0;
    for (iii; iii < total_popups; iii++)
    {
        if (popups[iii] != undefined)
        {
            console.log (popups[iii]);
            try {
                var element = document.getElementById (popups[iii]);
                element.style.right = right + "px";
                right = right + 280;
                element.style.display = "block";

                if ($ ("#vchat_hidden_" + popups[iii]).length == 1)
                {
                    $ ("#vchat_hidden_" + popups[iii]).html ('').hide ();
                }
                else
                {
                }
            } catch (error) {

            }

        }
    }

    var hidden_group_uid = new Array ();

    for (var jjj = iii; jjj < popups.length; jjj++)
    {
        var element = document.getElementById (popups[jjj]);
        element.style.display = "none";

        if ($ ("#vchat_hidden_" + popups[jjj]).length == 0)
        {
            var vpb_group_fname = new Array ();
            vpb_group_fname = vchat_getcookie ('group_fullname' + popups[jjj]) == null ? 0 : vchat_getcookie ('group_fullname' + popups[jjj]).split (/\,/);
            var dtotal_users = parseInt (vpb_group_fname.length);

            var dFullName;
            var dfirstname;

            if (vchat_isArray (vpb_group_fname))
            {
                for (var f = 0; f < parseInt (dtotal_users); f++)
                {
                    if (vpb_group_fname[f] != "")
                    {
                        var dfullname = vpb_group_fname[f];
                        var nameData = dfullname.split (' ');
                        dfirstname = nameData[0];

                    }
                    else
                    {
                        dfirstname = '';
                    }
                }
            }
            else
            {
                dfirstname = '';
            }

            var dcounted = parseInt (dtotal_users) - 1;
            var dothers_label = parseInt (dcounted) > 1 ? "others" : "person";

            if (dfirstname == "" || parseInt (dtotal_users) == 1)
            {
                if (vchat_getcookie ('group_fullname' + popups[jjj]) == null)
                {
                    dFullName = document.getElementById ('compose_new_message_text').value;
                }
                else
                {
                    dFullName = vchat_getcookie ('group_fullname' + popups[jjj]).length > 20 ? vchat_getcookie ('group_fullname' + popups[jjj]).substr (0, 20) + '...' : vchat_getcookie ('group_fullname' + popups[jjj]);
                }
            }
            else
            {
                dFullName = dfirstname.substr (0, 10) + ' and ' + parseInt (dcounted) + ' ' + dothers_label;
            }

            $ ("#vchats_hidden_wrapper").append ('<div onclick="vpb_show_this_vchat_window(\'' + popups[jjj] + '\');" class="vpb_hidden_chats" id="vchat_hidden_' + popups[jjj] + '">' + dFullName + '</div>');
        }
        else
        {
            var vpb_group_fname = new Array ();
            vpb_group_fname = vchat_getcookie ('group_fullname' + popups[jjj]) == null ? 0 : vchat_getcookie ('group_fullname' + popups[jjj]).split (/\,/);
            var dtotal_users = parseInt (vpb_group_fname.length);
            var dFullName, dfirstname;

            if (vchat_isArray (vpb_group_fname))
            {
                for (var f = 0; f < parseInt (dtotal_users); f++)
                {
                    if (vpb_group_fname[f] != "")
                    {
                        var dfullname = vpb_group_fname[f];
                        var nameData = dfullname.split (' ');
                        dfirstname = nameData[0];

                    }
                    else
                    {
                        dfirstname = '';
                    }
                }
            }
            else
            {
                dfirstname = '';
            }

            var dcounted = parseInt (dtotal_users) - 1;
            var dothers_label = parseInt (dcounted) > 1 ? "others" : "person";

            if (dfirstname == "" || parseInt (dtotal_users) == 1)
            {
                if (vchat_getcookie ('group_fullname' + popups[jjj]) == null)
                {
                    dFullName = document.getElementById ('compose_new_message_text').value;
                }
                else
                {
                    dFullName = vchat_getcookie ('group_fullname' + popups[jjj]).length > 20 ? vchat_getcookie ('group_fullname' + popups[jjj]).substr (0, 20) + '...' : vchat_getcookie ('group_fullname' + popups[jjj]);
                }
            }
            else
            {
                dFullName = dfirstname.substr (0, 10) + ' and ' + parseInt (dcounted) + ' ' + dothers_label;
            }
            $ ("#vchat_hidden_" + popups[jjj]).show ().html (dFullName);
        }
        hidden_group_uid[hidden_group_uid.length] = popups[jjj];

    }
    if (parseInt (hidden_group_uid.length) < 1)
    {
        $ ("#vchat_hidden_boxes_status").val ('0');
        $ ("#vchat_hidden_vtop").hide ();
        $ ("#vchat_hidden_vbottom").hide ();
        $ ("#vpb_vchat_hidden_boxes").hide ();
    }
    else
    {
        if ($ ("#vchat_hidden_boxes_status").val () == 1)
        {
        }
        else
        {
            $ ("#vchat_hidden_vtop").show ();
            $ ("#vpb_vchat_hidden_boxes").show ();
        }
    }
    var add_or_remove_s = parseInt (hidden_group_uid.length) > 1 ? 's' : '';
    $ ("#d_total_hidden_vchats").html (hidden_group_uid.length);
    $ (".add_or_remove_s").html (add_or_remove_s);
    $ ("#vtotal_hidden_vchats").val (parseInt (hidden_group_uid.length));

}


function vpb_start_conversation (session_username, fullname, username, group_id, group_name)
{
    $ ("#seen_message_notification").hide ();

    fullname_arr = fullname.split ('|').join (",");
    username_arr = username.split ('|').join (",");
    group_manager = 'michael.hampton';
    //photo_arr = photo.split ('|').join (",");

    vpb_removecookie ('group_fullname');
    vpb_removecookie ('group_username');
    vpb_removecookie ('group_photo');
    vpb_removecookie ('v_group_id');
    vpb_removecookie ('v_group_manager');
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

    setTimeout (function ()
    {
        vpb_setcookie ('group_fullname', fullname_arr, 30);
        vpb_setcookie ('group_username', username_arr, 30);
        //vpb_setcookie ('group_photo', photo_arr, 30);
        vpb_setcookie ('v_group_id', group_id, 30);
        vpb_setcookie ('v_group_manager', group_manager, 30);
        if (group_name != "")
        {
            vpb_setcookie ('group_name', group_name, 30);
        }
        else
        {
        }
//        if (group_pic != "")
//        {
//            vpb_setcookie ('group_pic', group_pic, 30);
//        }
//        else
//        {
//        }

        // Make changes to the edit conversation pop-up box when a new session is created
        $ ("#v_group_details_status").html ('');

        if (vpb_getcookie ('group_name') != "" && vpb_getcookie ('group_name') != null && vpb_getcookie ('group_name') != undefined)
        {
            $ ("#vgroup_name").val (vpb_getcookie ('group_name'));
        }
        else
        {
            $ ("#vgroup_name").val ('');
        }

        if (vpb_getcookie ('group_pic') != "" && vpb_getcookie ('group_pic') != null && vpb_getcookie ('group_pic') != undefined)
        {
            $ ("#vpb_display_group_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#conversation_photo_label_text").val () + '\', \'' + vpb_getcookie ('group_pic') + '\');" src="' + vpb_getcookie ('group_pic') + '" title=' + $ ("#conversation_photo_label_text").val () + ' border="0">');
        }
        else
        {
            $ ("#vpb_display_group_photo").html ('');
        }



        $ ("#v_messages_box").hide (); // Hide the notification pop up box
        $ ("#vpb_start").val (0);
        //document.getElementById ("vpb_pms_message_data").placeholder = $ ("#write_a_reply").val ();

        vpb_set_total_group_messages (group_id); // Set the total current conversations
        //vpb_start_and_load_more_messages ('start', 'not-applicable'); // Load conversations

        vpb_load_system_users ('new'); // Load system users

        vpb_display_new_users_in_group (); // Display users newly added in a group conversation
        vpb_display_users_in_group (); // Display users in current conversation group

        vpb_exit_compose_new_message (); // Exit compose new message box
    }, 10);

    vpb_show_the_remove_button ();
    vpb_get_all_photos_in_thread ();

    //vpb_min_and_max_chat_box (group_id, 'set'); // Set Min / Max

    // popups.unshift (group_id);
    //vpb_vchat_position_chat_boxes ();


    // Set message for current user to read
    var dataString = {"group_id": group_id, "page": "set_messages_to_read"};
    $.ajax ({
        type: "POST",
        url: '/blab/chat/setMessagesToRead',
        data: dataString,
        beforeSend: function ()
        {},
        success: function (response)
        {
        }
    });
}

// Exit Compose new message
function vpb_exit_compose_new_message ()
{
    $ ("#vpb_add_people_in_group").hide ();
    $ ("#vpb_new_message").hide ();
    $ ("#vpb_default_message").show ();

    $ ("#vpb_new_conversation").hide ();
    $ ("#vpb_default_conversation").show ();

    $ ("#vpb_pms_to_data").val ('');
    vpb_removecookie ('compose_new_message');

    if ($ ("#vpb_selected_user").val () != "")
    {
        //vpb_hight_light_user($("#vpb_selected_user").val());
    }
    else
    {
    }

    $ (".v_added_u_sers").hide ();

    // Allow only the group manager to add more people in group conversation
    vpb_show_the_remove_button ();

}

// Display all newly added users in a group
function vpb_display_new_users_in_group ()
{
    if (vpb_session_is_created ())
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();
        var vpb_group_users_photo = new Array ();

        vpb_group_users_fname = vpb_getcookie ('group_fullname').split (/\,/);
        vpb_group_users_name = vpb_getcookie ('group_username').split (/\,/);
        //vpb_group_users_photo = vpb_getcookie ('group_photo').split (/\,/);

        $ ("#vpb_added_users_box").html ('');

        for (var u = 0; u < vpb_group_users_name.length; u++)
        {
            if (vpb_group_users_name[u] != "")
            {
                var fullname = vpb_group_users_fname[u];
                var username = vpb_group_users_name[u];
                var photo = vpb_group_users_photo[u];

                $ ("#vpb_added_users_box").append ('<span id="vpb_new_user_in_group_' + username + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vpb_remove_new_user_from_group(\'' + fullname + '\', \'' + username + '\', \'' + photo + '\')"></i></span></span>');
            }
            else
            {
            }
        }
        setTimeout (function ()
        {
            $ ("#v_added_u_sers").animate ({scrollTop: parseInt ($ ('#v_added_u_sers').height ()) + 10000}, 1000);
        }, 1000);
    }
    else
    {
    }
}

// Check total messages for the current group
function vpb_set_total_group_messages (group_uid)
{
    var session_username = $ ("#session_uid").val ();
    var group_id = group_uid != "" ? group_uid : vpb_getcookie ('v_group_id') == null ? '' : vpb_getcookie ('v_group_id');

    if (session_username != "" && group_id != "")
    {
        var dataString = {"session_username": session_username, "group_id": group_id, "page": "set_total_group_messages"};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/getGroupMessageCount',
            data: dataString,
            beforeSend: function ()
            {},
            success: function (response)
            {
                if (parseInt (response) > 0)
                {
                    $ ("#vtotal_group_messages").val (parseInt (response));
                    vpb_removecookie ('v_last_loaded_message_id');
                }
                else
                {
                }
            }
        });
    }
    else
    {
    }
}

// Display the photos in the thread
function vpb_get_all_photos_in_thread ()
{
//    if (vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
//    {
//        var dataString = {"group_id": vpb_getcookie ('v_group_id'), "page": "get_all_photos_in_thread"};
//        $.ajax ({
//            type: "POST",
//            url: vpb_site_url + 'pms-processor.php',
//            data: dataString,
//            beforeSend: function ()
//            {},
//            success: function (response)
//            {
//                $ ("#vpb_photos_in_thread").html (response);
//            }
//        });
//    }
//    else
//    {
//    }
}

// Show or Hide the edit conversation name box
function vpb_edit_group_name ()
{
    $ ("#v_actions_box").hide ();
    $ ("#v_group_details_status").html ('');

    if (vpb_getcookie ('group_name') != "" && vpb_getcookie ('group_name') != null && vpb_getcookie ('group_name') != undefined)
    {

        $ ("#vgroup_name").val (vpb_getcookie ('group_name'));
    }
    else
    {
        $ ("#vgroup_name").val ('');
    }

    if (vpb_getcookie ('group_pic') != "" && vpb_getcookie ('group_pic') != null && vpb_getcookie ('group_pic') != undefined)
    {
        $ ("#vpb_display_group_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#conversation_photo_label_text").val () + '\', \'' + vpb_getcookie ('group_pic') + '\');" src="' + vpb_getcookie ('group_pic') + '" title=' + $ ("#conversation_photo_label_text").val () + ' border="0">');
    }
    else
    {
        $ ("#vpb_display_group_photo").html ('');
    }
    $ (".v_load_user_details").fadeToggle ();
}

//Save group details
function vpb_save_group_details ()
{
    var username = "michael.hampton";

    var group_id = vpb_getcookie ('v_group_id') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_id')));
    var group_manager = vpb_getcookie ('v_group_manager') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_manager')));
    var group_username = vpb_getcookie ('group_username') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_username')));
    var group_fullname = vpb_getcookie ('group_fullname') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_fullname')));
    var group_name = vpb_trim (vpb_strip_tags ($ ("#vgroup_name").val ()));

    var ext = $ ('#vgroup_picture').val ().split ('.').pop ().toLowerCase ();
    var vgroup_picture = $ ("#vgroup_picture").val ();

    if (username == "" || group_id == "" || group_manager == "" || group_username == "")
    {
        $ ("#v_group_details_status").html ('');

        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }
    else if (group_name == "")
    {
        $ ("#v_group_details_status").html ('<div class="vwarning">' + $ ("#empty_group_name_text").val () + '</div>');
        return false;
    }
    else if (vgroup_picture != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        document.getElementById ('vgroup_picture').value = '';
        document.getElementById ('vgrouppicture').title = 'No file is chosen';
        $ ("#v_group_details_status").html ($ ("#invalid_file_attachment").val ());
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

        vpb_data.append ("from_username", username);
        vpb_data.append ("group_id", group_id);
        vpb_data.append ("group_manager", group_manager);
        vpb_data.append ("group_username", group_username);
        vpb_data.append ("group_name", group_name);
        vpb_data.append ("page", 'vpb_save_group_details');

        $.ajax ({
            url: '/blab/chat/updateGroupChatName',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#v_group_details_status").html ('<div style="padding:10px;padding-bottom:6px;padding-top:0px;">' + $ ("#v_loading_btn").val () + '</div>');
                $ ("#vgroup_details_box").removeClass ('enabled_this_box');
                $ ("#vgroup_details_box").addClass ('disabled_this_box');
            },
            success: function (response)
            {

                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    $ ("#v_group_details_status").html ('');
                    $ ("#vgroup_details_box").removeClass ('disabled_this_box');
                    $ ("#vgroup_details_box").addClass ('enabled_this_box');

                    $ ("#v-pms-message").html ($ ("#general_system_error").val ());
                    $ ("#v-pms-alert-box").click ();
                    showErrorMessage (objResponse.message);
                }
                else
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
                    var data_brought = response.split ('|');
                    var group_pic = data_brought[1];

                    setTimeout (function ()
                    {
                        vpb_setcookie ('group_name', group_name, 30);
                        if (group_pic != "")
                        {
                            vpb_setcookie ('group_pic', group_pic, 30);
                        }
                        else
                        {
                        }

                        $ ("#vpb_display_group_photo").html ('<img onClick="vpb_view_this_image(\'' + $ ("#conversation_photo_label_text").val () + '\', \'' + group_pic + '\');" src="' + group_pic + '" title=' + $ ("#conversation_photo_label_text").val () + ' border="0">');

                        document.getElementById ('vgroup_picture').value = '';
                        document.getElementById ('vgrouppicture').title = 'No file is chosen';

                        vpb_display_users_in_group ();
                        vpb_start_and_load_more_messages ('group-details-updated', 'not-applicable');
                        vpb_load_system_users ('new');

                        $ ("#v_group_details_status").html ('');
                        $ ("#vgroup_details_box").removeClass ('disabled_this_box');
                        $ ("#vgroup_details_box").addClass ('enabled_this_box');

                        $ (".v_load_user_details").fadeOut ();

                    }, 100);
                }


            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_group_details();", 1000);
        });
    }
}

// Report spam or abuse
function vpb_report_this (fullname, username)
{
    $ ('#reportFullname').val (fullname);
    $ ('#reportUsername').val (username);
    $ ('#report_pm_data').val ('');
    document.getElementById ("report_pm_data").placeholder = $ ("#report_this_desc").val () + '' + fullname;
    $ ('#vpb_display_all_users_in_group_for_report').hide ();
    $ ('#vpb_reason_for_report_box').fadeIn ();
    $ ('#back_label_text').show ();
    $ ('#vsubmit_report').show ();
}

function vpb_cancel_report_this ()
{
    $ ('#report_pm_data').val ('');
    $ ('#report_post_status').html ('');
    $ ('#vpb_reason_for_report_box').hide ();
    $ ('#back_label_text').hide ();
    $ ('#vsubmit_report').hide ();
    $ ('#vpb_display_all_users_in_group_for_report').show ();
}

// Report a conversation
function vpb_report_the_a_user (session_fullname, session_username, session_email)
{
    var group_id = vpb_getcookie ('v_group_id') != null ? vpb_getcookie ('v_group_id') : "";
    var reportFullname = $ ("#reportFullname").val ();
    var reportUsername = $ ("#reportUsername").val ();
    var report_pm_data = vpb_trim ($ ("#report_pm_data").val ());

    if (group_id == "" || group_id == undefined)
    {
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#report_unstarted_message_label").val () + '</div>');
        return false;
    }
    else if (reportFullname == "" || reportUsername == "")
    {
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if (report_pm_data == "")
    {
        $ ("#report_pm_data").focus ();
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#empty_reporting_field_text").val () + '</div>');
        return false;
    }
    else if (report_pm_data.length < 5)
    {
        $ ("#report_pm_data").focus ();
        $ ("#report_post_status").html ('<div class="vwarning">' + $ ("#not_enough_reporting_field_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'group_id': group_id, 'reportFullname': reportFullname, 'reportUsername': reportUsername, 'report_pm_data': report_pm_data, 'page': 'report-a-conversation'};

        //$ ("#report_post_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $.post ('/blab/chat/reportChatUser', dataString, function (response)
        {
            var response_brougght = response.indexOf ('completed_successfuly');
            if (response_brougght != -1)
            {
                $ ("#report_post_status").html ('<div class="vsuccess">' + $ ("#report_send_successfully_message").val () + '</div>');
                $ ("#report_pm_data").val ('');
                setTimeout (function ()
                {
                    vpb_cancel_report_this ();
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
            setTimeout ("vpb_report_the_a_user('" + session_fullname + "', '" + session_username + "', '" + session_email + "');", 1000);
        });
    }
}

// View all photos in thread
function vpb_view_all_photos_in_thread ()
{
    $ ('#v_actions_box').hide ();

    var photo_availability = $ ('#photo_availability').val ();
    photo_availability = 'yes';

    if (photo_availability == "yes")
    {
        var total = $ ('#total_photo_availability').val ();
        var unique_photo_id = $ ('#unique_photo_id').val ();
        var last_counted_photo_number = $ ('#last_counted_photo').val ();

        var last_photo_link = $ ("#hidden_photo_link_" + unique_photo_id + "_" + 1).val ();

        vpb_popup_photo_box (unique_photo_id, parseInt (total), 1, last_photo_link);
    }
    else
    {
        $ ("#v-pms-message").html ($ ("#no_photos_in_thread_text").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }
}

//Photo scroller
function vpb_popup_photo_box (unique_photo_id, total, current, first_photo_link)
{
    $ (".vholder").html ('<img src="' + first_photo_link + '">');
    $ ("#current_status_id").val (parseInt (unique_photo_id));
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

// Load conversations when called
function vpb_start_and_load_more_messages (type, searchTerm)
{
    if (type == "start")
    {
        $ ("#vpb_start").val (0);
    }
    else
    {
    }
    var vpb_start = $ ("#vpb_start").val (); //Where to start loading the updates
    var vpb_total_per_load = $ ("#vpb_total_per_load").val (); //Total messages to load each time
    var session_uid = $ ("#session_uid").val (); //The username of the current logged in user
    var deletion_option_selected = $ ("#vpb_message_deletion_option_selected").val ();

    if (vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
    {
        var group_id = vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_id')));
        var last_message_id = vpb_getcookie ('v_last_loaded_message_id') != null ? vpb_trim (vpb_strip_tags (vpb_getcookie ('v_last_loaded_message_id'))) : "0";

        var dataString = {"session_username": session_uid, "group_id": group_id, "vpb_start": vpb_start, "type": type, "vpb_total_per_load": vpb_total_per_load, "last_message_id": last_message_id, "deletion_option_selected": deletion_option_selected, "searchTerm": searchTerm, "page": "vpb_load_conversations"};

        vpb_running_process = true; //prevent further ajax loading
        $ ("#seen_message_notification").hide ();

        if (type == "start")
        {
            $ ("#vpb_display_group_messages").html ('');
            $ ("#vpb_loading_group_messages").html ($ ("#vpb_loading_image_gif").val ());
        }
        else if (type == "auto-load")
        {
            $ ("#vpb_loading_group_messages").addClass ('vpb_more_messages_loading').html ($ ("#v_loading_btn").val ());
        }
        else if (type == "search-in-conversation")
        {
            $ ("#vpb_loading_searched_messages").html ($ ("#vpb_loading_image_gif").val ());
            $ ("#vasplus_main_pms_wrapers").removeClass ('enable_this_box');
            $ ("#vasplus_main_pms_wrapers").addClass ('disable_this_box');
        }
        else
        {
        }

//        $.post (vpb_site_url + 'pms-processor.php', dataString, function (response)
//        {
//            //Hide the loading image after loading data onto the page
//            $ ("#vpb_loading_group_messages").removeClass ('vpb_more_messages_loading').html ('');
//
//            if (type == "start")
//            {
//                $ ("#vpb_display_group_messages").html (response);
//                setTimeout (function ()
//                {
//                    $ ("#vpb_default_conversation").animate ({scrollTop: parseInt ($ ('#vpb_default_conversation').height ()) + 10000}, 1000);
//                }, 100);
//                $ ("#vpb_start").val (parseInt (vpb_start) + parseInt (vpb_total_per_load));
//            }
//            else if (type == "auto-load")
//            {
//                $ ("#vpb_start").val (parseInt (vpb_start) + parseInt (vpb_total_per_load));
//
//                $ ("#vpb_display_group_messages").prepend (
//                        $ (response).hide ().fadeIn ('slow')
//                        );
//                setTimeout (function ()
//                {
//                    $ ("#vpb_default_conversation").animate ({scrollTop: 100}, 1000);
//                }, 100);
//            }
//            else if (type == "group-details-updated")
//            {
//                $ ("#vpb_display_group_messages").append (response);
//                setTimeout (function ()
//                {
//                    $ ("#vpb_default_conversation").animate ({scrollTop: parseInt ($ ('#vpb_default_conversation').height ()) + 10000}, 1000);
//                }, 100);
//            }
//            else if (type == "search-in-conversation")
//            {
//                $ ("#vpb_loading_searched_messages").html (response);
//                setTimeout (function ()
//                {
//                    $ ("#vpb_default_conversation").animate ({scrollTop: parseInt ($ ('#vpb_default_conversation').height ()) + 10000}, 1000);
//                }, 100);
//            }
//            else
//            {
//                $ ("#vpb_display_group_messages").append (response);
//                setTimeout (function ()
//                {
//                    $ ("#vpb_default_conversation").animate ({scrollTop: parseInt ($ ('#vpb_default_conversation').height ()) + 10000}, 1000);
//                }, 100);
//
//                // Update the message just displayed to read
//                var dataString = {"username": $ ("#session_uid").val (), "group_id": group_id, "last_message_id": last_message_id, "page": "update_last_message_to_read"};
//                $.ajax ({
//                    type: "POST",
//                    url: vpb_site_url + 'pms-processor.php',
//                    data: dataString,
//                    beforeSend: function ()
//                    {},
//                    success: function (response)
//                    {}
//                });
//            }
//
//            vpb_running_process = false;
//
//            $ ("#vasplus_main_pms_wrapers").removeClass ('disable_this_box');
//            $ ("#vasplus_main_pms_wrapers").addClass ('enable_this_box');
//
//        }).fail (function (xhr, ajaxOptions, theError)
//        {
//            $ ("#vpb_loading_group_messages").removeClass ('vpb_more_messages_loading').html ('');
//            $ ("#v-pms-message").html ($ ("#general_system_error").val ());
//            $ ("#v-pms-alert-box").click ();
//            vpb_running_process = false;
//            //return false;
//        });
    }
}

var vpb_min_max_box = new Array ();

// Set cookie
function vchat_setcookie (name, value, days)
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

// Get cookie
function vchat_getcookie (name)
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
//Delete cookie
function vchat_removecookie (name)
{
    vchat_setcookie (name, "", -1);
}

//Chat box min/max
function vpb_min_and_max_chat_box (group_id, action)
{
    $ ('#vpb_vchat_smiley_box_' + group_id).hide ();
    if (action == "set")
    {
        if (vchat_getcookie ('vpb_chat_min_or_max_status') != "" && vchat_getcookie ('vpb_chat_min_or_max_status') != null && vchat_getcookie ('vpb_chat_min_or_max_status') != undefined)
        {
            if (vpb_vchat_isInArray (vchat_getcookie ('vpb_chat_min_or_max_status'), group_id))
            {

                $ ('#vpb_min_chat_' + group_id).hide ();
                $ ('#vpb_max_chat_' + group_id).show ();

                $ ('#vpb_chat_bx_inner_' + group_id).hide ();
            }
            else
            {
            }
        }
        else
        {
        }
    }
    else if (action == "minimize")
    {
        var v_data_found = 0;
        if (vchat_getcookie ('vpb_chat_min_or_max_status') != "" && vchat_getcookie ('vpb_chat_min_or_max_status') != null && vchat_getcookie ('vpb_chat_min_or_max_status') != undefined)
        {
            vpb_min_max_box = vchat_getcookie ('vpb_chat_min_or_max_status').split (/\,/);
            vpb_min_max_box[vpb_min_max_box.length] = group_id;
            if (vpb_min_max_box == "" || vpb_min_max_box == null || vpb_min_max_box == undefined)
            {
            }
            else
            {
                vchat_setcookie ('vpb_chat_min_or_max_status', vpb_min_max_box, 365);
            }
        }
        else
        {

            if (vpb_min_max_box == "" || vpb_min_max_box == null || vpb_min_max_box == undefined)
            {
                vpb_min_max_box[vpb_min_max_box.length] = group_id;
                if (vpb_min_max_box == "" || vpb_min_max_box == null || vpb_min_max_box == undefined)
                {
                }
                else
                {
                    vchat_setcookie ('vpb_chat_min_or_max_status', vpb_min_max_box, 365);
                }
            }
            else
            {

                for (j = 0; j < vpb_min_max_box.length; j++) {
                    if (vpb_min_max_box[j] == group_id)
                    {
                        v_data_found = 1;
                    }
                }

                if (v_data_found == 1)
                {
                }
                else
                {
                    vpb_min_max_box[vpb_min_max_box.length] = group_id;
                    if (vpb_min_max_box == "" || vpb_min_max_box == null || vpb_min_max_box == undefined)
                    {
                    }
                    else
                    {
                        vchat_setcookie ('vpb_chat_min_or_max_status', vpb_min_max_box, 365);
                    }
                }
            }
        }

        $ ('#vpb_min_chat_' + group_id).hide ();
        $ ('#vpb_max_chat_' + group_id).show ();
        $ ('#vpb_chat_bx_inner_' + group_id).slideToggle ();
    }
    else if (action == "maximize")
    {
        vpb_min_max_box = vchat_getcookie ('vpb_chat_min_or_max_status').split (/\,/);
        vpb_min_max_box.splice (vpb_min_max_box.indexOf (group_id), 1);
        vchat_setcookie ('vpb_chat_min_or_max_status', vpb_min_max_box, 365);

        $ ('#vpb_max_chat_' + group_id).hide ();
        $ ('#vpb_min_chat_' + group_id).show ();

        $ ('#vpb_chat_bx_inner_' + group_id).slideToggle ();
        $ ("#vpb_vchat_text" + group_id).focus ();
        //$("#vpb_message_box"+group_id).scrollTop($("#vpb_message_box"+group_id)[0].scrollHeight);

        setTimeout (function ()
        {
            $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);
        }, 100);

        vpb_vchat_header_notification ("#vchat_header_" + group_id, "stop");
    }
}

// Display the add more people to a group or leave a conversation button
function vpb_show_the_remove_button ()
{
    var session_username = "michael.hampton";

    if (session_username != "" && vpb_getcookie ('v_group_manager') != "" && vpb_getcookie ('v_group_manager') != null && vpb_getcookie ('v_group_manager') != undefined)
    {
        if (vpb_getcookie ('v_group_manager') == session_username)
        {
            $ ("#vpb_add_more_people_button").data ('original-title', $ ("#add_more_people_in_group_text").val ());
            $ ("#vpb_add_more_people_button").attr ('data-original-title', $ ("#add_more_people_in_group_text").val ());
            $ ("#vpb_add_more_people_button").attr ('title', $ ("#add_more_people_in_group_text").val ());

            $ ("#vpb_add_more_people_button").html ('<div style="display:inline-block;padding-left:0px; vertical-align:middle;"><i class="fa fa-plus-square add_more_people_hover" onClick="vpb_add_more_poeple_in_group();"></i></div>');

            $ ("#vpb_edit_group_name_button").html ('<div style="display:inline-block;padding-left:10px; vertical-align:middle;"><i class="fa fa-pencil-square add_more_people_hover" onClick="vpb_edit_group_name();"></i></div>');

            $ ("#vpb_add_people_in_group_link").show ();
            $ ("#vpb_edit_group_name_link").show ();
            $ ("#vpb_edit_group_photo_link").show ();

            $ ("#vpb_leave_the_group_link").show ();
        }
        else
        {
            if (vpb_session_is_created () && vpb_getcookie ('v_group_id') != "" && vpb_getcookie ('v_group_id') != null && vpb_getcookie ('v_group_id') != undefined)
            {
                $ ("#vpb_add_more_people_button").data ('original-title', $ ("#leave_the_group_text").val ());
                $ ("#vpb_add_more_people_button").attr ('data-original-title', $ ("#leave_the_group_text").val ());
                $ ("#vpb_add_more_people_button").attr ('title', $ ("#leave_the_group_text").val ());

                $ ("#vpb_add_more_people_button").html ('<div style="display:inline-block;padding-left:0px; vertical-align:middle;"><i class="fa fa-times add_more_people_hover" onClick="vpb_confirm_remove_self_from_group(\'' + session_username + '\');"></i></div>');

                $ ("#vpb_edit_group_name_button").html ('');

                $ ("#vpb_add_people_in_group_link").hide ();
                $ ("#vpb_edit_group_name_link").hide ();
                $ ("#vpb_edit_group_photo_link").hide ();

                $ ("#vpb_leave_the_group_link").show ();
            }
            else
            {
                $ ("#vpb_add_more_people_button").html ('');
                $ ("#vpb_edit_group_name_button").html ('');

                $ ("#vpb_add_people_in_group_link").hide ();
                $ ("#vpb_edit_group_name_link").hide ();
                $ ("#vpb_edit_group_photo_link").hide ();
                $ ("#vpb_leave_the_group_link").hide ();
            }
        }
    }
    else
    {
        $ ("#vpb_add_more_people_button").html ('');
        $ ("#vpb_edit_group_name_button").html ('');

        $ ("#vpb_add_people_in_group_link").hide ();
        $ ("#vpb_edit_group_name_link").hide ();
        $ ("#vpb_edit_group_photo_link").hide ();
        $ ("#vpb_leave_the_group_link").hide ();
    }
}

// Remove a user from a group conversation
function vpb_remove_new_user_from_group (fullname, username, photo)
{
    if (vpb_session_is_created ())
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();
        var vpb_group_users_photo = new Array ();
        vpb_group_users_fname = vpb_getcookie ('group_fullname').split (/\,/);
        vpb_group_users_name = vpb_getcookie ('group_username').split (/\,/);
        vpb_group_users_photo = vpb_getcookie ('group_photo').split (/\,/);
        vpb_setcookie ('group_fullname', vpb_remove_data (vpb_group_users_fname, fullname), 30);
        vpb_setcookie ('group_username', vpb_remove_data (vpb_group_users_name, username), 30);
        vpb_setcookie ('group_photo', vpb_remove_data (vpb_group_users_photo, photo), 30);
        $ ("#vpb_new_user_in_group_" + username).fadeOut ();
        setTimeout (function ()
        {
            $ ("#vpb_new_user_in_group_" + username).remove ();
        }, 50);
        vpb_display_users_in_group ();
        var group_id = vpb_getcookie ('v_group_id') != null ? vpb_getcookie ('v_group_id') : "";
        if (group_id != "" && group_id != undefined)
        {
            var dataString = {"username": username, "group_id": group_id, "page": "vpb_remove_user_from_group"};
            $.ajax ({
                type: "POST",
                url: '/blab/chat/removeUserFromGroupChat',
                data: dataString,
                beforeSend: function ()
                {},
                success: function (response)
                {
                    vpb_load_system_users ('new');
                }
            });
        }
        else
        {
        }
    }
    else
    {
        $ ("#v-pms-message").html ($ ("#general_system_error").val ());
        $ ("#v-pms-alert-box").click ();
        return false;
    }
}


//Show/Hide Smiley Box
function vpb_pms_smiley_box ()
{
    var vpb_box_state = $ ("#vpb_the_pms_smiley_box").css ('display');
    if (vpb_box_state == 'block')
    {
        $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
        $ ("#add_smile_button").addClass ('vfooter_wraper');
        $ ("#vpb_the_pms_smiley_box").slideUp ();
    }
    else
    {
        $ ("#add_smile_button").removeClass ('vfooter_wraper');
        $ ("#add_smile_button").addClass ('vfooter_wraper_active');
        $ ("#vpb_the_pms_smiley_box").slideDown ();
    }
}

function scrollToBottom ()
{
    if ($ (".over-body").length > 0)
    {
        scrollHeight = $ (".over-body")[0].scrollHeight || 0;
        $ (".over-body").animate ({
            scrollTop: scrollHeight
        }, 0);
    }

}

//Add smiley to pms box when clicked on
function vpb_add_smiley_to_pms_status (smiley)
{
    var old_pms_message = $ ("#chatMsg").val ();
    if (old_pms_message == "")
    {
        $ ("#chatMsg").focus ();
        $ ("#chatMsg").val (smiley);
    }
    else
    {
        $ ("#chatMsg").focus ();
        $ ("#chatMsg").val (old_pms_message + ' ' + smiley);
    }

    $ ("#add_smile_button").removeClass ('vfooter_wraper_active');
    $ ("#add_smile_button").addClass ('vfooter_wraper');
    $ ("#vpb_the_pms_smiley_box").hide ();
}


/**
 * Add a new chat message
 * 
 * @param {string} message
 */
function send_message (message, userId, type, filename)
{

    filename = filename || "";
    message = message || $ ("#chatMsg").val ();
    type = type || "text";
    var group_id = vpb_getcookie ('v_group_id') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_id')));
    var group_manager = vpb_getcookie ('v_group_manager') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('v_group_manager')));
    var group_username = vpb_getcookie ('group_username') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_username')));
    var group_fullname = vpb_getcookie ('group_fullname') == null ? '' : vpb_trim (vpb_strip_tags (vpb_getcookie ('group_fullname')));
    var message = $.trim (message);
    var url_in_message = vpb_get_url_in_message (message);
    var ext = $ ('#add_photos').val ().split ('.').pop ().toLowerCase ();
    var file_ext = $ ('#add_files').val ().split ('.').pop ().toLowerCase ();
    var photos = $ ("#add_photos").val ();
    var files = $ ("#add_files").val ();

    if (message == "" &&
            files == "" &&
            photos == "" &&
            $ ("#added_video_url").val () == ""
            || message == "Write a reply..." &&
            files == "" &&
            photos == "" &&
            $ ("#added_video_url").val () == "")
    {
//$("#v-pms-message").html($("#empty_new_message_field").val());
        showErrorMessage ();
        return false;
    }
    else if (photos != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        $ ("#vpb_added_photos").fadeOut ();
        $ ("#v-pms-message").html ($ ("#invalid_file_attachment").val ());
        document.getElementById ('add_photos').value = '';
        document.getElementById ('browsedPhotos').title = 'No file is chosen';
        showErrorMessage ('Invalid file type');
        return false;
    }
    else if (files != "" && $.inArray (file_ext, ["pdf", "doc", "docx", "txt", "zip"]) == -1)
    {
        $ ("#v-pms-message").html ($ ("#wrong_files_added_text").val ());
        document.getElementById ('add_files').value = '';
        showErrorMessage ('Invalid file type');
        return false;
    }
    else
    {
        if (message == "")
            if (photos != "")
            {
                message = "Photo";
            }
            else if (files != "")
            {
                message = "File";
            }
            else if ($ ("#added_video_url").val () != "")
            {
                message = "Video";
            }
            else
            {
            }


        $.ajax ({
            url: '/blab/chat/addMessage',
            method: 'post',
            data: {msg: message, userId: userId, filename: filename, type: type, "url_in_message": url_in_message, "group_id": group_id, "group_manager": group_manager, "group_username": group_username, "group_fullname": group_fullname},
            success: function (data)
            {
                vpb_add_files_to_message (parseInt (data), null);
                $ ('#chatMsg').val ('');
                get_messages ();
                $ ("#vpb_submitBox").prop ("disabled", true);
            }
        });
    }
}


/**
 * Get's the chat messages.
 */
function get_messages ()
{
    if (vpb_getcookie ("compose_new_message") == "yes" && vpb_getcookie ('v_group_id') === "")
    {
        return false;
    }

    if (vpb_getcookie ('v_group_id') !== "")
    {
        var strUrl = '/blab/chat/getMessage/' + vpb_getcookie ('v_group_id');
    }
    else
    {
        var userId = $ (".selectedChat").attr ("user-id");

        var strUrl = '/blab/chat/getMessage/' + null + '/' + userId;
    }

    $.ajax ({
        url: strUrl,
        method: 'GET',
        success: function (data)
        {
            $ ('.over-body').html (data);
            scrollToBottom ();
        }
    });
}

/**
 * Initializes the chat application
 */
function boot_chat ()
{
    var chatArea = $ ('#chatMsg');

    // Load the messages every 5 seconds
    setInterval (get_messages, 20000);
    // Bind the keyboard event
    $ ('textarea#chatMsg').on ("keydown", function (vpb_event)
    {

        if ($ (this).val ().length > 0)
        {
            $ ("#vpb_submitBox").prop ("disabled", false);
        }
        else
        {
            $ ("#vpb_submitBox").prop ("disabled", true);
        }

        var vpb_keycode = (vpb_event.keyCode ? vpb_event.keyCode : vpb_event.which);

        if (vpb_event.keyCode == 13 && vpb_event.shiftKey == 0)
        {
            if (vpb_getcookie ('press_enter_to_submit') && vpb_session_is_created () || $ ('input#vpb_press_enter_for_submit').is (':checked') && vpb_session_is_created ())
            {
                if (vpb_trim ($ ("textarea#chatMsg").val ()) == "" || $ ("textarea#chatMsg").val () == "Write a reply..." || $ ("textarea#chatMsg").val () == "Write your message...")
                {
                    $ ("textarea#chatMsg").val ("").animate ({
                        "min-height": "40px"
                    }, "fast");
                    vpb_event.preventDefault ();
                    return false;
                }
                else
                {
                    var message = $ ("textarea#chatMsg").val ();
                    var userId = $ (this).attr ("user-id");
                    send_message (message, userId);
                }
            }
            else
            {
                //Enable the enter key and do not submit for on enter key press 
                var message = $ ("textarea#chatMsg").val ();
                var userId = $ (this).attr ("user-id");
                send_message (message, userId);
            }
        }
        else
        {
            //Do nothing man
        }
    });
}

// Initialize the chat
boot_chat ();
$ ("#photo").on ("click", function ()
{
    $ (".chatWindow #photoFile").click ();
});

$ ("#photoFile").change (function ()
{
    if (typeof $ ("#photoFile")[0].files[0] != "undefined")
    {
        file = $ (".chatWindow #photoFile")[0].files[0];
        fd = new FormData ();
        fd.append ('file', file);
        $.ajax ({
            xhr: function ()
            {
                var xhr = new window.XMLHttpRequest ();
                xhr.upload.addEventListener ("progress", function (evt)
                {
                    if (evt.lengthComputable)
                    {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt (percentComplete * 100);
                        console.log (percentComplete);
                        $ ("#msgForm").css ({background: "linear-gradient(90deg, #009bcd " + percentComplete + "%, white 0%)"});
                    }
                }, false);
                return xhr;
            },
            url: "/blab/chat/chatUpload",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function (result)
            {
                $ ("#msgForm").css ({background: ""});
                val = $.trim ($ ("#chatMsg").val ()) !== "" ? $ ("#chatMsg").val () : "File uploaded";
                if (result != "")
                {
                    var userId = $ ("#chatMsg").attr ("user-id");

                    send_message (val, userId, 'img', result);
                    $ ("#chatMsg").val ('');
                }
            }
        });
        return false;
    }
});
$ ('#voice').on ("click", function ()
{
    $that = $ (this);
    if ($that.hasClass ("active"))
    {
        Fr.voice.export (function (blob)
        {
            val = $.trim ($ ("#chatMsg").val ()) !== "" ? $ ("#chatMsg").val () : 'Voice message uploaded';


            var fd = new FormData ();
            fd.append ('file', blob);
            $.ajax ({
                xhr: function ()
                {
                    var xhr = new window.XMLHttpRequest ();
                    xhr.upload.addEventListener ("progress", function (evt)
                    {
                        if (evt.lengthComputable)
                        {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt (percentComplete * 100);
                            $ ("#msgForm").css ({background: "linear-gradient(90deg, #009bcd " + percentComplete + "%, white 0%)"});
                        }
                    }, false);
                    return xhr;
                },
                url: "/blab/chat/chatUpload",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function (result)
                {
                    $ ("#msgForm").css ({background: ""});
                    val = $.trim ($ ("#chatMsg").val ()) !== "" ? $ ("#chatMsg").val () : 'Voice message uploaded';
                    var userId = $ ("#chatMsg").attr ("user-id");
                    send_message (val, userId, 'audio', result);
                    $ ("#chatMsg").val ('');
                }
            });
        }, "blob");
        Fr.voice.stop ();
        $that.removeClass ("active");
        $ (".recording").hide ();
    }
    else
    {
        Fr.voice.record (false, function ()
        {
            $ (".recording").show ();
            $ ('#voice').addClass ("active");
        });
    }
});
$ ("#fullscreen").on ("click", function ()
{
    if ($ (".panel-chat").hasClass ("fullscreen-window"))
    {
        $ (".panel-chat").css ("min-width", 200);
        $ (".panel-chat").removeClass ("fullscreen-window");
    }
    else
    {
        $ (".panel-chat").css ("min-width", 0);
        $ (".panel-chat").addClass ("fullscreen-window");
    }
});
$ (document).off ("click", ".deleteChatMessage");
$ (document).on ('click', '.deleteChatMessage', function ()
{
    var id = $ (this).attr ("deleteid");
    swal ({
        title: "Are you sure?",
        text: "You will not be able to recover this message!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function ()
    {
        $.ajax ({
            url: '/blab/chat/deleteChatMessage',
            method: 'POST',
            data: {id: id},
            success: function (response)
            {
                try {

                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 1)
                    {
                        swal ("Deleted!", "The message has been deleted.", "success");
                        get_messages ();
                    }
                    else
                    {
                        showErrorMessage ('We were unable to delete the chat message');
                    }

                } catch (error) {
                    showErrorMessage ();
                }
            }
        });
    });
    return false;
});
$ ("#searchMessagesBox").keyup (function ()
{

    // Retrieve the input field text and reset the count to zero
    var filter = $ (this).val (), count = 0;
    // Loop through the comment list
    $ (".chatWrappera").each (function ()
    {

        // If the list item does not contain the text phrase fade it out
        if ($ (this).text ().search (new RegExp (filter, "i")) < 0)
        {
            $ (this).fadeOut ();
            // Show the list item if the phrase matches and increase the count by 1
        }
        else
        {

            $ (this).css ("background-color", "#fffbd2");
            $ (this).show ();
            count++;
        }
    });
    // Update the count
    var numberItems = count;
    $ ("#filter-count").text ("Number of Comments = " + count);
});