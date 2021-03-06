<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventCategoryFactory
 *
 * @author michael.hampton
 */
class EventCategoryFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect();
    }

     /**
     * 
     * @return type
     */
    public function getAllCategories ()
    {
        $arrResults = $this->db->_query ("SELECT name, id FROM `event_category` ORDER BY name ASC", []);

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
            $objCategory = new EventCategory ($arrResult['id']);
            $objCategory->setName ($arrResult['name']);

            $arrCategories[] = $objCategory;
        }

        return $arrCategories;
    }

}