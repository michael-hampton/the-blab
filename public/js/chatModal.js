var vpb_session_photo;
var vpb_session_fname;
var vpb_smiley_dir;
var vpb_site_url = $ ("#vchat_website_link").val () != undefined ? $ ("#vchat_website_link").val () : '';
var vx_run;
var vpb_original_title_message = document.title;

function vchat_run_system_updates ()
{
    alert ("Mike");

    if ($ ("#vchat_is_disabled").val () == 1)
    {
        if ($ ("#vchat_has_disabled").val () == 1)
        {
            vchat_removecookie ('compose_new_message');
            vchat_removecookie ('group_uid');

            $ ("#vpb_chat_main_box").hide ();
            $ (".vpb_vchat_box").hide ();
            $ ("#vpb_vchat_hidden_boxes").hide ();
        }
        else
        {
        }
        window.clearInterval (vx_run);
        vx_run = 0;
        return false;
    }
    else
    {
        // Place functions here
        vpb_vchat_check_updates ();
        vchat_check_for_seen_message_notification ('');

        //Load and display friends box on page load
        if ($ ("#vchat_loading_type").val () == "previous")
        {
        }
        else
        {
            vpb_load_vchat_friends_box ('checking');
        }

        //Count friends online
        vchat_count_friends_online ();

        // Auto set users to Online/Offline
        vchat_update_user_status ();

        // Place functions here
        vpb_check_friends_requests ();
        vpb_check_notifications ();

        vpb_check_a_new_messages ();

        total_pushed++;
        if (vx_run)
            window.clearInterval (vx_run);

        //$("#chat_texting").html('Total Pushed: '+heartbeat++ +' = Interval: '+vx_interval+' = vx_run: '+vx_run);

        if (parseInt (total_pushed) > 20)
        {
            vx_interval = parseInt (vx_interval) + 10000;
            if (parseInt (total_pushed) > 50)
            {
                window.clearInterval (vx_run);
                vx_run = 0;
                vx_interval = 10000;
                total_pushed = 1;
                heartbeat = 1;
            }
        }
        else
        {
            //vx_interval = parseInt(vx_interval);
        }
        vx_run = window.setInterval (vchat_run_system_updates, vx_interval); // start the setInterval()
    }
}


//creates markup for a new popup. Adds the id to popups array.
function vpb_open_new_vchat_box (username)
{
    if ($ ("#vchat_is_disabled").val () == 1)
        return false;

    if (vchat_getcookie ('group_uid') != "" && vchat_getcookie ('group_uid') != null || vchat_getcookie ('group_uid') != undefined)
    {
        var group_uid = new Array ();
        group_uid = vchat_getcookie ('group_uid').split (/\,/);
        var last_element = group_uid[group_uid.length - 1];
    }
    else
    {
        var group_uid = new Array ();
        var last_element = "";
    }

    if (vchat_getcookie ('compose_new_message') == "yes" && $ ("#" + last_element).length == 1)
    {
        if (last_element == "")
        {
        }
        else
        {
            $ ("#vpb_vchat_to_data" + last_element).focus ();
        }
        return false;
    }
    else if (last_element != "" && vchat_getcookie ('group_username' + last_element) == "" && $ ("#" + last_element).length == 1)
    {
        $ ("#vpb_vchat_to_data" + last_element).focus ();
        return false;
    }
    else
    {
        var group_id = vchat_gen_random ();
        var chat_box_label = document.getElementById ('compose_new_message_text').value;

        if (vchat_getcookie ('group_uid') != "" && vchat_getcookie ('group_uid') != null && vchat_getcookie ('group_uid') != undefined && vpb_vchat_isInArray (vchat_getcookie ('group_uid'), group_id))
        {
            alert ($ ("#vchat_general_systems_error").val ());
        }
        else
        {
            for (var iii = 0; iii < popups.length; iii++)
            {
                //already registered. Bring it to front.
                if (group_id == popups[iii])
                {
                    Array.remove (popups, iii);

                    popups.unshift (group_id);

                    vpb_vchat_position_chat_boxes ();
                    return;
                }
            }
            // Display the chat html contents
            vpb_display_html_contents (group_id, chat_box_label, username);

            popups.unshift (group_id);
            vpb_vchat_position_chat_boxes ();
            vpb_add_more_friends_to_chat (group_id);

            $ ("#vchat_group_sid").val (group_id);

            $ ("#vpb_vchat_to_data" + group_id).focus ();

//            var group_uid = new Array ();
//
//            if (vchat_getcookie ('group_uid') != "" && vchat_getcookie ('group_uid') != null && vchat_getcookie ('group_uid') != undefined)
//            {
//                group_uid = vchat_getcookie ('group_uid').split (/\,/);
//                group_uid[group_uid.length] = group_id;
//            }
//            else
//            {
//                group_uid[group_uid.length] = group_id;
//            }
//            vchat_setcookie ('group_uid', group_uid, 30);
//
//            vchat_setcookie ('compose_new_message', 'yes', 30);
//            vchat_setcookie ('v_group_manager' + group_id, document.getElementById ('vchat_sessions_uid').value, 30);
        }
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

// Generate Communication data
function vchat_gen_random ()
{
    //return Math.random().toString().split("0.").join("1").toString().split(".").join("");
    return Math.floor ((Math.random () * 4294967295) + 1);
}

//this variable represents the total number of popups can be displayed according to the viewport width
var total_popups = 0;

//arrays of popups ids
var popups = [];


// Chat HTML Contents
function vpb_display_html_contents (group_id, chat_box_label, username)
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }

    username = username || "";

    var add_more_people = '';

    if (vchat_getcookie ('v_group_manager' + group_id) != "" && vchat_getcookie ('v_group_manager' + group_id) != null && vchat_getcookie ('v_group_manager' + group_id) != undefined)
    {
        if (vchat_getcookie ('v_group_manager' + group_id) == $ ("#vchat_sessions_uid").val ())
        {
            var add_more_people = '<span id="add_more_friends_to_chat' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons vhideThis"  onclick="vpb_add_more_friends_to_chat(\'' + group_id + '\');" original-title="' + $ ("#add_more_friends_to_chat").val () + '"><i class="fa fa-user-plus vfooter_icon"></i></span>';
        }
        else
        {
            var add_more_people = '';

        }
    }
    else
    {
        var add_more_people = '<span id="add_more_friends_to_chat' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons vhideThis"  onclick="vpb_add_more_friends_to_chat(\'' + group_id + '\');" original-title="' + $ ("#add_more_friends_to_chat").val () + '"><i class="fa fa-user-plus vfooter_icon"></i></span>';
    }

    var element = '<div class="vpb_vchat_box" username="' + username + '" id="' + group_id + '" onclick="vpb_vchat_page_title(\'' + vpb_original_title_message + '\',\'stop\');">';

    element = element + '<div class="vpb_vchat_smiley_box_wrapper" id="vpb_vchat_smiley_box_' + group_id + '"><div class="vpb_smiley_header_box"><down class="vasplus_tooltip_icons" onclick="vpb_vchat_add_smiley_box(\'' + group_id + '\');" original-title="' + $ ("#vchat_close_tab").val () + '">x</down><div style="clear:both;"></div></div><div class="vpb_smiley_inner_box"><span class="vc_smiley_a" title="Smile" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':)\');"></span><span class="vc_smiley_b" title="Frown, Sad" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':(\');"></span><span class="vc_smiley_c" title="Blushing angel" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':blushing-angel:\');"></span><span class="vc_smiley_d" title="Cat face" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':cat-face:\');"></span><span class="vc_smiley_e" title="Confused" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'o.O\');"></span><span class="vc_smiley_f" title="Cry" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':cry:\');"></span><span class="vc_smiley_g" title="Laughing devil" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':laughing-devil:\');"></span><span class="vc_smiley_h" title="Shocked and surprised" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':O\');"></span><span class="vc_smiley_i" title="Glasses" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'B)\');"></span><span class="vc_smiley_j" title="Grin, Big Smile" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':D\');"></span><span class="vc_smiley_k" title="Upset and angry" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':grumpy:\');"></span><span class="vc_smiley_l" title="Heart" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':heart:\');"></span><span class="vc_smiley_m" title="Kekeke happy" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'^_^\');"></span><span class="vc_smiley_n" title="Kiss" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':kiss:\');"></span><span class="vc_smiley_o" title="Pacman" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':v\');"></span><span class="vc_smiley_p" title="Penguin" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':penguin:\');"></span><span class="vc_smiley_q" title="Unsure" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':unsure:\');"></span><span class="vc_smiley_r" title="Cool" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'B|\');"></span><span class="vc_smiley_s" title="Annoyed, sighing or bored" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'-_-\');"></span><span class="vc_smiley_t" title="Love" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':lve:\');"></span><span class="vc_smiley_u" title="Christopher Putnam" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':putnam:\');"></span><span class="vc_smiley_zb" title="Shark" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'(^^^)\');"></span><span class="vc_smiley_v" title="Wink" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \';-)\');"></span><span class="vc_smiley_w" title="No idea" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'(off)\');"></span><span class="vc_smiley_x" title="Got an idea" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'(on)\');"></span><span class="vc_smiley_y" title="Cup of tea" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \':tea-cup:\');"></span><span class="vc_smiley_z" title="No, thumb down" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'(n)\');"></span><span class="vc_smiley_za" title="Yes, thumb up" onclick="vpb_vchat_add_chat_smiley_a(\'' + group_id + '\', \'(y)\');"></span><div style="clear:both;"></div></div></div>';

    element = element + '<input type="hidden" id="vchat_who_sent_last_' + group_id + '" value="">';

    element = element + '<ul id="v_vchat_users_in_list' + group_id + '" class="dropdown-menu pull_pms pull-top pull-center vasplus_bosy vchat_suggested_to_u_box vpb_user_in_lists" aria-labelledby="v_vchat_users_in_list' + group_id + '" onmouseover="vchat_get_user_onmouseover_data(\'' + group_id + '\');" onmouseout="vchat_get_user_mouseout_data(\'' + group_id + '\');">\
	<li class="v_suggested_to_u_inner_box">\
	<div id="vpb_display_all_users_in_group' + group_id + '"></div>\
	</li>\
	<div style="clear:both;"></div>\
	</ul>';

    element = element + '<ul id="v_vchat_users_box' + group_id + '" class="dropdown-menu pull_pms pull-top pull-center vasplus_bosy vchat_suggested_to_u_box" aria-labelledby="vpb_vchat_to_data' + group_id + '"><li class="v_suggested_to_u_inner_box"><div id="vpb_vchat_users_displayer' + group_id + '"></div></li></ul>';


    element = element + '<div id="vchat_header_' + group_id + '" class="vheader vpb_chat_box_header_a">';
    element = element + '<div id="vpb-current-group-name' + group_id + '" class="vheader-left">' + chat_box_label + '</div>';

    element = element + '<div class="vheader-right"> 		\
	<a class="vpb_close_button vasplus_tooltip_icons" onclick="javascript:return vpb_vchat_close_chat_boxes(\'' + group_id + '\');" original-title="' + $ ("#vchat_close_tab").val () + '"></a> \
	\
	 <a class="vpb_min_button vasplus_tooltip_icons" id="vpb_min_chat_' + group_id + '" onclick="javascript:return vpb_min_and_max_chat_box(\'' + group_id + '\', \'minimize\');" original-title="' + $ ("#vchat_minimize").val () + '"></a>	\
	<a class="vpb_max_button vasplus_tooltip_icons" id="vpb_max_chat_' + group_id + '" onclick="javascript:return vpb_min_and_max_chat_box(\'' + group_id + '\', \'maximize\');" original-title="' + $ ("#vchat_maximize").val () + '"></a>	 \
	<input type="file" id="add_files' + group_id + '" onChange="document.getElementById(\'add_vchat_photos\').value=\'\';vchat_send_files(this, \'' + $ ("#browsed_photos_information").val () + '\', \'' + $ ("#photo_prev_information").val () + '\');" style="display:none;" multiple />\
	</div>';

    element = element + '<div style="clear: both"></div></div>';

    element = element + '<div class="v_chat_bx_inner" id="vpb_chat_bx_inner_' + group_id + '">';

    element = element + '<div class="vpb_vchat_sending_file_box" id="vchat_sending_file_' + group_id + '"><div id="vchat_display_sending_file_' + group_id + '"></div></div>';

    element = element + '<div class="vpb_vchat_done_sending_file_box" id="vchat_done_sending_file_' + group_id + '">';

    element = element + '<div id="vpb_vchat_top' + group_id + '" class="vpb_vchat_top">\
	<div id="v_added_u_sers' + group_id + '" class="v_added_u_sers">\
	\
	<div class="input-group vtop_background">\
	  \
	<span class="input-group-addon compose_to_text_outer"><div class="compose_to_text">To:</div></span>\
	\
	<span class="vchat_added_u_sers_span">\
	<span id="vpb_added_users_box' + group_id + '" class="vpb-added-users-wraper"></span>\
	\
	  <textarea id="vpb_vchat_to_data' + group_id + '" onkeyup="vpb_vchat_search_for_friends(this,\'' + group_id + '\');" class="vchat-input" placeholder="Name" autocomplete="off" onclick="vpb_show_vchat_searched_user(\'' + group_id + '\');"></textarea>\
	</span>\
	</div>\
	<div style="clear:both;"></div>\
                \
	</div>\
	</div>';


    element = element + '<div id="vpb_vchat_bottom' + group_id + '" class="vpb_vchat_bottom" onclick="vpb_vchat_header_notification(\'#vchat_header_' + group_id + '\',\'stop\');">\
	\
	<div id="vpb_chat_content_box_' + group_id + '" class="vpb_chat_content_box">\
	<ul>\
		<input type="hidden" id="vpb_is_process_running' + group_id + '" value="0">\
		<input type="hidden" id="vtotal_group_messages' + group_id + '" value="0">\
		<input type="hidden" id="last_message_id' + group_id + '" value="0">\
		<input type="hidden" id="vpb_start' + group_id + '" value="0">\
		<div id="vpb_loading_group_messages' + group_id + '"></div>\
		<div id="vpb_display_conversations' + group_id + '"></div>\
	</ul>\
	<div style="clear:both;"></div>\
	 \
	</div>\
	<div class="seen_message_notification_box" id="seen_message_notification_box' + group_id + '">\
     <div class="seen_message_notification" id="seen_message_notification' + group_id + '"></div>\
     </div>\
	</div>';

    element = element + '<div class="vpb_vchat_bottom_box_outer" id="vpb_vchat_bottom_html' + group_id + '"><div class="vpb_vchat_bottom_box_inner">\
		  <textarea class="vchat-form-control vpb-textarea vchat_text_box" placeholder="' + $ ("#write_a_messages_text").val () + '" onkeyup="vpb_vchat_textarea_expansion(this, ' + group_id + ');" onkeydown="vchat_send_message(event, ' + group_id + ');" onclick="vpb_show_conversation_box(' + group_id + ');vpb_vchat_header_notification(\'#vchat_header_' + group_id + '\',\'stop\');vpb_vchat_page_title(\'' + vpb_original_title_message + '\',\'stop\');" id="vpb_vchat_text' + group_id + '"></textarea> \
		  \
		  \
		  <div class="vchat-footer">\
\
			<span id="add_a_file_button' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons"  onclick="document.getElementById(\'current_group_id\').value=\'' + group_id + '\';document.getElementById(\'add_files' + group_id + '\').click();" original-title="' + $ ("#add_a_file_text").val () + '"><i class="fa fa-link vfooter_icon"></i></span>\
			\
			<span id="add_a_photo_button' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons"  onclick="document.getElementById(\'current_group_id\').value=\'' + group_id + '\';document.getElementById(\'vchat-add-photos-box\').click();" original-title="' + $ ("#add_a_photo_text").val () + '"><i class="fa fa-camera vfooter_icon"></i></span>\
			\
			<span id="add_a_video_text' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons"  onclick="document.getElementById(\'current_group_id\').value=\'' + group_id + '\';document.getElementById(\'vchat-add-videos-box\').click();document.getElementById(\'vchat_video_action\').value=\'popup\';" original-title="' + $ ("#add_a_video_text").val () + '"><i class="fa fa-youtube-play vfooter_icon"></i></span>\
			\
			<span id="add_smile_button' + group_id + '" class="vfooter_wraper vasplus_tooltip_icons"  onclick="vpb_vchat_add_smiley_box(\'' + group_id + '\');" original-title="' + $ ("#add_a_smiley_text").val () + '"><i class="fa fa-smile-o vfooter_icon"></i></span>\
			\
			<span id="see_full_conversation_button' + group_id + '" style="display:none;" class="vfooter_wraper vasplus_tooltip_icons"  onclick="window.location.href=\'/blab/chat/messenger/' + group_id + '\';" original-title="' + $ ("#see_full_conversation").val () + '"><i class="fa fa-envelope vfooter_icon"></i></span>\
			\
			' + add_more_people + '\
			<div style="clear:both;"></div>\
			</div>\
		   </div>\
		   </div>';
    element = element + '</div>';
    element = element + '</div>';
    element = element + '</div>';

    $ (element).appendTo ($ ("body"));

    // $ ('.vasplus_tooltip_icons').tipsy ({fade: true, gravity: 'sw'});
}



//calculate the total number of popups suitable and then populate the toatal_popups variable.
function vpb_vchat_position_chat_boxes ()
{
    var width = window.innerWidth - 202;
    if (parseInt (width) < 520)
    {
        total_popups = 0;
        $ ("#vpb_chat_main_box").hide ();
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

// Add more friends to chat or conversation
function vpb_add_more_friends_to_chat (group_id)
{
    $ ("#v_vchat_users_box" + group_id).fadeOut ();
    $ ("#vpb_vchat_bottom" + group_id).hide ();
    $ ("#vpb_vchat_top" + group_id).fadeIn ();
    $ ("#add_more_friends_to_chat" + group_id).hide ();

    setTimeout (function ()
    {
        $ ("#v_added_u_sers" + group_id).animate ({scrollTop: parseInt ($ ('#v_added_u_sers' + group_id).height ()) + 10000}, 1000);
        $ ("#vpb_vchat_to_data" + group_id).focus ();
    }, 100);
}


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

// Add new user to group
function vpb_vchat_add_new_user_to_group (group_id, fullname, username, photo)
{


//    if (vpb_vchat_session_is_created (group_id) && vpb_vchat_isInArray (vchat_getcookie ('group_username' + group_id), username))
//    {
//        // Already in the list
//        $ ("#vpb_user_selected-" + username + group_id).hide ();
//    }
//    else
//    {
//        var group_fullname = new Array ();
//        var group_username = new Array ();
//        var group_photo = new Array ();
//
//        if (vpb_vchat_session_is_created (group_id))
//        {
//            group_fullname = vchat_getcookie ('group_fullname' + group_id).split (/\,/);
//            group_username = vchat_getcookie ('group_username' + group_id).split (/\,/);
//            group_photo = vchat_getcookie ('group_photo' + group_id).split (/\,/);
//
//            group_fullname[group_fullname.length] = fullname;
//            group_username[group_username.length] = username;
//            group_photo[group_photo.length] = photo + '#' + username;
//        }
//        else
//        {
//            group_username[group_username.length] = username;
//            group_fullname[group_fullname.length] = fullname;
//            group_photo[group_photo.length] = photo + '#' + username;
//        }
//
//        vchat_setcookie ('group_fullname' + group_id, group_fullname, 30);
//        vchat_setcookie ('group_username' + group_id, group_username, 30);
//        vchat_setcookie ('group_photo' + group_id, group_photo, 30);
//
//
//        $ ("#vpb_user_selected-" + username + group_id).hide ();
//        $ ("#vpb_vchat_to_data" + group_id).val ('').focus ();
//
//        $ ("#vpb_added_users_box" + group_id).append ('<span id="vpb_new_user_in_group_' + username + group_id + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vchat_remove_new_user_from_group(\'' + group_id + '\', \'' + fullname + '\', \'' + username + '\', \'' + photo + '\')"></i></span></span>');
//
//        setTimeout (function ()
//        {
//            $ ("#v_added_u_sers" + group_id).animate ({scrollTop: parseInt ($ ('#v_added_u_sers' + group_id).height ()) + 10000}, 1000);
//        }, 10);

    vchat_display_users_in_group (group_id, 'run', username, photo, fullname);

//        if (group_id != "" && group_id != undefined)
//        {
//            var dataString = {"username": username, "group_id": group_id, "page": "vpb_add_removed_user_in_group_again"};
//            $.ajax ({
//                type: "POST",
//                url: vpb_site_url + 'vchat-processor.php',
//                data: dataString,
//                beforeSend: function ()
//                {},
//                success: function (response)
//                {}
//            });
//        }
//        else
//        {
//        }
    //}
}

// Blink page title
var titleInterval = 0;
function vpb_vchat_page_title (vpb_new_title_message, action)
{
    if (!action)
        action = 'stop';
    if (action === 'start')
    {
        if (titleInterval)
            clearInterval (titleInterval);

        titleInterval = setInterval (function ()
        {
            if (document.title == vpb_original_title_message)
            {
                document.title = vpb_new_title_message;
            }
            else
            {
                document.title = vpb_original_title_message;
            }
        }, 800);
    }
    else
    {
        $ ("#vasplusChatAudio").html ('');
        document.title = vpb_original_title_message;
        clearInterval (titleInterval);
        titleInterval = 0;
    }
}


// Display all newly added users in a group
function vchat_display_users_in_group (group_id, status, username, photo, fullname)
{

    $ ("#vpb_display_all_users_in_group" + group_id).append ('<div style="clear:both;"></div>\
				<div class="vpb_msg_wraper">\
				<div class="vpb_msg_wraper_photo">\
				<img onclick="window.location.href=\'/blab/index/profile/' + username + '\';" style="cursor:pointer;" src="' + photo + '" border="0">\
				</div>\
\
				<div class="vpb_msg_wraper_name">\
				<span class="vpb_msg_wraper_name_left">\
				<span onclick="window.location.href=\'/blab/index/profile/' + username + '\';">' + fullname + '</span>\
				</span>\
				\
				<div style="clear:both;"></div>\
				</div>\
				</div>');

    total_users = 1;
    if (parseInt (total_users) == 0)
    {
        $ ("#vpb-current-group-name" + group_id).html ('<span class="vpb_compose_new_message_text">' + document.getElementById ('compose_new_message_text').value + '</span>');
    }
    else if (parseInt (total_users) == 1)
    {
        $ ("#vpb-current-group-name" + group_id).html ('<span onmouseover="vchat_get_user_onmouseover_data(\'' + group_id + '\');" onmouseout="vchat_get_user_mouseout_data(\'' + group_id + '\');"  class="vpb_vchat_added_users_names" onclick="window.location.href=\'/blab/index/profile/' + username + '\';">' + fullname + '</span>');
    }
    else
    {
        var counted = parseInt (total_users) - 1;
        var others_label = parseInt (counted) > 1 ? "others" : "person";

        $ ("#vpb-current-group-name" + group_id).html ('<span onmouseover="vchat_get_user_onmouseover_data(\'' + group_id + '\');" onmouseout="vchat_get_user_mouseout_data(\'' + group_id + '\');" class="vpb_vchat_added_users_names" onclick="window.location.href=\'/blab/index/profile/' + username + '\';">' + firstname + '</span> and <span onmouseover="vchat_get_user_onmouseover_data(\'' + group_id + '\');" onmouseout="vchat_get_user_mouseout_data(\'' + group_id + '\');" class="vpb_vchat_added_users_names">' + parseInt (counted) + ' ' + others_label + '</span>');
    }

    if (status == "auto")
    {
        vpb_add_more_friends_to_chat (group_id);
    }
    else
    {
    }

    $ ("#vpb_vchat_bottom_html" + group_id).css ('display', 'block');

    $ ("#vpb_vchat_top" + group_id).css ('min-height', 100 + '%');
    $ ("#vpb_vchat_top" + group_id).css ('max-height', 100 + '%');
    $ ("#vpb_vchat_top" + group_id).css ('height', 100 + '%');
}

// Show the conversation box
function vpb_show_conversation_box (group_id)
{
    $ ("#v_vchat_users_box" + group_id).fadeOut ();
    $ ("#vpb_vchat_top" + group_id).hide ();
    $ ("#vpb_vchat_bottom" + group_id).fadeIn ();
    $ ("#add_more_friends_to_chat" + group_id).fadeIn ();

    setTimeout (function ()
    {
        $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);
    }, 100);
}


//Resize chat box
function vpb_vchat_textarea_expansion (vpb_chat_textarea, group_id)
{
    var vpb_textarea_box = vpb_chat_textarea;
    setTimeout (function ()
    {
        if (vpb_textarea_box.scrollHeight >= 120)
        {
            vpb_textarea_box.css ('overflow-y', 'hidden');
            vpb_textarea_box.scrollHeight + '120px';
        }
        else
        {
            vpb_textarea_box.style.cssText = 'height:3; padding:0';
            vpb_textarea_box.style.cssText = 'height:' + vpb_textarea_box.scrollHeight + 'px';
        }

        // Adjust the chat content box
        var box_height = 274 - parseInt (vpb_textarea_box.scrollHeight);
        $ ("#vpb_chat_content_box_" + group_id).css ('min-height', parseInt (box_height) + 'px');
        $ ("#vpb_chat_content_box_" + group_id).css ('max-height', parseInt (box_height) + 'px');
        $ ("#vpb_chat_content_box_" + group_id).css ('height', parseInt (box_height) + 'px');
    }, 0);
}

//Send message
function vchat_send_message (vpb_event, group_id)
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {
        $ ("textarea#vpb_vchat_text" + group_id).val ("").change ();
        vpb_event.preventDefault ();
        return false;
    }
    else
    {
        var vpb_keycode = (vpb_event.keyCode ? vpb_event.keyCode : vpb_event.which);

        if (vpb_event.keyCode == 13 && vpb_event.shiftKey == 0)
        {
            var group_username = $ (".vpb_vchat_box[id=" + group_id + "]").attr ("username");
            var username = $ ("#vchat_sessions_uid").val ();

            if (group_id != "" && group_id != null && group_id != undefined)
            {
                if (vchat_trim ($ ("textarea#vpb_vchat_text" + group_id).val ()) == "" || $ ("textarea#vpb_vchat_text" + group_id).val () == "Write a message...")
                {
                    $ ("textarea#vpb_vchat_text" + group_id).val ("").change ();
                    vpb_event.preventDefault ();
                    return false;
                }
                else
                {

                    var message = vchat_trim ($ ("#vpb_vchat_text" + group_id).val ());
                    var url_in_message = vchat_get_url_in_message (message);

                    var msg_id = vchat_gen_random ();
                    var time_sent = new Date ();

                    var formatted_mgs = "";

                    if (username == "" || username == null || username == undefined)
                    {
                        $ ("#v-vchat-message").html ('VPB1: ' + $ ("#vchat_general_systems_error").val ());
                        $ ("#v-vchat-alert-box").click ();
                        return false;
                    }
                    else if (group_id == "" || group_id == null || group_id == undefined)
                    {
                        $ ("#v-vchat-message").html ('VPB2: ' + $ ("#vchat_general_systems_error").val ());
                        $ ("#v-vchat-alert-box").click ();
                        return false;
                    }
                    else if (group_username == "" || group_username == null || group_username == undefined)
                    {
                        $ ("#v-vchat-message").html ($ ("#empty_to_group_users").val ());
                        $ ("#v-vchat-alert-box").click ();
                        return false;
                    }
                    else if (message == "" || message == "Write a message...")
                    {
                        $ ("#v-vchat-message").html ($ ("#empty_new_message_field").val ());
                        $ ("#v-vchat-alert-box").click ();
                        return false;
                    }
                    else
                    {
                        var dataString = {"from_username": username, "group_id": group_id, "group_username": group_username, "message": message, "url_in_message": url_in_message, "page": "send-the-message"};

                        $ ("#seen_message_notification" + group_id).html ('');

                        if ($ ("#vchat_who_sent_last_" + group_id).val () == username)
                        {
                            $ ("#vpb_display_conversations" + group_id).append ('<li><span style="max-width:230px;width:auto;" class="right">' + vchat_chat_smilies (vchat_create_chat_links (vchat_newLine (vchat_special_text (message)))) + '<div id="vfetched_link_' + group_id + msg_id + '"></div><div class="vpb_ms_wraper_name_right" style="margin-top:5px;">' + vchat_date_time (time_sent) + '</div></span><div class="clear"></div></li>').hide ().fadeIn ('slow');
                        }
                        else
                        {
                            $ ("#vpb_display_conversations" + group_id).append ('<li><img class="right" src="' + vpb_session_photo + '"><span class="right">' + vchat_chat_smilies (vchat_create_chat_links (vchat_newLine (vchat_special_text (message)))) + '<div id="vfetched_link_' + group_id + msg_id + '"></div><div class="vpb_ms_wraper_name_right" style="margin-top:5px;">' + vchat_date_time (time_sent) + '</div></span><div class="clear"></div></li>').hide ().fadeIn ('slow');
                        }

                        $ ("#vchat_who_sent_last_" + group_id).val (username)

                        setTimeout (function ()
                        {
                            $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);
                        }, 100);

                        $.post ('/blab/chat/saveMessage', dataString, function (response)
                        {
                            vchat_removecookie ('compose_new_message');

                            var objResponse = $.parseJSON (response);

                            if (objResponse.sucess == 0)
                            {
                                $ ("#v-vchat-message").html (response);
                                $ ("#v-vchat-alert-box").click ();
                                return false;
                            }
                            else
                            {
                                $ ("#see_full_conversation_button" + group_id).show ();

                                window.clearInterval (vx_run);
                                vx_run = 0;
                                vx_interval = 10000;
                                total_pushed = 1;
                                heartbeat = 1;
                                vx_run = window.setInterval (vchat_run_system_updates, vx_interval); // start the setInterval()

                                // If there is link in the message then fetch the link
                                if (url_in_message != "")
                                {
                                    var v_data = response.split ('&');
                                    var id = v_data[1];
                                    var group_sid = v_data[2];
                                    if (id == "" || group_sid == "")
                                    {
                                    }
                                    else
                                    {
                                        var dataStringb = {"from_username": username, "group_id": group_sid, "message_id": id, "page": "add-link-to-message"};
//                                        $.post (vpb_site_url + 'vchat-processor.php', dataStringb, function (responseLink)
//                                        {
//                                            $ ("#vfetched_link_" + group_id + msg_id).html (responseLink);
//                                        });
                                    }
                                }
                                else
                                {
                                }
                            }

                        }).fail (function (error_response)
                        {
                            setTimeout ("vchat_send_message('" + group_id + "');", 1000);
                        });
                    }
                    $ ("textarea#vpb_vchat_text" + group_id).val ("").change ();
                    $ ("textarea#vpb_vchat_text" + group_id).focus ();
                    vpb_event.preventDefault ();
                    return false;
                }
            }
            else
            {
                //Do not send message if session is not created
                $ ("textarea#vpb_vchat_text" + group_id).val ("").change ();
                vpb_event.preventDefault ();
                return false;
            }
        }
        else
        {
        }
    }
}

// Fetch supported videos via URL
function vpb_vchat_fetch_video ()
{
    var session_uid = $ ("#vchat_sessions_uid").val ();
    var video_url = $ ("#add_vchat_video_url").val ();
    var group_id = $ ("#current_group_id").val ();
    var group_username = vchat_getcookie ('group_username' + group_id);

    if (video_url == "")
    {
        $ ("#vchat-display-video").html ('');
        $ ("#v-vchat-message").html ($ ("#no_vchat_video_link_message").val ());
        $ ("#v-vchat-alert-box").click ();
        return false;
    }
    else if (group_username == "" || group_username == null || group_username == undefined)
    {
        $ ("#vchat-display-video").html ('');
        $ ("#add_vchat_video_url").val ('');
        $ ("#added_vchat_video_url").val ('');
        $ ("#close_vchat_video_popup_button").click ();

        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
        setTimeout (function ()
        {
            $ ("#v-vchat-alert-box").click ();
        }, 10);
        return false;
    }
    else if (group_id == "" || group_id == null || group_id == undefined)
    {
        $ ("#vchat-display-video").html ('');
        $ ("#add_vchat_video_url").val ('');
        $ ("#added_vchat_video_url").val ('');
        $ ("#close_vchat_video_popup_button").click ();

        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
        setTimeout (function ()
        {
            $ ("#v-vchat-alert-box").click ();
        }, 10);
        return false;
    }
    else
    {
        var dataString = {'page': 'fetch_video_via_url', 'video_url': video_url, 'from_username': session_uid, 'group_id': group_id, 'group_username': group_username};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/fetchVideo',
            data: dataString,
            beforeSend: function ()
            {
                $ ("#vchat_video_modal_footer").hide ();
                $ ("#vchat-display-video").html ($ ("#vchat_loading_btn").val ());
                //return false;
            },
            success: function (response)
            {
                $ ("#vchat_video_modal_footer").show ();

                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 1)
                {
                    var video_id = objResponse.data.id;
                    var video_type = objResponse.data.type;

                    $ ("#add_vchat_video_url").val ('');
                    $ ("#added_vchat_video_url").val (response);

                    if (video_type == "youtube")
                    {
                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="https://www.youtube.com/embed/' + video_id + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else if (video_type == "vimeo")
                    {
                        var response_sub_data = video_id.split ('|');
                        var video_uid = response_sub_data[0];
                        var video_option = response_sub_data[1];
                        //alert(response+' - ID = '+video_uid+' - Option = '+video_option);

                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="https://player.vimeo.com/video/' + video_uid + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else if (video_type == "metacafe")
                    {
                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="http://www.metacafe.com/embed/' + video_id + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else if (video_type == "dailymotion")
                    {
                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="//www.dailymotion.com/embed/video/' + video_id + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else if (video_type == "flickr")
                    {
                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="http://www.flickr.com/apps/video/stewart.swf?photo_id=' + video_id + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else if (video_type == "myspace")
                    {
                        $ ("#vchat-display-video").html ('<div class="embed-responsive embed-responsive-16by9">\
						  <iframe allowfullscreen class="embed-responsive-item" src="//media.myspace.com/play/video/' + video_id + '"></iframe>\
						</div>');

                        $ ("#vchat_video_send_button").show ();
                    }
                    else
                    {
                        // Unconfirmed
                        $ ("#vchat-display-video").html ('');
                        $ ("#add_vchat_video_url").val ('');
                        $ ("#added_vchat_video_url").val ('');

                        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
                        $ ("#v-vchat-alert-box").click ();
                        return false;
                    }
                }
                else
                {
                    $ ("#vchat-display-video").html ('');
                    $ ("#add_vchat_video_url").val ('');
                    $ ("#added_vchat_video_url").val ('');

                    $ ("#v-vchat-message").html (response);
                    $ ("#v-vchat-alert-box").click ();
                    return false;
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_vchat_fetch_video();", 1000);
        });
    }
}

// Display file size
function vchat_show_file_size (file_field)
{
    var vpb_file_size = ($ ("#" + file_field)[0].files[0].size / 1024);
    var vpb_actual_size;
    if (vpb_file_size / 1024 > 1)
    {
        if (((vpb_file_size / 1024) / 1024) > 1)
        {
            vpb_file_size = (Math.round (((vpb_file_size / 1024) / 1024) * 100) / 100);
            vpb_actual_size = vpb_file_size + "Gb";
        }
        else
        {
            vpb_file_size = (Math.round ((vpb_file_size / 1024) * 100) / 100);
            vpb_actual_size = vpb_file_size + "Mb";
        }
    }
    else
    {
        vpb_file_size = (Math.round (vpb_file_size * 100) / 100);
        vpb_actual_size = vpb_file_size + "kb";
    }
    return vpb_actual_size;
}

function vpb_hide_chat_sidebar (status)
{
    if ($ ("#vchat_sessions_uid").val () != "" && $ ("#vchat_sessions_uid").val () != undefined)
    {
        $ ("#vpb_chat_main_box").hide ();
        $ (".vpb_chat_options_box").hide ();
        $ ("#vpb_chat_main_box_b").show ();

        var dataString = {'page': 'vpb_hide_chat_sidebar', 'from_username': $ ("#vchat_sessions_uid").val (), 'chat_status': status};

//        $.post (vpb_site_url + 'vchat-processor.php', dataString, function (response)
//        {
//            var responsebrought = response.indexOf ('chat-is-turnd-off');
//            if (responsebrought != -1)
//            {
//                $ (".headers").css ("opacity", "0.6");
//                $ ("#vchat_is_disabled").val ('1');
//            }
//            else
//            {
//                alert ("Lexi");
//                $ (".headers").css ("opacity", "1");
//                $ ("#vchat_is_disabled").val ('0');
//            }
//            $ ("#vpb_chat_main_box").hide ();
//            $ (".vpb_chat_options_box").hide ();
//            $ ("#vpb_chat_main_box_b").show ();
//            return false;
//        }).fail (function ()
//        {
//            setTimeout ("vpb_hide_chat_sidebar('" + status + "');", 1000);
//        });
    }
    else
    {
        $ ("#v-vchat-message").html ($ ("#vchat_general_systems_error").val ());
        $ ("#v-vchat-alert-box").click ();
        return false;
    }
}


//Set height of Main Chat Box
function vchat_show_main_box_b ()
{

    $ (".vpb_chat_options_box").hide ();

    $ ("#vpb_chat_main_box_b").hide ();

    $ ("#vpb_chat_main_box").show ();

    if ($ ("#vchat_sessions_uid").val () != "" && $ ("#vchat_sessions_uid").val () != undefined)
    {

        $ (".headers").css ("opacity", "1");

        alert ("OK");

        $ ("#vchat_is_disabled").val ('0');

    }
    else
    {
        $ ("#v-vchat-message").html ($ ("#vchat_general_systems_error").val ());
        $ ("#v-vchat-alert-box").click ();
        return false;
    }

    return false;
}

// Show Option Box
function vpb_chat_option_boxed ()
{
    $ (".vpb_chat_options_box").toggle ();
}


// Continue with the added photos to message
function vchat_send_files (vpb_selector_, info, prev_label)
{
    if ($ ("#vchat_is_disabled").val () == 1)
        return false;

    var group_id = $ ("#current_group_id").val ();

    var group_username = $ (".vpb_vchat_box[id=" + group_id + "]").attr ("username");
    var username = $ ("#vchat_sessions_uid").val ();

    //var username = vchat_trim (vchat_strip_tags ($ ("#vchat_sessions_uid").val ()));
    //var group_manager = vchat_getcookie ('v_group_manager' + group_id) == null ? '' : vchat_trim (vchat_strip_tags (vchat_getcookie ('v_group_manager' + group_id)));
    //var group_username = vchat_getcookie ('group_username' + group_id) == null ? '' : vchat_trim (vchat_strip_tags (vchat_getcookie ('group_username' + group_id)));
    //var group_fullname = vchat_getcookie ('group_fullname' + group_id) == null ? '' : vchat_trim (vchat_strip_tags (vchat_getcookie ('group_fullname' + group_id)));
    var time_sent = new Date ();

    var photos = $ ("#add_vchat_photos").val ();
    var files = $ ("#add_files" + group_id).val ();
    var vfiles = document.getElementById ('add_files' + group_id).files;

    var photos_ext = photos.split ('.').pop ().toLowerCase ();
    var files_ext = files.split ('.').pop ().toLowerCase ();

    if ($ ("#add_files" + group_id).val () != "" && $.inArray (files_ext, ["jpg", "jpeg", "gif", "png"]) != -1 || $ ("#add_files" + group_id).val () != "" && $.inArray (files_ext, ["pdf", "doc", "docx", "txt", "zip", "rar"]) == -1)
    {
        $ ("#add_files" + group_id).val ('');
        $ ("#v-vchat-message").html ($ ("#wrong_vchat_files_added_text").val ());
        $ ("#v-vchat-alert-box").click ();
        return false;
    } // Maximum allowed file size = 61 MB OR 61.69 Mb
    else if ($ ("#add_files" + group_id).val () != "" && vfiles[0].size > parseInt (64687347))
    {
        $ ("#v-vchat-message").html ($ ("#big_file_size_text").val () + '<b>' + vchat_show_file_size ('add_files' + group_id) + '</b>');
        $ ("#v-vchat-alert-box").click ();
        $ ("#add_files" + group_id).val ('');
        return false;
    }
    else
    {
    }

    vpb_show_conversation_box (group_id);

    if (group_id == "" || group_id == null || group_id == undefined)
    {
        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
        setTimeout (function ()
        {
            $ ("#v-vchat-alert-box").click ();
        }, 10);
        return false;
    }
    else if (group_username == "" || group_username == null || group_username == undefined)
    {
        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
        setTimeout (function ()
        {
            $ ("#v-vchat-alert-box").click ();
        }, 10);
        return false;
    }
    else if (vpb_selector_ == "" || vpb_selector_ == null || vpb_selector_ == undefined)
    {
        $ ("#v-vchat-message").html ($ ("#no_vchat_proper_data_passed").val ());
        setTimeout (function ()
        {
            $ ("#v-vchat-alert-box").click ();
        }, 10);
        return false;
    }
    else
    {
        if (photos != "" && $.inArray (photos_ext, ["jpg", "jpeg", "gif", "png"]) != -1)
        {
            var cid = 1, last_id = last_cid = '';
            var counts_photo = 1;
            var counting_photo = 1;
            var message_id = vchat_gen_random ();
            var total_photos = 1;

            $ ("#vpb_display_conversations" + group_id).append ('<li><img class="right" src="' + vpb_session_photo + '"><span class="right"><div id="photos_displayer_' + group_id + message_id + '"><input type="hidden" id="vtotal_photos_' + message_id + '" value=""></div><div class="vpb_ms_wraper_name_right" style="margin-top:5px;">' + vchat_date_time (time_sent) + '</div></span><div class="clear"></div></li>').hide ().fadeIn ('slow');

            $.each (vpb_selector_.files, function (vpb_o_, file)
            {
                var file_ext = file.name.split ('.').pop ().toLowerCase ();

                if (file.name.length > 0 && $.inArray (file_ext, ["jpg", "jpeg", "gif", "png"]) != -1)
                {
                    if (!file.type.match ('image.*'))
                    {
                        // Not supported
                    }
                    else
                    {
                        $ ('#vchat-display-attachment-preview').html ('');

                        var vpb_htm_photos = "";

                        var reader = new FileReader ();
                        reader.onload = function (e)
                        {
                            var photo_link = e.target.result;

                            $ ("#photos_displayer_" + group_id + message_id).append ('\
						  <div class="preview_photo_sized" title="Click to enlarge ' + escape (file.name) + '">\
							<a style="cursor:pointer;" onClick="vchat_popup_photo_box(\'' + message_id + '\', \'' + total_photos++ + '\', \'' + counting_photo++ + '\', \'' + photo_link + '\');">\
							  <img src="' + photo_link + '" />\
							</a>\
						  </div><input type="hidden" id="hidden_photo_link_' + message_id + '_' + counts_photo++ + '" value="' + photo_link + '">');

                        }
                        reader.readAsDataURL (file);
                        cid++;
                        $ ("#vtotal_photos_" + message_id).val (total_photos++);
                    }
                }
                else
                {
                }
            });
            setTimeout (function ()
            {
                $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);
            }, 100);
        }
        else
        {
        }

        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('add_vchat_photos').files;
        var vpb_file = document.getElementById ('add_files' + group_id).files;

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

        if (photos != "")
        {
            vpb_data.append ("from_username", username);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("group_username", group_username);
            vpb_data.append ("page", 'vpb_add_images_to_message');
        }
        else if (files != "")
        {
            vpb_data.append ("from_username", username);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("group_username", group_username);
            vpb_data.append ("page", 'vpb_add_files_to_message');
        }
        else
        {
            vpb_data.append ("from_username", username);
            vpb_data.append ("group_id", group_id);
            vpb_data.append ("group_username", group_username);
            vpb_data.append ("page", 'vpb_add_none_to_message');
        }

        $.ajax ({
            url: '/blab/chat/uploadFile',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                vchat_removecookie ('compose_new_message');
                if ($ ("#add_files" + group_id).val () != "")
                {
                    $ ("#vchat_display_sending_file_" + group_id).html ('<div style="padding-top:25%;">' + $ ("#vchat_loading_image_gif").val () + '</div>');
                    $ ("#vchat_done_sending_file_" + group_id).hide ();
                    $ ("#vchat_sending_file_" + group_id).fadeIn ();
                }
                else
                {
                }
            },
            success: function (response)
            {
                window.clearInterval (vx_run);
                vx_run = 0;
                vx_interval = 10000;
                total_pushed = 1;
                heartbeat = 1;
                vx_run = window.setInterval (vchat_run_system_updates, vx_interval); // start the setInterval()


                $ ("#vchat_sending_file_" + group_id).hide ();
                $ ("#vchat_done_sending_file_" + group_id).fadeIn ();

                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    $ ("#v-vchat-message").html ($ ("#vchat_general_systems_error").val ());
                    $ ("#v-vchat-alert-box").click ();
                    return false;
                }
                else
                {
                    $ ("#see_full_conversation_button" + group_id).show ();

                    $ ('#vchat-display-attachment-preview').html ('');
                    $ ('#add_vchat_photos').val ('');

                    if ($ ("#add_files" + group_id).val () != "")
                    {
                        var vchat = JSON.parse (response); // this contains the json data from the php file
                        if (vchat.message_id)
                        {
                            $ ("#vpb_display_conversations" + group_id).append (vchat.message).hide ().fadeIn ('slow');
                            setTimeout (function ()
                            {
                                $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);

                                $ ('#add_files' + group_id).val ('');

                            }, 100);
                        }
                    }
                }
            }
        }).fail (function (error_response)
        {
            $ ("#vchat_sending_file_" + group_id).hide ();
            $ ("#vchat_done_sending_file_" + group_id).fadeIn ();

            $ ("#v-vchat-message").html ($ ("#vchat_general_systems_error").val ());
            $ ("#v-vchat-alert-box").click ();
            return false;
        });
    }
}

function vchat_run_system_updates ()
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {
        if ($ ("#vchat_has_disabled").val () == 1)
        {
            vchat_removecookie ('compose_new_message');
            vchat_removecookie ('group_uid');

            $ ("#vpb_chat_main_box").hide ();
            $ (".vpb_vchat_box").hide ();
            $ ("#vpb_vchat_hidden_boxes").hide ();
        }
        else
        {
        }
        window.clearInterval (vx_run);
        vx_run = 0;
        return false;
    }
    else
    {
        // Place functions here
        vpb_vchat_check_updates ();
        vchat_check_for_seen_message_notification ('');

        //Load and display friends box on page load
        if ($ ("#vchat_loading_type").val () == "previous")
        {
        }
        else
        {
            vpb_load_vchat_friends_box ('checking');
        }

        //Count friends online
        vchat_count_friends_online ();

        // Auto set users to Online/Offline
        vchat_update_user_status ();

        // Place functions here
        vpb_check_friends_requests ();
        vpb_check_notifications ();
        vpb_check_a_new_messages ();

        total_pushed++;
        if (vx_run)
            window.clearInterval (vx_run);

        //$("#chat_texting").html('Total Pushed: '+heartbeat++ +' = Interval: '+vx_interval+' = vx_run: '+vx_run);

        if (parseInt (total_pushed) > 20)
        {
            vx_interval = parseInt (vx_interval) + 10000;
            if (parseInt (total_pushed) > 50)
            {
                window.clearInterval (vx_run);
                vx_run = 0;
                vx_interval = 10000;
                total_pushed = 1;
                heartbeat = 1;
            }
        }
        else
        {
            //vx_interval = parseInt(vx_interval);
        }
        vx_run = window.setInterval (vchat_run_system_updates, vx_interval); // start the setInterval()
    }
}

var system_had_started = false;
// Check for seen message notification
function vchat_check_for_seen_message_notification (group_id)
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }
    else
    {
        if (vchat_getcookie ('group_uid') != "" && vchat_getcookie ('group_uid') != null && vchat_getcookie ('group_uid') != undefined)
        {
            if (system_had_started == true)
                return false;
            system_had_started = true;

            var group_uid = new Array ();
            group_uid = vchat_getcookie ('group_uid').split (/\,/);
            var total_users = group_uid.length;

            for (var u = 0; u < parseInt (total_users); u++)
            {
                if (group_uid[u] != "")
                {
                    var group_id = group_uid[u];

                    if ($ ("#" + group_id).css ('display') != "none")
                    {
                        var session_username = $ ("#vchat_sessions_uid").val ();

                        alert ("NOTIFICATION");
                        return false;

                        var dataString = {"session_username": session_username, "group_id": group_id, "page": "check_for_seen_message_notification"};
//                        $.ajax ({
//                            type: "POST",
//                            url: vpb_site_url + 'vchat-processor.php',
//                            data: dataString,
//                            beforeSend: function ()
//                            {},
//                            success: function (response)
//                            {
//                                var vchat = JSON.parse (response); // this contains the json data from the php file
//                                if (vchat.from_username && vchat.group_id && vchat.response)
//                                {
//                                    if (vchat.from_username == session_username)
//                                    {
//                                        $ ("#seen_message_notification" + vchat.group_id).html ('');
//                                        $ ("#seen_message_notification" + vchat.group_id).html ('<i class="fa fa-check"></i> Seen by ' + vchat.response);
//                                        $ ("#seen_message_notification_box" + vchat.group_id).show ();
//                                    }
//                                    else
//                                    {
//                                        $ ("#seen_message_notification_box" + group_id).hide ();
//                                    }
//                                }
//                                else
//                                {
//                                }
//                                system_had_started = false;
//                            }
//                        });
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
}

// Update status
var completedCalled = false;
function vchat_update_user_status ()
{
    if ($ ("#vchat_is_disabled").val () == 1)
        return false;

    if (completedCalled == true)
        return false;
    completedCalled = true;

    if ($ ("#vchat_sessions_uid").val () != "" && $ ("#vchat_sessions_uid").val () != undefined)
    {

        var session_uid = $ ("#vchat_sessions_uid").val ();
        var dataString = {'page': 'vpb_update_user_status', 'username': session_uid};
//        $.post (vpb_site_url + 'vchat-processor.php', dataString, function (response)
//        {
//            completedCalled = false;
//        });
    }
    else
    {
    }
}

// Check friend Requests
function vpb_check_friends_requests ()
{
    pollTask ();
}

// Check Notifications
function vpb_check_notifications ()
{
    load_unseen_notification ();
}

// Check for new messages notifier
function vpb_check_a_new_messages ()
{
    var session_username = $ ("#session_uid").val ();

    if (session_username != "")
    {
        var dataString = {"username": session_username, "page": "check_a_new_messages"};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/checknewMessages',
            data: dataString,
            beforeSend: function ()
            {},
            success: function (response)
            {
                if (parseInt (response) > 0)
                {
                    $ ("#totalMessages").html (parseInt (response));
                    $ ("#message_counter").fadeIn ();
                }
                else
                {
                    $ ("#message_counter").fadeOut ();
                }
            }
        });
    }
    else
    {
    }
}

counter_running = false;

//Count friends online
function vchat_count_friends_online ()
{
    if ($ ("#vchat_is_disabled").val () == 1)
        return false;

    if (counter_running == true)
        return false;
    counter_running = true;

    if ($ ("#vchat_sessions_uid").val () != "" && $ ("#vchat_sessions_uid").val () != undefined)
    {
        var vpb_friends_counter_data = {'page': 'vpb_friends_counter', 'from_username': $ ("#vchat_sessions_uid").val ()};
        $.ajax ({
            type: "POST",
            url: '/blab/chat/countOnlineFriends',
            data: vpb_friends_counter_data,
            cache: false,
            success: function (response)
            {
                if ($ ("#counter_online_users").val () != "" && $ ("#counter_online_users").val () != undefined)
                {
                    if ($ ("#counter_online_users").val () == response)
                    {
                    }
                    else
                    {
                        $ ("#vpb_counter_result").html (response);
                        $ ("#counter_online_users").val (response);

                        window.clearInterval (vx_run);
                        vx_run = 0;
                        vx_interval = 10000;
                        total_pushed = 1;
                        heartbeat = 1;
                        vx_run = window.setInterval (vchat_run_system_updates, vx_interval);
                    }
                }
                else
                {
                    $ ("#vpb_counter_result").html (response);
                    $ ("#counter_online_users").val (response);
                }
                counter_running = false;
            }
        });
    }
    else
    {
    }
}

// Search for friend in sidebar
function vpb_search_friends_sidebar (searchTerm)
{
  if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }

    loading_users = true;

    if (searchTerm == "" || searchTerm == "Search")
    {
        $ ("#search_stop_loading").val ('0');
        loading_users = false;
        vpb_load_vchat_friends_box ('auto');
    }
    else if (searchTerm.length > parseInt (15))
    {
        $ ("#search_stop_loading").val ('1');
        $ ("#vpb_chat_friends_box").html ('<div style="padding:10px;">' + $ ("#vchat_long_search_term_text").val () + '</div>');
        loading_users = false;
        return false;
    }
    else
    {
        var searchType = $ ("#vchat_loading_type").val () != "searched" ? $ ("#vchat_loading_type").val () : "users";

        var dataString = {'page': 'vpb_chat_search_for_people', 'searchText': searchTerm, 'searchType': searchType};
        
        $.ajax ({
            type: 'POST',
            url: '/blab/chat/getChatFriends',
            data: dataString,
            cache: true,
            beforeSend: function ()
            {
                $ ("#search_stop_loading").val ('1');
                $ ("#vpb_chat_friends_box").show ().html ('<br><br><br><br>' + $ ("#vchat_loading_image_gif").val ());
            },
            success: function (response)
            {

                if ($.trim (response) !== "")
                {
                    if ($ ("#vchat_is_disabled").val () == 1)
                    {
                        $ (".headers").css ("opacity", "0.6");
                        $ ("#vchat_is_disabled").val ('0');
                    }
                    else
                    {
                        $ (".headers").css ("opacity", "1");
                        $ ("#vchat_is_disabled").val ('0');
                    }

                    $ ("#vchat_loading_type").val () == "previous" ? "" : $ ("#vchat_loading_type").val ('previous');
                    loading_users = false;
                    $ ("#search_stop_loading").val ('0');
                    $ ("#vpb_chat_friends_box").html (response);
                }
                else
                {
                    alert("55");
                    
                    loading_users = false;
                    $ ("#search_stop_loading").val ('0');
                    vpb_load_vchat_friends_box ('auto');
                }
            }
        });
    }
}

loading_users = false;

function vpb_load_vchat_friends_box (action)
{
    if ($ ("#vchat_loading_type").val () == 'users' && action != 'users' && action != 'off' && action != 'auto' && action != 'checking' || $ ("#vchat_loading_type").val () == 'searched' && action != 'users')
    {
        return false;
    }
    else if ($ ("#vchat_sessions_uid").val () == "" || $ ("#vchat_sessions_uid").val () == undefined)
    {
        if ($ ("#vchat_users_loaded").val () != 1)
        {
            document.getElementById ('vchat_users_loaded').value = '1';
            $ ("#v-vchat-message").html ('VCHAT-A: ' + $ ("#vchat_general_systems_error").val ());
            $ ("#v-vchat-alert-box").click ();
        }
        else
        {
        }
        return false;
    }
    else if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }
    else if (loading_users == true && action != 'auto' && action != 'off' || $ ("#search_stop_loading").val () == 1 && action != 'auto')
    {
        if (action == 'users')
        {
            $ ("#v-vchat-message").html ($ ("#vchat_bg_process_running_text").val ());
            $ ("#v-vchat-alert-box").click ();
            return false;
        }
        else
        {
        }
    }
    else
    {
    }

    loading_users = true;

    if (action == 'users')
    {
        $ ("#vchat_cother_users").hide ();
        $ ("#vchat_prev_cons").show ();
        $ ("#vchat_loading_type").val ('checking');
    }
    else
    {
    }


    if (action != 'users' && action != 'auto' && action != 'off' && document.getElementById ('vchat_users_loaded').value == 1)
    {
    }
    else
    {
        $ ("#vpb_chat_friends_box").html ('<br><br><br><br>' + $ ("#vchat_loading_image_gif").val ());
    }
    
    var searchText = $.trim($("#vasplus_chat_users_search").val()) !== "" ? $("#vasplus_chat_users_search").val() : '';

    var dataStrnge = {'page': 'vpb_load_vchat_friends_box', 'searchText': searchText, 'from_username': $ ("#vchat_sessions_uid").val (), 'action': 'normal'};

    // $("#chat_texting").html(total_pushed);

    $.post ('/blab/chat/getChatFriends', dataStrnge, function (response)
    {
        if (document.getElementById ('vchat_users_loaded_last').value != "" && document.getElementById ('vchat_users_loaded_last').value == vchat.status)
        {
        }
        else
        {
            $ ("#vchat_open_new_chat_window").show ();
            $ ("#vpb_chat_friends_box").html (response);
            document.getElementById ('vchat_users_loaded').value = '1';
            //document.getElementById('vchat_users_loaded_last').value = vchat.status;
            //$("#search_stop_loading").val('0');
        }


        if ($ ("#vchat_is_disabled").val () == 1)
        {
            $ (".headers").css ("opacity", "0.6");
            $ ("#vchat_is_disabled").val ('1');

            if (vx_run)
                window.clearInterval (vx_run);
            vx_run = 0;
            vx_interval = 10000;
            total_pushed = 1;
            heartbeat = 1;

            $ ("#vpb_counter_result").html ('Off');
            $ ("#counter_online_users").val ('Off');
            $ ("#vchat_loading_type").val ('users');
            $ (".vpb_vchat_box").hide ();
            $ ("#vpb_vchat_hidden_boxes").hide ();
            $ ("#vchat_cother_users").hide ();
            $ ("#vchat_prev_cons").show ();
        }
        else
        {
            $ (".headers").css ("opacity", "1");
            $ ("#vchat_is_disabled").val ('0');
        }

        loading_users = false;


    }).fail (function ()
    {
        setTimeout ("vpb_load_vchat_friends_box('" + action + "');", 1000);
    });
}

var system_has_started = false;

function vpb_vchat_check_updates ()
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }

    $ (".vpb_vchat_box").each (function ()
    {
        var group_id = $ (this).attr ("id");
        var username = $ (this).attr ("username");

        setTimeout (function ()
        {
            //$ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);

            // $ ("#seen_message_notification" + group_id).html ('');
            //$ ("#last_message_id" + group_id).val (message_id);
            //$ ("#vtotal_group_messages" + group_id).val (total_group_messages);

            $ ("#seen_message_notification" + group_id).html ('');

            vchat_start_and_load_more_messages ('start', group_id, username);

            //$ ("#vpb_vchat_text" + group_id).focus ();
        }, 100);
    });
}

//this function can remove a array element.
Array.remove = function (array, from, to)
{
    var rest = array.slice ((to || from) + 1 || array.length);
    array.length = from < 0 ? array.length + from : from;
    return array.push.apply (array, rest);
};



// Preview File(s)
function vchat_image_preview (vpb_selector_, info, prev_label)
{
    var id = 1, last_id = last_cid = '';
    $.each (vpb_selector_.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match ('image.*'))
            {
                // Not supported
                if (parseInt (id) == 1)
                {
                    $ ('#vchat-display-attachment-preview').html ('');
                    $ ('#add_vchat_photos').val ('');

                    $ ("#v-vchat-message").html ($ ("#wrong_vchat_photos_added_text").val ());
                    $ ("#v-vchat-alert-box").click ();
                    return false;
                }
                else
                {
                }
            }
            else
            {
                $ ('#vchat-display-attachment-preview').html ('<br><div class="alert alert-warning" style="text-align:left;">' + info + '</div><br>');

                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vchat-display-attachment-preview').append ('\
					  <div class="col-xs-6 col-md-3" title="Click to enlarge ' + escape (file.name) + '">\
						<a style="cursor:pointer;" class="thumbnail" onClick="vchat_view_this_image(\'' + prev_label + '\', \'' + e.target.result + '\');">\
						  <img src="' + e.target.result + '" />\
						</a>\
					  </div>');
                }
                reader.readAsDataURL (file);

                var fileLabel = parseInt (id) > 1 ? ' files selected' : ' file selected';
                document.getElementById ('browsed_vchat_Photos').title = parseInt (id) + fileLabel;
                id++;
            }
        }
        else
        {
            return false;
        }
    });
}

var headerInterval = 0;

// Blink chat headers
function vpb_vchat_header_notification (element, action)
{
    if (action === 'start')
    {
        if (headerInterval)
            clearInterval (headerInterval);

        headerInterval = setInterval (function ()
        {
            $ (element).toggleClass ('vpb_chat_box_header_b vpb_chat_box_header_a');

        }, 800);
    }
    else
    {
        $ (element).removeClass ('vpb_chat_box_header_b');
        $ (element).addClass ('vpb_chat_box_header_a');
        clearInterval (headerInterval);
        headerInterval = 0;
    }
}

//Trim
function vchat_trim (s)
{
    return s.replace (/^\s+|\s+$/, '');
}
// Strip_tags
function vchat_strip_tags (input, allowed)
{
    allowed = (((allowed || '') + '').toLowerCase ().match (/<[a-z][a-z0-9]*>/g) || []).join ('');
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
            commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace (commentsAndPhpTags, '').replace (tags, function ($0, $1)
    {
        return allowed.indexOf ('<' + $1.toLowerCase () + '>') > -1 ? $0 : '';
    });
}

// The first seen URL in a message
function vchat_get_url_in_message (message)
{
    var vpb_message_regex_url = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    var vpb_url_in_message = vchat_trim (message).match (vpb_message_regex_url);

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

/* Add smilies to chat */
function vchat_chat_smilies (vpb_chat_text)
{
    var vpb_chat_text_conversion = vpb_chat_text, vpb_a_smilies = [{vpb_smiley_symbol: ':)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'smile.png" title="Smile" align="absmiddle">'}, {vpb_smiley_symbol: ':(', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'frown.png" title="Frown, Sad" align="absmiddle">'}, {vpb_smiley_symbol: ':blushing-angel:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'angel.png" title="Blushing angel" align="absmiddle">'}, {vpb_smiley_symbol: ':cat-face:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'colonthree.png" title="Cat face" align="absmiddle">'}, {vpb_smiley_symbol: 'o.O', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'confused.png" title="Confused" align="absmiddle">'}, {vpb_smiley_symbol: 'O.o', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'confused.png" title="Confused" align="absmiddle">'}, {vpb_smiley_symbol: ':cry:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'cry.png" title="Cry" align="absmiddle">'}, {vpb_smiley_symbol: ':laughing-devil:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'devil.png" title="Laughing devil" align="absmiddle">'}, {vpb_smiley_symbol: ':O', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'gasp.png" title="Shocked and surprised" align="absmiddle">'}, {vpb_smiley_symbol: 'B)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'glasses.png" title="Glasses" align="absmiddle">'}, {vpb_smiley_symbol: ':D', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'grin.png" title="Grin, Big Smile" align="absmiddle">'}, {vpb_smiley_symbol: ':grumpy:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'grumpy.png" title="Upset and angry" align="absmiddle">'}, {vpb_smiley_symbol: ':heart:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'heart.png" title="Heart" align="absmiddle">'}, {vpb_smiley_symbol: '^_^', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'kiki.png" title="Kekeke happy" align="absmiddle">'}, {vpb_smiley_symbol: ':kiss:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'kiss.png" title="Kiss" align="absmiddle">'}, {vpb_smiley_symbol: ':v', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'pacman.png" title="Pacman" align="absmiddle">'}, {vpb_smiley_symbol: ':penguin:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'penguin.gif" title="Penguin" align="absmiddle">'}, {vpb_smiley_symbol: ':unsure:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'unsure.png" title="Unsure" align="absmiddle">'}, {vpb_smiley_symbol: 'B|', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: 'B-|', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '8-|', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '8|', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'sunglasses.png" title="Cool" align="absmiddle">'}, {vpb_smiley_symbol: '-_-', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'squint.png" title="Annoyed, sighing or bored" align="absmiddle">'}, {vpb_smiley_symbol: ':lve:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'red_heart_love.gif" title="Love" align="absmiddle">'}, {vpb_smiley_symbol: ':putnam:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'putnam.gif" title="Christopher Putnam" align="absmiddle">'}, {vpb_smiley_symbol: ';)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'wink.png" title="Wink" align="absmiddle">'}, {vpb_smiley_symbol: ';-)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'wink.png" title="Wink" align="absmiddle">'}, {vpb_smiley_symbol: '(off)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'no_idea.gif" title="No idea" align="absmiddle">'}, {vpb_smiley_symbol: '(on)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'got_idea.gif" title="Got an idea" align="absmiddle">'}, {vpb_smiley_symbol: ':tea-cup:', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'cup_of_tea.png" title="Cup of tea" align="absmiddle">'}, {vpb_smiley_symbol: '(n)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'no_thumbs_down.gif" title="No, thumb down" align="absmiddle">'}, {vpb_smiley_symbol: '(y)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'yes_thumbs_up.gif" title="Yes, thumb up" align="absmiddle">'}, {vpb_smiley_symbol: '(^^^)', vpb_smiley_im: '<img class="vchatSmiley" src="' + vpb_smiley_dir + 'shark.gif" title="Shark" align="absmiddle">'}, {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}, {vpb_smiley_symbol: "=P", vpb_smiley_im: "tongue"}];
    for (var i = 0; i < vpb_a_smilies.length; i++) {
        vpb_chat_text_conversion = vpb_chat_text_conversion.replace (vpb_a_smilies[i].vpb_smiley_symbol, vpb_a_smilies[i].vpb_smiley_im);
    }
    return vpb_chat_text_conversion;
}

/* Add links to chat URLs */
function vchat_create_chat_links (vpb_chat_text)
{
    var vpb_converted_links, vpb_https_http_ftp_links, vpb_www_links, vpb_email_links;
    vpb_https_http_ftp_links = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    vpb_converted_links = vpb_chat_text.replace (vpb_https_http_ftp_links, '<a style="color: blue;" class="hover_text" href="$1" target="_blank">$1</a>');
    pb_www_links = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    vpb_converted_links = vpb_converted_links.replace (vpb_www_links, '$1<a style="color: blue;" class="hover_text" href="http://$2" target="_blank">$2</a>');
    vpb_email_links = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    vpb_converted_links = vpb_converted_links.replace (vpb_email_links, '<a style="color: blue;" class="hover_text" href="mailto:$1" target="_blank">$1</a>');
    return vpb_converted_links;
}


/* Add break to contents */
function vchat_newLine (vdata, vxml)
{
    var createNewLines = (vxml || typeof vxml === 'undefined') ? '<br ' + '/>' : '<br>';
    return (vdata + '').replace (/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + createNewLines + '$2');
}

function vchat_special_text (ivtech)
{
    var vpb_new_text = ivtech.replace (/\"/gi, "&quot;");
    vpb_new_text = vpb_new_text.replace (/\</gi, "&lt;");
    vpb_new_text = vpb_new_text.replace (/\>/gi, "&gt;");
    /*vpb_new_text = vpb_new_text.replace(/\ /gi,"&nbsp;");*/vpb_new_text = vpb_new_text.replace (/\Â¡/gi, "&iexcl;");
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

/* Show date and time sent */
function vchat_date_time (date)
{
    var vpb_getHour, vpb_am_or_pm;
    var vpb_getMinutes = ('0' + date.getMinutes ()).slice (-2);
    vpb_getHour = date.getHours ();
    if (vpb_getHour < 12)
    {
        vpb_am_or_pm = "am";
    }
    else
    {
        vpb_am_or_pm = "pm";
    }

    if (vpb_getHour === 0)
    {
        vpb_getHour = 12;
    }

    if (vpb_getHour < 10)
    {
        vpb_getHour = '0' + vpb_getHour;
    }

    if (vpb_getHour > 12)
    {
        vpb_getHour = vpb_getHour - 12;
    }

    return (date.getMonth () + 1) + "/" + date.getDate () + ', ' + vpb_getHour + ":" + vpb_getMinutes + "" + vpb_am_or_pm; /*date.getFullYear()*/
}

// Check if a chat session exit or not
function vpb_vchat_session_is_created (group_id)
{
    if (vchat_getcookie ('group_username' + group_id) != "" && vchat_getcookie ('group_username' + group_id) != null && vchat_getcookie ('group_username' + group_id) != undefined)
    {
        return true;
    }
    else
    {
        return false;
    }
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


// Open new or continue with previous chat
function vpb_open_vchat (session_uid, fullname, username, photo)
{
    if ($ (".vpb_vchat_box[username=" + username + "]").length > 0)
    {
        return false;
    }

    vpb_open_new_vchat_box (username);

    var group_id = $ ("#vchat_group_sid").val ();


    if (group_id != "" && group_id != null && group_id != undefined && fullname != "" && username != "")
    {
        vpb_vchat_add_new_user_to_group (group_id, fullname, username, photo);
        vpb_show_conversation_box (group_id);
        vchat_removecookie ('compose_new_message');
        $ ('#vpb_vchat_text' + group_id).focus ();
    }

    vchat_start_and_load_more_messages ('start', group_id, username);
}

function vpb_open_existing_chat_box (type)
{
    if ($ ("#vchat_is_disabled").val () == 1)
    {

        return false;
    }

    var chat_box_label = document.getElementById ('compose_new_message_text').value;

    var ignoreMessagesColl = $ (".vpb_friends_main_box").map (function ()
    {
        return $ (this).attr ("groupid");
    }).get ().join (",");

    group_uid = ignoreMessagesColl.split (/\,/);
    var total_users = parseInt (group_uid.length);

    for (var u = 0; u < parseInt (total_users); u++)
    {
        if (group_uid[u] != "")
        {
            var group_id = group_uid[u];

            if ($ (".vpb_friends_main_box[groupid=" + group_id + "]").length === 0 || $ ("#" + group_id).css ('display') == "none")
            {
                vpb_display_html_contents (group_id, chat_box_label);

                // Display users newly added in a group conversation
                vchat_display_new_users_in_group (group_id);
                // Display users in current conversation group
                vchat_display_users_in_group (group_id, 'auto');

                vpb_min_and_max_chat_box (group_id, 'set'); // Set Min / Max

                popups.unshift (group_id);
                vpb_vchat_position_chat_boxes ();

                vchat_start_and_load_more_messages ('start', group_id);

                $ ('#vpb_vchat_text' + group_id).focus ();
                vchat_removecookie ('compose_new_message');
            }
            else
            {
            } // Do not open chat window which is already open again
        }
    }
}

// Load conversations when called
function vchat_start_and_load_more_messages (type, group_id, username)
{
    username = username || group_id;

    if ($ ("#vchat_is_disabled").val () == 1)
        return false;

    $ ("#vpb_is_process_running" + username).val (1);
    if (type == "start")
    {
        $ ("#vpb_start" + group_id).val (0);
    }
    else
    {
    }

    var vpb_start = $ ("#vpb_start" + group_id).val (); //Where to start loading the updates
    var vpb_total_per_load = $ ("#vpb_vchat_total_per_load").val (); //Total messages to load each time
    var session_uid = $ ("#vchat_sessions_uid").val (); //The username of the current logged in user


    var dataString = {"session_username": session_uid, "group_id": username, "vpb_start": vpb_start, "type": type, "vpb_total_per_load": vpb_total_per_load, "last_message_id": last_message_id, "page": "vpb_load_conversations"};

    vpb_running_process = true; //prevent further ajax loading

    var last_message_id = vchat_trim ($ ("#last_message_id" + group_id).val ()) != "" && vchat_trim ($ ("#last_message_id" + group_id).val ()) != undefined ? vchat_trim (vchat_strip_tags ($ ("#last_message_id" + group_id).val ())) : "0";

    if (type == "start")
    {
        //$ ("#vpb_display_conversations" + group_id).html ('');
        $ ("#vpb_loading_group_messages" + group_id).html ($ ("#vchat_loading_image_gif").val ());
    }
    else if (type == "auto-load")
    {
        $ ("#vpb_loading_group_messages" + group_id).addClass ('vpb_more_messages_loading').html ($ ("#vchat_loading_btn").val ());
        //return false;
    }
    else
    {
    }

    $.post ('/blab/chat/loadMessages', dataString, function (response)
    {
        var vchat = JSON.parse (response); // this contains the json data from the php file

        if (vchat.data.message_id)
        {
            $ ("#see_full_conversation_button" + group_id).show ();

            //Hide the loading image after loading data onto the page
            $ ("#vpb_loading_group_messages" + group_id).removeClass ('vpb_more_messages_loading').html ('');

            if (type == "start")
            {
                $ ("#vpb_display_conversations" + group_id).html (vchat.data.message);

                $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);

                $ ("#vpb_start" + group_id).val (parseInt (vpb_start) + parseInt (vpb_total_per_load));

                // Update the message just displayed to read
                var dataString = {"username": $ ("#vchat_sessions_uid").val (), "group_id": group_id, "page": "set_messages_to_read"};
//                $.ajax ({
//                    type: "POST",
//                    url: vpb_site_url + 'vchat-processor.php',
//                    data: dataString,
//                    beforeSend: function ()
//                    {},
//                    success: function (response)
//                    {}
//                });

                $ ("#last_message_id" + group_id).val (vchat.message_id);
                $ ("#vtotal_group_messages" + group_id).val (vchat.total_group_messages);
                $ ("#seen_message_notification_box" + group_id).hide ();
            }
            else if (type == "auto-load")
            {
                $ ("#vpb_start" + group_id).val (parseInt (vpb_start) + parseInt (vpb_total_per_load));

                $ ("#vpb_display_conversations" + group_id).prepend (
                        $ (vchat.message).hide ().fadeIn ('slow')
                        );

                setTimeout (function ()
                {
                    $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: 100}, 1000);
                }, 100);
            }
            else
            {
                $ ("#vpb_display_conversations" + group_id).append (vchat.message);

                setTimeout (function ()
                {
                    $ ("#vpb_chat_content_box_" + group_id).animate ({scrollTop: parseInt ($ ('#vpb_chat_content_box_' + group_id).height ()) + 10000}, 1000);
                }, 100);

                // Set message for current user to read
                var dataString = {"username": $ ("#vchat_sessions_uid").val (), "group_id": group_id, "last_message_id": last_message_id, "page": "update_last_message_to_read"};
//                $.ajax ({
//                    type: "POST",
//                    url: vpb_site_url + 'vchat-processor.php',
//                    data: dataString,
//                    beforeSend: function ()
//                    {},
//                    success: function (response)
//                    {}
//                });
            }

            vpb_running_process = false;
            $ ("#vpb_is_process_running" + group_id).val (0);
            $ ("#seen_message_notification_box" + group_id).hide ();

            vchat_check_for_seen_message_notification (group_id);
        }
        else
        {

            $ ("#vpb_loading_group_messages" + group_id).removeClass ('vpb_more_messages_loading').html ('');
            vpb_running_process = false;
        }
    }).fail (function (xhr, ajaxOptions, theError)
    {
        $ ("#vpb_loading_group_messages" + group_id).removeClass ('vpb_more_messages_loading').html ('');
        $ ("#v-vchat-message").html ($ ("#vchat_general_systems_error").val ());
        $ ("#v-vchat-alert-box").click ();
        vpb_running_process = false;
        //return false;
    });


}

// Display all newly added users in a group
function vchat_display_new_users_in_group (group_id)
{
    if (vpb_vchat_session_is_created (group_id))
    {
        var vpb_group_users_fname = new Array ();
        var vpb_group_users_name = new Array ();
        var vpb_group_users_photo = new Array ();

        vpb_group_users_fname = vchat_getcookie ('group_fullname' + group_id).split (/\,/);
        vpb_group_users_name = vchat_getcookie ('group_username' + group_id).split (/\,/);
        vpb_group_users_photo = vchat_getcookie ('group_photo' + group_id).split (/\,/);

        $ ("#vpb_added_users_box" + group_id).html ('');

        for (var u = 0; u < vpb_group_users_name.length; u++)
        {
            if (vpb_group_users_name[u] != "")
            {
                var fullname = vpb_group_users_fname[u];
                var username = vpb_group_users_name[u];
                var photo = vpb_group_users_photo[u];

                $ ("#vpb_added_users_box" + group_id).append ('<span id="vpb_new_user_in_group_' + username + group_id + '"><span class="vpb_added_users_to_com">' + fullname + ' <i class="fa fa-times-circle hoverings" onclick="vchat_remove_new_user_from_group(\'' + group_id + '\', \'' + fullname + '\', \'' + username + '\', \'' + photo + '\')"></i></span></span>');
            }
            else
            {
            }
        }
        setTimeout (function ()
        {
            $ ("#v_added_u_sers" + group_id).animate ({scrollTop: parseInt ($ ('#v_added_u_sers' + group_id).height ()) + 10000}, 1000);
        }, 1000);
    }
    else
    {
    }
}

// Load users in conversation when mouseover and when mouseout
var vpb_user_loader_timer = 600,
        vpb_time_out = null;

function vchat_get_user_onmouseover_data (group_id)
{
    if ($ ("#vpb_vchat_smiley_box_" + group_id).css ('display') == 'none' && $ ("#v_vchat_users_box" + group_id).css ('display') == 'none')
    {
        if (vpb_time_out)
            clearTimeout (vpb_time_out);
        $ ("#v_vchat_users_in_list" + group_id).fadeIn ();
    }
    else
    {
        vchat_get_user_mouseout_data (group_id);
    }
}

function vpb_set_selected_chat_option (selected_option)
{

    if (selected_option == "Off")
    {
        $ (".headers").css ("opacity", "0.6");

        window.clearInterval (vx_run);
        vx_run = 0;
        vx_interval = 10000;
        total_pushed = 1;
        heartbeat = 1;

        $ ("#vpb_counter_result").html ('Off');
        $ ("#counter_online_users").val ('Off');
        $ ("#vchat_loading_type").val ('users');
        $ (".vpb_vchat_box").hide ();
        $ ("#vpb_vchat_hidden_boxes").hide ();
        $ ("#vchat_cother_users").hide ();
        $ ("#vchat_prev_cons").show ();

        document.getElementById ('vchat_users_loaded').value = '0';
        vpb_load_vchat_friends_box ('off');
    }
    else
    {
        $ (".headers").css ("opacity", "1");

        alert ("66");

        $ ("#vchat_is_disabled").val ('0');

        $ (".vpb_vchat_box").show ();
        $ ("#vpb_vchat_hidden_boxes").show ();

        vx_interval = 10000;
        total_pushed = 1;
        heartbeat = 1;
        vx_run = window.setInterval (vchat_run_system_updates, vx_interval); // start the setInterval()
    }

    $ (".vpb_chat_option_boxes_icons").hide ();
    $ ("#vpb_c_" + selected_option).fadeIn ();
    $ (".vpb_chat_options_box").hide ();

    var dataString = {'page': 'vpb_update_chat_status', 'from_username': $ ("#vchat_sessions_uid").val (), 'chat_status': selected_option};

//    $.post (vpb_site_url + 'vchat-processor.php', dataString, function (response)
//    {
    // Load chat users
    document.getElementById ('vchat_users_loaded_last').value = 'loading';
    vpb_load_vchat_friends_box ('auto');

    if (selected_option == "Off")
    {
        $ ("#vchat_is_disabled").val ('1');
    }
    else
    {
        $ ("#vchat_is_disabled").val ('0');
    }
//
//    }).fail (function ()
//    {
//        setTimeout ("vpb_set_selected_chat_option('" + selected_option + "');", 1000);
//    });
}


function vchat_get_user_mouseout_data (group_id)
{
    if (vpb_time_out)
        clearTimeout (vpb_time_out);

    vpb_time_out = setTimeout (function ()
    {
        $ ("#v_vchat_users_in_list" + group_id).css ('display', 'none');
    }, parseInt (vpb_user_loader_timer));
}



//Show/Hide Smiley Box
function vpb_vchat_add_smiley_box (group_id)
{
    var vpb_box_state = $ ('#vpb_vchat_smiley_box_' + group_id).css ('display');
    if (vpb_box_state == 'block')
    {
        $ ('#vpb_vchat_smiley_box_' + group_id).fadeOut ();
    }
    else
    {
        $ ('.vpb_vchat_smiley_box_wrapper').hide ();
        $ ('#vpb_vchat_smiley_box_' + group_id).fadeIn ();
    }
}
//Add smiley to chat box when clicked on
function vpb_vchat_add_chat_smiley_a (group_id, smiley)
{
    var old_chat_message = $ ('#vpb_vchat_text' + group_id).val ();
    if (old_chat_message == "")
    {
        $ ('#vpb_vchat_text' + group_id).focus ();
        $ ('#vpb_vchat_text' + group_id).val (smiley);
    }
    else
    {
        $ ('#vpb_vchat_text' + group_id).focus ();
        $ ('#vpb_vchat_text' + group_id).val (old_chat_message + ' ' + smiley);
    }
}

// Refresh the Functions that check for update
var vx_interval = 10000; // initial condition
var total_pushed = 1;

$ (function ()
{
    vpb_site_url = $ ("#vchat_website_link").val ();//window.location.protocol + "//" + window.location.host + "/";
    vx_run = $ ("#vchat_is_disabled").val () == 1 ? 0 : window.setInterval (vchat_run_system_updates, vx_interval);

    setTimeout (function ()
    {
        vpb_site_url = $ ("#vchat_website_link").val () != undefined ? $ ("#vchat_website_link").val () : '';
    }, 1000);
});

$ (function ()
{
    setTimeout (function ()
    {
        vpb_open_existing_chat_box ('start');

//        $ ('.vasplus_tooltip_icons').tipsy ({fade: true, gravity: 'sw'});
//        $ ('.vasplus_tooltip_icon').tipsy ({fade: true, gravity: 'se'});
//        $ ('.vasplus_tooltip_photo_e').tipsy ({fade: true, gravity: 'e'});


        $ (document).on ("click", function (e)
        {
            var $clicked = $ (e.target);
            if (!$clicked.parents ().hasClass ("dropdown-menu") && !$clicked.parents ().hasClass ("vpb_vchat_box") && $clicked.attr ('id') != "v_suggested_users_box")
            {
                $ (".vchat_suggested_to_u_box").hide ();
            }
        });

        $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'hidden');
        $ ('.vpb_chat_friends_box_class').on ('mouseenter mouseleave', function (vpb_evt)
        {
            if (vpb_evt.type == "mouseenter")
            {
                $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'auto');
            }
            else
            {
                $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'hidden');
            }
        });

        $ ('#vasplus_chat_users_search').on ('click', function ()
        {
            $ (".vpb_chat_options_box").hide ();
        });


        //To detect the div scroll so as to auto load conversations
        $ ('.vpb_chat_content_box').scroll (function (e)
        {
            var group_id = $ (this).attr ('id').replace ('vpb_chat_content_box_', '');

            // Check if other processes are running then pause
            if (parseInt ($ ("#vpb_is_process_running" + group_id).val ()) > 0)
            {
            }
            else
            {
                //The current user has scrolled to the top of the current div
                if (parseInt ($ ("#vpb_chat_content_box_" + group_id).scrollTop ()) == 0)
                {
                    if (parseInt ($ ("#vtotal_group_messages" + group_id).val ()) > parseInt ($ ("#vpb_vchat_total_per_load").val ()))
                    {
                        var vpb_start = $ ("#vpb_start" + group_id).val (); //Where to start loading the updates
                        //There are still more records to load
                        if (parseInt (vpb_start) <= parseInt ($ ("#vtotal_group_messages" + group_id).val ()) && vpb_running_process == false)
                        {
                            vchat_start_and_load_more_messages ('auto-load', group_id);
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

                var theHeight = parseInt (document.getElementById ("vpb_chat_content_box_" + group_id).scrollHeight) - parseInt (document.getElementById ("vpb_chat_content_box_" + group_id).scrollTop)

                if (parseInt (theHeight) < parseInt ($ (this).height ()) + 10)
                {
                    $ ("#seen_message_notification" + group_id).fadeIn ('slow');
                }
                else
                {
                    $ ("#seen_message_notification" + group_id).hide ();
                }
            }
        });

        vpb_session_photo = vpb_site_url + $ ("#vpb_session_uphoto").val ();
        vpb_session_fname = $ ("#session_fname").val ();
        vpb_smiley_dir = vpb_site_url + $ ("#vpb_smileys_dir_path").val ();

    }, 4000);

// Display the chat sidebar
    if ($ ("#vpb_chat_main_box").length > 0 || $ ("#vchat_is_disabled").val () == 1)
    {
    }
    else
    {
        $ ('<div>\
	\
	<div id="vpb_chat_main_box" class="vpb_chat_main_box_wrapper">\
	<div id="vpb_headers" class="headers"><span class="headers_aaa"><span onclick="vpb_hide_chat_sidebar(\'hide\');" style="cursor:pointer;">' + $ ("#vchat_side_bar_title_text").val () + '</span></span><span class="headers_bbb">\
	\
	<span class="vasplus_tooltip_icon" original-title="' + $ ("#vchat_new_message_text").val () + '"><i class="fa fa-pencil-square-o" aria-hidden="true" style="cursor:pointer;" onClick="vpb_open_new_vchat_box();" id="vchat_open_new_chat_window"></i></span>&nbsp;&nbsp;\
	\
	<span id="vchat_prev_cons" class="vasplus_tooltip_icon" original-title="' + $ ("#vchat_conversations_text").val () + '"><i class="fa fa-envelope-o" aria-hidden="true" style="cursor:pointer;" onclick="vpb_load_vchat_prev_message_box(\'previous\');"></i></span>\
	\
	<span id="vchat_cother_users" style="display:none;" class="vasplus_tooltip_icon" original-title="' + $ ("#vchat_users_online_text").val () + '"><i class="fa fa-users" aria-hidden="true" style="cursor:pointer;" onclick="vpb_load_vchat_friends_box(\'users\');"></i></span>\
	\
	</span> <div style="clear:both;"></div></div>\
	\
	<div id="vpb_chat_friends_box" class="vpb_chat_friends_box_class"></div>\
	\
	<div id="main_chat_bottom_box"><div style="padding:5px;" id="vpb_search_box_wrap" class="vpb_search_box_wrapper">\
	\
	<span class="vpb_chat_search" style="margin-top:3px;" onclick="vpb_chat_focus_search_box();"></span>\
	\
	 <input type="text" id="vasplus_chat_users_search" placeholder="' + $ ("#vchat_search_place_holder_chat_text").val () + '" />\
	 \
	 <span id="vpbchatoptionandbar">\
	 <span class="vpb_chat_hide_sidebar_and_options_box vasplus_tooltip_icon" original-title="' + $ ("#vchat_hide_side_bar_text").val () + '" onclick="vpb_hide_chat_sidebar(\'hide\');">\
	 <span class="vpb_chat_hide_sidebar_icons"></span>\
	</span>\
	<span class="vpb_chat_hide_sidebar_and_options_box vasplus_tooltip_icon" original-title="' + $ ("#vchat_options_text").val () + '" onclick="vpb_chat_option_boxed();"><span class="vpb_chat_options_icons"></span></span></span>\
	</div>\
	</div>\
	</div>\
	\
	<div class="vpb_chat_options_box">\
	<div id="vpb_chat_options_inner_box" align="left" onclick="vpb_set_selected_chat_option(\'Online\');">\
	<div style="float:left; width:25px" align="left">\
	<span id="vpb_c_Online" class="vpb_chat_option_boxes_icons"></span>&nbsp;\
	</div><div style="float:left;">' + $ ("#vchat_set_status_to_online").val () + '</div><br clear="all" />\
	</div>\
	\
	<div id="vpb_chat_options_inner_box" align="left" onclick="vpb_set_selected_chat_option(\'Busy\');">\
	<div style="float:left; width:25px" align="left">\
	<span id="vpb_c_Busy" class="vpb_chat_option_boxes_icons"></span>&nbsp;\
	</div><div style="float:left;">' + $ ("#vchat_set_status_to_busy").val () + '</div><br clear="all" />\
	</div>\
	\
	<div id="vpb_chat_options_inner_box" align="left" onclick="vpb_set_selected_chat_option(\'Off\');">\
	<div style="float:left; width:25px" align="left">\
	<span id="vpb_c_Off" class="vpb_chat_option_boxes_icons"></span>&nbsp;\
	</div><div style="float:left;">' + $ ("#vchat_turn_off_chat_text").val () + '</div><br clear="all" />\
	</div>\
	\
	</div>\
	\
	<div class="vpb_chat_main_box_b_wrapper" onclick="vchat_show_main_box_b();" id="vpb_chat_main_box_b"><span class="vpb_bottom_chat_icon"></span> <span style="float:left; margin-left:5px; padding-bottom:2px; color:#666;">' + $ ("#vchat_side_bar_title_text").val () + ' (<span id="vpb_counter_result">0</span>)</span><div style="clear:both;"></div></div><div id="vpb_chats_heart_beat" style="display:none;"></div><input type="hidden" id="search_stop_loading" value="" /><input type="hidden" id="process_is_running" value="" /><input type="hidden" id="counter_online_users" value="" /><input type="hidden" id="username_of_last_user_saved" value="" /><input type="hidden" id="vchat_users_loaded" value="0" /><input type="hidden" id="vchat_users_loaded_last" value="" /><input type="hidden" id="vchat_group_sid" value="" /><input type="hidden" id="vchat_loading_type" value="users" /><span id="chat_texting"></span>\
	\
	<div id="vpb_vchat_hidden_boxes" class="vchat_hidden_boxes">\
	\
	<div onclick="vpb_hide_vchat_hidden_vtop();" id="vchat_hidden_vtop" style="display:none;" class="btn btn-default btn btn-success btn-pms-continue">' + $ ("#vchat_hidden_vbutton").val () + '</div>\
	\
	<div id="vchat_hidden_vbottom" class="vchat_hidden_vbottom">\
	<div onclick="vpb_hide_vchat_hidden_vbottom();" id="vchat_hidden_cbutton" class="vchat_hidden_cbutton">' + $ ("#vchat_hidden_hbutton").val () + '</div>\
	\
	<div class="vchat_hidden_header">' + $ ("#vchat_side_bar_title_text").val () + '</div>\
	\
	<div class="vchat_hidden_wrapper" id="vchats_hidden_wrapper"></div>\
	</div><input type="hidden" id="vchat_hidden_boxes_status" value="0" />\
	\
	</div>\
	<input type="hidden" id="search_stop_loading" value="" /><div style="display:none;" id="vasplusChatAudio"></div>\
	</div>').appendTo ($ ("body"));
    }

//Auto check user sidebar settings

    if ($ ("#vchat_is_disabled").val () == 1)
    {
        return false;
    }
    else
    {
        $.post ('/blab/chat/getChatFriends', {}, function (response)
        {
            $ ("#vpb_chat_friends_box").html (response);

            setTimeout (function ()
            {
                $ ("#vpb_chat_main_box_b").hide ();
                $ ("#vpb_chat_main_box").show ();
            }, 2000);
        });
    }

    $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'hidden');
    $ ('.vpb_chat_friends_box_class').on ('mouseenter mouseleave', function (vpb_evt)
    {
        if (vpb_evt.type == "mouseenter")
        {
            $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'auto');
        }
        else
        {
            $ ('.vpb_chat_friends_box_class').css ('overflow-y', 'hidden');
        }
    });
    $ ('#vasplus_chat_users_search').on ('click', function ()
    {
        $ (".vpb_chat_options_box").hide ();
    });
    // Auto search for users
    var vchat_timer;
    var vchat_x;
    $ ('#vasplus_chat_users_search').keyup (function ()
    {
        var searchTerm = $ (this).val ();
        if (vchat_x)
        {
            vchat_x.abort ()
        } // If there is an existing XHR, abort it.
        clearTimeout (vchat_timer); // Clear the timer so we don't end up with dupes.
        vchat_timer = setTimeout (function ()
        { // assign timer a new timeout 

            vchat_x = vpb_search_friends_sidebar (searchTerm);
        }, 1000); // 2000ms delay, tweak for faster/slower
    });
});
