var selectedImageType = "basic";
var selectedImageId = null;

function readURL2 (input)
{

    if (input.files && input.files[0])
    {
        var reader = new FileReader ();

        reader.onload = function (e)
        {
            $ ('#timelineProfilePic > img').attr ('src', e.target.result).width ('100%');

        };

        reader.readAsDataURL (input.files[0]);
    }
}

function readURL (input)
{

    if (input.files && input.files[0])
    {
        var reader = new FileReader ();

        reader.onload = function (e)
        {
            $ ('#timelineBackground > img').attr ('src', e.target.result);
            $ ('#timelineBackground > img').attr ('id', 'timelineBGload');
            $ ('#timelineBackground > img').addClass ("headerimage");
            $ ('#timelineBackground').prepend ('<div id="uX1" class="bgSave wallbutton blackButton">Save Cover</div>');
            $ ("#timelineShade").hide ();
            $ ("#bgimageform").hide ();
        };

        reader.readAsDataURL (input.files[0]);
    }
}

//                                $ (document).ready (function ()
//                                {
$ ('.cd-panel-close').on ('click', function (event)
{

    $ ('.cd-panel').removeClass ('is-visible');
    $ ("#page-wrapper").fadeIn ();
    event.preventDefault ();

});

function shakeIt ()
{
    $ ('#shake-it').effect ('shake');
}

$ (".menuItem").on ("click", function ()
{
    //$(this).parent().parent().hide();
    //$("#drop1").removeAttr("checked");

    var type = $ (this).attr ("type");

    switch (type) {

        case "groups":

            var strUrl = "/blab/group/getAllGroups";
            var el = $ ("#vasplus_groups");
            break;

        case "events":
            var strUrl = "/blab/event/getAllEvents";
            var el = $ ("#vasplus_events");
            break;

        case "pages":
            var strUrl = "/blab/page/getAllPages";
            var el = $ ("#vasplus_pages");
            break;

        case "likes":
            var strUrl = "/blab/like/getAllLikes";
            var el = $ ("#vasplus_likes");
            break;
    }

    var userId = $ ("#userId").val ();

    if (!userId)
    {
        alert ("Invalid user found");
        return false;
    }

    $.ajax ({
        url: strUrl + '?userId=' + userId,
        type: 'GET',
        success: function (response)
        {
            try {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    showErrorMessage (objResponse.message);
                }
            } catch (error) {
                hideElements ();
                el.find (".text-wrapper").html (response);
                el.show ();
            }


        }, error: function (e)
        {
            showErrorMessage ();

        }
    });

    event.preventDefault ();

});

function hideElements ()
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
}

$ (".timelineUploadBG").on ("click", function ()
{
    $ ("#bgphotoimg").change ();
});

/* Uploading Profile BackGround Image */
$ ('body').on ('change', '#bgphotoimg', function ()
{

    var input = $ ("#bgphotoimg");

    readURL (this);
});

$ (".editlink").on ("click", function (e)
{
    e.preventDefault ();

    var type = $ (this).attr ("type");

    var dataset = $ (this).prev (".datainfo");
    var savebtn = $ (this).next (".savebtn");
    var cancelbtn = $ (this).next ().next (".cancel");
    var theid = dataset.attr ("id");
    var newid = theid + "-form";
    var currval = dataset.text ();

    dataset.empty ();

    switch (type) {
        case "phone":

            currval = currval.split ('|');

            $ ('<input style="padding:10px;" type="text" name="telephone1-form" id="telephone1-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
            $ ('<input style="padding:10px;" type="text" name="telephone2-form" id="telephone2-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
            break;

        case "name":

            currval = currval.split ('|');

            $ ('<input style="padding:10px;" type="text" name="firstname-form" id="firstname-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
            $ ('<input style="padding:10px;" type="text" name="lastname-form" id="lastname-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
            break;

        case "address":
            currval = currval.split ('|');

            $ ('<input style="padding:10px;" type="text" name="town-form" id="town-form" value="' + currval[0] + '" class="hlite">').appendTo (dataset);
            $ ('<input style="padding:10px;" type="text" name="address-form" id="address-form" value="' + currval[1] + '" class="hlite">').appendTo (dataset);
            $ ('<input style="padding:10px;" type="text" name="postcode-form" id="postcode-form" value="' + currval[2] + '" class="hlite">').appendTo (dataset);
            break;

        default:
            $ ('<input type="text" name="' + newid + '" id="' + newid + '" value="' + currval + '" class="hlite">').appendTo (dataset);
            break;
    }

    $ (this).css ("display", "none");
    savebtn.css ("display", "block");
    cancelbtn.css ("display", "block");
});

$ (".cancel").on ("click", function ()
{

    var elink = $ (this).prev ().prev (".editlink");
    $ (this).prev ().css ("display", "none");
    var dataset = elink.prev (".datainfo");
    var newid = dataset.attr ("id");

    var cinput = "#" + newid + "-form";
    var einput = $ (cinput);

    var txtData = [];

    elink.parent ().find ("input").each (function ()
    {

        txtData.push ($ (this).val ());

    });

    dataset.html (txtData.join (" | "));

    $ (this).css ("display", "none");
    elink.parent ().find ("input").remove ();
    elink.css ("display", "block");

});

$ (".savebtn").on ("click", function (e)
{
    e.preventDefault ();
    var elink = $ (this).prev (".editlink");
    var dataset = elink.prev (".datainfo");
    $ (this).prev ().css ("display", "block");
    var newid = dataset.attr ("id");
    $ (this).next ().css ("display", "none");

    var txtData = [];
    var numeric = {};

    elink.parent ().find ("input").each (function ()
    {
        numeric[this.name] = this.value;
        txtData.push ($ (this).val ());
        $ (this).remove ();

    });

    $.ajax ({
        url: '/blab/user/updateProfileData',
        type: 'POST',
        data: numeric,
        success: function (response)
        {
            formatResponse (response, 'Your profile information has been updated successfully.');
        }, error: function (e)
        {


        }
    });

    dataset.html (txtData.join (" | "));

    $ (this).css ("display", "none");
});

$ ('body').on ('click', '.userProfileImg', function ()
{
    $ ("#profileimgaa").click ();
});

$ ("#profileimgaa").off ();
$ ("#profileimgaa").on ("change", function ()
{
    $ ("#profileimgaa").hide ();
    readURL2 (this);
    $ ("#userImage").submit ();
});

$ ("#userImage").on ("submit", function ()
{
    //stop submit the form, we will post it manually.
    event.preventDefault ();
    // Get form
    var form = $ ('#userImage')[0];
    // Create an FormData object
    var data = new FormData (form);

    // If you want to add an extra field for the FormData
    // disabled the submit button
    $.ajax ({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "/blab/user/uploadProfile",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function (response)
        {

            alert (response);
            return false;
        },
        error: function (e)
        {


        }
    });

    return false;
});



/* Banner position drag */
$ ("body").on ('mouseover', '.headerimage', function ()
{
    var y1 = $ ('#timelineBackground').height ();
    var y2 = $ ('.headerimage').height ();
    $ (this).draggable ({
        scroll: false,
        axis: "y",
        drag: function (event, ui)
        {
            if (ui.position.top >= 0)
            {
                ui.position.top = 0;
            }
            else if (ui.position.top <= y1 - y2)
            {
                ui.position.top = y1 - y2;
            }
        },
        stop: function (event, ui)
        {
        }
    });
});

$ ("body").on ('click', '.bgSave', function ()
{
    $ ("#bgimageform").submit ();
});

$ ("#bgimageform").on ("submit", function ()
{
    var p = $ ("#timelineBGload").attr ("style");
    var Y = p.split ("top:");
    var Z = Y[1].split (";");
    var dataString = '&position=' + Z[0];

    $ ("#position").val ($ (".bgImage").offset ().top);

    //stop submit the form, we will post it manually.
    event.preventDefault ();
    // Get form
    var form = $ ('#bgimageform')[0];
    // Create an FormData object
    var data = new FormData (form);

    console.log (data);

    // If you want to add an extra field for the FormData
    // disabled the submit button
    $.ajax ({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "/blab/user/saveBackgroundImage",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data)
        {

            $ (".bgImage").fadeOut ('slow');
            $ (".bgSave").fadeOut ('slow');
            $ ("#timelineShade").fadeIn ("slow");
            // $ ("#timelineBGload").removeClass ("headerimage").css ({'margin-top': data});
            $ ("#timelineBackground").html (data);
            return false;
        },
        error: function (e)
        {


        }
    });

    return false;
});
