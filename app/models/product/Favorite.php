<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Favorite
 *
 * @author michael.hampton
 */
class Favorite
{

    /**
     *
     * @var type 
     */
    private $db;

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

}
