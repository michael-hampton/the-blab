<?php

use Phalcon\Mvc\View;

class AdvertController extends ControllerBase
{

    public function createBusinessAdvertAction ()
    {
        return $this->dispatcher->forward (
                        [
                            "controller" => "advert",
                            "action" => "createAdvert",
                            "params" => ["type" => "advert"]
                        ]
        );
    }

    public function createAdvertAction ()
    {

        $type = $this->dispatcher->getParam ("type", "string");
        
        if(  trim ($type) !== "profile") {
            $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);
        }
        
        $this->view->type = $type;

        $arrCountries = (new Location())->getCountries ();

        if ( $arrCountries === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrLanguages = (new Location())->getLangauges ();

        if ( $arrLanguages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrGenders = array("Male", "Female");

        $this->view->arrLanguages = $arrLanguages;
        $this->view->arrCountries = $arrCountries;
        $this->view->arrGenders = $arrGenders;
    }

    public function saveAdvertAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['advertGender']) ||
                !isset ($_POST['advertTitle']) ||
                !isset ($_POST['advertLanguage']) ||
                !isset ($_POST['advertLocation']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objAdvert = (new AdvertFactory())->createAdvert (
                new User ($_SESSION['user']['user_id']), $_POST['advertTitle'], $_POST['advertLocation'], $_POST['advertLanguage'], $_POST['advertGender']);

        if ( $objAdvert === false )
        {
            $this->ajaxresponse ("error", "Unable to create advert");
        }

        if ( empty ($_FILES) )
        {
            $this->ajaxresponse ("error", "No files uploaded");
        }

        if ( empty ($_POST['tags']) )
        {
            $this->ajaxresponse ("error", "Missing tags");
        }

        $arrTags = json_decode ($_POST['tags'], true);

        if ( empty ($_POST['url']) )
        {
            $this->ajaxresponse ("error", "Missing Url");
        }

        $arrUrls = json_decode ($_POST['url'], true);

        $arrFiles = $_FILES;

        foreach ($arrFiles as $key => $arrFile) {

            $key = str_replace ("file-", "", $key);

            if ( empty ($arrFile['name']) )
            {
                continue;
            }

            $target_dir = $this->rootPath . "/blab/public/uploads/adverts/";
            $target_file = $target_dir . basename ($arrFile["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

            if ( $arrFile["size"] > 500000 )
            {
                continue;
            }

            // Allow certain file formats
            if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
            {
                continue;
            }

            $check = getimagesize ($arrFile["tmp_name"]);

            if ( $check !== false )
            {
                
            }
            else
            {
                continue;
            }

            if ( move_uploaded_file ($arrFile["tmp_name"], $target_file) )
            {
                $fileLocation = str_replace ($this->rootPath, "", $target_file);
                $title = $arrTags[$key];
                $url = $arrUrls[$key];

                $objBanner = (new BannerFactory())->createBanner ($fileLocation, $title, $url, $objAdvert);

                if ( $objBanner === false )
                {
                    $this->ajaxresponse ("error", "Unable to create banner");
                }
            }
        }

        $this->ajaxresponse ("success", "success");
    }

    public function indexAction ()
    {
        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrAdverts = (new AdvertFactory())->getAdvertsForUser (new User ($_SESSION['user']['user_id']));

        if ( $arrAdverts === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $this->view->arrAdverts = $arrAdverts;
    }

    /**
     * 
     * @param type $advertId
     */
    public function getBannersAction ($advertId)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !is_numeric ($advertId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrBanners = (new BannerFactory())->getBannersForAdvert (new Advert ($advertId));

        if ( !empty ($arrBanners) )
        {
            foreach ($arrBanners as $arrBanner) {
                echo '<div class="col-lg-4 pull-left">Title: ' . $arrBanner->getCaption () . '<br> Url: ' . $arrBanner->getUrl () . '<br>
                    <a href="#" class="deleteBanner" id="' . $arrBanner->getId () . '">Delete</a><br>
                        <img src="' . $arrBanner->getImageLocation () . '"></div>';
            }
        }
    }

    /**
     * 
     * @param type $bannerId
     */
    public function deleteBannerAction ()
    {
        if ( empty ($_POST['bannerId']) || !is_numeric ($_POST['bannerId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $bannerId = $_POST['bannerId'];

        $this->view->disable ();

        $blResponse = (new Banner ($bannerId))->delete ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    /**
     * 
     * @param type $id
     */
    public function deleteAdvertAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['advertId']) || !is_numeric ($_POST['advertId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $id = $_POST['advertId'];

        $blResponse = (new Advert ($id))->delete ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function createProfileBannerAction ()
    {

        return $this->dispatcher->forward (
                        [
                            "controller" => "advert",
                            "action" => "createAdvert",
                            "params" => ["type" => "profile"]
                        ]
        );
    }

}
