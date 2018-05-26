<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageCategoryFactory
 *
 * @author michael.hampton
 */
class PageCategoryFactory
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
     * @param PageType $objPageType
     * @return \PageCategory|boolean
     */
    public function getCategoriesForPageType (PageType $objPageType)
    {
        $pageType = $objPageType->getId ();

        $arrResults = $this->db->_query ("SELECT * FROM `page_category` pc
                                        INNER JOIN categories c ON c.id = pc.category_id
                                        WHERE page_type_id = :pageType", [':pageType' => $pageType]);

        return $this->loadObject ($arrResults);
    }

    /**
     * 
     * @return type
     */
    public function getAllCategories ()
    {
        $arrResults = $this->db->_query ("SELECT name, id AS category_id FROM `categories`", []);

        return $this->loadObject ($arrResults);
    }

    /**
     * 
     * @param type $arrResults
     * @return \PageCategory|boolean
     */
    private function loadObject ($arrResults)
    {
        
        if ( $arrResults === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrCategories = [];

        foreach ($arrResults as $arrResult) {
            $objCategory = new PageCategory ($arrResult['category_id']);
            $objCategory->setName ($arrResult['name']);

            $arrCategories[$arrResult['category_id']] = $objCategory;
        }

        return $arrCategories;
    }

}
