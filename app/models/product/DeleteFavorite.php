<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DeleteFavorite
 *
 * @author michael.hampton
 */
class DeleteFavorite
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
        $this->db->connect();
    }

    /**
     * 
     * @param User $objUser
     * @param Product $objProduct
     * @return boolean
     */
    public function deleteFavoriteFromDatabase (User $objUser, Product $objProduct)
    {
                
        $result = $this->db->delete ("product_favorite", "user_id = :userId AND product_id = :productId", [":userId" => $objUser->getId (), ":productId" => $objProduct->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
