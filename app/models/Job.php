<?php

/**
 * Description of Job
 *
 * @author michael.hampton
 */
class Job
{

    /**
     *
     * @var type 
     */
    private $db;

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
    private $minSalary;

    /**
     *
     * @var type 
     */
    private $maxSalary;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $title;

    /**
     *
     * @var type 
     */
    private $description;

    /**
     *
     * @var type 
     */
    private $skills;

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     * 
     */
    public function __construct ($id)
    {
        $this->db = new Database();
        $this->db->connect ();

        $this->id = $id;

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate job object");
        }
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
    public function getId ()
    {
        return $this->id;
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
    public function getMinSalary ()
    {
        return $this->minSalary;
    }

    /**
     * 
     * @return type
     */
    public function getMaxSalary ()
    {
        return $this->maxSalary;
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
     * @return type
     */
    public function getTitle ()
    {
        return $this->title;
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
    public function getSkills ()
    {
        return $this->skills;
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
     * @param type $userId
     */
    public function setUserId ($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 
     * @param type $minSalary
     */
    public function setMinSalary ($minSalary)
    {
        $this->minSalary = $minSalary;
    }

    /**
     * 
     * @param type $maxSalary
     */
    public function setMaxSalary ($maxSalary)
    {
        $this->maxSalary = $maxSalary;
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
     * @param type $title
     */
    public function setTitle ($title)
    {
        $this->title = $title;
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
     * @param type $skills
     */
    public function setSkills ($skills)
    {
        $this->skills = $skills;
    }

    private function populateObject ()
    {
        
    }

    private function validate ()
    {
        
    }

    public function save ()
    {
        
    }

    public function delete ()
    {
        
    }

}
