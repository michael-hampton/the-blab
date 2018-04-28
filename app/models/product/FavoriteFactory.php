<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FavoriteFactory
 *
 * @author michael.hampton
 */
class FavoriteFactory
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
     * @param User $objUser
     * @param ProductFactory $objProductFactory
     * @return boolean
     */
    public function getFavoritesForUser (User $objUser, ProductFactory $objProductFactory)
    {
        $arrResults = $this->db->_query ("SELECT * FROM `product_favorite` f
                                            INNER JOIN product p ON p.id = f.`product_id`
                                            WHERE f.user_id = :userId", [":userId" => $objUser->getId ()]);

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

        foreach ($arrResults as $arrResult) {
            $objProduct = $objProductFactory->getProduct ($arrResult['product_id']);

            if ( $objProduct === false )
            {
                continue;
            }

            $arrProducts[] = $objProduct;
        }

        return $arrProducts;
    }

    /**
     * 
     * @param User $objUser
     * @param Product $objProduct
     * @return type
     * @throws Exception
     */
    private function checkProductAlreadyAddedForUser (User $objUser, Product $objProduct)
    {
        $arrResult = $this->db->_select ("product_favorite", "user_id = :userId AND product_id = :productId", [":userId" => $objUser->getId (), ":productId" => $objProduct->getId ()]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }

        return !empty ($arrResult[0]);
    }

    /**
     * 
     * @param User $objUser
     * @param Product $objProduct
     * @return boolean
     */
    public function saveFavoriteForUser (User $objUser, Product $objProduct)
    {
        if ( $this->checkProductAlreadyAddedForUser ($objUser, $objProduct) === true )
        {
            $this->validationFailures[] = "Product is already in your favorites list";
            return false;
        }

        $result = $this->db->create ("product_favorite", ["user_id" => $objUser->getId (), "product_id" => $objProduct->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
