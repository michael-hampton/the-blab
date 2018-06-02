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
     * @param type $skills
     * @return \Job|boolean
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
            "is_acvtive" => 1,
            "title" => $title,
            "description" => $description,
            "salary_min" => $salaryMin,
            "salary_max" => $salaryMax,
            "location" => $location,
            "responsibilities" => $responsibilities,
            "perks" => $perks,
            "duration" => $duration,
            "expires" => date ("Y-m-d", strtotime ($expires)),
            "skills" => $skills
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return new Job ($result);
    }

    /**
     * 
     * @param type $location
     * @param type $salaryMin
     * @param type $salaryMax
     * @return boolean|array
     */
    public function getJobs (Page $objPage = null, $locations = null, $salaryMin = null, $salaryMax = null, $duration = null)
    {
        $arrParams = [];

        $sql = "SELECT j.*, p.page_name FROM jobs j
                INNER JOIN page p ON p.id = j.page_id
                WHERE is_active = 1";

        if ( $objPage !== null )
        {
            $sql .= " AND page_id = :pageId";
            $arrParams[":pageId"] = $objPage->getId ();
        }

        if ( $locations !== null )
        {
            $sql .= " AND LOCATION IN(";

            foreach ($locations as $key => $location) {
                $sql .= ":location" . $key . ",";
                $arrParams[":location" . $key] = $location;
            }

            $sql = rtrim ($sql, ",");

            $sql .= ")";
        }

        if ( $salaryMin !== null && $salaryMax !== null )
        {
            $sql .= " AND salary_min >= :minSalary AND salary_max <= :maxSalary";
            $arrParams[":minSalary"] = $salaryMin;
            $arrParams[":maxSalary"] = $salaryMax;
        }

        if ( $duration !== null )
        {
            $sql .= " AND duration = :duration";
            $arrParams[":duration"] = $duration;
        }

        $sql .= " ORDER BY title ASC";

        $arrResults = $this->db->_query ($sql, $arrParams);

        return $this->loadObject ($arrResults);
    }

    /**
     * 
     * @param type $arrResults
     * @return \Job|boolean
     */
    private function loadObject ($arrResults)
    {
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
            $objJob = new Job ($arrResult['id']);
            $objJob->setDescription ($arrResult['description']);
            $objJob->setDuration ($arrResult['duration']);
            $objJob->setExpires ($arrResult['expires']);
            $objJob->setLocation ($arrResult['location']);
            $objJob->setMaxSalary ($arrResult['salary_max']);
            $objJob->setMinSalary ($arrResult['salary_min']);
            $objJob->setPerks ($arrResult['perks']);
            $objJob->setResponsibilities ($arrResult['responsibilities']);
            $objJob->setSkills ($arrResult['skills']);
            $objJob->setTitle ($arrResult['title']);
            $objJob->setUserId ($arrResult['user_id']);
            $objJob->setPageId ($arrResult['page_id']);
            $objJob->setPageName ($arrResult['page_name']);

            $arrJobs[] = $objJob;
        }

        return $arrJobs;
    }

    /**
     * 
     * @param Page $objPage
     * @return boolean
     */
    public function getSalaryRange (Page $objPage = null)
    {
        $arrParams = [];

        $sql = "SELECT MIN(salary_min) AS min, MAX(salary_max) AS max FROM jobs";

        if ( $objPage !== null )
        {
            $sql .= " WHERE page_id = :pageId";
            $arrParams[":pageId"] = $objPage->getId ();
        }

        $arrResult = $this->db->_query ($sql, $arrParams);

        if ( $arrResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return $arrResult;
    }

    /**
     * 
     * @param Page $objPage
     * @return boolean
     */
    public function getLocations (Page $objPage = null)
    {
        $arrParams = [];

        $sql = "SELECT location FROM jobs";

        if ( $objPage !== null )
        {
            $sql .= " WHERE page_id = :pageId";
            $arrParams[":pageId"] = $objPage->getId ();
        }

        $arrResult = $this->db->_query ($sql, $arrParams);

        if ( $arrResult === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return $arrResult;
    }

}
