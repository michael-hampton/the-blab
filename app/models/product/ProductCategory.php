<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductCategory
 *
 * @author michael.hampton
 */
class ProductCategory
{

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $name;

    /**
     *
     * @var type 
     */
    private $db;
    
    /**
     *
     * @var type 
     */
    private $count;

    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate object from db");
        }
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * 
     * @return type
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param type $name
     */
    public function setName ($name)
    {
        $this->name = $name;
    }
    
    /**
     * 
     * @return type
     */
    public function getCount ()
    {
        return $this->count;
    }

    /**
     * 
     * @param type $count
     */
    public function setCount ($count)
    {
        $this->count = $count;
    }

    
    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->delete ("product_category", "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function save ()
    {
        $result = $this->db->update ("product_category", ["category_name" => $this->name], "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function populateObject ()
    {
        $result = $this->db->_select ("product_category", "id = :id", [":id" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Unable to get category result from db", E_USER_WARNING);
            return false;
        }

        $this->name = $result[0]['category_name'];
    }

}
