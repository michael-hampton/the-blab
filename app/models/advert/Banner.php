<?php

/**
 * Description of Banner
 *
 * @author michael.hampton
 */
class Banner
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
    private $caption;

    /**
     *
     * @var type 
     */
    private $url;

    /**
     *
     * @var type 
     */
    private $imageLocation;
    
    /**
     *
     * @var type 
     */
    private $advertId;

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

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate object");
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
    public function getCaption ()
    {
        return $this->caption;
    }

    /**
     * 
     * @return type
     */
    public function getUrl ()
    {
        return $this->url;
    }

    /**
     * 
     * @return type
     */
    public function getImageLocation ()
    {
        return $this->imageLocation;
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
     * @param type $caption
     */
    public function setCaption ($caption)
    {
        $this->caption = $caption;
    }

    /**
     * 
     * @param type $url
     */
    public function setUrl ($url)
    {
        $this->url = $url;
    }
    
    /**
     * 
     * @return type
     */
    public function getAdvertId ()
    {
        return $this->advertId;
    }

    /**
     * 
     * @param type $advertId
     */
    public function setAdvertId ($advertId)
    {
        $this->advertId = $advertId;
    }

    
    /**
     * 
     * @param type $imageLocation
     */
    public function setImageLocation ($imageLocation)
    {
        $this->imageLocation = $imageLocation;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->db->_select ("advert_banner", "id = :id", [":id" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        $this->caption = $result[0]['caption'];
        $this->imageLocation = $result[0]['image_location'];
        $this->advertId = $result[0]['advert_id'];
        $this->url = $result[0]['url'];

        return true;
    }
    
    /**
     * 
     * @return boolean
     */
    public function delete()
    {
        $result = $this->db->delete("advert_banner", "id = :id", [":id" => $this->id]);
        
        if ( $result === false)
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        return true;
    }

}
