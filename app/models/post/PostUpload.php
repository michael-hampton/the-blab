<?php

/**
 * Description of PostUpload
 *
 * @author michael.hampton
 */
class PostUpload
{

    /**
     *
     * @var type 
     */
    private $validationFailures = [];


    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    
    /**
     * 
     * @param type $keyname
     * @param type $arrFiles
     * @param type $target_dir
     * @param User $objUser
     * @param UploadFactory $objUpload
     * @param AdvertFactory $objAdvertFacotry
     * @param BannerFactory $objBannerFactory
     * @param type $blAddToStory
     * @return type
     */
    public function multipleUploadValidation (
    $rootPath, $arrFiles, $target_dir, User $objUser, UploadFactory $objUpload, AdvertFactory $objAdvertFacotry, BannerFactory $objBannerFactory, $blAddToStory = false
    )
    {
        $arrIds = [];

        if ( !is_dir ($target_dir) )
        {
            mkdir ($target_dir);
        }

        foreach ($arrFiles["pictures"]['name'] as $key => $name) {

            $target_file = $target_dir . basename ($name);

            $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

            if ( $_FILES[$key]["size"][$key] > 500000 )
            {
                $this->validationFailures[] = "The file you tried to upload is to big";
            }

// Allow certain file formats
            if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
            {
                $this->validationFailures[] = "The file you tried to upload is in the wrong format.";
            }

            $check = getimagesize ($_FILES["pictures"]["tmp_name"][$key]);

            if ( $check === false )
            {
                $this->validationFailures[] = "Unable to save upload 02";
            }

            if ( count ($this->validationFailures) > 0 )
            {
                return false;
            }

            if ( $this->compress ($_FILES["pictures"]["tmp_name"][$key], $target_file, 80) )

            //if ( move_uploaded_file ($_FILES["pictures]["tmp_name"][$key], $target_file))
            {
                $savedLocation = str_replace ($rootPath, "", $target_file);

                $objUploaded = $objUpload->createUpload ($objUser, $savedLocation);

                if ( $objUploaded === false )
                {
                    $this->validationFailures[] = "Unable to save upload";
                }

                $arrIds[] = $objUploaded->getId ();

                if ( $blAddToStory === true )
                {
                    $arrAdvert = $objAdvertFacotry->getProfileBannerForUser ($objUser, $objBannerFactory);

                    if ( empty ($arrAdvert[0]) )
                    {
                        $objAdvert = $objAdvertFacotry->createProfileBanner ($objUser, $objUser->getFirstName () . ' ' . $objUser->getLastName ());

                        if ( $objAdvert === false )
                        {
                            $this->validationFailures[] = "Unable to add uploads to story";
                        }
                    }
                    else
                    {
                        $objAdvert = $arrAdvert[0];
                    }

                    $blBannerResult = $objBannerFactory->addToStory ($objUser, $objUploaded, $objAdvert, new NotificationFactory (), new EmailNotificationFactory ());

                    if ( $blBannerResult === false )
                    {
                        $this->validationFailures[] = "Unable to add picture(s) to story";
                    }
                }
            }
            else
            {
                $this->validationFailures[] = "Unable to complete upload";
            }
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        return $arrIds;
    }
    
    private function compress ($source, $destination, $quality)
    {

        $info = getimagesize ($source);

        if ( $info['mime'] == 'image/jpeg' )
        {
            $image = imagecreatefromjpeg ($source);
        }
        elseif ( $info['mime'] == 'image/gif' )
        {
            $image = imagecreatefromgif ($source);
        }
        elseif ( $info['mime'] == 'image/png' )
        {
            $image = imagecreatefrompng ($source);
        }

        if ( !isset ($image) )
        {
            return false;
        }

        return imagejpeg ($image, $destination, $quality);
    }

}
