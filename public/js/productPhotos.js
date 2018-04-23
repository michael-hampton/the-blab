/********************************************************************************
 * Shopping Cart Version 6.0
 * Written by Vasplus Programming Blog
 * Website: www.vasplus.info
 * Email: vasplusblog@gmail.com OR info@vasplus.info
 
 *********************************Copyright Info***********************************
 * This is a paid script and must not be sold by any client
 * Please do not remove this copyright information from the top of this page
 * All Copy Rights Reserved by Vasplus Programming Blog
 ***********************************************************************************/
var currentIndex = 0,
        items = $ ('.vasplus_scroller_wrap div'),
        itemAmt = items.length;

function vcart_scroll_items ()
{
    var item = $ ('.vasplus_scroller_wrap div').eq (parseInt (currentIndex));
    items.hide ();
    //item.css('display','inline-block');
    item.fadeIn ('slow');
}

var autoSlide = setInterval (function ()
{
    currentIndex += 1;
    if (parseInt (currentIndex) > parseInt (itemAmt) - 1)
    {
        currentIndex = 0;
    }
    vcart_scroll_items ();
}, 4000);


$ (function ()
{
    $ ('#vcart_next').click (function ()
    {
        clearInterval (autoSlide);
        currentIndex += 1;
        if (parseInt (currentIndex) > parseInt (itemAmt) - 1)
        {
            currentIndex = 0;
        }
        vcart_scroll_items ();
    });

    $ ('#vcart_prev').click (function ()
    {
        clearInterval (autoSlide);
        currentIndex -= 1;
        if (parseInt (currentIndex) < 0)
        {
            currentIndex = itemAmt - 1;
        }
        vcart_scroll_items ();
    });
});


var photos = [];

function sendMessageToSeller ()
{
    var userId = $ ("#seller").val ();
    var message = $ ("#theMessage").val ();

    var dataString = {"userId": userId, "message": message, "productId": $ ("#productId").val ()};

    $.post ('/blab/product/sendMessageToSeller', dataString, function (response)
    {
        try {
            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                alert("Unable to send message");
            }
            else
            {
               alert("Message has been sent successfully");
            }
        } catch (error) {
            alert("Something went wrong whilst trying to send the message");
        }
    });
}

function vpb_show_product_photos (id, product_name, sellers_name, user_id, total_photos)
{
    var photo_array = $ ("#productsPics_" + parseInt (id)).val ();
    var productsDescription = $ ("#productsDescs_" + parseInt (id)).val ();

    photos = jQuery.parseJSON (photo_array);

    $ ("#vcartCurrentPicx").html ("");

    for (var u = 0; u < parseInt (total_photos); u++)
    {
        if (photos[u] != "")
        {
            $ ("#vcartCurrentPicx").append ("\
			<div style='display: none;'>\
			  <img src='" + photos[u] + "'/>\
			</div>");
        }
    }

    $ ('#vcart_prodname').html (product_name);
    $ ('#vcart_item_description').html (productsDescription);
    $ ("#sellersName").html (' Send private message to ' + sellers_name);
    $ ("#seller").val (user_id);
    $ ("#productId").val (id);

    if (parseInt (total_photos) == 1)
    {
        $ ('#vcart_next_or_prev').hide ();
        clearInterval (autoSlide);
    }
    else
    {
        $ ('#vcart_next_or_prev').show ();
    }

    //clearInterval(autoSlide);
    currentIndex = 0;

    setTimeout (function ()
    {
        items = $ ('.vasplus_scroller_wrap div'),
                itemAmt = parseInt (total_photos);

        vcart_scroll_items ();
        $ ('#show_product_details').modal ('show');

    }, 500);
}