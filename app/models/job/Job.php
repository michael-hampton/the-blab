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
    private $responsibilities;

    /**
     *
     * @var type 
     */
    private $perks;

    /**
     *
     * @var type 
     */
    private $duration;

    /**
     *
     * @var type 
     */
    private $expires;

    /**
     *
     * @var type 
     */
    private $pageId;

    /**
     *
     * @var type 
     */
    private $pageName;

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

    /**
     * 
     * @return type
     */
    public function getResponsibilities ()
    {
        return $this->responsibilities;
    }

    /**
     * 
     * @return type
     */
    public function getPerks ()
    {
        return $this->perks;
    }

    /**
     * 
     * @param type $responsibilities
     */
    public function setResponsibilities ($responsibilities)
    {
        $this->responsibilities = $responsibilities;
    }

    /**
     * 
     * @param type $perks
     */
    public function setPerks ($perks)
    {
        $this->perks = $perks;
    }

    /**
     * 
     * @return type
     */
    public function getDuration ()
    {
        return $this->duration;
    }

    /**
     * 
     * @return type
     */
    public function getExpires ()
    {
        return $this->expires;
    }

    /**
     * 
     * @param type $duration
     */
    public function setDuration ($duration)
    {
        $this->duration = $duration;
    }

    /**
     * 
     * @param type $expires
     */
    public function setExpires ($expires)
    {
        $this->expires = $expires;
    }

    /**
     * 
     * @return type
     */
    public function getPageId ()
    {
        return $this->pageId;
    }

    /**
     * 
     * @return type
     */
    public function getPageName ()
    {
        return $this->pageName;
    }

    /**
     * 
     * @param type $pageId
     */
    public function setPageId ($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * 
     * @param type $pageName
     */
    public function setPageName ($pageName)
    {
        $this->pageName = $pageName;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->db->_query ("SELECT j.*, p.page_name FROM jobs j
                                    INNER JOIN page p ON p.id = j.page_id
                                    WHERE j.id = :jobId", [":jobId" => $this->id]);

        if ( $result === false || empty ($result) )
        {
            trigger_error ("Unable to populate object", E_USER_WARNING);
            return false;
        }

        $this->title = $result[0]['title'];
        $this->description = $result[0]['description'];
        $this->minSalary = $result[0]['salary_min'];
        $this->maxSalary = $result[0]['salary_max'];
        $this->location = $result[0]['location'];
        $this->responsibilities = $result[0]['responsibilities'];
        $this->perks = $result[0]['perks'];
        $this->userId = $result[0]['user_id'];
        $this->duration = $result[0]['duration'];
        $this->expires = $result[0]['expires'];
        $this->pageId = $result[0]['page_id'];
        $this->skills = $result[0]['skills'];
        $this->pageId = $result[0]['page_id'];
        $this->pageName = $result[0]['page_name'];

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function validate ()
    {
        if ( trim ($this->title) === "" || !is_string ($this->title) )
        {
            $this->validationFailures[] = "Title is a mandatory field";
        }

        if ( trim ($this->description) === "" || !is_string ($this->description) )
        {
            $this->validationFailures[] = "Description is a mandatory field";
        }

        if ( trim ($this->minSalary) === "" || !is_string ($this->minSalary) )
        {
            $this->validationFailures[] = "Minimum salary is a mandatory field";
        }

        if ( trim ($this->maxSalary) === "" || !is_string ($this->maxSalary) )
        {
            $this->validationFailures[] = "Maximum salary is a mandatory field";
        }

        if ( trim ($this->location) === "" || !is_string ($this->location) )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

//        if ( trim ($this->responsibilities) === "" || !is_string ($this->responsibilities) )
//        {
//            $this->validationFailures[] = "Responsibilities is a mandatory field";
//        }
//        if ( trim ($this->perks) === "" || !is_string ($this->perks) )
//        {
//            $this->validationFailures[] = "Perks is a mandatory field";
//        }

        if ( trim ($this->duration) === "" || !is_string ($this->duration) )
        {
            $this->validationFailures[] = "Duration is a mandatory field";
        }

        if ( trim ($this->expires) === "" || !is_string ($this->expires) )
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
     * @return boolean
     */
    public function save ()
    {
        if ( $this->validate () === false )
        {
            return false;
        }

        $result = $this->db->update ("jobs", [
            "title" => $this->title,
            "description" => $this->description,
            "salary_min" => $this->minSalary,
            "salary_max" => $this->maxSalary,
            "location" => $this->location,
            "responsibilities" => $this->responsibilities,
            "perks" => $this->perks,
            "duration" => $this->duration,
            "expires" => $this->expires,
            "skills" => $this->skills
                ], "id = :jobId", [":jobId" => $this->id]
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
     * @return boolean
     */
    public function delete ()
    {
        $result = $this->db->update ("jobs", ["is_active" => 0], "id = :jobId", [":jobId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return type
     * @throws Exception
     */
    public function checkUserHasAppliedForJob (User $objUser)
    {
        $result = $this->db->_select ("job_application", "user_id = :userId AND job_id = :jobId", [":jobId" => $this->id, ":userId" => $objUser->getId ()]);

        if ( $result === false )
        {
            throw new Exception ("Db query failed");
        }
        
        return !empty($result);
    }

}
