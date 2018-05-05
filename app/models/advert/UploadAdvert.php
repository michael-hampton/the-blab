<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadAdvert
 *
 * @author michael.hampton
 */
trait UploadAdvert
{

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     * 
     * @param array $arrFiles
     * @param Advert $objAdvert
     * @param type $arrTags
     * @param type $arrUrls
     * @param type $type
     * @return boolean
     */
    public function uploadFiles (array $arrFiles, Advert $objAdvert, $arrTags, $arrUrls, $type = 'advert')
    {
        if ( empty ($arrFiles) )
        {
            return false;
        }

        $blIsValid = true;

        foreach ($arrFiles as $key => $arrFile) {

            $key = str_replace ("file-", "", $key);

            if ( empty ($arrFile['name']) )
            {
                $this->validationFailures[] = "Invalid file uploaded";
                $blIsValid = false;
            }

            $target_dir = trim ($type) === 'advert' ? $this->rootPath . "/blab/public/uploads/adverts/" : $this->rootPath . "/blab/public/uploads/adverts/profile/";

            if ( !is_dir ($target_dir) )
            {
                if ( !mkdir ($target_dir) )
                {
                    trigger_error ("Unable to create directory {$target_dir}", E_USER_WARNING);
                    return false;
                }
            }

            $target_file = $target_dir . basename ($arrFile["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

            if ( $arrFile["size"] > 500000 )
            {
                $this->validationFailures[] = "The file you uploaded is to large";
                $blIsValid = false;
            }

            // Allow certain file formats
            if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
            {
                $this->validationFailures[] = "Invalid file type";
                $blIsValid = false;
            }

            $check = getimagesize ($arrFile["tmp_name"]);

            if ( $check === false )
            {
                $this->validationFailures[] = "Unable to determine size of file";
                $blIsValid = false;
            }

            if ( $blIsValid === false )
            {
                return false;
            }

            if ( move_uploaded_file ($arrFile["tmp_name"], $target_file) )
            {
                $fileLocation = str_replace ($this->rootPath, "", $target_file);

                $title = $arrTags[$key];
                $url = $arrUrls[$key];

                $objBanner = (new BannerFactory())->createBanner ($fileLocation, $title, $url, $objAdvert);

                if ( $objBanner === false )
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

}
