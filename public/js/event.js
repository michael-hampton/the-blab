// Show report this group box
function vpb_shoq_report_this_event_box (fullname, username, email, group_id, group_name)
{
    $ ("#the_eventID").val (group_id);
    $ ("#the_eventNamed").val (group_name);
    $ ('#report-this-event').modal ('show');
}

// Conform before removing a friend from the tagged list
function vpb_confirm_delete_current_event_details (group_id, group_manager, group_name)
{
    $ ("#deventid").val (group_id);
    $ ("#deventmanager").val (group_manager);
    $ ("#deventname").val (group_name);
    $ ("#delete_event_message_text").html ($ ("#group_delete_message_text").val () + ' <b>' + group_name + '</b>?');
    $ ("#v-delete-this-event-alert-box").click ();
}

function vpb_delete_this_event_now ()
{
    var group_id = $ ("#deventid").val ();
    var group_manager = $ ("#deventmanager").val ();
    var group_name = $ ("#deventname").val ();

    var dataString = {"group_id": group_id, "group_manager": group_manager, "group_name": group_name, "page": "delete_this_group"};

    $.ajax ({
        type: "POST",
        url: '/blab/event/deleteEvent',
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
        setTimeout ("vpb_delete_this_event_now();", 10000);
    });
}

// Show all Page Group Members
function vpb_show_event_page_members ()
{
    $ (".event-details").hide ();
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

// Report a post
function vpb_report_the_event (session_fullname, session_username, session_email)
{
    var group_id = $ ("#the_eventID").val ();
    var group_name = $ ("#the_eventNamed").val ();

    var report_group_data = vpb_trim ($ ("#report_event_data").val ());

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

        $ ("#report-this-event").removeClass ('enable_this_box');
        $ ("#report-this-event").addClass ('disable_this_box');

        $.post ('/blab/event/reportEvent', dataString, function (response)
        {
            $ ("#report-this-event").removeClass ('disable_this_box');
            $ ("#report-this-event").addClass ('enable_this_box');

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
            setTimeout ("vpb_report_the_event('" + session_fullname + "', '" + session_username + "', '" + session_email + "');", 10000);
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function vpb_show_event_videos ()
{
    $ (".event-details").hide ();
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

/**
 * 
 * @param {type} the_group_name
 * @returns {undefined}
 */
function vpb_load_event_photos (the_group_name)
{

    $ (".event-details").hide ();
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


/**
 * 
 * @param {int} memberEventStatus
 * @param {int} newStatus
 * @returns {Boolean}
 */
function changeEventStatus (memberEventStatus, newStatus)
{
    var newStatus = parseInt (newStatus);

    if (!newStatus)
    {
        showErrorMessage ("Invalid status");
        return false;
    }

    var element = $ (".getStatusList[status='" + memberEventStatus + "']").find ("h2");

    if (!element)
    {
        showErrorMessage ("Invalid element");
        return false;
    }

    var currentCount = parseInt (element.html ());

    var newCount = currentCount - 1;
    element.html (newCount);

    var element2 = $ (".getStatusList[status='" + newStatus + "']").find ("h2");

    if (!element2)
    {
        showErrorMessage ("Unable to get current count 2a");
        return false;
    }

    var currentCount2 = parseInt (element2.html ());

    var newCount2 = currentCount2 + 1;
    element2.html (newCount2);

    return true;
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

function formatResponse (response, message)
{

    try {
        var objResponse = $.parseJSON (response);

        if (objResponse.sucess == 0)
        {
            showErrorMessage (objResponse.message);
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

    $ ('#eventcommentform').submit (handleEventSubmit);

    $ (".getStatusList").on ("click", function ()
    {
        var status = $ (this).attr ("status");

        if (!status)
        {
            alert ("Unable to get status");
            return false;
        }

        if (!groupId)
        {
            alert ("Unable to get event id");
            return false;
        }

        $.ajax ({
            url: '/blab/event/getEventStatusList',
            data: {"status": status, "eventId": groupId},
            type: 'POST',
            success: function (response)
            {
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage (objResponse.message);
                    }

                } catch (error) {
                    $ ("#eventListModal").find (".modal-body").html (response);
                    $ ("#eventListModal").modal ("show");
                }
            }
        });
    });

    $ (".eventResponse").on ("click", function ()
    {
        var status = $ (this).attr ("status");

        if (!status)
        {
            alert ("Unable to get status");
            return false;
        }

        if (!groupId)
        {
            alert ("Unable to get event id");
            return false;
        }

        $.ajax ({
            url: '/blab/event/updateEventStatus/' + status + '/' + groupId,
            type: 'GET',
            success: function (response)
            {
                changeEventStatus (memberEventStatus, status);
                showSuccessMessage ('Your response has been sent');
            }
        });

    });

    $ (document).off ("click", "#timelineProfilePic");
    $ (document).on ('click', '#timelineProfilePic', function ()
    {
        $ ("#profileimgaa").click ();

    });

    $ (".invitationWindow").on ("click", function ()
    {
        var groupId = $ (this).attr ("event-id");

        if (!groupId)
        {
            showErrorMessage ();
            return false;
        }

        $.ajax ({
            type: "GET",
            url: "/blab/event/showAllEventInvitations/" + groupId,
            success: function (response)
            {
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage (objResponse.message);
                    }
                } catch (error) {
                    $ ("#showAllEventInvitations").find (".modal-body").html (response);
                    $ ("#showAllEventInvitations").modal ("show");
                }

            },
            error: function (e)
            {

                alert ("Error");
            }

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
            url: "/blab/event/updateEventImage",
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

/**
 * 
 * @returns {Boolean}
 */
function handleEventSubmit ()
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
        "eventId": groupId,
        tags: tags
    };
    postEventComment (data);
    return false;
}

/**
 * 
 * @param {type} data
 * @returns {undefined}
 */
function postEventComment (data)
{

    // send the data to the server
    $.ajax ({
        type: 'POST',
        url: '/blab/event/postEventComment',
        data: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: postEventSuccess,
        error: postEventError
    });
}

/**
 * 
 * @param {type} data
 * @param {type} textStatus
 * @param {type} jqXHR
 * @returns {Boolean}
 */
function postEventSuccess (data, textStatus, jqXHR)
{
    $ ('#eventcommentform').get (0).reset ();
    load_unseen_notification ();

    $ ("#posts-list").prepend (data);
    return false;
}

/**
 * 
 * @param {type} jqXHR
 * @param {type} textStatus
 * @param {type} errorThrown
 * @returns {undefined}
 */
function postEventError (jqXHR, textStatus, errorThrown)
{
    // display error
    showErrorMessage (errorThrown);
}