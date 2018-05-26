<?php

/**
 * Description of FileUpload
 *
 * @author michael.hampton
 */
class FileUpload
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
    private $maxSize = 500000;

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
     * @param type $target_dir
     */
    public function __construct ($target_dir = 'uploads')
    {
        $this->targetDir = $target_dir;
    }

    /**
     * 
     * @param array $arrFile
     * @return boolean
     */
    public function prepareUpload (array $arrFile)
    {
        $this->name = $arrFile['name'];
        $this->type = $arrFile['type'];
        $this->tempName = $arrFile['tmp_name'];
        $this->size = $arrFile['size'];

        $this->targetFile = $this->targetDir . basename ($this->name);

        return true;
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
     * 
     * @return boolean
     */
    public function validateUpload ()
    {

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
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
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

}
