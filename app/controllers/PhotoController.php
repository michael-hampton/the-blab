<?php

use Phalcon\Mvc\View;

/**
 * Description of PhotoController
 *
 * @author michael.hampton
 */
class PhotoController extends ControllerBase
{

    public function saveNewAlbumAction ()
    {
        $this->view->disable ();

        $objAlbum = new AlbumFactory();

        if ( empty ($_POST['albumDescription']) || empty ($_POST['albumName']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = $objAlbum->create ($_POST['albumName'], $_POST['albumDescription'], new User ($_SESSION['user']['user_id']));

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
    }

    public function tagPhotoAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['userId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objSelectedUser = new User ($_POST['userId']);

        $newUser = new User ($_SESSION['user']['user_id']);

        $blResponse = (new NotificationFactory())->createNotification ($objSelectedUser, $newUser->getUsername () . " Tagged you in a photo");

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objPhotoTag = new PhotoTagFactory();

        $blResponse = $objPhotoTag->createPhotoTag ($_POST['pos_x'], $_POST['pos_y'], $_POST['title'], new Upload ($_POST['photoId']), $newUser, $objSelectedUser);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "CANT SAVE");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function deletePhotoAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResult = (new Upload ($_POST['id']))->delete ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }

    public function displayPictureAction ()
    {
        $this->view->disable ();

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $objComment = new PhotoCommentFactory();

        if ( $_POST['action'] == 'get_info' && (int) $_POST['id'] > 0 )
        {
            // get photo info
            $iPid = (int) $_POST['id'];

            $objUpload = new Upload ($iPid);

            $arrTags = (new PhotoTagFactory())->getTagsForPhoto ($objUpload);

            if ( $arrTags === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            // draw last 10 comments
            $sComments = '';
            $aComments = $objComment->getCommentsForUpload ($objUpload);

            if ( $aComments === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

            foreach ($aComments as $i => $aCmtsInfo) {

                $sWhen = date ('F j, Y H:i', strtotime ($aCmtsInfo->getDateAdded ()));
                $sComments .=
                        '<div class="comment" id="' . $aCmtsInfo->getId () . '">
                <p>Comment from ' . $aCmtsInfo->getAuthor () . ' <span>(' . $sWhen . ')</span>:</p>
                <p>' . $aCmtsInfo->getComment () . '</p>
            </div>';
            }

            $sCommentsBlock = '<div class="comments" id="comments">
    <h2>Comments</h2>
    <div id="comments_warning1" style="display:none">Don`t forget to fill both fields (Name and Comment)</div>
    <div id="comments_warning2" style="display:none">You cant post more than one comment per 10 minutes (spam protection)</div>
    <form onsubmit="return false;">
        <table>
        <input type="hidden" id="uploadId" value="' . $iPid . '">
            <tr><td class="field"><textarea name="text" id="text" class="form-control" style="width:100%;"></textarea></td></tr>
            <tr><td class="field"><button class="btn btn-primary" onclick="submitComment(); return false;">Post comment</button></td></tr>
        </table>
    </form>
    <div id="comments_list">' . $sComments . '</div>
</div>';

            $objUploadFactory = new UploadFactory();

            $arrPagination = $objUploadFactory->buildPagination ($objUpload, $objUser);

            if ( $arrPagination === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }

//    // Prev & Next navigation
            $sNext = $sPrev = '';
            $sPrevBtn = ($arrPagination[0]['prev_id']) ? '<div class="preview_prev" onclick="getPhotoPreviewAjx(\'' . $arrPagination[0]['prev_id'] . '\')"><i style="font-size:44px !important;" class="fa fa-arrow-circle-o-left"></div>' : '';
            $sNextBtn = ($arrPagination[0]['next_id']) ? '<div class="preview_next" onclick="getPhotoPreviewAjx(\'' . $arrPagination[0]['next_id'] . '\')"><i style="font-size:44px !important;" class="fa fa-arrow-circle-o-right"></i></div>' : '';

            $arrHTML = [];
            $arrHTML['data1'] = [];

            $arrHTML['data1'][] = '<div id="image_panel"><img id="imageMap" class="fileUnitSpacer" style="height:400px;" src="' . $objUpload->getFileLocation () . '">' . $sPrevBtn . $sNextBtn;

            $tagHTML = '';

            foreach ($arrTags as $arrTag) {
                $tagHTML .= '<div class="tagged"  style="width:100px; height:50px; left:' . $arrTag->getPosX () . ';top:' . $arrTag->getPosY () . ';" ><div class="tagged_box" style="width: 105px; height: 55px;display:none;" ></div><div class="tagged_title" style="top: 60px ;display:none;" >' .
                        $arrTag->getTitle () . '</div></div>';
            }

            $form = "<div id = 'form_panel'>"
                    . "<div class = 'row'>"
                    . "<div class = 'label'>Title</div>"
                    . "<div class = 'field'>"
                    . "<input type = 'text' id = 'title' />"
                    . "</div>"
                    . "</div>"
                    . "<div class = 'row'>"
                    . "<div class = 'label'></div>"
                    . "<div class = 'field'>"
                    . "<input type = 'button' disabled='disabled' value = 'Add Tag' class = 'addTag' />"
                    . "</div>"
                    . "</div>";

            $arrHTML['data1'][] = "<div id='mapper' ><img src='save.png' class='openDialog' /></div>";
            $arrHTML['data1'][] = '<div id="planetmap">' . $tagHTML . '</div>';
            $arrHTML['data1'][] = $form;
            $arrHTML['data1'][] = "</div>";
            $arrHTML['data1'][] = "</div>";
            $arrHTML['data1'][] = "</div>";


            $arrHTML ['data2'] = $sCommentsBlock;

            echo json_encode ($arrHTML);
        }
    }

    public function viewAlbumsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( isset ($_GET['userId']) )
        {
            $userId = $_GET['userId'];
        }
        else
        {
            $userId = $_SESSION['user']['user_id'];
        }

        $objUser = new User ($userId);
        $objUpload = new UploadFactory();

        $arrAlbumPhotos = $objUpload->getPhotosForAlbum ();

        if ( $arrAlbumPhotos === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrAlbumPhotos = $arrAlbumPhotos;

        $this->view->arrPhotos = $objUpload->getUploadaForUser ($objUser);

        if ( $this->view->arrPhotos === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrAlbums = (new AlbumFactory())->getAlbums ($objUser);

        if ( $arrAlbums === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrAlbums = $arrAlbums;
    }

    public function albumAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( isset ($_GET['userId']) )
        {
            $userId = $_GET['userId'];
        }
        else
        {
            $userId = $_SESSION['user']['user_id'];
        }


        $objUser = new User ($userId);
        $objUpload = new UploadFactory();

        $arrAlbumPhotos = $objUpload->getPhotosForAlbum ();

        if ( $arrAlbumPhotos === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrAlbumPhotos = $arrAlbumPhotos;

        $this->view->arrPhotos = $objUpload->getUploadaForUser ($objUser);

        if ( $this->view->arrPhotos === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrAlbums = (new AlbumFactory())->getAlbums ($objUser);

        if ( $arrAlbums === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrAlbums = $arrAlbums;
    }

    public function addPhotoToAlbumAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['photoId']) || empty ($_POST['albumId']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        try {
            $objAlbumFactory = new UploadFactory();
            $blResult = $objAlbumFactory->addtoAlbum (new Upload ($_POST['photoId']), new Album ($_POST['albumId']));

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", $this->defaultErrrorMessage);
            }
        } catch (Exception $ex) {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", 'success');
    }

    public function viewAlbumAction ($albumId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( trim ($albumId) === "" || !is_numeric ($albumId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->albumId = $albumId;
        $objUploadFactory = new UploadFactory();

        $objAlbum = new Album ($albumId);

        $arrPhotos = $objUploadFactory->getPhotosForAlbum ($objAlbum);

        if ( $arrPhotos === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrPhotos = $arrPhotos;
        $this->view->objAlbum = $objAlbum;
    }

    public function savePhotoCommentAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) ||
                empty ($_POST['uploadId']) ||
                empty ($_POST['action']) ||
                empty ($_POST['text']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $objUpload = new Upload ($_POST['uploadId']);

        if ( $_POST['action'] == 'accept_comment' )
        {
            $objCommentFactory = new PhotoCommentFactory();

            $blResult = $objCommentFactory->create ($objUser, $objUpload, $_POST['text']);

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", "Unable to save");
            }
        }
        
         $this->ajaxresponse ("success", "success");
    }

    public function refreshCommentsAction ()
    {
        $this->view->disable ();

        $objComment = new PhotoCommentFactory();

        if ( empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUpload = new Upload ($_POST['id']);

        $sComments = '';
        $aComments = $objComment->getCommentsForUpload ($objUpload);

        if ( $aComments === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($aComments as $aCmtsInfo) {

            $sWhen = date ('F j, Y H:i', strtotime ($aCmtsInfo->getDateAdded ()));
            $sComments .=
                    '<div class="comment" id="' . $aCmtsInfo->getId () . '">
                <p>Comment from ' . $aCmtsInfo->getAuthor () . ' <span>(' . $sWhen . ')</span>:</p>
                <p>' . $aCmtsInfo->getComment () . '</p>
            </div>';
        }
        
        echo $sComments;
    }

}
