<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventCategory
 *
 * @author michael.hampton
 */
class EventCategory
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
    private $id;
    
    /**
     *
     * @var type 
     */
    private $name;

    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect ();
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
}
