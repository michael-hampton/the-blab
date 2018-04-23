<?php
/**
 * Description of PageType
 *
 * @author michael.hampton
 */
class PageType
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
    private $name;
    
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
}
