<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageFactory
 *
 * @author michael.hampton
 */
class PageFactory
{

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();

        $this->db->connect ();
    }

    /**
     * 
     * @param type $searchText
     * @return type
     */
    public function getAllPages ($searchText)
    {

        $arrResults = $this->db->_query ("SELECT * FROM `page` 
                                        WHERE (`page_name` LIKE :pageName OR `url` LIKE :pageName) 
                                        ORDER BY page_name ASC", [":pageName" => '%' . $searchText . '%']);

        $arrPages = $this->buildPageObject ($arrResults);

        return $arrPages;
    }

    /**
     * 
     * @param type $arrResults
     * @return boolean|\Page
     */
    private function buildPageObject ($arrResults)
    {
        if ( $arrResults === false || !is_array ($arrResults) )
        {
            trigger_error ("Unable to build object");
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        try {
            foreach ($arrResults as $arrResult) {
                $objPage = new Page ($arrResult['url']);
                $objPage->setName ($arrResult['page_name']);
                $objPage->setDescription ($arrResult['description']);
                $objPage->setUserId ($arrResult['user_id']);
                $objPage->setUrl ($arrResult['url']);
                $objPage->setPageType ($arrResult['page_type_id']);
                $objPage->setCategories ($arrResult['category_id']);
                $objPage->setFileLocation ($arrResult['image_location']);

                $arrPages[] = $objPage;
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }

        return $arrPages;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean|\Page
     */
    public function getPagesForUser (User $objUser)
    {
        $arrResults = $this->db->_select("page", "user_id = :userId", [":userId" => $objUser->getId()]);
        $arrPages = $this->buildPageObject ($arrResults);

        return $arrPages;
    }
    
      /**
     * 
     * @param User $objUser
     * @return boolean|\Page
     */
    public function getPagesForProfile (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT p.* FROM page p
                                        INNER JOIN page_follower pf ON pf.page_id = p.id
                                        WHERE pf.user_id = :userId", [':userId' => $objUser->getId ()]);

        $arrPages = $this->buildPageObject ($arrResults);

        return $arrPages;
    }

    /**
     * 
     * @param User $objUser
     * @return type
     */
    public function getPagesMemberOf (User $objUser)
    {
        $arrResults = $this->db->_query ("SELECT p.* 
                                         FROM `page_follower` pf 
                                         INNER JOIN page p ON p.id = pf.page_id 
                                         WHERE pf.`user_id` = :userId", [':userId' => $objUser->getId ()]);
        
        $arrPages = $this->buildPageObject ($arrResults);

        return $arrPages;
    }

    /**
     * 
     * @param User $objUser
     * @param PageType $objPageType
     * @param type $arrCategories
     * @param type $name
     * @param type $description
     * @param type $url
     * @param type $linkHref
     * @param type $fileLocation
     * @param type $address
     * @param type $postcode
     * @param type $telephoneNo
     * @param type $websiteUrl
     * @return boolean|\Page
     */
    public function createPage (
    User $objUser, PageType $objPageType, $arrCategories, $name, $description, $url, $linkHref, $fileLocation = '', $address, $postcode, $telephoneNo, $websiteUrl
    )
    {

        if ( trim ($name) === "" )
        {
            $this->validationFailures[] = "Name is a mandatory field";
        }

        if ( trim ($description) === "" )
        {
            $this->validationFailures[] = "Description is a mandatory field";
        }

        if ( trim ($url) === "" )
        {
            $this->validationFailures[] = "URL is a mandatory field";
        }

        if ( trim ($arrCategories) === "" )
        {
            $this->validationFailures[] = "Category is a mandatory field";
        }

        if ( $this->checkUrlExists ($url) === true )
        {
            $this->validationFailures[] = "The page with this name already exists";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("page", [
            "description" => $description,
            "page_name" => $name,
            "user_id" => $objUser->getId (),
            "url" => $url,
            "page_type_id" => $objPageType->getId (),
            "category_id" => $arrCategories,
            "image_location" => $fileLocation,
            "link_href" => $linkHref,
            "postcode" => $postcode,
            "address" => $address,
            "telephone_no" => $telephoneNo,
            "website_url" => $websiteUrl
                ]
        );

        if ( $result === false )
        {
            return false;
        }


        return new Page ($url);
    }

    /**
     * 
     * @param type $url
     * @return type
     * @throws Exception
     */
    private function checkUrlExists ($url)
    {
        $result = $this->db->_select ("page", "url = :url", [':url' => $url]);

        if ( $result === false )
        {
            throw new Exception ("Unable to run check exists query");
        }

        if ( !empty ($result[0]) )
        {
            return true;
        }

        return false;
    }

    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

}
