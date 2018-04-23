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

// Get the full website URL
var vpb_site_url = window.location.href.substring (0, window.location.href.lastIndexOf ('/') + 1);


//Get basename of file
function v_basename (url)
{
    //return ((url=/(([^\/\\\.#\? ]+)(\.\w+)*)([?#].+)?$/.exec(url))!= null)? url[2]: '';
    return url.replace (/\\/g, '/').replace (/.*\//, '');
}

function vpb_trim (s)
{
    return s.replace (/^\s+|\s+$/, '');
}
// Generate Communication data
function vpb_generte_random ()
{
    return Math.random ().toString ().split ("0.").join ("1").toString ().split (".").join ("");
}

function vpb_auto_grow (o)
{
    //var height = o.style.height.replace('px', '');
    o.style.overflow = (o.scrollHeight > 200 ? "auto" : "hidden");
    o.style.height = "1px";
    setTimeout (function ()
    {
        o.style.height = (o.scrollHeight) + "px";
    }, 1);
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

//vpb_setcookie('myCookie', 'The value of the cookie', 7)
//var myCookie = vpb_getcookie('myCookie');
//vpb_removecookie('myCookie')



//this function can remove a array element.
Array.remove = function (array, from, to)
{
    var rest = array.slice ((to || from) + 1 || array.length);
    array.length = from < 0 ? array.length + from : from;
    return array.push.apply (array, rest);
};

//this variable represents the total number of popups can be displayed according to the viewport width
var total_popups = 0;

//arrays of popups ids
var popups = [];
var product_codes = [];
var product_name = [];
var product_price = [];
var product_image = [];
var product_color = [];
var product_qty = [];
var product_size = [];
var product_total_price = [];

// Remove an item from the cart array
function vchat_remove_data (arr, itemToRemove)
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

function vpb_formatDollar (num)
{
    var p = num.toFixed (2).split (".");
    return p[0].split ("").reverse ().reduce (function (acc, num, i, orig)
    {
        return  num + (i && !(i % 3) ? "," : "") + acc;
    }, "") + "." + p[1];
}

function vpb_formatDollars (num)
{
    return num
            .toFixed (2) // always two decimal digits
            .replace (".", ",") // replace decimal point character with ,
            .replace (/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + " â‚¬" // use . as a separator
}
function vpb_format_price (value)
{
    if (value == '')
    {
        value = 0;
    }
    var newValue = parseFloat (value).toFixed (2);
    // if new value is Nan (when input is a string with no integers in it)
    if (isNaN (newValue))
    {
        value = 0;
        newValue = parseFloat (value).toFixed (2);
    }
    // apply new value to element
    return newValue;
}

function vpb_close_added_item_notification_box ()
{
    $ ("#v_added_items_notification_box").fadeOut ();
}


// Display all newly added users in a group
function vpb_display_items_in_cart (action)
{
    if (vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')) == "" || vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')) == null || vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')) == undefined)
    {
        if (action == "auto")
        {
        }
        else if (action == "run")
        {
            $ ("#totalItemsInCart").html (0);
            $ ('#vpb_items_in_cart').modal ('hide');
            $ ("#v-cart-message").html ($ ("#no_more_item_in_shopping_cart_text_label").val ());
            $ ("#v-cart-alert-box").click ();
        }
        else
        {
            $ ("#totalItemsInCart").html (0);
            $ ('#vpb_items_in_cart').modal ('hide');
            $ ("#v-cart-message").html ($ ("#empty_shopping_cart_text_label").val ());
            $ ("#v-cart-alert-box").click ();
        }
        return false;
    }
    else
    {
        popups = vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')).split (/\,/);

        if (action == "auto")
        {
        }
        else
        {
            var total_items_in_cart = parseInt (popups.length);
            var the_sum = 0;
            var counted_a = counted_b = counted_c = counted_d = counted_e = counted_f = 1;

            $ ("#vcart_added_items_in_cart").html ('');

            var paypal_form = '<form id = "paypal_checkout" action = "https://www.paypal.com/cgi-bin/webscr" method = "post">';
            paypal_form = paypal_form + '<input name = "cmd" value = "_cart" type = "hidden">\
            <input name = "upload" value = "1" type = "hidden">\
            <input name = "no_note" value = "0" type = "hidden">\
            <input name = "bn" value = "PP-BuyNowBF" type = "hidden">\
            <input name = "tax" value = "0" type = "hidden">\
			<input name="charset" value="utf-8" type = "hidden">\
            <input name = "rm" value = "2" type = "hidden">\
			<input name = "cpp_header_image" value = "' + $ ("#the_website_logo_link").val () + '" type = "hidden">\
         														\
            <input name = "business" value = "' + $ ("#the_website_email").val () + '" type = "hidden">\
            <input name = "handling_cart" value = "0" type = "hidden">\
            <input name = "currency_code" value = "' + $ ("#currency_code").val () + '" type = "hidden">\
            <input name = "lc" value = "' + $ ("#country_code").val () + '" type = "hidden">\
            <input name = "cbt" value = "' + $ ("#return_to_site_text_label").val () + $ ("#the_website_name").val () + '" type = "hidden">\
			<input name = "return" value = "' + $ ("#payment_notifier_link").val () + '?data=' + MD5 ('vcsuccess') + '" type = "hidden">\
            <input name = "cancel_return" value = "' + $ ("#payment_notifier_link").val () + '?data=' + MD5 ('vccancel') + '" type = "hidden">\
			<input type="hidden" name="notify_url" value="' + $ ("#payment_notifier_link").val () + '?data=' + MD5 ('vcnotify') + '">\
            <input name = "custom" value = "' + $ ("#the_remote_address").val () + '" type = "hidden">';

            for (var u = 0; u < parseInt (total_items_in_cart); u++)
            {
                if (popups[u] != "")
                {
                    var product_codev = popups[u];
                    var product_namev = vpb_getcookie ('product_name' + product_codev);
                    var product_pricev = vpb_getcookie ('product_price' + product_codev);
                    var product_imagev = vpb_getcookie ('product_image' + product_codev);

                    var product_colorv = vpb_getcookie ('product_color' + product_codev);
                    var product_qtyv = vpb_getcookie ('product_qty' + product_codev);
                    var product_sizev = vpb_getcookie ('product_size' + product_codev);
                    var product_total_pricev = vpb_getcookie ('product_total_price' + product_codev);

                    $ ("#vcart_added_items_in_cart").append ('<div id="vcart_item_' + product_codev + '" class="vcart_items_wrap_d">\
					 <div class="vcart_items_wrap_e">\
					 <span style="float:left;" class="vcart_items_name">' + product_namev + '</span>\
					 <span class="vcart_remove_btn cbtn"><i title="' + $ ("#remove_item_from_cart_text_label").val () + '" class="fa fa-trash-o fa-fw vcart_remove_item vasplus_tooltip_menu" aria-hidden="true" onclick="vpb_remove_item_from_cart(\'' + product_codev + '\', \'clear-single\');"></i></span>\
					 <div style="clear:both;"></div>\
					 <span class="vcart_items_name_b">\
					 Quantity: ' + product_qtyv + '<br>\
					 Color: ' + product_colorv + '<br>\
					 Size: ' + product_sizev + '<br>\
					 </span>\
					 </div>\
					 <div class="vcart_items_wrap_f">\
					 <span class="product_price_text_label">' + $ ("#product_price_text_label").val () + ':</span> ' + $ ("#currency_symbol").val () + '' + accounting.formatMoney (product_total_pricev) + ' <span class="currency_code">' + $ ("#currency_code").val () + '</span>\
					 </div>\
					 <div style="clear:both;"></div>\
					 </div>');
                    the_sum += parseFloat (product_total_pricev);


                    paypal_form = paypal_form + '\
					 <div id = "item_' + counted_a++ + '" class = "itemwrap">\
						<input name = "item_name_' + counted_b++ + '" value = "' + product_namev + '" type = "hidden">\
						<input name = "item_number_' + counted_c++ + '" value = "' + product_codev + '" type = "hidden">\
						<input name = "quantity_' + counted_d++ + '" value = "' + product_qtyv + '" type = "hidden">\
						<input name = "amount_' + counted_e++ + '" value = "' + product_pricev + '" type = "hidden">\
						<input name = "shipping_' + counted_f++ + '" value = "0" type = "hidden">\
					</div>';
                }
            }

            paypal_form = paypal_form + '<input id="ppcheckoutbtn" value = "Checkout" class = "button" type = "submit">';
            paypal_form = paypal_form + '</form>';

            vpb_setcookie ('theTransactionSum', accounting.formatMoney (the_sum), 30);

            $ ("#vcart_check_uses").html (paypal_form);
            $ ("#totalItemsInCart").html (parseInt (total_items_in_cart));
            $ ('#vcart_grand_total').html (accounting.formatMoney (the_sum));
            $ ('#vpb_items_in_cart').modal ('show');
        }
    }
}


// Add new product to cart temporarily
function vpb_add_item_to_cart (product_code, product_name, product_price, product_image, product_color, product_size)
{
    if (vpb_getcookie ('paymentCode') == "" || vpb_getcookie ('paymentCode') == null || vpb_getcookie ('paymentCode') == undefined)
    {
        var paymentCode = vpb_generte_random ();
        vpb_setcookie ('paymentCode', paymentCode, 30);
    }
    else
    {
    }

    if (vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')) && vpb_vchat_isInArray (vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')), product_code))
    {
        for (var iii = 0; iii < popups.length; iii++)
        {
            //already registered. Bring it to front.
            if (product_code == popups[iii])
            {
                Array.remove (popups, iii);
                popups.unshift (product_code);
            }
        }
        vpb_removecookie ('product_name' + product_code);
        vpb_removecookie ('product_price' + product_code);
        vpb_removecookie ('product_image' + product_code);

        vpb_removecookie ('product_color' + product_code);
        vpb_removecookie ('product_qty' + product_code);
        vpb_removecookie ('product_size' + product_code);
        vpb_removecookie ('product_total_price' + product_code);

        vpb_setcookie ('product_codes' + vpb_getcookie ('session_user_data'), popups, 30);

        var total_added_items = parseInt (popups.length);

        if (isNaN ($ ("#product_qty_" + product_code).val ()))
        {
            var product_qty = 1;
        }
        else if (!$ ("#product_qty_" + product_code).val ().match (/^[0-9]+$/))
        {
            var product_qty = 1;
        }
        else
        {
            var product_qty = $ ("#product_qty_" + product_code).val ();
        }

        var item_total = parseFloat (product_price) * parseInt (product_qty);

        vpb_setcookie ('product_name' + product_code, product_name, 30);
        vpb_setcookie ('product_price' + product_code, product_price, 30);
        vpb_setcookie ('product_image' + product_code, product_image, 30);
        vpb_setcookie ('product_color' + product_code, product_color, 30);
        vpb_setcookie ('product_qty' + product_code, product_qty, 30);
        vpb_setcookie ('product_size' + product_code, product_size, 30);
        vpb_setcookie ('product_total_price' + product_code, item_total, 30);

        $ ("#totalItemsInCart").html (parseInt (total_added_items));

        $ ("#v_added_items_notification_box").fadeIn ();
        setTimeout (function ()
        {
            $ ("#v_added_items_notification_box").fadeOut ();
        }, 3000);
    }
    else
    {
        popups.unshift (product_code);

        vpb_setcookie ('product_codes' + vpb_getcookie ('session_user_data'), popups, 30);

        var total_added_items = parseInt (popups.length);

        if (isNaN ($ ("#product_qty_" + product_code).val ()))
        {
            var product_qty = 1;
        }
        else if (!$ ("#product_qty_" + product_code).val ().match (/^[0-9]+$/))
        {
            var product_qty = 1;
        }
        else
        {
            var product_qty = $ ("#product_qty_" + product_code).val ();
        }

        var item_total = parseFloat (product_price) * parseInt (product_qty);

        vpb_setcookie ('product_name' + product_code, product_name, 30);
        vpb_setcookie ('product_price' + product_code, product_price, 30);
        vpb_setcookie ('product_image' + product_code, product_image, 30);
        vpb_setcookie ('product_color' + product_code, product_color, 30);
        vpb_setcookie ('product_qty' + product_code, product_qty, 30);
        vpb_setcookie ('product_size' + product_code, product_size, 30);
        vpb_setcookie ('product_total_price' + product_code, item_total, 30);

        $ ("#totalItemsInCart").html (parseInt (total_added_items));

        $ ("#v_added_items_notification_box").fadeIn ();
        setTimeout (function ()
        {
            $ ("#v_added_items_notification_box").fadeOut ();
        }, 3000);

    }
}


//Remove an item from cart temporarily
function vpb_remove_item_from_cart (product_code, action)
{
    popups = vpb_getcookie ('product_codes' + vpb_getcookie ('session_user_data')).split (/\,/);

    for (var iii = 0; iii < popups.length; iii++)
    {
        var nproduct_code = popups[iii];
        if (action == "clear-all-at-once")
        {
            Array.remove (popups, iii);

            vpb_removecookie ('product_name' + nproduct_code);
            vpb_removecookie ('product_price' + nproduct_code);
            vpb_removecookie ('product_image' + nproduct_code);

            vpb_removecookie ('product_color' + nproduct_code);
            vpb_removecookie ('product_qty' + nproduct_code);
            vpb_removecookie ('product_size' + nproduct_code);
            vpb_removecookie ('product_total_price' + nproduct_code);

            vpb_setcookie ('product_codes' + vpb_getcookie ('session_user_data'), vchat_remove_data (popups, nproduct_code), 30);
            $ ("#totalItemsInCart").html (0);

            if (popups != "" && parseInt (popups.length) > 0)
            {
                setTimeout (function ()
                {
                    vpb_remove_item_from_cart (product_code, action);
                }, 50);
            }
            else
            {
            }
            return;
        }
        else if (action == "clear-all")
        {
            Array.remove (popups, iii);

            vpb_removecookie ('product_name' + nproduct_code);
            vpb_removecookie ('product_price' + nproduct_code);
            vpb_removecookie ('product_image' + nproduct_code);

            vpb_removecookie ('product_color' + nproduct_code);
            vpb_removecookie ('product_qty' + nproduct_code);
            vpb_removecookie ('product_size' + nproduct_code);
            vpb_removecookie ('product_total_price' + nproduct_code);

            vpb_setcookie ('product_codes' + vpb_getcookie ('session_user_data'), vchat_remove_data (popups, nproduct_code), 30);
            vpb_display_items_in_cart ('run');

            if (popups != "" && parseInt (popups.length) > 0)
            {
                $ ("#clearing_status").html ($ ("#v_loading_btn").val ());
                $ ("#clear_cart_button").hide ();

                setTimeout (function ()
                {
                    vpb_remove_item_from_cart (product_code, action);
                }, 1000);
            }
            else
            {
                $ ("#clearing_status").html ('');
                $ ("#clear_cart_button").show ();
            }
            return;
        }
        else
        {
            if (product_code == nproduct_code)
            {
                Array.remove (popups, iii);

                vpb_removecookie ('product_name' + product_code);
                vpb_removecookie ('product_price' + product_code);
                vpb_removecookie ('product_image' + product_code);

                vpb_removecookie ('product_color' + product_code);
                vpb_removecookie ('product_qty' + product_code);
                vpb_removecookie ('product_size' + product_code);
                vpb_removecookie ('product_total_price' + product_code);

                vpb_setcookie ('product_codes' + vpb_getcookie ('session_user_data'), vchat_remove_data (popups, product_code), 30);
                vpb_display_items_in_cart ('run');
                return;
            }
        }
    }
}


// Show site menu
function vpb_show_site_menu ()
{
    $ ("#v_actions_box").hide ();

    if ($ ("#v_site_menu_box").css ('display') == "none")
    {
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


// Update Passwd
function vpb_update_passwd (my_identity)
{
    if (vpb_getcookie ('session_user_data') != "victor")
    {
        $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
    }
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

        $ ("#update_passwd_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
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




// Update Passwd
function vpb_update_account (my_identity)
{
    if (vpb_getcookie ('session_user_data') != "victor")
    {
        $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
    }
    var vfullname = $ ("#vfullname").val ();
    var vemail_address = $ ("#vemail_address").val ();

    if (my_identity == "" || my_identity == undefined || my_identity == null)
    {
        $ ("#vfullname").focus ();
        $ ("#account_setting_status").html ('<div class="vwarning">' + $ ("#general_system_error").val () + '</div>');
        return false;
    }
    else if (vfullname == "" || vfullname == undefined || vfullname == null || vfullname == "Fullname")
    {
        $ ("#vfullname").focus ();
        $ ("#account_setting_status").html ('<div class="vwarning">' + $ ("#account_no_fullname_text").val () + '</div>');
        return false;
    }
    else if (vemail_address == "" || vemail_address == "Email Address")
    {
        $ ("#vemail_address").focus ();
        $ ("#account_setting_status").html ('<div class="vwarning">' + $ ("#account_no_email_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'my_identity': my_identity, 'vfullname': vfullname, 'vemail_address': vemail_address, 'page': 'update-user-account'};

        $ ("#account_setting_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
            var response_brougght = response.indexOf ('saved successfully');
            if (response_brougght != -1)
            {
                $ ("#account_setting_status").html (response);
                setTimeout (function ()
                {
                    $ ('.modal').modal ('hide');
                    $ ("#account_setting_status").html ('');
                }, 8000);
                return false;
            }
            else
            {
                $ ("#account_setting_status").html (response);
                return false;
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_update_account('" + my_identity + "');", 10000);
        });
    }
}


// Hide profile popups
function vpb_hide_profile_popups ()
{
    // Hide the website menus
    $ ("#account_setting_status").html ('');
    $ ("#update_passwd_status").html ('');
    $ ("#v_site_menu_box").hide ();
    $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
    $ ("#v_site_menu").addClass ('vpb_notifications_icon');
}


// logout
function vpb_log_user_off (url)
{
    vpb_removecookie ('session_user_data');
    vpb_removecookie ('session_user_datas');
    vpb_removecookie ('fullname_id');
    vpb_removecookie ('email_id');
    vpb_removecookie ('uep_data');
    vpb_removecookie ('vpb_ckpoint');
    vpb_removecookie ('last_visited_url');

    setTimeout (function ()
    {
        window.location.replace (url);
    }, 500);
}

// Set time for the site
function vpb_admin_time ()
{
    var currentTime = new Date ();

    var day = currentTime.getDate ();
    var month = currentTime.getMonth () + 1;
    var year = currentTime.getFullYear ();

    var the_date = day + "/" + month + "/" + year;

    var hours = currentTime.getHours ();
    var minutes = currentTime.getMinutes ();
    var seconds = currentTime.getSeconds ();

    if (minutes < 10)
    {
        minutes = "0" + minutes
    }
    var the_time = parseInt (hours) > 11 ? hours + ":" + minutes + ":" + seconds + " PM" : hours + ":" + minutes + ":" + seconds + " AM";

    $ ("#vpb_display_timw").html (the_date + ' - ' + the_time);

    setTimeout (vpb_admin_time, 1000);
}


// Load purchased history
function vpb_users_payment_history (page_id)
{
    var page_name = $ ("#vpb_display_page_name").val ();

    if (page_name == "")
    {
        $ ("#vpb_display_page_name").html ($ ("#admin_manage_purchases_in_shop_text").val ());

        $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
        $ (".admin_cp_menu_active").addClass ('enable_this_box');

        $ ("#manage_purchases").removeClass ('enable_this_box');
        $ ("#manage_purchases").addClass ('admin_cp_menu_active');
    }
    else
    {
    }

    var dataString = {"page_id": page_id, "page_name": page_name, "page": "load-payment-history"};
    $.ajax ({
        type: "POST",
        url: vpb_site_url + 'shop-processor.php',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            if (parseInt (page_id) == 1)
            {
                $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ($ ("#v_sending_btn").val ());
            }
        },
        success: function (response)
        {
            var response_brought = response.indexOf ('VPB_ERROR');
            if (response_brought != -1)
            {
                $ ("#vpb_loading_data_in_the_system").html (response);
                return false;
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ('');
                $ ("#vpb_loading_data_in_the_system").html (response);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_users_payment_history('" + page_id + "');", 10000);
    });
}



//View product Detail
function vpb_view_product_detail (id)
{
    if ($ ("#u-box-" + id).css ('display') == "none")
    {
        $ (".u-box").hide ();
        $ ("#u-box-" + id).fadeToggle ();
        $ ('html, body').animate ({
            scrollTop: $ ("#u-box-" + id).offset ().top - parseInt (120)
        }, 500);
        return false;
    }
    else
    {
        $ ("#u-box-" + id).fadeToggle ();
        $ ('html, body').animate ({
            scrollTop: $ ("#u-box-" + id).offset ().top - parseInt (120)
        }, 500);
        return false;
    }
}



function vpb_search_for_purchases_items ()
{
    var searchTerm = $ ('#users_search_term').val ();
    var page_name = $ ("#vpb_display_page_name").val ();

    if (searchTerm == "")
    {
        return false;
    }
    else
    {
        var dataString = {'searchTerm': searchTerm, "page_name": page_name, 'page': 'search-for-purchases-items'};

        $ ("#vpb_search_results").html ($ ("#v_sending_btn").val ());

        $ (".vback_ground").removeClass ('enable_this_box');
        $ (".vback_ground").addClass ('disable_this_box');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
            $ (".vback_ground").removeClass ('disable_this_box');
            $ (".vback_ground").addClass ('enable_this_box');

            $ ('#default_members').hide ();
            $ ("#vpb_search_results").fadeIn ().html (response);

        }).fail (function (error_response)
        {
            setTimeout ("vpb_search_users();", 10000);
        });
    }
}

// Load site details
function vpb_admin_load_site_settings ()
{
    $ ("#vpb_display_page_name").html ($ ("#admin_settings_text").val ());

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#manage_settings").removeClass ('enable_this_box');
    $ ("#manage_settings").addClass ('admin_cp_menu_active');

    var dataString = {"page": "load-system-settings"};
    $.ajax ({
        type: "POST",
        url: vpb_site_url + 'shop-processor.php',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
        },
        success: function (response)
        {
            $ ("#vpb_loading_data_in_the_system").html (response);
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_admin_load_site_settings();", 10000);
    });
}


// Save site details
function vpb_save_site_details ()
{
    if (vpb_getcookie ('session_user_data') != "victor")
    {
        $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
    }
    var site_name = $ ("#site_name").val ();
    var site_email = $ ("#site_email").val ();
    var description = $ ("#description").val ();
    var keywords = $ ("#keywords").val ();
    var description_long = $ ("#description_long").val ();
    var site_address = $ ("#site_address").val ();
    var reputation = $ ("#reputation").val ();
    var policy = $ ("#policy").val ();
    var terms = $ ("#terms").val ();
    var cookie_policy = $ ("#cookie_policy").val ();
    var copyright_text = $ ("#copyright_text").val ();


    if (site_name == "" || site_email == "" || description == "" || keywords == "")
    {
        $ ("#save_site_details_status").html ('<div class="vwarning">' + $ ("#admin_website_save_deails_empty_fields_text").val () + '</div>');
        return false;
    }
    else
    {
        var dataString = {'site_name': site_name, 'site_email': site_email, 'description': description, 'keywords': keywords, 'description_long': description_long, 'site_address': site_address, 'reputation': reputation, 'policy': policy, 'terms': terms, 'cookie_policy': cookie_policy, 'copyright_text': copyright_text, 'page': 'save-website-details'};

        $ ("#save_site_details_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  alt="Loading" /></div></center>');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
            $ ("#save_site_details_status").html (response);
            return false;
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_site_details();", 10000);
        });
    }
}


// Load users details
function vpb_users_pagination (page_id)
{
    $ ("#vpb_display_page_name").html ($ ("#admin_users_management_text").val ());

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#manage_users").removeClass ('enable_this_box');
    $ ("#manage_users").addClass ('admin_cp_menu_active');

    var dataString = {"page_id": page_id, "page": "load-system-users"};
    $.ajax ({
        type: "POST",
        url: vpb_site_url + 'shop-processor.php',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            if (parseInt (page_id) == 1)
            {
                $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ($ ("#v_sending_btn").val ());
            }
        },
        success: function (response)
        {
            var response_brought = response.indexOf ('VPB_ERROR');
            if (response_brought != -1)
            {
                $ ("#vpb_loading_data_in_the_system").html (response);
                return false;
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ('');
                $ ("#vpb_loading_data_in_the_system").html (response);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_users_pagination('" + page_id + "');", 10000);
    });
}

//View User Detail
function vpb_view_user_detail (id)
{
    if ($ ("#u-box-" + id).css ('display') == "none")
    {
        $ (".u-box").hide ();
        $ ("#u-box-" + id).fadeToggle ();
        $ ('html, body').animate ({
            scrollTop: $ ("#u-box-" + id).offset ().top - parseInt (110)
        }, 500);
        return false;
    }
    else
    {
        $ ("#u-box-" + id).fadeToggle ();
        $ ('html, body').animate ({
            scrollTop: $ ("#u-box-" + id).offset ().top - parseInt (110)
        }, 500);
        return false;
    }
}


/* Confirm user account deletion */
function vpb_user_account_deletion (id, fullname)
{
    $ ('#vasplus_programming_blog_hidden_item_id').val (id);
    var messaged = $ ('#admin_delete_user_account_text').val () + '<b>' + fullname + '</b>?';

    $ ('#delete_account_question').html (messaged);
    $ ('#deletion-account-confirmation').modal ('show');
}
// Delete user account
function vpblog_user_account_confirmation_action (status)
{
    var id = $ ('#vasplus_programming_blog_hidden_item_id').val ();

    if (status == "yes")
    {
        $ ('#deletion-account-confirmation').modal ('hide');

        if (vpb_getcookie ('session_user_data') != "victor")
        {
            $ ("#delete-this-" + id).html (' ' + $ ("#v_sending_btn").val ());
            setTimeout (function ()
            {
                $ ("#user_wraper-" + id).slideUp (1000);
            }, 2000);

            setTimeout (function ()
            {
                $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
                $ ("#v-cart-alert-box").click ();
            }, 4000);
            return false;
        }
        else
        {
        }

        var dataString = {'id': id, 'page': 'delete-this-user-account'};

        $.ajax ({
            type: "POST",
            url: vpb_site_url + 'shop-processor.php',
            data: dataString,
            cache: false,
            beforeSend: function ()
            {
                $ ("#delete-this-" + id).html (' ' + $ ("#v_sending_btn").val ());
            },
            success: function (response)
            {
                $ ("#user_wraper-" + id).slideUp (1000);
                setTimeout (function ()
                {
                    vpb_users_pagination ('1');
                }, 1000);
            }
        }).fail (function (error_response)
        {
            vpblog_user_account_confirmation_action (status);
        });
    }
    else if (status == "no")
    {
        $ ('#deletion-account-confirmation').modal ('hide');
        return false;
    }
    else
    { /*Unknown status brought */
    }
}


// Get the new purcahsed amount
function vpb_load_new_product_total ()
{
    var dataString = {'page': 'get-the-new-purchased-total'};

    $.ajax ({
        type: "POST",
        url: vpb_site_url + 'shop-processor.php',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {},
        success: function (response)
        {
            $ ("#thePurchasedTotal").html (response);
        }
    }).fail (function (error_response)
    {
        vpb_load_new_product_total ();
    });
}

/* Confirm deletion of a purchased Item */
function vpb_purchased_deletion (id, product_name, fullname)
{
    $ ('#vasplus_programming_blog_hidden_item_id').val (id);

    if (fullname != "")
    {
        var messaged = $ ('#admin_delete_product_text').val () + '<b>' + product_name + '</b> purchased by <b>' + fullname + '</b>?';
    }
    else
    {
        var messaged = $ ('#admin_delete_product_text').val () + '<b>' + product_name + '</b>?';
    }

    $ ('#delete_purchased_question').html (messaged);
    $ ('#deletion-purchased-confirmation').modal ('show');
}
// Delete purchased item
function vpblog_purchased_confirmation_action (status)
{
    var id = $ ('#vasplus_programming_blog_hidden_item_id').val ();

    if (status == "yes")
    {
        $ ('#deletion-purchased-confirmation').modal ('hide');

        if (vpb_getcookie ('session_user_data') != "victor")
        {
            $ ("#delete-this-" + id).html (' ' + $ ("#v_sending_btn").val ());
            setTimeout (function ()
            {
                $ ("#user_wraper-" + id).slideUp (1000);
            }, 2000);

            setTimeout (function ()
            {
                $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
                $ ("#v-cart-alert-box").click ();
            }, 4000);
            return false;
        }
        else
        {
        }

        var dataString = {'id': id, 'page': 'delete-this-purchased-item'};

        $.ajax ({
            type: "POST",
            url: vpb_site_url + 'shop-processor.php',
            data: dataString,
            cache: false,
            beforeSend: function ()
            {
                $ ("#delete-this-" + id).html (' ' + $ ("#v_sending_btn").val ());
            },
            success: function (response)
            {
                $ ("#user_wraper-" + id).slideUp (1000);
                setTimeout (function ()
                {
                    vpb_load_new_product_total ();
                }, 1000);
            }
        }).fail (function (error_response)
        {
            vpblog_user_account_confirmation_action (status);
        });
    }
    else if (status == "no")
    {
        $ ('#deletion-purchased-confirmation').modal ('hide');
        return false;
    }
    else
    { /*Unknown status brought */
    }
}

// Search for a user account
function vpb_search_users ()
{
    var searchTerm = $ ('#users_search_term').val ();

    if (searchTerm == "")
    {
        return false;
    }
    else
    {
        var dataString = {'searchTerm': searchTerm, 'page': 'search-for-users'};

        $ ("#vpb_search_results").html ($ ("#v_sending_btn").val ());

        $ (".vback_ground").removeClass ('enable_this_box');
        $ (".vback_ground").addClass ('disable_this_box');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
            $ (".vback_ground").removeClass ('disable_this_box');
            $ (".vback_ground").addClass ('enable_this_box');

            $ ('#default_members').hide ();
            $ ("#vpb_search_results").fadeIn ().html (response);

        }).fail (function (error_response)
        {
            setTimeout ("vpb_search_users();", 10000);
        });
    }
}





// Load Categories
function vpb_load_category_pagination (page_id)
{
    $ ("#vpb_display_page_name").html ($ ("#admin_create_manage_categories_text").val ());

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#create_manage_categories").removeClass ('enable_this_box');
    $ ("#create_manage_categories").addClass ('admin_cp_menu_active');

    var dataString = {"page_id": page_id, "page": "load-category"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/getCategories',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            if (parseInt (page_id) == 1)
            {
                $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ($ ("#v_sending_btn").val ());
            }
        },
        success: function (response)
        {
            var response_brought = response.indexOf ('VPB_ERROR');
            if (response_brought != -1)
            {
                $ ("#vpb_loading_data_in_the_system").html (response);
                return false;
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ('');
                $ ("#vpb_loading_data_in_the_system").html (response);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_category_pagination('" + page_id + "');", 10000);
    });
}





// Add or Update the category
function vpb_add_category (id)
{
    var category_name = id == "add" ? $ ("#category_name").val () : $ ("#category_name" + id).val ();

    if (category_name == "" || category_name == null || category_name == $ ("#admin_category_name_placeholder_text").val ())
    {
        if (id == "add")
        {
            $ ("#add_category_status").html ('<div class="vwarning">' + $ ("#admin_enter_category_name_text").val () + '</div>');
            id == "add" ? $ ("#category_name").focus () : $ ("#category_name" + id).focus ();
            return false;
        }
        else
        {
            $ ('#category_box_' + id).hide ();
            $ ('#category_text_' + id).fadeIn ();
            $ ('#cancel_' + id).hide ();
            $ ('#edit_' + id).fadeIn ();
        }
    }
    else
    {

        var dataString = {'id': id, 'category_name': category_name, 'page': 'add-update-category'};

        if (id == "add")
        {
            $ ("#add_category_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading..." title="Loading..." /></div></center>');
        }
        else
        {
            $ ("#category_text_" + id).html ('<center><div align="center"><img style="" src="' + vpb_site_url + 'img/loadings.gif" align="absmiddle"  alt="Loading..." title="Loading..." /></div></center>');
            $ ('#category_box_' + id).hide ();
            $ ('#category_text_' + id).fadeIn ();
            $ ('#cancel_' + id).hide ();
            $ ('#edit_' + id).fadeIn ();
        }

        $.post ('/blab/product/saveNewCategory', dataString, function (response)
        {

            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 1)
            {
                if (id == "add")
                {
                    $ ("#category_name").val ('');
                    $ ("#add_category_status").html (response);
                    vpb_load_category_pagination ('1');
                }
                else
                {
                    $ ("#category_text_" + id).html (category_name);
                }
                return false;
            }
            else
            {
                if (id == "add")
                {
                    $ ("#add_category_status").html (response);
                    return false;
                }
                else
                {
                    $ ('#category_box_' + id).hide ();
                    $ ('#category_text_' + id).fadeIn ();
                    $ ('#cancel_' + id).hide ();
                    $ ('#edit_' + id).fadeIn ();
                }
            }


        }).fail (function (error_response)
        {
            setTimeout ("vpb_add_category('" + id + "');", 10000);
        });
    }
}

// Delete a category
function vpb_delete_this_category ()
{
    if (vpb_getcookie ('session_user_data') != "victor")
    {
        $ ("#v-cart-message").html ($ ("#admin_readonly_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
    }
    var id = $ ("#vasplus_data_id").val ();

    if (id == "" || id == undefined)
    {
        $ ("#v-cart-message").html ($ ("#general_system_error").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
        var dataString = {'id': id, 'page': 'delete-this-category'};

        $ ('.modal').modal ('hide');
        $ ("#category_wrapper_" + id).slideUp ();

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
        }).fail (function (error_response)
        {
            setTimeout (function ()
            {
                vpb_delete_this_category ();
            }, 1000);
        });
    }
}



// Load Add Products Box
function vpb_admin_load_add_products_box ()
{
    $ ("#vpb_display_page_name").html ($ ("#admin_add_products_text").val ());

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#add_products").removeClass ('enable_this_box');
    $ ("#add_products").addClass ('admin_cp_menu_active');

    var dataString = {"page": "load-add-products-box"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/addProduct',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
        },
        success: function (response)
        {
            $ ("#vpb_loading_data_in_the_system").html (response);

            $ ("#pcode").val (vpb_generte_random ());
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_admin_load_add_products_box();", 10000);
    });
}



// Make sure the product quantities are only numbers and not below 1
function vpb_track_quantity (input)
{
    if (input.value != "")
    {
        var newInput = input.value.replace (/[^0-9+.]/g, '');
        if (parseInt (newInput) < 1)
        {
            input.value = 1;
        }
        else if (isNaN (newInput))
        {
            input.value = 1;
        }
        else if (!newInput.match (/^[0-9]+$/))
        {
            input.value = 1;
        }
        else
        {
            input.value = newInput;
        }
    }
    else
    {
    }
}




// Load and display shopping cart products
function vpb_load_shopping_cart_products (page_id, category_name, id)
{
    if (id != "" && id != undefined)
    {
        $ (".vcart_end").hide ()
        $ (".vcart_started").show ();

        $ ("#start_" + parseInt (id)).hide ()
        $ ("#end_" + parseInt (id)).show ();

        $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
        $ (".admin_cp_menu_active").addClass ('enable_this_box');

        $ ("#category_" + parseInt (id)).removeClass ('enable_this_box');
        $ ("#category_" + parseInt (id)).addClass ('admin_cp_menu_active');

        $ ("#vpb_display_page_name").html ('<i class="fa fa-angle-double-right" aria-hidden="true"></i> ' + category_name);
    }
    else
    {
        $ (".vcart_end").hide ();
        $ (".vcart_started").show ();

        $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
        $ (".admin_cp_menu_active").addClass ('enable_this_box');

        $ ("#vpb_display_page_name").html ('');
    }

    category_name = category_name || "";
    id = id || "";

    var dataString = {"page_id": page_id, "category_name": category_name, "searchText": "", "category_id": id, "page": "load_shopping_cart_products"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/searchProducts',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            if (parseInt (page_id) == 1)
            {
                $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ($ ("#v_sending_btn").val ());
            }
        },
        success: function (response)
        {
            var response_brought = response.indexOf ('VPB_ERROR');
            if (response_brought != -1)
            {
                $ ("#vpb_loading_data_in_the_system").html (response);
                return false;
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ('');
                $ ("#vpb_loading_data_in_the_system").html (response);
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_category_pagination('" + page_id + "');", 10000);
    });
}


// Show and hide the search box when called
function vpb_toggle_search_box ()
{
    $ ("#vcart_search_box").slideToggle ();

    $ (".fa-search-minus, .fa-search-plus").toggle ();
}


// Search for products
function vpb_search_products ()
{
    $ (".vcart_end").hide ()
    $ (".vcart_started").show ();

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#vpb_display_page_name").html ('');

    var searchTerm = $ ('#products_search_term').val ();

    if (searchTerm == "")
    {
        return false;
    }
    else
    {
        var dataString = {'searchText': searchTerm, 'category_id': '', 'page_id': '', 'page': 'search-for-products'};

        $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());

        $ (".vback_ground").removeClass ('enable_this_box');
        $ (".vback_ground").addClass ('disable_this_box');

        $.post ('/blab/product/searchProducts', dataString, function (response)
        {
            $ (".vback_ground").removeClass ('disable_this_box');
            $ (".vback_ground").addClass ('enable_this_box');

            $ ("#vpb_loading_data_in_the_system").fadeIn ().html (response);

            $ ('#products_search_term').val ('');

        }).fail (function (error_response)
        {
            setTimeout ("vpb_search_products();", 10000);
        });
    }
}



function vpb_manage_shopping_cart_products (page_id, category_name, id)
{
    $ ("#vpb_display_page_name").html ($ ("#admin_manage_products_in_shop_text").val ());

    $ (".admin_cp_menu").removeClass ('admin_cp_menu_active');
    $ (".admin_cp_menu_active").addClass ('enable_this_box');

    $ ("#manage_products").removeClass ('enable_this_box');
    $ ("#manage_products").addClass ('admin_cp_menu_active');

    var dataString = {"page_id": page_id, "category_name": category_name, "category_id": id, "page": "manage_shopping_cart_products"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/manageProducts',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            if (parseInt (page_id) == 1)
            {
                $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ($ ("#v_sending_btn").val ());
            }
        },
        success: function (response)
        {
            var response_brought = response.indexOf ('VPB_ERROR');
            if (response_brought != -1)
            {
                $ ("#vpb_loading_data_in_the_system").html (response);
                return false;
            }
            else
            {
                $ ("#vpb_loading_datas_in_the_system").html ('');
                $ ("#vpb_loading_data_in_the_system").html (response);

                $ ('html, body').stop ().animate ({
                    scrollTop: 0
                }, 1500, 'easeInOutExpo');
            }
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_load_category_pagination('" + page_id + "');", 10000);
    });
}







/* Confirm user account deletion */
function vpb_product_deletion (id, product_name)
{
    $ ('#vasplus_programming_blog_hidden_item_id').val (id);
    var messaged = $ ('#admin_delete_product_text').val () + '<b>' + product_name + '</b>?';

    $ ('#delete_product_question').html (messaged);
    $ ('#deletion-product-confirmation').modal ('show');
}
// Delete user account
function vpblog_product_confirmation_action (status)
{
    var id = $ ('#vasplus_programming_blog_hidden_item_id').val ();

    if (status == "yes")
    {
        $ ('#deletion-product-confirmation').modal ('hide');


        var dataString = {'id': id, 'page': 'delete-this-product'};

        $.ajax ({
            type: "POST",
            url: '/blab/product/deleteProduct',
            data: dataString,
            cache: false,
            beforeSend: function ()
            {
                $ ("#delete-this-" + parseInt (id)).html (' ' + $ ("#v_sending_btn").val ());
            },
            success: function (response)
            {
                $ ("#vc_product_" + parseInt (id)).fadeOut (1000);
            }
        }).fail (function (error_response)
        {
            vpblog_product_confirmation_action (status);
        });
    }
    else if (status == "no")
    {
        $ ('#deletion-product-confirmation').modal ('hide');
        return false;
    }
    else
    { /*Unknown status brought */
    }
}





// Load Edit Products Box
function vpb_admin_load_edit_products_box (id)
{
    var dataString = {"id": parseInt (id), "page": "load-edit-products-box"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/editProduct',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            $ ("#vpb_loading_data_in_the_system").html ($ ("#vpb_loading_image_gif").val ());
        },
        success: function (response)
        {
            $ ("#vpb_loading_data_in_the_system").html (response);

            $ ('html, body').stop ().animate ({
                scrollTop: 0
            }, 1500, 'easeInOutExpo');
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_admin_load_edit_products_box('" + id + "');", 10000);
    });
}







// Change product photo
//Add Image File to Post
function vpb_change_photo (product_id, photo_id)
{

    var photos = $ ("#add_photos_" + parseInt (photo_id)).val ();
    var ext = $ ('#add_photos_' + parseInt (photo_id)).val ().split ('.').pop ().toLowerCase ();

    if (photos == "")
    {
        return false;
    }
    else if (photos != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        $ ("#v-cart-message").html ($ ("#invalid_file_attachment").val ());
        document.getElementById ('add_photos_' + parseInt (photo_id)).value = '';
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('add_photos_' + parseInt (photo_id)).files;

        // Create a formdata object and append the files
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });

        vpb_data.append ("product_id", product_id);
        vpb_data.append ("photo_id", photo_id);
        vpb_data.append ("page", 'vpb_change_product_photo');

        $.ajax ({
            url: '/blab/product/changePhoto',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#vcart_photos_bottom_" + parseInt (photo_id)).hide ();

                $ ("#vcart_photos_top_" + parseInt (photo_id)).html ('<center><div align="center" style="width:200px;"><img src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  alt="Loading" /></div></center>');
            },
            success: function (response)
            {
                var objResponse = $.parseJSON (response);

                if (objResponse.sucess == 0)
                {
                    $ ("#vcart_photos_top_" + parseInt (photo_id)).html ('<center><div align="center" style="width:200px;">Error occured</div></center>');
                    $ ("#vcart_photos_bottom_" + parseInt (photo_id)).show ();
                }
                else
                {
                    var path = objResponse.data.location;
                                        
                    if(!path) {
                        return false;
                    }
                    
                    $ ('#add_photos_' + parseInt (photo_id)).val ('');
                    $ ("#vcart_new_photo_" + parseInt (photo_id)).html ('<img src="'+path+'">');

                    $ ("#vcart_photos_top_" + parseInt (photo_id)).html ('');
                    $ ("#vcart_photos_bottom_" + parseInt (photo_id)).show ();
                }
            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_change_photo('" + product_id + "', '" + photo_id + "');", 10000);
        });
    }
}

// Delete or Remove a product photo
function vpb_remove_photo (product_id, photo_id)
{

    var dataString = {"product_id": parseInt (product_id), "photo_id": parseInt (photo_id), "page": "vpb_remove_photo"};
    $.ajax ({
        type: "POST",
        url: '/blab/product/deleteImage',
        data: dataString,
        cache: false,
        beforeSend: function ()
        {
            $ ("#vcart_photos_bottom_" + parseInt (photo_id)).hide ();

            $ ("#vcart_photos_top_" + parseInt (photo_id)).html ('<center><div align="center" style="width:200px;"><img src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  alt="Loading" /></div></center>');
        },
        success: function (response)
        {
            $ ("#vcart_photos_wrp_" + parseInt (photo_id)).fadeOut ();
        }
    }).fail (function (error_response)
    {
        setTimeout ("vpb_remove_photo('" + product_id + "', '" + photo_id + "');", 10000);
    });
}



// Validate email addresses
function email_is_valid (email)
{
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test (email);
}




// Contact us
function vpb_contact_us_now ()
{
    var fname = vpb_trim ($ ("#fname").val ());
    var uname = vpb_trim ($ ("#uname").val ());
    var femail = vpb_trim ($ ("#femail").val ());
    var fsubject = vpb_trim ($ ("#fsubject").val ());
    var fmessage = vpb_trim ($ ("#fmessage").val ());


    if (fname == "")
    {
        $ ("#fname").val ('').focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_a_text").val () + '</div>');
    }
    else if (uname == "")
    {
        $ ("#uname").val ('').focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_b_text").val () + '</div>');
    }
    else if (femail == "")
    {
        $ ("#femail").val ('').focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_c_text").val () + '</div>');
    }
    else if (!email_is_valid (femail))
    {
        $ ("#femail").focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_d_text").val () + '</div>');
    }
    else if (fsubject == "")
    {
        $ ("#fsubject").val ('').focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_e_text").val () + '</div>');
    }
    else if (fmessage == "")
    {
        $ ("#fmessage").val ('').focus ();
        $ ('#contact_status').html ('<div class="vwarning">' + $ ("#ct_error_f_text").val () + '</div>');
    }
    else
    {
        var dataString = {'fname': fname, 'uname': uname, 'femail': femail, 'fsubject': fsubject, 'fmessage': fmessage, 'page': 'contact-us-for-help'};

        $ ("#no_waiting").hide ();
        $ ("#waiting").show ();

        $ ("#contact_status").html ('<center><div align="center"><img style="margin:10px;" src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  title="Loading..." /></div></center>');

        $.post (vpb_site_url + 'shop-processor.php', dataString, function (response)
        {
            $ ("#waiting").hide ();
            $ ("#no_waiting").show ();

            var respons_brought = response.indexOf ('vsuccess');
            if (respons_brought != -1)
            {
                //$("#uname").val('');
                //$("#fname").val('');
                //$("#femail").val('');
                $ ("#fsubject").val ('').change ();
                $ ("#fmessage").val ('');
                $ ("#contact_status").html (response);
                $ ("textarea#fmessage").animate ({
                    "height": "80px"
                }, "fast");
                $ ('html, body').stop ().animate ({
                    scrollTop: 0
                }, 1500, 'easeInOutExpo');
                $ ("#fmessage").val (' ').change ();
                setTimeout (function ()
                {
                    $ ("#contact_status").html ('');
                }, 8000);
                return false;
            }
            else
            {
                $ ("#contact_status").html (response);
                return false;
            }

        }).fail (function (error_response)
        {
            setTimeout ("vpb_contact_us_now();", 1000);
        });
    }
}





// Save product details
function vpb_save_product_details (product_id)
{
    var pname = vpb_trim ($ ("#pname").val ());
    var pprice = vpb_trim ($ ("#pprice").val ());
    var pcode = vpb_trim ($ ("#pcode").val ());
    var pcolor = vpb_trim ($ ("#pcolor").val ());
    var psize = vpb_trim ($ ("#psize").val ());
    var pcategory = vpb_trim ($ ("#pcategory").val ());
    var pdescription = vpb_trim ($ ("#pdescription").val ());

    if (product_id == "")
    {
        $ ("#v-cart-message").html ($ ("#general_system_error").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pname == "")
    {
        $ ("#pname").focus ();
        $ ("#v-cart-message").html ($ ("#pname_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pprice == "")
    {
        $ ("#pprice").focus ();
        $ ("#v-cart-message").html ($ ("#pprice_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcode == "")
    {
        $ ("#pcode").focus ();
        $ ("#v-cart-message").html ($ ("#pcode_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcolor == "")
    {
        $ ("#v-cart-message").html ($ ("#pcolor_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (psize == "")
    {
        $ ("#v-cart-message").html ($ ("#psize_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcategory == "")
    {
        $ ("#v-cart-message").html ($ ("#pcategory_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pdescription == "")
    {
        $ ("#pdescription").focus ();
        $ ("#v-cart-message").html ($ ("#pdescription_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
        // Create a formdata object and append the files
        var vpb_data = new FormData ();

        /*
         //Proceed now because a user has selected some files
         var vpb_files = document.getElementById('add_photos').files;
         
         $.each(vpb_files, function(keys, values)
         {
         vpb_data.append(keys, values);
         });
         */
        vpb_data.append ("product_id", product_id);
        vpb_data.append ("pname", pname);
        vpb_data.append ("pprice", pprice);
        vpb_data.append ("pcode", pcode);
        vpb_data.append ("pcolor", pcolor);
        vpb_data.append ("psize", psize);
        vpb_data.append ("pcategory", pcategory);
        vpb_data.append ("pdescription", pdescription);
        vpb_data.append ("page", 'vpb_save_product_submission');


        $.ajax ({
            url: '/blab/product/saveUpdatedProduct',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#add_new_product_box").removeClass ('enable_this_box');
                $ ("#add_new_product_box").addClass ('disable_this_box');

                $ ("#vpb_product_save_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  alt="Loading" /></div></center>');
            },
            success: function (response)
            {
                $ ("#add_new_product_box").removeClass ('disable_this_box');
                $ ("#add_new_product_box").addClass ('enable_this_box');

                $ ("#vpb_product_save_status").html (response);

            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_save_product_details('" + product_id + "');", 10000);
        });
    }
}

































function vpb_resize_this (evnt)
{
    var observe;
    if (window.attachEvent)
    {
        observe = function (element, event, handler)
        {
            element.attachEvent ('on' + event, handler);
        };
    }
    else
    {
        observe = function (element, event, handler)
        {
            element.addEventListener (event, handler, false);
        };
    }

    var text = document.getElementById ('vpb_wall_share_post_data');
    var prevHeight = text.offsetHeight;//scrollHeight

    function resize ()
    {
        text.style.height = 'auto';
        text.style.height = text.scrollHeight + 'px';
    }
    /* 0-timeout to get the already changed text */
    function delayedResize ()
    {
        window.setTimeout (resize, 1);
    }
    observe (text, 'change', resize);
    observe (text, 'cut', delayedResize);
    observe (text, 'paste', delayedResize);
    observe (text, 'drop', delayedResize);
    observe (text, 'keydown', delayedResize);

    resize ();
    text.focus ();
}


// Check profile page security
function vpb_security_check_point ()
{
    var dataString = {"page": "security_check_point"};
    $.ajax ({
        type: "POST",
        url: vpb_site_url + 'shop-processor.php',
        data: dataString,
        beforeSend: function ()
        {},
        success: function (response)
        {
            var response_brought = response.indexOf ('user-session-has-expired');
            if (response_brought != -1)
            {
                vpb_log_user_off (vpb_site_url + "login.php?id=user-session-has-expired");
            }
            else
            {
            }
        }
    });
}


function googleTranslateElementInit2 ()
{
    new google.translate.TranslateElement ({pageLanguage: 'en', autoDisplay: false}, 'google_translate_element2');
}
/* <![CDATA[ */
eval (function (p, a, c, k, e, r)
{
    e = function (c)
    {
        return(c < a ? '' : e (parseInt (c / a))) + ((c = c % a) > 35 ? String.fromCharCode (c + 29) : c.toString (36))
    };
    if (!''.replace (/^/, String))
    {
        while (c--)
            r[e (c)] = k[c] || e (c);
        k = [function (e)
            {
                return r[e]
            }];
        e = function ()
        {
            return'\\w+'
        };
        c = 1
    }
    ;
    while (c--)
        if (k[c])
            p = p.replace (new RegExp ('\\b' + e (c) + '\\b', 'g'), k[c]);
    return p
} ('6 7(a,b){n{4(2.9){3 c=2.9("o");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}', 43, 43, '||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'.split ('|'), 0, {}));
/* ]]> */



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
                $ ("#v-cart-message").html ($ ("#invalid_file_attachment").val ());
                document.getElementById ('add_photos').value = '';
                document.getElementById ('browsedPhotos').title = 'No file is chosen';
                $ ("#v-cart-alert-box").click ();
                return false;
            }
            else
            {
                //Clear previous previewed files and start again
                $ ('#vpb-display-attachment-preview').html ('');
                var reader = new FileReader ();
                reader.onload = function (e)
                {
                    $ ('#vpb-display-attachment-preview').append (
                            '<div class="vpb_preview_wrapper"> \
				   <img class="vpb_image_style" src="' + e.target.result + '" title="' + escape (file.name) + '" onClick="vpb_view_this_image(\'Photo Preview\', \'' + e.target.result + '\');" style="cursor:pointer;" /><br /> \
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


// Photo enlargement 
function vpb_view_this_image (title, photo)
{
    $ ("#photo_viewer_box_title").html (title);
    $ ("#photo_viewed_contents").html ('<img class="vpb_image_style" style="max-width:440px !important; width:100%;height:auto;margin:0 auto;" src="' + photo + '" />');
    $ ('#vpb_photo_viewer_display_box').modal ('show');

}


// Add new product to shopping cart
function vpb_submit_product_details ()
{
    var pname = vpb_trim ($ ("#pname").val ());
    var pprice = vpb_trim ($ ("#pprice").val ());
    var pcode = vpb_trim ($ ("#pcode").val ());
    var pcolor = vpb_trim ($ ("#pcolor").val ());
    var psize = vpb_trim ($ ("#psize").val ());
    var pcategory = vpb_trim ($ ("#pcategory").val ());
    var pdescription = vpb_trim ($ ("#pdescription").val ());

    var photos = $ ("#add_photos").val ();
    var ext = $ ('#add_photos').val ().split ('.').pop ().toLowerCase ();


    if (pname == "")
    {
        $ ("#pname").focus ();
        $ ("#v-cart-message").html ($ ("#pname_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pprice == "")
    {
        $ ("#pprice").focus ();
        $ ("#v-cart-message").html ($ ("#pprice_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcode == "")
    {
        $ ("#pcode").focus ();
        $ ("#v-cart-message").html ($ ("#pcode_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcolor == "")
    {
        $ ("#v-cart-message").html ($ ("#pcolor_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (psize == "")
    {
        $ ("#v-cart-message").html ($ ("#psize_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pcategory == "")
    {
        $ ("#v-cart-message").html ($ ("#pcategory_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (pdescription == "")
    {
        $ ("#pdescription").focus ();
        $ ("#v-cart-message").html ($ ("#pdescription_text").val ());
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else if (photos != "" && $.inArray (ext, ["jpg", "jpeg", "gif", "png"]) == -1)
    {
        $ ("#v-cart-message").html ($ ("#invalid_file_attachment").val ());
        document.getElementById ('add_photos').value = '';
        document.getElementById ('browsedPhotos').title = 'No file is chosen';
        $ ("#v-cart-alert-box").click ();
        return false;
    }
    else
    {
        //Proceed now because a user has selected some files
        var vpb_files = document.getElementById ('add_photos').files;

        // Create a formdata object and append the files
        var vpb_data = new FormData ();

        $.each (vpb_files, function (keys, values)
        {
            vpb_data.append (keys, values);
        });

        vpb_data.append ("pname", pname);
        vpb_data.append ("pprice", pprice);
        vpb_data.append ("pcode", pcode);
        vpb_data.append ("pcolor", pcolor);
        vpb_data.append ("psize", psize);
        vpb_data.append ("pcategory", pcategory);
        vpb_data.append ("pdescription", pdescription);
        vpb_data.append ("page", 'vpb_add_new_product_submission');

        $.ajax ({
            url: '/blab/product/saveNewProduct',
            type: 'POST',
            data: vpb_data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function ()
            {
                $ ("#add_new_product_box").removeClass ('enable_this_box');
                $ ("#add_new_product_box").addClass ('disable_this_box');

                $ ("#vpb_product_added_status").html ('<center><div align="center"><img style="margin-top:10px;" src="' + vpb_site_url + 'img/loading.gif" align="absmiddle"  alt="Loading" /></div></center>');
            },
            success: function (response)
            {
                $ ("#add_new_product_box").removeClass ('disable_this_box');
                $ ("#add_new_product_box").addClass ('enable_this_box');

                $ ('#vpb-display-attachment-preview').html ('');
                $ ('#add_photos').val ('');

                $ ("#pname").val ('');
                $ ("#pprice").val ('');
                $ ("#pcode").val ('');
                $ ("#pcolor").val ('').change ();
                $ ("#psize").val ('').change ();
                $ ("#pcategory").val ('').change ();
                $ ("#pdescription").val ('').change ();

                $ ("#vpb_product_added_status").html (response);

                $ ("#pcode").val (vpb_generte_random ());

            }
        }).fail (function (error_response)
        {
            setTimeout ("vpb_submit_product_details();", 10000);
        });
    }
}

// Popup photo when called
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
    window.scrollTo (500, 0);
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

$ (function ()
{
    vpb_security_check_point ();

    vpb_display_items_in_cart ('auto');
    $ ("#totalItemsInCart").html (parseInt (popups.length));


//    $ ('.vasplus_tooltip_menu').tipsy ({fade: true, gravity: 'w'});
//    $ ('.vasplus_tooltip_popup').tipsy ({fade: true, gravity: 'e'});


    $ (document).on ("click", function (e)
    {
        var $clicked = $ (e.target);
        if (!$clicked.parents ().hasClass ("dropdown-menu") && !$clicked.parents ().hasClass ("dropdown") && !$clicked.parents ().hasClass ("input-group-addon-plus") && !$clicked.parents ().hasClass ("form-control-plus"))
        {
            // Hide the website menus
            $ ("#v_site_menu_box").hide ();
            $ ("#v_site_menu").removeClass ('vpb_notifications_icon_active');
            $ ("#v_site_menu").addClass ('vpb_notifications_icon');
        }
    });


    // Keep the drop-down box open when clicked on the header
    $ ("li.dropdown-header").on ("click", function (e)
    {
        e.stopPropagation ();
        return false;
    });

    $ ('#vas-scroll-to-top').hide ();

    //To detect the page scroll so as to auto load status updates
    $ (window).scroll (function (e)
    {
        if ($ (this).scrollTop () > 100)
        {
            $ ('#vas-scroll-to-top').fadeIn ();
        }
        else
        {
            $ ('#vas-scroll-to-top').fadeOut ();
        }

    });


    $ ('#vas-scroll-to-top').click (function (event)
    {
        var $anchor = $ (this);
        $ ('html, body').stop ().animate ({
            scrollTop: 0
        }, 1500, 'easeInOutExpo');
        //event.preventDefault();
        setTimeout (function ()
        {
            $anchor.fadeOut ();
        }, 1000);
    });
});


var MD5 = function (string)
{

    function RotateLeft (lValue, iShiftBits)
    {
        return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
    }

    function AddUnsigned (lX, lY)
    {
        var lX4, lY4, lX8, lY8, lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
        if (lX4 & lY4)
        {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4)
        {
            if (lResult & 0x40000000)
            {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            }
            else
            {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        }
        else
        {
            return (lResult ^ lX8 ^ lY8);
        }
    }

    function F (x, y, z)
    {
        return (x & y) | ((~x) & z);
    }
    function G (x, y, z)
    {
        return (x & z) | (y & (~z));
    }
    function H (x, y, z)
    {
        return (x ^ y ^ z);
    }
    function I (x, y, z)
    {
        return (y ^ (x | (~z)));
    }

    function FF (a, b, c, d, x, s, ac)
    {
        a = AddUnsigned (a, AddUnsigned (AddUnsigned (F (b, c, d), x), ac));
        return AddUnsigned (RotateLeft (a, s), b);
    }
    ;

    function GG (a, b, c, d, x, s, ac)
    {
        a = AddUnsigned (a, AddUnsigned (AddUnsigned (G (b, c, d), x), ac));
        return AddUnsigned (RotateLeft (a, s), b);
    }
    ;

    function HH (a, b, c, d, x, s, ac)
    {
        a = AddUnsigned (a, AddUnsigned (AddUnsigned (H (b, c, d), x), ac));
        return AddUnsigned (RotateLeft (a, s), b);
    }
    ;

    function II (a, b, c, d, x, s, ac)
    {
        a = AddUnsigned (a, AddUnsigned (AddUnsigned (I (b, c, d), x), ac));
        return AddUnsigned (RotateLeft (a, s), b);
    }
    ;

    function ConvertToWordArray (string)
    {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWords_temp1 = lMessageLength + 8;
        var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
        var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
        var lWordArray = Array (lNumberOfWords - 1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while (lByteCount < lMessageLength) {
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt (lByteCount) << lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
        lBytePosition = (lByteCount % 4) * 8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
        lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
        lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
        return lWordArray;
    }
    ;

    function WordToHex (lValue)
    {
        var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
        for (lCount = 0; lCount <= 3; lCount++) {
            lByte = (lValue >>> (lCount * 8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString (16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr (WordToHexValue_temp.length - 2, 2);
        }
        return WordToHexValue;
    }
    ;

    function Utf8Encode (string)
    {
        string = string.replace (/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt (n);

            if (c < 128)
            {
                utftext += String.fromCharCode (c);
            }
            else if ((c > 127) && (c < 2048))
            {
                utftext += String.fromCharCode ((c >> 6) | 192);
                utftext += String.fromCharCode ((c & 63) | 128);
            }
            else
            {
                utftext += String.fromCharCode ((c >> 12) | 224);
                utftext += String.fromCharCode (((c >> 6) & 63) | 128);
                utftext += String.fromCharCode ((c & 63) | 128);
            }

        }

        return utftext;
    }
    ;

    var x = Array ();
    var k, AA, BB, CC, DD, a, b, c, d;
    var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
    var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
    var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
    var S41 = 6, S42 = 10, S43 = 15, S44 = 21;

    string = Utf8Encode (string);

    x = ConvertToWordArray (string);

    a = 0x67452301;
    b = 0xEFCDAB89;
    c = 0x98BADCFE;
    d = 0x10325476;

    for (k = 0; k < x.length; k += 16) {
        AA = a;
        BB = b;
        CC = c;
        DD = d;
        a = FF (a, b, c, d, x[k + 0], S11, 0xD76AA478);
        d = FF (d, a, b, c, x[k + 1], S12, 0xE8C7B756);
        c = FF (c, d, a, b, x[k + 2], S13, 0x242070DB);
        b = FF (b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
        a = FF (a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
        d = FF (d, a, b, c, x[k + 5], S12, 0x4787C62A);
        c = FF (c, d, a, b, x[k + 6], S13, 0xA8304613);
        b = FF (b, c, d, a, x[k + 7], S14, 0xFD469501);
        a = FF (a, b, c, d, x[k + 8], S11, 0x698098D8);
        d = FF (d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
        c = FF (c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
        b = FF (b, c, d, a, x[k + 11], S14, 0x895CD7BE);
        a = FF (a, b, c, d, x[k + 12], S11, 0x6B901122);
        d = FF (d, a, b, c, x[k + 13], S12, 0xFD987193);
        c = FF (c, d, a, b, x[k + 14], S13, 0xA679438E);
        b = FF (b, c, d, a, x[k + 15], S14, 0x49B40821);
        a = GG (a, b, c, d, x[k + 1], S21, 0xF61E2562);
        d = GG (d, a, b, c, x[k + 6], S22, 0xC040B340);
        c = GG (c, d, a, b, x[k + 11], S23, 0x265E5A51);
        b = GG (b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
        a = GG (a, b, c, d, x[k + 5], S21, 0xD62F105D);
        d = GG (d, a, b, c, x[k + 10], S22, 0x2441453);
        c = GG (c, d, a, b, x[k + 15], S23, 0xD8A1E681);
        b = GG (b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
        a = GG (a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
        d = GG (d, a, b, c, x[k + 14], S22, 0xC33707D6);
        c = GG (c, d, a, b, x[k + 3], S23, 0xF4D50D87);
        b = GG (b, c, d, a, x[k + 8], S24, 0x455A14ED);
        a = GG (a, b, c, d, x[k + 13], S21, 0xA9E3E905);
        d = GG (d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
        c = GG (c, d, a, b, x[k + 7], S23, 0x676F02D9);
        b = GG (b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
        a = HH (a, b, c, d, x[k + 5], S31, 0xFFFA3942);
        d = HH (d, a, b, c, x[k + 8], S32, 0x8771F681);
        c = HH (c, d, a, b, x[k + 11], S33, 0x6D9D6122);
        b = HH (b, c, d, a, x[k + 14], S34, 0xFDE5380C);
        a = HH (a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
        d = HH (d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
        c = HH (c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
        b = HH (b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
        a = HH (a, b, c, d, x[k + 13], S31, 0x289B7EC6);
        d = HH (d, a, b, c, x[k + 0], S32, 0xEAA127FA);
        c = HH (c, d, a, b, x[k + 3], S33, 0xD4EF3085);
        b = HH (b, c, d, a, x[k + 6], S34, 0x4881D05);
        a = HH (a, b, c, d, x[k + 9], S31, 0xD9D4D039);
        d = HH (d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
        c = HH (c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
        b = HH (b, c, d, a, x[k + 2], S34, 0xC4AC5665);
        a = II (a, b, c, d, x[k + 0], S41, 0xF4292244);
        d = II (d, a, b, c, x[k + 7], S42, 0x432AFF97);
        c = II (c, d, a, b, x[k + 14], S43, 0xAB9423A7);
        b = II (b, c, d, a, x[k + 5], S44, 0xFC93A039);
        a = II (a, b, c, d, x[k + 12], S41, 0x655B59C3);
        d = II (d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
        c = II (c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
        b = II (b, c, d, a, x[k + 1], S44, 0x85845DD1);
        a = II (a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
        d = II (d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
        c = II (c, d, a, b, x[k + 6], S43, 0xA3014314);
        b = II (b, c, d, a, x[k + 13], S44, 0x4E0811A1);
        a = II (a, b, c, d, x[k + 4], S41, 0xF7537E82);
        d = II (d, a, b, c, x[k + 11], S42, 0xBD3AF235);
        c = II (c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
        b = II (b, c, d, a, x[k + 9], S44, 0xEB86D391);
        a = AddUnsigned (a, AA);
        b = AddUnsigned (b, BB);
        c = AddUnsigned (c, CC);
        d = AddUnsigned (d, DD);
    }

    var temp = WordToHex (a) + WordToHex (b) + WordToHex (c) + WordToHex (d);

    return temp.toLowerCase ();
}