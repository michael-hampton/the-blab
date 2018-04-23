<?php

require_once 'models/User.php';
require_once 'models/Database.php';
require_once 'models/Upload.php';
require_once 'models/PhotoCommentFactory.php';

session_start ();

$userId = $_SESSION['user']['user_id'];

$objUser = new User ($userId);

$objComment = new PhotoCommentFactory();

$db = new Database();

$db->connect ();

if ( $_POST['action'] == 'get_info' && (int) $_POST['id'] > 0 )
{
    // get photo info
    $iPid = (int) $_POST['id'];

    $objUpload = new Upload ($iPid);


    // draw last 10 comments
    $sComments = '';
    $aComments = $objComment->getCommentsForUpload ($objUpload);

    foreach ($aComments as $i => $aCmtsInfo) {

        $sWhen = date ('F j, Y H:i', strtotime ($aCmtsInfo['created']));
        $sComments .=
                '<div class="comment" id="' . $aCmtsInfo['id'] . '">
                <p>Comment from ' . $aCmtsInfo['user_id'] . ' <span>(' . $sWhen . ')</span>:</p>
                <p>' . $aCmtsInfo['comment'] . '</p>
            </div>';
    }

    $sCommentsBlock = '<div class="comments" id="comments">
    <h2>Comments</h2>
    <div id="comments_warning1" style="display:none">Don`t forget to fill both fields (Name and Comment)</div>
    <div id="comments_warning2" style="display:none">You cant post more than one comment per 10 minutes (spam protection)</div>
    <form onsubmit="return false;">
        <table>
        <input type="hidden" id="uploadId" value="'.$iPid.'">
            <tr><td class="label"><label>Your name: </label></td><td class="field"><input type="text" value="" title="Please enter your name" id="name" /></td></tr>
            <tr><td class="label"><label>Comment: </label></td><td class="field"><textarea name="text" id="text"></textarea></td></tr>
            <tr><td class="label">&nbsp;</td><td class="field"><button onclick="submitComment(); return false;">Post comment</button></td></tr>
        </table>
    </form>
    <div id="comments_list">' . $sComments . '</div>
</div>';

    require_once 'models/UploadFactory.php';
    $objUploadFactory = new UploadFactory();
        
    $arrPagination = $objUploadFactory->buildPagination ($objUpload, $objUser);

//    // Prev & Next navigation
    $sNext = $sPrev = '';
    $sPrevBtn = ($arrPagination[0]['prev_id']) ? '<div class="preview_prev" onclick="getPhotoPreviewAjx(\'' . $arrPagination[0]['prev_id'] . '\')"><img src="images/prev.png" alt="prev" /></div>' : '';
    $sNextBtn = ($arrPagination[0]['next_id']) ? '<div class="preview_next" onclick="getPhotoPreviewAjx(\'' . $arrPagination[0]['next_id'] . '\')"><img src="images/next.png" alt="next" /></div>' : '';

    echo json_encode (array(
        'data1' => '<img class="fileUnitSpacer" style="height:400px;" src="' . $objUpload->getFileLocation () . '">' . $sPrevBtn . $sNextBtn,
        'data2' => $sCommentsBlock
    ));
    exit;
}