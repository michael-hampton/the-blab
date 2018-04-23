<?php
/**
 * Description of PhotoComment
 *
 * @author michael.hampton
 */
class PhotoComment
{

    /**
     *
     * @var type 
     */
    private $author;
    
    /**
     *
     * @var type 
     */
    private $userId;
            
          /**
           *
           * @var type 
           */
    private $comment;
    
    /**
     *
     * @var type 
     */
    private $dateAdded;
    
    /**
     *
     * @var type 
     */
    private $id;
    
    /**
     *
     * @var type 
     */
    private $uploadId;
    
    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->id = $id;
    }
    
    /**
     * 
     * @return type
     */
    public function getAuthor ()
    {
        return $this->author;
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
    public function getComment ()
    {
        return $this->comment;
    }

    /**
     * 
     * @return type
     */
    public function getDateAdded ()
    {
        return $this->dateAdded;
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
    public function getUploadId ()
    {
        return $this->uploadId;
    }

    /**
     * 
     * @param type $author
     */
    public function setAuthor ($author)
    {
        $this->author = $author;
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
     * @param type $comment
     */
    public function setComment ($comment)
    {
        $this->comment = $comment;
    }

    /**
     * 
     * @param type $dateAdded
     */
    public function setDateAdded ($dateAdded)
    {
        $this->dateAdded = $dateAdded;
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
     * @param type $uploadId
     */
    public function setUploadId ($uploadId)
    {
        $this->uploadId = $uploadId;
    }
    
    private function populateObject()
    {
        
    }


}
