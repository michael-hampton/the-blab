<?php
class Video
{
    
    /**
     *
     * @var type 
     */
    private $id;
    
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
    public function getId()
    {
        return $this->id;
    }
}