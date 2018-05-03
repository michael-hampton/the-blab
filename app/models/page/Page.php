<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page
 *
 * @author michael.hampton
 */
class Page
{

    /**
     *
     * @var type 
     */
    private $fileLocation;

    /**
     *
     * @var type 
     */
    private $name;

    /**
     *
     * @var type 
     */
    private $description;

    /**
     *
     * @var type 
     */
    private $url;

    /**
     *
     * @var type 
     */
    private $userId;

    /**
     *
     * @var type 
     */
    private $pageType;

    /**
     *
     * @var type 
     */
    private $categories;

    /**
     *
     * @var type 
     */
    private $objDatabase;

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $address;

    /**
     *
     * @var type 
     */
    private $postcode;

    /**
     *
     * @var type 
     */
    private $telephoneNo;

    /**
     *
     * @var type 
     */
    private $websiteUrl;

    /**
     *
     * @var type 
     */
    private $likeCount;

    /**
     * 
     * @param type $id
     */
    public function __construct ($url)
    {
        $this->url = $url;

        $this->objDatabase = new Database();
        $this->objDatabase->connect ();

        if ( $this->populatePageObject () === false )
        {
            throw new Exception ("Unable to populate object");
        }
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
    public function getDescription ()
    {
        return $this->description;
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
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @return type
     */
    public function getPageType ()
    {
        return $this->pageType;
    }

    /**
     * 
     * @return type
     */
    public function getCategories ()
    {
        return $this->categories;
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
     * @param type $description
     */
    public function setDescription ($description)
    {
        $this->description = $description;
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
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $pageType
     */
    public function setPageType ($pageType)
    {
        $this->pageType = $pageType;
    }

    /**
     * 
     * @param type $categories
     */
    public function setCategories ($categories)
    {
        $this->categories = $categories;
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
     * @param type $fileLocation
     */
    public function setFileLocation ($fileLocation)
    {
        $this->fileLocation = $fileLocation;
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
    public function getAddress ()
    {
        return $this->address;
    }

    /**
     * 
     * @return type
     */
    public function getPostcode ()
    {
        return $this->postcode;
    }

    /**
     * 
     * @return type
     */
    public function getTelephoneNo ()
    {
        return $this->telephoneNo;
    }

    /**
     * 
     * @return type
     */
    public function getWebsiteUrl ()
    {
        return $this->websiteUrl;
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
     * @param type $address
     */
    public function setAddress ($address)
    {
        $this->address = $address;
    }

    /**
     * 
     * @param type $postcode
     */
    public function setPostcode ($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * 
     * @return type
     */
    public function getLikeCount ()
    {
        return $this->likeCount;
    }

    /**
     * 
     * @param type $likeCount
     */
    public function setLikeCount ($likeCount)
    {
        $this->likeCount = $likeCount;
    }

    /**
     * 
     * @param type $telephoneNo
     */
    public function setTelephoneNo ($telephoneNo)
    {
        $this->telephoneNo = $telephoneNo;
    }

    /**
     * 
     * @param type $websiteUrl
     */
    public function setWebsiteUrl ($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
    }

    /**
     * 
     * @return boolean
     */
    public function populatePageObject ()
    {
        $result = $this->objDatabase->_select ("page", "url = :url", [':url' => $this->url]);

        if ( $result === false || empty ($result[0]) )
        {
            return false;
        }

        $this->description = $result[0]['description'];
        $this->userId = $result[0]['user_id'];
        $this->pageType = $result[0]['page_type_id'];
        $this->name = $result[0]['page_name'];
        $this->categories = $result[0]['category_id'];
        $this->fileLocation = $result[0]['image_location'];
        $this->address = $result[0]['address'];
        $this->postcode = $result[0]['postcode'];
        $this->websiteUrl = $result[0]['website_url'];
        $this->telephoneNo = $result[0]['telephone_no'];
        $this->likeCount = $result[0]['like_count'];
        $this->id = $result[0]['id'];

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function save ()
    {
        $result = $this->objDatabase->update ("page", [
            "description" => $this->description,
            "page_name" => $this->name,
            "category_id" => $this->categories,
            "page_type_id" => $this->pageType,
            "address" => $this->address,
            "website_url" => $this->websiteUrl,
            "telephone_no" => $this->telephoneNo,
            "postcode" => $this->postcode
                ], "id = :id", [":id" => $this->id]
        );

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
    public function delete ()
    {
        $result = $this->objDatabase->delete ("page", "url = :url", [":url" => $this->url]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $imageLocation
     * @return boolean
     */
    public function updatePageImage ($imageLocation)
    {
        $result = $this->objDatabase->update ("page", ["image_location" => $imageLocation], "id = :pageId", [":pageId" => $this->id]);

        if ( $result === false )
        {
            return false;
        }

        return true;
    }

    

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    public function likePage (User $objUser)
    {
        $checkResult = $this->objDatabase->_select ("page_like", "page_id = :pageId AND user_id = :userId", [":postId" => $this->id, ":userId" => $objUser->getId ()]);

        if ( $checkResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( !empty ($checkResult[0]) )
        {
            trigger_error ("User tried to like something twice", E_USER_WARNING);
            return false;
        }

        $result = $this->objDatabase->_query ("UPDATE page 
                            SET like_count = 
                                if(like_count is not null, like_count + 1, 1) 
                            WHERE id = :pageId", [":pageId" => $this->id], true);

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $result = $this->objDatabase->create ('page_like', array(
            'page_id' => $this->id,
            'user_id' => $objUser->getId (),
            'date_added' => date ("Y-m-d H:i:s")
        ));

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
