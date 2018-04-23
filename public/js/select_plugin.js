/********************************************************************************
 * Social Network Website
 * Written by Vasplus Programming Blog
 * Website: www.vasplus.info
 * Email: vasplusblog@gmail.com OR info@vasplus.info
 
 *********************************Copyright Info***********************************
 * This is a paid script and must not be sold by any client
 * Please do not remove this copyright information from the top of this page
 * All Copy Rights Reserved by Vasplus Programming Blog
 ***********************************************************************************/

function vasplus_create_drop_down_box ()
{

    $ ("select").each (function ()
    {
        var vasplus_select_box = $ (this).attr ('id');

        if (vasplus_select_box == "vpb_heard" || vasplus_select_box == "vpb_country" || vasplus_select_box == "egender" || vasplus_select_box == "eday" || vasplus_select_box == "emonth" || vasplus_select_box == "eyear" || vasplus_select_box == "elanguage" || vasplus_select_box == "ecountries" || vasplus_select_box == "skills" || vasplus_select_box == "question" || vasplus_select_box == "etitle" || vasplus_select_box == "category" || vasplus_select_box == "sub_category" || vasplus_select_box == "payment_methods" || vasplus_select_box == "listing_type" || vasplus_select_box == "job_type" || vasplus_select_box == "job_skills" || vasplus_select_box == "fsubject" || vasplus_select_box == "project_duration" || vasplus_select_box == "hire_project_duration" || vasplus_select_box == "admin_status" || vasplus_select_box == "percentage_fee_status" || vasplus_select_box == "total_fee_status" || vasplus_select_box == "my_timezone")
        {
        }
        else
        {
            var source = $ ("#" + vasplus_select_box);
            //source.css('display', 'none');

            var selected = source.find ("option[selected]").html () !== undefined ? source.find ("option[selected]") : source.find ("option:first-child");

            var targeted_id = "target-" + vasplus_select_box;

            $ ('<dl id="' + targeted_id + '" class="vasplus_drop_down_box"></dl>').insertAfter (source);

            var vpb_options = $ ("option", source);

            $ ("#" + targeted_id).append ('<dt><a href="javascript:void(0);" id="' + vasplus_select_box + '_dt">' + selected.text () + '<span class="value">' + selected.val () + '</span></a></dt>')
            $ ("#" + targeted_id).append ('<dd><ul id="' + vasplus_select_box + '_ul"></ul></dd>')

            vpb_options.each (function ()
            {
                $ ("#" + targeted_id + " dd ul").append ('<li><a href="javascript:void(0);" id="' + vasplus_select_box + '_dd">' + $ (this).text () + '<span class="value">' + $ (this).val () + '</span><input type="hidden" class="value" value="' + $ (this).val () + '"></a></li>');
            });
        }
    });
}

(function ($)
{
    vasplus_create_drop_down_box ();


    $ (".vasplus_drop_down_box dt a").click (function ()
    {
        var vasplus_select_box = $ (this).attr ('id').replace ('_dt', '');

        if ($ ("#" + vasplus_select_box + '_ul').css ('display') == 'none')
        {
            $ (this).css ('background-color', '#fff');
            $ (".vasplus_drop_down_box dd ul").hide ();
            $ ("#" + vasplus_select_box + '_ul').slideDown ('fast');
        }
        else
        {
            $ (this).css ('background-color', '#CCC');
            $ (".vasplus_drop_down_box dd ul").hide ();
            $ ("#" + vasplus_select_box + '_ul').slideUp ('fast');
        }

    });

    $ (document).bind ('click', function (e)
    {
        var $clicked = $ (e.target);
        if (!$clicked.parents ().hasClass ("vasplus_drop_down_box"))
            $ (".vasplus_drop_down_box dd ul").css ('display', 'none');

    });

    $ (".vasplus_drop_down_box dd ul li a").click (function ()
    {
        var text = $ (this).html ();
        var vasplus_select_box_id = $ (this).attr ('id');
        var source = vasplus_select_box_id.replace ('_dd', '');
        $ ("#" + source + "_dt").html (text);
        $ (".vasplus_drop_down_box dd ul").hide ();
        var final_source = $ ("#" + source);
        //final_source.val($(this).find("span.value").html());
        //alert(source);
        $ (".vasplus_drop_down_box dt a").css ('background-color', '#CCC');

        $ ("#" + source).val ($ (this).find ("input.value").val ()).change ();
        //$("#"+source).trigger('change');
        //doGTranslate(strip_tags($(this).find("span.value").html()));

        //$("#"+source).val(strip_tags($(this).find("span.value").html())).trigger('change');
        //alert($(this).find("input.value").val());

        //doGTranslate('en|en');
    });
}) (jQuery);