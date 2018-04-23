<style>
    /* project styles */
    .container {
        border: 1px solid #111111;
        color: #000000;
        margin: 20px auto;
        overflow: hidden;
        padding: 15px;
        position: relative;
        text-align: center;
        width: 1090px;
        -moz-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }
    .photo {
        border: 1px solid transparent;
        float: left;
        margin: 4px;
        overflow: hidden;
        padding: 4px;
        white-space: nowrap;
        /* CSS3 Box sizing property */
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -o-box-sizing: border-box;
        box-sizing: border-box;
        /* CSS3 transition */
        -moz-transition: border 0.2s ease 0s;
        -ms-transition: border 0.2s ease 0s;
        -o-transition: border 0.2s ease 0s;
        -webkit-transition: border 0.2s ease 0s;
        transition: border 0.2s ease 0s;
    }
    .photo:hover {
        border-color: #444;
    }
    .photo img {
        cursor: pointer;
        width: 200px;
    }
    .photo p, .photo i {
        display: block;
    }
    .photo p {
        font-weight: bold;
    }
    /* preview styles */
    #photo_preview {
        background-color: rgba(0, 0, 0, 0.7);
        bottom: 0;
        color: #000000;
        display: none;
        left: 0;
        overflow: hidden;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 10;
    }
    .photo_wrp {
        background-color: #FAFAFA;
        height: auto;
        margin: 100px auto 0;
        overflow: hidden;
        padding: 15px;
        text-align: center;
        vertical-align: middle;
        width: 1000px;
        -moz-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
    }
    .close {
        cursor: pointer;
        float: right;
    }
    .pleft {
        float: left;
        overflow: hidden;
        position: relative;
        width: 600px;
    }
    .pright {
        float: right;
        position: relative;
        width: 360px;
    }
    .preview_prev, .preview_next {
        cursor: pointer;
        margin-top: -64px;
        opacity: 0.5;
        position: absolute;
        top: 50%;
        -moz-transition: opacity 0.2s ease 0s;
        -ms-transition: opacity 0.2s ease 0s;
        -o-transition: opacity 0.2s ease 0s;
        -webkit-transition: opacity 0.2s ease 0s;
        transition: opacity 0.2s ease 0s;
    }
    .preview_prev:hover, .preview_next:hover {
        opacity: 1;
    }
    .preview_prev {
        left: 20px;
    }
    .preview_next {
        right: 40px;
    }
    /* comments styles */
    #comments form {
        margin: 10px 0;
        text-align: left;
    }
    #comments table td.label {
        color: #000;
        font-size: 13px;
        padding-right: 3px;
        text-align: right;
        width: 105px;
    }
    #comments table label {
        color: #000;
        font-size: 16px;
        font-weight: normal;
        vertical-align: middle;
    }
    #comments table td.field input, #comments table td.field textarea {
        border: 1px solid #96A6C5;
        font-family: Verdana,Arial,sans-serif;
        font-size: 16px;
        margin-top: 2px;
        padding: 6px;
        width: 250px;
    }
    #comments_list {
        margin: 10px 0;
        text-align: left;
    }
    #comments_list .comment {
        border-top: 1px solid #000;
        padding: 10px 0;
    }
    #comments_list .comment:first-child {
        border-top-width:0px;
    }
    #comments_list .comment span {
        font-size: 11px;
    }
</style>


<?php
session_start ();

if ( isset ($_GET['userId']) )
{
    $userId = $_GET['userId'];
}
else
{
    $userId = $_SESSION['user']['user_id'];
}

require_once 'models/User.php';
require_once 'models/UploadFactory.php';

$objUser = new User ($userId);
$objUpload = new UploadFactory();

$arrPhotos = $objUpload->getUploadaForUser ($objUser);

if ( !empty ($arrPhotos) )
{

    $sPhotos = '<div class="col-lg-12 pull-left">';

    foreach ($arrPhotos as $arrPhoto) {

        $arrPhoto['title'] = "Test";
        $arrPhoto['description'] = "description";

        $sPhotos .= '<div class="photo col-lg-2 pull-left"><img src="' . str_replace ($_SERVER['DOCUMENT_ROOT'].'/blab/', '', $arrPhoto['file_location']) . '" id="' . $arrPhoto['upload_id'] . '" /><p>' . $arrPhoto['title'] . ' item</p><i>' . $arrPhoto['description'] . '</i></div>';
    }
    
    $sPhotos .= '</div>';
}
?>


<h2>blab like photo gallery with comments</h2>

<!-- Container with last photos -->
<div class="container">
    <h1>Last photos:</h1>
    <?= $sPhotos ?>
</div>
<!-- Hidden preview block -->
<div id="photo_preview" style="display:none">
    <div class="photo_wrp">
        <img class="close" src="images/close.gif" />
        <div style="clear:both"></div>
        <div class="pleft">test1</div>
        <div class="pright">test2</div>
        <div style="clear:both"></div>
    </div>
</div>

<script>
    // close photo preview block
    function closePhotoPreview ()
    {
        $ ('#photo_preview').hide ();
        $ ('#photo_preview .pleft').html ('empty');
        $ ('#photo_preview .pright').html ('empty');
    }
    ;
    // display photo preview block
    function getPhotoPreviewAjx (id)
    {
        $.post ('photos_ajx.php', {action: 'get_info', id: id},
                function (data)
                {
                    $ ('#photo_preview .pleft').html (data.data1);
                    $ ('#photo_preview .pright').html (data.data2);
                    $ ('#photo_preview').show ();
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
            alert ("Mike");
            $.post ('controllers/savePhotoComment.php', {action: 'accept_comment', uploadId: $ ("#uploadId").val (), name: sName, text: sText, id: id},
                    function (data)
                    {
                        if (data != '1')
                        {
                            $ ('#comments_list').fadeOut (1000, function ()
                            {
                                $ (this).html (data);
                                $ (this).fadeIn (1000);
                            });
                        }
                        else
                        {
                            $ ('#comments_warning2').fadeIn (1000, function ()
                            {
                                $ (this).fadeOut (1000);
                            });
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
    $ (function ()
    {
        // onclick event handlers
        $ ('#photo_preview .photo_wrp').click (function (event)
        {
            event.preventDefault ();
            return false;
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
        $ ('.container .photo img').click (function (event)
        {
            if (event.preventDefault)
                event.preventDefault ();
            getPhotoPreviewAjx ($ (this).attr ('id'));
        });
    })
</script>
