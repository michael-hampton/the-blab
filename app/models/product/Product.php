<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product
 *
 * @author michael.hampton
 */
class Product implements ObjectInterface
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
    private $seller;

    /**
     *
     * @var type 
     */
    private $name;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $price;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $productCode;

    /**
     *
     * @var type 
     */
    private $colour;

    /**
     *
     * @var type 
     */
    private $size;

    /**
     *
     * @var type 
     */
    private $category;

    /**
     *
     * @var type 
     */
    private $description;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $arrImages = [];

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

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate object");
        }
    }

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
     * @return type
     */
    public function getPrice ()
    {
        return $this->price;
    }

    public function getProductCode ()
    {
        return $this->productCode;
    }

    public function getColour ()
    {
        return $this->colour;
    }

    public function getSize ()
    {
        return $this->size;
    }

    public function getCategory ()
    {
        return $this->category;
    }

    public function getDescription ()
    {
        return $this->description;
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
     * @param type $price
     */
    public function setPrice ($price)
    {
        $this->price = $price;
    }

    /**
     * 
     * @param type $productCode
     */
    public function setProductCode ($productCode)
    {
        $this->productCode = $productCode;
    }

    /**
     * 
     * @param type $colour
     */
    public function setColour ($colour)
    {
        $this->colour = $colour;
    }

    /**
     * 
     * @param type $size
     */
    public function setSize ($size)
    {
        $this->size = $size;
    }

    /**
     * 
     * @param type $category
     */
    public function setCategory ($category)
    {
        $this->category = $category;
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
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @return type
     */
    public function getLocation ()
    {
        return $this->location;
    }

    /**
     * 
     * @param type $location
     */
    public function setLocation ($location)
    {
        $this->location = $location;
    }

    /**
     * 
     * @param type $description
     */
    public function setDescription ($description)
    {
        $this->description = $description;
    }

    /**
     * 
     * @return type
     */
    public function getArrImages ()
    {
        return $this->arrImages;
    }

    /**
     * 
     * @param type $arrImages
     */
    public function setArrImages ($arrImages)
    {
        $this->arrImages = $arrImages;
    }

    /**
     * 
     * @return type
     */
    public function getSeller ()
    {
        return $this->seller;
    }

    /**
     * 
     * @param type $seller
     */
    public function setSeller ($seller)
    {
        $this->seller = $seller;
    }

    /**
     * 
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->delete ("product", "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

     /**
     *
     * @var array 
     */
    private $validationFailures = [];

    /**
     * 
     * @return type
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    /**
     * 
     * @return boolean
     */
    private function validate ()
    {
        if ( trim ($this->name) === "" || !is_string($this->name))
        {
            $this->validationFailures[] = "Product name cannot be empty";
        }

        if ( trim ($this->description) === "" || !is_string($this->description))
        {
            $this->validationFailures[] = "Description cannot be empty";
        }

        if ( trim ($this->category) === "")
        {
            $this->validationFailures[] = "Page type cannot be empty";
        }

        if ( trim ($this->productCode) === "" )
        {
            $this->validationFailures[] = "Category cannot be empty";
        }

        if ( count ($this->validationFailures) > 0 )
        {
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
        if ( $this->validate () === false )
        {
            return false;
        }
    
        $result = $this->db->update ("product", [
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "colour" => $this->colour,
            "size" => $this->size,
            "product_code" => $this->productCode,
            "category" => $this->category,
            "location" => $this->location
                ], "id = :id", [":id" => $this->id]
        );

        if ( $result === false )
        {
            trigger_error ("Db Query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    public function populateObject ()
    {
        $result = $this->db->_query ("SELECT p.*, 
                                            CONCAT(u.fname, ' ' , u.lname) AS seller 
                                    FROM `product` p 
                                    INNER JOIN users u ON u.uid = p.user_id
                                    WHERE p.id = :id", [":id" => $this->id]);

        if ( $result === false || empty ($result[0]) )
        {
            trigger_error ("Unable to get product results", E_USER_WARNING);
            return false;
        }

        $this->name = $result[0]['name'];
        $this->description = $result[0]['description'];
        $this->colour = $result[0]['colour'];
        $this->size = $result[0]['size'];
        $this->productCode = $result[0]['product_code'];
        $this->location = $result[0]['location'];
        $this->price = $result[0]['price'];
        $this->category = $result[0]['category'];
        $this->userId = $result[0]['user_id'];
        $this->seller = $result[0]['seller'];
    }

    /**
     * 
     * @param Upload $objUpload
     * @return boolean
     */
    public function saveProductImage (Upload $objUpload)
    {
        $result = $this->db->create ("product_images", ["product_id" => $this->id, "upload_id" => $objUpload->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}