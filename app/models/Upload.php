<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Upload
 *
 * @author michael.hampton
 */
class Upload
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
    private $userId;

    /**
     *
     * @var type 
     */
    private $created;

    /**
     *
     * @var type 
     */
    private $fileLocation;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function __construct ($id)
    {
        $this->id = $id;
        $this->db = new Database();
        $this->db->connect ();

        if ( $this->populateOnject () === false )
        {
            throw new Exception ("Failed to populate object");
        }
    }

    /**
     * 
     * @return type
     */
    function getId ()
    {
        return $this->id;
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
     * @return type
     */
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @return type
     */
    public function getCreated ()
    {
        return $this->created;
    }

    /**
     * 
     * @return type
     */
    public function getFileLocation ()
    {
        return $this->fileLocation;
    }

    /**
     * 
     * @param type $userId
     */
    public function setUserId (type $userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $created
     */
    public function setCreated (type $created)
    {
        $this->created = $created;
    }

    /**
     * 
     * @param type $fileLocation
     */
    public function setFileLocation (type $fileLocation)
    {
        $this->fileLocation = $fileLocation;
    }

    /**
     * 
     * @return boolean
     */
    private function populateOnject ()
    {
        $result = $this->db->_select ("uploads", "upload_id = :uploadId", [':uploadId' => $this->id]);

        if ( $result === FALSE || empty ($result[0]) )
        {
            return false;
        }

        $this->fileLocation = $result[0]['file_location'];
        $this->created = $result[0]['created'];
        $this->userId = $result[0]['user_id'];

        return true;
    }

    private function deleteUpload ()
    {
        $result = $this->db->delete ("uploads", "upload_id = :uploadId", [":uploadId" => $this->id]);

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
    public function delete ()
    {
        return $this->deleteUpload ();
    }

    /**
     * 
     * @return boolean
     */
    public function deleteProductImage ()
    {
        $result = $this->deleteUpload ();

        if ( $result === false )
        {
            return false;
        }

        $result = $this->db->delete ("product_images", "upload_id = :uploadId", [":uploadId" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

}
