<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageTypeFactory
 *
 * @author michael.hampton
 */
class PageTypeFactory
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
     * @return \PageType|boolean
     */
    public function getPageTypes ()
    {
        $arrResults = $this->db->_select ("page_type");

        if ( $arrResults === false )
        {
            trigger_error("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrPageTypes = [];

        foreach ($arrResults as $arrResult) {
            $objPageType = new PageType($arrResult['id']);
            $objPageType->setName ($arrResult['name']);

            $arrPageTypes[] = $objPageType;
        }

        return $arrPageTypes;
    }

}
