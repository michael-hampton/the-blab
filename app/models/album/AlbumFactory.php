<?php

/**
 * Class AlbumFactory
 */
class AlbumFactory
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
     * @param type $name
     * @param type $description
     * @param User $objUser
     * @return \Album|boolean
     */
    public function create ($name, $description, User $objUser)
    {
        if ( trim ($name) === "" )
        {
            return false;
        }

        $result = $this->db->create ("album", ["name" => $name, "description" => $description, "user_id" => $objUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return new Album ($result);
    }

    /**
     * 
     * @param User $objUser
     * @return \Album|boolean
     */
    public function getAlbums (User $objUser)
    {
        $arrResults = $this->db->_select ("album", "user_id = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        if(empty($arrResults[0])) {
            return [];
        }
        
        
        $arrAlbums = [];
        
        foreach ($arrResults as $arrResult) {
            $objAlbum = new Album($arrResult['id']);
            
            $arrAlbums[] = $objAlbum;
        }
        
        return $arrAlbums;
    }

}
