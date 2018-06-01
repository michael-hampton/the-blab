<?php

/**
 * Description of JobFactory
 *
 * @author michael.hampton
 */
class JobFactory
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
    private $validationFailures = [];

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
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
     * @param type $title
     * @param type $description
     * @param type $salaryMin
     * @param type $salaryMax
     * @param type $location
     * @return boolean
     */
    private function validate ($title, $description, $salaryMin, $salaryMax, $location)
    {
        if ( trim ($title) === "" || !is_string ($title) )
        {
            $this->validationFailures[] = "Title is invalid";
        }

        if ( trim ($title) === "" || !is_string ($title) )
        {
            
        }

        if ( trim ($title) === "" || !is_string ($title) )
        {
            
        }

        if ( trim ($title) === "" || !is_string ($title) )
        {
            
        }
        
        if(  count ($this->validationFailures) > 0) {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param type $title
     * @param type $description
     * @param type $salaryMin
     * @param type $salaryMax
     * @param type $location
     */
    public function createJob (User $objUser, $title, $description, $salaryMin, $salaryMax, $location)
    {
        
    }

    /**
     * 
     * @param type $location
     * @param type $salaryMin
     * @param type $salaryMax
     * @return type
     */
    public function getJobs ($location, $salaryMin, $salaryMax)
    {
        return [];
    }

    public function getSalaryRange ()
    {
        
    }

    public function getLocations ()
    {
        
    }

}
