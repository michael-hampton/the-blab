<?php

/**
 * Description of FileUploadTest
 *
 * @author michael.hampton
 */
class FileUploadTest
{

    /**
     *
     * @var type 
     */
    private $name;

    /**
     *
     * @var type 
     */
    private $size;

    /**
     *
     * @var type 
     */
    private $tempName;

    /**
     *
     * @var type 
     */
    private $maxSize;

    /**
     *
     * @var type 
     */
    private $type;

    /**
     *
     * @var type 
     */
    private $targetDir;

    /**
     *
     * @var type 
     */
    private $targetFile;

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     *
     * @var type 
     */
    private $arrFileTypes = [];

    /**
     * 
     * @param type $target_dir
     */
    public function __construct ($target_dir = 'uploads', $maxSize = 500000, $arrFileTypes = array("png", "jpeg", "jpg", "gif"))
    {
        $this->targetDir = $target_dir;
        $this->maxSize = $maxSize;
        $this->arrFileTypes = $arrFileTypes;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function getSize ()
    {
        return $this->size;
    }

    public function getTempName ()
    {
        return $this->tempName;
    }

    public function getMaxSize ()
    {
        return $this->maxSize;
    }

    public function getType ()
    {
        return $this->type;
    }

    /**
     * 
     * @param type $name
     */
    public function setName ($name)
    {
        $this->name = $name;
        $this->targetFile = $this->targetDir . basename ($this->name);
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
     * @param type $tempName
     */
    public function setTempName ($tempName)
    {
        $this->tempName = $tempName;
    }

    /**
     * 
     * @param type $maxSize
     */
    public function setMaxSize ($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * 
     * @param type $type
     */
    public function setType ($type)
    {
        $this->type = $type;
    }

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
     * @return type
     */
    public function getTargetFile ()
    {
        return $this->targetFile;
    }

    /**
     * validate all required fields have a value
     * 
     * @return type
     */
    private function validateFields ()
    {
        if ( trim ($this->name) === "" )
        {
            $this->validationFailures[] = "File name is missing";
        }

        if ( trim ($this->tempName) === "" )
        {
            $this->validationFailures[] = "File Temp name is missing";
        }

        if ( trim ($this->type) === "" )
        {
            $this->validationFailures[] = "File Type is missing";
        }

        if ( trim ($this->size) === "" )
        {
            $this->validationFailures[] = "File Size is missing";
        }

        if ( trim ($this->targetFile) === "" )
        {
            $this->validationFailures[] = "File Size is missing";
        }

        return count ($this->validationFailures) > 0 ? false : true;
    }

    /**
     * 
     * @return boolean
     */
    public function validateUpload ()
    {
        if ( $this->validateFields () === false )
        {
            return false;
        }

        $check = getimagesize ($this->tempName);

        $imageFileType = strtolower (pathinfo ($this->targetFile, PATHINFO_EXTENSION));

        if ( $check === false )
        {
            $this->validationFailures[] = "File is not an image.";
        }

        // Check file size
        if ( $this->size > $this->maxSize )
        {
            $this->validationFailures[] = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ( !in_array ($imageFileType, $this->arrFileTypes) )
        {
            $this->validationFailures[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
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
    public function saveUpload ()
    {
        if ( !move_uploaded_file ($this->tempName, $this->targetFile) )
        {
            return false;
        }

        return true;
    }

    public function getBlob ()
    {
        return file_get_contents ($this->tempName);
    }

}
