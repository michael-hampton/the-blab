<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductCategoryFactory
 *
 * @author michael.hampton
 */
class ProductCategoryFactory
{

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
     * @var type 
     */
    private $validationFailures = [];

    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    public function createCategory ($name)
    {
        if ( trim ($name) === "" || !is_string ($name) )
        {
            $this->validationFailures[] = "Name cannot empty";

            return false;
        }

        $result = $this->db->create ("product_category", ["category_name" => $name]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

   /**
    * 
    * @param type $page
    * @param type $limit
    * @param type $orderBy
    * @param type $orderDir
    * @return boolean|\ProductCategory
    */
    public function getCategories ($page = null, $limit = null, $orderBy = null, $orderDir = null)
    {
        $arrResults = $this->db->_query("SELECT *, 
                                        (SELECT COUNT(*) 
                                            FROM product 
                                            WHERE category = pc.id) AS count 
                                        FROM product_category pc
                                        ORDER BY pc.category_name ASC");

        if ( $arrResults === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }
        
        if(empty($arrResults[0])) {
            return [];
        }
        
        $arrCategories = [];
        
        foreach ($arrResults as $arrResult) {
            $objCategory = new ProductCategory($arrResult['id']);
            $objCategory->setName($arrResult['category_name']);
            $objCategory->setCount($arrResult['count']);
            
            $arrCategories[] = $objCategory;
        }
        
        return $arrCategories;
    }

}
