// Site searching for friends
var completedSearchRequest = false;

function vpb_search_for_friends ()
{
    var friend = $ ("#vfrnd_data").val ();
    var session_uid = $ ("#session_uid").val ();

    if (friend != "")
    {
        $ ("#vpb_display_search_results").show ().html ('<ul id="v_search_results_box" class="dropdown-menu open bullet pull-center vasplus_bosy" style="right: auto; left: 0px; border-radius:0px; border-top:1px solid #E1E1E1; font-size:13px !important; font-family:arial !important;width:100%; display:block;padding:10px;" aria-labelledby="v_search_results_box"><li class="dropdown-header dropdown-header-plus">' + $ ("#v_loading_btn").val () + '</li></ul>');

        var dataString = {'friend': friend, 'username': session_uid, 'page': 'search_for_friends'};

        //if(completedSearchRequest == true) { return false; } else {}
        //completedSearchRequest = true;

        $.post ('/blab/index/search', dataString, function (response)
        {
            //completedSearchRequest = false;


            if (response == "")
            {
                $ ("#vpb_display_search_results").hide ();
            }
            else
            {
                $ ("#vpb_display_search_results").show(); 
                $ ("#vpb_display_search_results > ul > li").html (response);
            }


        }).fail (function (error_response)
        {
            setTimeout ("vpb_search_for_friends();", 10000);
        });

    }
    else
    {
        $ ("#vfrnd_data").focus ();
        $ ("#vpb_display_search_results").hide ();
        return false;
    }
}