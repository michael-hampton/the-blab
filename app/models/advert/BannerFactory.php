<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BannerFactory
 *
 * @author michael.hampton
 */
class BannerFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

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
     * @param type $imageLocation
     * @param type $caption
     * @param type $url
     * @param Advert $objAdvert
     * @return \Banner|boolean
     */
    public function createBanner ($imageLocation, $caption, $url, Advert $objAdvert)
    {
        if ( trim ($imageLocation) === "" )
        {
            $this->validationFailures[] = "Image location is missing";
        }

        if ( trim ($caption) === "" )
        {
            $this->validationFailures[] = "Caption is missing";
        }

        if ( trim ($url) === "" )
        {
            $this->validationFailures[] = "Url is missing";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("advert_banner", ["advert_id" => $objAdvert->getId (), "caption" => $caption, "url" => $url, "image_location" => $imageLocation]);

        if ( $result === false )
        {
            trigger_error ("Db query failed to insert", E_USER_WARNING);
            return false;
        }

        return new Banner ($result);
    }

    /**
     * 
     * @param User $objUser
     * @param Upload $objUpload
     * @param Advert $objAdvert
     * @param NotificationFactory $objNotificationFactory
     * @param EmailNotificationFactory $objEmailNotificationFactory
     * @return \Banner|boolean
     */
    public function addToStory (User $objUser, Upload $objUpload, Advert $objAdvert, NotificationFactory $objNotificationFactory, EmailNotificationFactory $objEmailNotificationFactory)
    {

        $result = $this->db->create ("advert_banner", ["advert_id" => $objAdvert->getId (), "caption" => "", "url" => "", "image_location" => $objUpload->getFileLocation ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed to insert", E_USER_WARNING);
            return false;
        }

        $message = $objUser->getFirstName () . ' ' . $objUser->getLastName () . 'just added a picture to their story';
        $body = 'click here to see it ';

        $objEmail = $objEmailNotificationFactory->createNotification ($objUser, $message, $body);
        $objEmail->sendEmail ();
        $objNotificationFactory->createNotification ($objUser, $body);

        return new Banner ($result);
    }

    /**
     * 
     * @param Advert $objAdvert
     * @return \Banner|boolean
     */
    public function getBannersForAdvert (Advert $objAdvert)
    {
        $arrResults = $this->db->_select ("advert_banner", "advert_id = :advertId", [":advertId" => $objAdvert->getId ()], "*", "id ASC");

        if ( $arrResults === false )
        {
            trigger_error ("Unable to get adverts", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrBanners = [];

        foreach ($arrResults as $arrResult) {
            $objBanner = new Banner ($arrResult['id']);
            $arrBanners[] = $objBanner;
        }

        return $arrBanners;
    }

}
