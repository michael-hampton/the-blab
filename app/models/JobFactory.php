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
     * @param type $responsibilities
     * @param type $perks
     * @param type $duration
     * @param type $expires
     * @return boolean
     */
    private function validate ($title, $description, $salaryMin, $salaryMax, $location, $responsibilities, $perks, $duration, $expires, $skills)
    {
        if ( trim ($title) === "" || !is_string ($title) )
        {
            $this->validationFailures[] = "Title is a mandatory field";
        }

        if ( trim ($description) === "" || !is_string ($description) )
        {
            $this->validationFailures[] = "Description is a mandatory field";
        }

        if ( trim ($salaryMin) === "" || !is_string ($salaryMin) )
        {
            $this->validationFailures[] = "Minimum salary is a mandatory field";
        }

        if ( trim ($salaryMax) === "" || !is_string ($salaryMax) )
        {
            $this->validationFailures[] = "Maximum salary is a mandatory field";
        }

        if ( trim ($location) === "" || !is_string ($location) )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

//        if ( trim ($responsibilities) === "" || !is_string ($responsibilities) )
//        {
//            $this->validationFailures[] = "Responsibilities is a mandatory field";
//        }
//
//        if ( trim ($perks) === "" || !is_string ($perks) )
//        {
//            $this->validationFailures[] = "Perks is a mandatory field";
//        }

        if ( trim ($duration) === "" || !is_string ($duration) )
        {
            $this->validationFailures[] = "Duration is a mandatory field";
        }

        if ( trim ($expires) === "" || !is_string ($expires) )
        {
            $this->validationFailures[] = "Expires is a mandatory field";
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @param Page $objPage
     * @param type $title
     * @param type $description
     * @param type $salaryMin
     * @param type $salaryMax
     * @param type $location
     * @param type $responsibilities
     * @param type $perks
     * @param type $duration
     * @param type $expires
     * @return boolean
     */
    public function createJob (User $objUser, Page $objPage, $title, $description, $salaryMin, $salaryMax, $location, $responsibilities, $perks, $duration, $expires, $skills)
    {
        if ( $this->validate ($title, $description, $salaryMin, $salaryMax, $location, $responsibilities, $perks, $duration, $expires) === false )
        {
            return false;
        }

        $result = $this->db->create ("jobs", [
            "user_id" => $objUser->getId (),
            "page_id" => $objPage->getId (),
            "title" => $title,
            "description" => $description,
            "salary_min" => $salaryMin,
            "salary_max" => $salaryMax,
            "location" => $location,
            "responsibilities" => $responsibilities,
            "perks" => $perks,
            "duration" => $duration,
            "expires" => $expires,
            "skills" => $skills
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $location
     * @param type $salaryMin
     * @param type $salaryMax
     * @return boolean|array
     */
    public function getJobs ($location = null, $salaryMin = null, $salaryMax = null)
    {

        if ( $location !== null )
        {
            
        }

        if ( $salaryMin !== null && $salaryMax !== null )
        {
            
        }

        $arrResults = "";

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrJobs = [];

        foreach ($arrResults as $arrResult) {
            
        }

        return $arrJobs;
    }

    public function getSalaryRange ()
    {
        
    }

    public function getLocations ()
    {
        
    }

}
