<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFactory
 *
 * @author michael.hampton
 */
class ProductFactory
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
    private $totalCount;

    /**
     *
     * @var type 
     */
    private $pageNo;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    private $validationFailures = [];

    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    public function getTotalCount ()
    {
        return $this->totalCount;
    }

    public function getPageNo ()
    {
        return $this->pageNo;
    }

    /**
     * 
     * @param User $objUser
     * @param type $searchText
     * @param ProductCategory $objCategory
     * @param type $location
     * @param type $page
     * @param type $limit
     * @param type $orderBy
     * @param type $orderDir
     * @param type $blCountOnly
     * @return type
     */
    private function searchProducts (User $objUser = null, $searchText = null, ProductCategory $objCategory = null, $location = null, $page = null, $limit = null, $orderBy = null, $orderDir = null, $blCountOnly = false)
    {

        if ( $blCountOnly === true )
        {
            $sql = "SELECT COUNT(*) AS rows";
        }
        else
        {
            $sql = "SELECT *,  CONCAT(u.fname, ' ' , u.lname)  AS seller";
        }

        $sql .= " FROM product
                INNER JOIN users u ON u.uid = user_id
                WHERE 1=1";

        $arrParams = [];

        if ( $objUser !== null )
        {
            $sql .= " AND user_id = :userId ";
            $arrParams[':userId'] = $objUser->getId ();
        }

        if ( $searchText !== null )
        {
            $sql .= " AND name LIKE :searchText ";
            $arrParams[':searchText'] = '%' . $searchText . '%';
        }

        if ( $objCategory !== null )
        {
            $sql .= " AND category = :category ";
            $arrParams[":category"] = $objCategory->getId ();
        }

        if ( $location !== null )
        {
            
        }

        if ( $page !== null )
        {
            $sql .= " LIMIT {$page}, {$limit}";
        }

        echo $sql;


        $arrResults = $this->db->_query ($sql, $arrParams);

        if ( $blCountOnly === true )
        {

            return (int) $arrResults[0]['rows'];
        }

        return $arrResults;
    }

    private function getPagination (User $objUser = null, $searchText = null, ProductCategory $objCategory = null, $location = null, $page = null, $limit = null, $orderBy = null, $orderDir = null)
    {
        $results = $this->searchProducts ($objUser, $searchText, $objCategory, $location, $page, $limit, $orderBy, $orderDir, true);
    }

    /**
     * 
     * @param User $objUser
     * @param type $searchText
     * @param type $category
     * @param type $location
     * @param type $page
     * @param type $limit
     * @param type $orderBy
     * @param type $orderDir
     * @return \Product|boolean
     */
    public function getProducts (User $objUser = null, $searchText = null, ProductCategory $objCategory = null, $location = null, $page = null, $limit = null, $orderBy = null, $orderDir = null)
    {

        $vpb_get_total_pages = $this->searchProducts ($objUser, $searchText, $objCategory, $location, null, null, $orderBy, $orderDir, true);

        if ( $vpb_get_total_pages === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        $this->totalCount = $vpb_get_total_pages;

        if ( $page !== null )
        {
            $page = ($page - 1) * $limit;
            $this->pageNo = $page;
        }

        $arrResults = $this->searchProducts ($objUser, $searchText, $objCategory, $location, $page, $limit, $orderBy, $orderDir, false);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrProducts = [];

        $objUploadFactory = new UploadFactory();

        foreach ($arrResults as $arrResult) {
            $objProduct = new Product ($arrResult['id']);
            $objProduct->setSeller ($arrResult['seller']);

            $arrImages = $objUploadFactory->getImagesForProduct ($objProduct);

            if ( !empty ($arrImages) )
            {
                $objProduct->setArrImages ($arrImages);
            }

            $arrProducts[] = $objProduct;
        }

        return $arrProducts;
    }

    /**
     * 
     * @param User $objUser
     * @param ProductCategory $objCategory
     * @param type $size
     * @param type $colour
     * @param type $productCode
     * @param type $name
     * @param type $description
     * @param type $location
     * @param type $price
     * @return \Product|boolean
     */
    public function createProduct (User $objUser, ProductCategory $objCategory, $size, $colour, $productCode, $name, $description, $location, $price)
    {

        if ( trim ($name) === "" || !is_string ($name) )
        {
            $this->validationFailures[] = "name cant be empty";
        }

        if ( trim ($objCategory->getId ()) === "" || !is_numeric ($objCategory->getId ()) )
        {
            $this->validationFailures[] = "category cant be empty";
        }

        if ( trim ($price) === "" )
        {
            $this->validationFailures[] = "price cant be empty";
        }

        if ( trim ($location) === "" )
        {
            $this->validationFailures[] = "location cant be empty";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        $result = $this->db->create ("product", [
            "name" => $name,
            "product_code" => $productCode,
            "category" => $objCategory->getId (),
            "location" => $location,
            "description" => $description,
            "user_id" => $objUser->getId (),
            "price" => $price,
            "colour" => $colour,
            "size" => $size]
        );

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return new Product ($result);
    }

    /**
     * 
     * @param type $id
     * @return \Product|boolean
     */
    public function getProduct ($id)
    {
        try {
            $objProduct = new Product ($id);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }

        return $objProduct;
    }

}
