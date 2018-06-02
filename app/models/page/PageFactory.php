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
     * @param User $objUser
     * @param PageReactionFactory $objPageReactionFactory
     * @param type $searchText
     * @param type $page
     * @param type $pageLimit
     * @param PageCategory $category
     * @return type
     */
    public function getAllPages (PageReactionFactory $objPageReactionFactory, User $objUser = null, $searchText = null, $page = null, $pageLimit = null, PageCategory $category = null)
    {

        $arrWhere = [];

        $sql = "SELECT  p.*, COUNT(pf.id) AS followers, COUNT(pl.id) AS like_count  FROM `page` p  
                LEFT JOIN page_follower pf ON pf.page_id = p.id
                LEFT JOIN page_like pl ON pl.page_id = p.id
                WHERE 1=1";

        if ( $objUser !== null )
        {
            $sql .= " AND p.user_id != :userId";
            $arrWhere[':userId'] = $objUser->getId ();
        }


        if ( $searchText !== null )
        {
            $sql .= " AND (`page_name` LIKE :pageName OR `url` LIKE :pageName)";
            $arrWhere[":pageName"] = '%' . $searchText . '%';
        }

        if ( $category !== null )
        {
            $sql .= " AND category_id = :categoryId";
            $arrWhere[':categoryId'] = $category->getId ();
        }

        $sql .= " GROUP BY p.id ORDER BY page_name ASC";

        if ( $page !== null )
        {
            $sql .= " LIMIT {$page}, {$pageLimit}";
        }

        $arrResults = $this->db->_query ($sql, $arrWhere);

        $arrPages = $this->buildPageObject ($arrResults, $objPageReactionFactory);

        return $arrPages;
    }

    /**
     * 
     * @param type $arrResults
     * @return boolean|\Page
     */
    private function buildPageObject ($arrResults, PageReactionFactory $objPageReactionFactory = null)
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

                if ( empty ($arrResult['page_name']) || empty ($arrResult['url']) )
                {
                    continue;
                }

                $objPage = new Page ($arrResult['url']);
                $objPage->setName ($arrResult['page_name']);
                $objPage->setDescription ($arrResult['description']);
                $objPage->setUserId ($arrResult['user_id']);
                $objPage->setUrl ($arrResult['url']);
                $objPage->setPageType ($arrResult['page_type_id']);
                $objPage->setCategories ($arrResult['category_id']);
                $objPage->setFileLocation ($arrResult['image_location']);

                if ( isset ($arrResult['followers']) )
                {

                    $objPage->setFollowCount ($arrResult['followers']);
                }
                if ( isset ($arrResult['like_count']) )
                {
                    $objPage->setLikeCount ($arrResult['like_count']);
                }

                if ( $objPageReactionFactory !== null )
                {
                    $arrFollowers = $objPageReactionFactory->getFollowersForPage ($objPage);

                    if ( !empty ($arrFollowers) )
                    {
                        $objPage->setArrFollowers ($arrFollowers);
                    }
                }

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
        $arrResults = $this->db->_select ("page", "user_id = :userId", [":userId" => $objUser->getId ()]);
        $arrPages = $this->buildPageObject ($arrResults);

        return $arrPages;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean|\Page
     */
    public function getPagesForProfile (User $objUser, PageReactionFactory $objPageReactionFactory)
    {
        $arrResults = $this->db->_query ("SELECT p.*, COUNT(pf.id) AS followers, COUNT(pl.id) AS like_count FROM page p
                                        LEFT JOIN page_follower pf ON pf.page_id = p.id
                                        LEFT JOIN page_like pl ON pl.page_id = p.id
                                        WHERE p.user_id = :userId", [':userId' => $objUser->getId ()]);

        $arrPages = $this->buildPageObject ($arrResults, $objPageReactionFactory);

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
     * @param type $pageId
     * @return boolean|\Page
     */
    public function getPageById ($pageId)
    {
        $arrResult = $this->db->_select ("page", "id = :pageId", [":pageId" => $pageId]);

        if ( $arrResult === false || empty($arrResult) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        return new Page($arrResult[0]['url']);
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
