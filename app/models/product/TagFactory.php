<?php

/**
 * Description of TagFactory
 *
 * @author michael.hampton
 */
class TagFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    /**
     * 
     * @return \Tag|boolean
     */
    public function getTagsForShop ()
    {
        $arrResults = $this->db->_select ("message_tag", "type = :type", [":type" => "product"]);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        $arrTags = [];

        foreach ($arrResults as $arrResult) {
            $objTag = new Tag ($arrResult['id']);
            $objTag->setDescription ($arrResult['description']);

            $arrTags[] = $objTag;
        }

        return $arrTags;
    }

}
