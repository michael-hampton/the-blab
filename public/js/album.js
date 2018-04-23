var selectedUser = null;


// display photo preview block
function getPhotoPreviewAjx (id)
{
    $.post ('/blab/photo/displayPicture', {action: 'get_info', id: id},
            function (data)
            {
                $ ('#photo-comment-body .pleft').html (data.data1);
                $ ('#photo-comment-body .pright').html (data.data2);
                $ ('#photo-comment').modal ("show");
                tagging (id);
            }, "json"
            );
}
;
// submit comment
function submitComment (id)
{
    var sName = $ ('#name').val ();
    var sText = $ ('#text').val ();
    if (sText)
    {
        $.post ('/blab/photo/savePhotoComment', {action: 'accept_comment', uploadId: $ ("#uploadId").val (), name: sName, text: sText, id: id},
                function (response)
                {
                    try {
                        var objResponse = $.parseJSON (response);

                        if (objResponse.sucess == 0)
                        {
                            showErrorMessage ('Unable to save comment');
                        } else {
                            refreshComments();
                        }
                    } catch (error) {
                        showErrorMessage ();
                    }

                }
        );
    }
    else
    {
        $ ('#comments_warning1').fadeIn (1000, function ()
        {
            $ (this).fadeOut (1000);
        });
    }
}
;
// init
// onclick event handlers
$ ('#photo_preview .photo_wrp').click (function (event)
{
    event.preventDefault ();
    return false;
});

$ (".createNewAlbum").on ("click", function ()
{
    $ ("#createAlbumModal").modal ("show");
});

$ ('#photo_preview').click (function (event)
{
    closePhotoPreview ();
});

$ ('#photo_preview img.close').click (function (event)
{
    closePhotoPreview ();
});

// display photo preview ajaxy
$ ('.photo img').click (function (event)
{
    if (event.preventDefault)
        event.preventDefault ();
    getPhotoPreviewAjx ($ (this).attr ('id'));
});

function refreshComments ()
{
    var uploadId = $ ("#uploadId").val ();

    $.post ('/blab/photo/refreshComments', {id: uploadId},
            function (response)
            {
                try {
                    var objResponse = $.parseJSON (response);

                    if (objResponse.sucess == 0)
                    {
                        showErrorMessage ('Unable to refresh comments');
                    }
                } catch (error) {
                    $ ("#comments_list").html (response);
                }
            });
}

function tagging (photoId)
{
    var cache = {};
    var drew = false;

    $ ("#title").on ("keyup", function (event)
    {
        var query = $ ("#title").val ();

        if ($ ("#title").val ().length > 2)
        {

//                //Check if we've searched for this term before
//                if (query in cache)
//                {
//                    results = cache[query];
//                    buildAutoCompleteResults (results, drew);
//
//                }
//                else
//                {
            var results = null;

            $.ajax ({
                type: 'GET',
                url: '/blab/index/autoSuggest?term=' + $ ("#title").val (),
                success: function (response)
                {
                    buildAutoCompleteResults (response, drew);

                }
            });

            //Add results to cache
            cache[query] = results;
            //}
        }
        else
        {
            $ ("#res").html ("");
        }
    });

    $ ("#imageMap").click (function (e)
    {


        var image_left = $ (this).offset ().left;
        var click_left = e.pageX;
        var left_distance = click_left - image_left;

        var image_top = $ (this).offset ().top;
        var click_top = e.pageY;
        var top_distance = click_top - image_top;

        var mapper_width = $ ('#mapper').width ();
        var imagemap_width = $ ('#imageMap').width ();

        var mapper_height = $ ('#mapper').height ();
        var imagemap_height = $ ('#imageMap').height ();

        if ((top_distance + mapper_height > imagemap_height) && (left_distance + mapper_width > imagemap_width))
        {
            $ ('#mapper').css ("left", (click_left - mapper_width - image_left))
                    .css ("top", (click_top - mapper_height - image_top))
                    .css ("width", "100px")
                    .css ("height", "100px")
                    .show ();
        }
        else if (left_distance + mapper_width > imagemap_width)
        {


            $ ('#mapper').css ("left", (click_left - mapper_width - image_left))
                    .css ("top", top_distance)
                    .css ("width", "100px")
                    .css ("height", "100px")
                    .show ();

        }
        else if (top_distance + mapper_height > imagemap_height)
        {
            $ ('#mapper').css ("left", left_distance)
                    .css ("top", (click_top - mapper_height - image_top))
                    .css ("width", "100px")
                    .css ("height", "100px")
                    .show ();
        }
        else
        {


            $ ('#mapper').css ("left", left_distance)
                    .css ("top", top_distance)
                    .css ("width", "100px")
                    .css ("height", "100px")
                    .show ();
        }


        $ ("#mapper").resizable ({containment: "parent"});
        $ ("#mapper").draggable ({containment: "parent"});

    });

    $ (document).off ("mouseover", ".tagged");
    $ (document).on ('mouseover', '.tagged', function ()
    {
        if ($ (this).find (".openDialog").length == 0)
        {
            $ (this).find (".tagged_box").css ("display", "block");
            $ (this).css ("border", "5px solid #EEE");

            $ (this).find (".tagged_title").css ("display", "block");
        }


    });


    $ (".tagged").on ("mouseout", function ()
    {
        if ($ (this).find (".openDialog").length == 0)
        {
            $ (this).find (".tagged_box").css ("display", "none");
            $ (this).css ("border", "none");
            $ (this).find (".tagged_title").css ("display", "none");
        }


    });



    $ (".tagged").on ("click", function ()
    {
        $ (this).find (".tagged_box").html ("<img src='del.png' class='openDialog' value='Delete' onclick='deleteTag(this)' />\n\
        <img src='save.png' onclick='editTag(this);' value='Save' />");

        var img_scope_top = $ ("#imageMap").offset ().top + $ ("#imageMap").height () - $ (this).find (".tagged_box").height ();
        var img_scope_left = $ ("#imageMap").offset ().left + $ ("#imageMap").width () - $ (this).find (".tagged_box").width ();

        $ (this).draggable ({containment: [$ ("#imageMap").offset ().left, $ ("#imageMap").offset ().top, img_scope_left, img_scope_top]});

    });

    $ (".addTag").on ("click", function ()
    {
        var position = $ ('#mapper').position ();


        var pos_x = position.left;
        var pos_y = position.top;
        var pos_width = $ ('#mapper').width ();
        var pos_height = $ ('#mapper').height ();

        $.ajax ({
            type: 'POST',
            url: '/blab/photo/tagPhoto',
            data: {pos_x: pos_x, pos_y: pos_y, title: $ ("#title").val (), photoId: photoId, userId: selectedUser},
            success: function (response)
            {
                alert (response);

            }
        });


        $ ('#planetmap').append ('<div class="tagged"  style="width:' + pos_width + ';height:' +
                pos_height + ';left:' + pos_x + ';top:' + pos_y + ';" ><div   class="tagged_box" style="width:' + pos_width + ';height:' +
                pos_height + ';display:none;" ></div><div class="tagged_title" style="top:' + (pos_height + 5) + ';display:none;" >' +
                $ ("#title").val () + '</div></div>');

        $ ("#mapper").hide ();
        $ ("#title").val ('');
        $ ("#form_panel").hide ();
    });

    $ (".openDialog").on ("click", function ()
    {
        $ ("#form_panel").fadeIn ("slow");
    });
}

function buildAutoCompleteResults (results, drew)
{
    if ($ ("#res").length <= 0)
    {
        $ ("#title").after ('<ul id="res"></ul>');
    }


    //Bind click event to list elements in results
    $ ("#res").on ("click", "li", function ()
    {
        selectedUser = $ (this).attr ("userid");
        $ ("#title").val ($ (this).text ());
        $ ("#res").empty ();
        $ (".addTag").removeAttr ("disabled");
    });

    //Clear old results

    $ ("#res").html ('');


    var data = $.parseJSON (results);

    $.each (data, function (i, item)
    {
        $ ("#res").append ("<li userid='" + item.uid + "'>" + item.value + "</li>");
    });
}

$ (".deletePhoto").on ("click", function ()
{
    var id = $ (this).attr ("id");

    $.ajax ({
        type: "POST",
        url: '/blab/photo/deletePhoto',
        data: {id: id},
        success: function (response)
        {
            $ (".photo[id=" + id + "]").remove ();
        }
    });

    return false;
});

$ (".dropbtn").on ("click", function ()
{
    $ (".dropdown-content").hide ();

    if ($ (this).next (".dropdown-content").hasClass ("active-drop"))
    {
        $ (this).next (".dropdown-content").removeClass ("active-drop").stop ().slideUp (100);
    }
    else
    {
        $ (this).next (".dropdown-content").addClass ("active-drop").stop ().slideDown (100);
    }



});

$ (".viewAllAlbums").on ("click", function ()
{
    $.ajax ({
        type: "GET",
        url: '/blab/photo/viewAlbums',
        success: function (response)
        {
            $ ("#vpb_display_photos").html (response);
        }
    });

    return false;
});

$ (".openalbum").on ("click", function ()
{
    var id = $ (this).parent ().attr ("id");

    $.ajax ({
        type: "GET",
        url: '/blab/photo/viewAlbum/' + id,
        success: function (response)
        {
            $ ("#vpb_display_photos").html (response);
        }
    });
});

$ (".SaveAlbum").on ("click", function ()
{
    if ($.trim ($ ("#albumName").val ()) === "")
    {
        showErrorMessage ('Name cannot be empty');
        return false;
    }

    $.ajax ({
        type: "POST",
        url: '/blab/photo/saveNewAlbum',
        data: $ ("#albumForm").serialize (),
        success: function (response)
        {
            alert (response);
        }
    });
});

// add event handler
var addEvent = (function ()
{
    if (document.addEventListener)
    {
        return function (el, type, fn)
        {
            if (el && el.nodeName || el === window)
            {
                el.addEventListener (type, fn, false);
            }
            else if (el && el.length)
            {
                for (var i = 0; i < el.length; i++) {
                    addEvent (el[i], type, fn);
                }
            }
        };
    }
    else
    {
        return function (el, type, fn)
        {
            if (el && el.nodeName || el === window)
            {
                el.attachEvent ('on' + type, function ()
                {
                    return fn.call (el, window.event);
                });
            }
            else if (el && el.length)
            {
                for (var i = 0; i < el.length; i++) {
                    addEvent (el[i], type, fn);
                }
            }
        };
    }
}) ();
// inner variables
var dragItems;
updateDataTransfer ();
var dropAreas = document.querySelectorAll ('[droppable=true]');
// preventDefault (stops the browser from redirecting off to the text)
function cancel (e)
{
    if (e.preventDefault)
    {
        e.preventDefault ();
    }
    return false;
}
// update event handlers
function updateDataTransfer ()
{
    dragItems = document.querySelectorAll ('[draggable=true]');
    for (var i = 0; i < dragItems.length; i++) {
        addEvent (dragItems[i], 'dragstart', function (event)
        {
            event.dataTransfer.setData ('obj_id', this.id);
            return false;
        });
    }
}
// dragover event handler
addEvent (dropAreas, 'dragover', function (event)
{
    if (event.preventDefault)
        event.preventDefault ();
    // little customization
    this.style.borderColor = "#000";
    return false;
});
// dragleave event handler
addEvent (dropAreas, 'dragleave', function (event)
{
    if (event.preventDefault)
        event.preventDefault ();
    // little customization
    this.style.borderColor = "#ccc";
    return false;
});
// dragenter event handler
addEvent (dropAreas, 'dragenter', cancel);
// drop event handler
addEvent (dropAreas, 'drop', function (event)
{
    var album = this.id;

    if (event.preventDefault)
    {

        event.preventDefault ();
    }
    // get dropped object
    var iObj = event.dataTransfer.getData ('obj_id');

    $.ajax ({
        type: 'POST',
        url: '/blab/photo/addPhotoToAlbum',
        data: {photoId: iObj, albumId: album},
        success: function (response)
        {
            var objResponse = $.parseJSON (response);

            if (objResponse.sucess == 0)
            {
                showErrorMessage (objResponse.message);
            }
            else
            {
                showSuccessMessage ('Photo was added to the album successfully.');
            }

        }
    });


    var oldObj = document.getElementById (iObj);


    // get its image src
    var oldSrc = document.getElementById ('46').getElementsByTagName ('img')[0].src;

    oldObj.className += 'hidden';
    var oldThis = this;
    setTimeout (function ()
    {
        oldObj.parentNode.removeChild (oldObj); // remove object from DOM
        // add similar object in another place
        oldThis.innerHTML += '<a id="' + iObj + '" draggable="true"><img src="' + oldSrc + '" /></a>';
        // and update event handlers
        updateDataTransfer ();
        // little customization
        oldThis.style.borderColor = "#ccc";
    }, 500);
    return false;
});
