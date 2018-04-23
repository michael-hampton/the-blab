<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdvertFactory
 *
 * @author michael.hampton
 */
class AdvertFactory
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

    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    /**
     * 
     * @param User $objUser
     * @param type $title
     * @param type $location
     * @param type $language
     * @param type $gender
     * @return \Advert|boolean
     */
    public function createAdvert (User $objUser, $title, $location, $language, $gender)
    {
        if ( trim ($title) === "" )
        {
            $this->validationFailures[] = "Title is a mandatory field";
        }

        if ( trim ($location) === "" )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

        if ( trim ($language) === "" )
        {
            $this->validationFailures[] = "Language is a mandatory field";
        }

        if ( trim ($gender) === "" )
        {
            $this->validationFailures[] = "Gender is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("advert", ["user_id" => $objUser->getId (), "title" => $title, "location" => $location, "gender" => $gender, "language" => $language]);

        if ( $result === false )
        {
            trigger_error ("Db query failed to insert", E_USER_WARNING);
            return false;
        }

        return new Advert ($result);
    }

    /**
     * 
     * @param User $objUser
     * @param type $blWithBanners
     * @param BannerFactory $objBannerFactory
     * @return \Advert|boolean
     */
    public function getAdvertsForUser (User $objUser = null, $blWithBanners = false, BannerFactory $objBannerFactory = null)
    {
        $arrWhere = [];
        $where = "is_active = 1";

        if ( $objUser !== null )
        {
            $where = " AND user_id = :userId";
            $arrWhere[":userId"] = $objUser->getId ();
        }

        $arrResults = $this->db->_select ("advert", $where, $arrWhere, "*", "title ASC");

        if ( $arrResults === false )
        {
            trigger_error ("Unable to get adverts", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrAdverts = [];

        foreach ($arrResults as $arrResult) {
            $objAdvert = new Advert ($arrResult['id']);

            if ( $blWithBanners === true && $objBannerFactory !== null )
            {
                $arrBanners = $objBannerFactory->getBannersForAdvert ($objAdvert);

                if ( !empty ($arrBanners) )
                {
                    $objAdvert->setArrBanners ($arrBanners);
                }
            }

            $arrAdverts[] = $objAdvert;
        }

        return $arrAdverts;
    }

}
