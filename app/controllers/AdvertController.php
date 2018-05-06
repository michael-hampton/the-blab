<?php

use Phalcon\Mvc\View;

class AdvertController extends ControllerBase
{

    use UploadAdvert;

    public function createAdvertAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $this->view->type = 'advert';

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

    public function saveProfileBannerAction ()
    {
        $this->view->disable ();

        if (
                !isset ($_POST['bannerType']) ||
                empty ($_POST['url']) ||
                empty ($_POST['tags']) ||
                !isset ($_POST['advertTitle'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        if ( empty ($_FILES) )
        {
            $this->ajaxresponse ("error", "No files uploaded");
        }

        $objAdvert = (new AdvertFactory())->createProfileBanner (
                new User ($_SESSION['user']['user_id']), $_POST['advertTitle']);

        if ( $objAdvert === false )
        {
            $this->ajaxresponse ("error", "Unable to create advert");
        }

        $arrTags = json_decode ($_POST['tags'], true);

        $arrUrls = json_decode ($_POST['url'], true);

        $arrFiles = $_FILES;

        if ( $this->uploadFiles ($arrFiles, $objAdvert, $arrTags, $arrUrls, 'profile') === false )
        {
            $this->ajaxresponse ("error", $this->getValidationFailures ());
        }

        $this->ajaxresponse ("success", "success");
    }

    public function saveAdvertAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['advertGender']) ||
                !isset ($_POST['advertTitle']) ||
                !isset ($_POST['advertLanguage']) ||
                !isset ($_POST['advertLocation']) ||
                !isset ($_POST['url']) ||
                empty ($_POST['tags']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        if ( empty ($_FILES) )
        {
            $this->ajaxresponse ("error", "No files uploaded");
        }

        $objAdvert = (new AdvertFactory())->createAdvert (
                new User ($_SESSION['user']['user_id']), $_POST['advertTitle'], $_POST['advertLocation'], $_POST['advertLanguage'], $_POST['advertGender']);

        if ( $objAdvert === false )
        {
            $this->ajaxresponse ("error", "Unable to create advert");
        }

        $arrTags = json_decode ($_POST['tags'], true);

        $arrUrls = json_decode ($_POST['url'], true);

        $arrFiles = $_FILES;

        if ( $this->uploadFiles ($arrFiles, $objAdvert, $arrTags, $arrUrls) === false )
        {
            $this->ajaxresponse ("error", $this->getValidationFailures ());
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
        $this->view->type = "profile";

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $arrBanner = (new AdvertFactory())->getProfileBannerForUser (new User ($_SESSION['user']['user_id']), new BannerFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrBanner === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($arrBanner) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "advert",
                                "action" => "displayProfileBanner",
                                "params" => ["arrBanners" => $arrBanner]
                            ]
            );
        }
    }
    
    public function displayProfileBannerAction()
    {
          $this->view->arrBanner = $this->dispatcher->getParam ("arrBanners");
    }

}
