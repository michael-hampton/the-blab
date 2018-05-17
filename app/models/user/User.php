<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author michael.hampton
 */
class User
{

    /**
     *
     * @var int 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $backgroundPosition;

    /**
     *
     * @var string 
     */
    private $username;

    /**
     *
     * @var type 
     */
    private $address;

    /**
     *
     * @var type 
     */
    private $postCode;

    /**
     *
     * @var string 
     */
    private $email;

    /**
     *
     * @var string 
     */
    private $firstName;

    /**
     *
     * @var type 
     */
    private $backgroundImage;

    /**
     *
     * @var string 
     */
    private $lastName;

    /**
     *
     * @var string 
     */
    private $password;

    /**
     *
     * @var int 
     */
    private $isActive;

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $profileImage;

    /**
     *
     * @var type 
     */
    private $dob;

    /**
     *
     * @var type 
     */
    private $jobTitle;

    /**
     *
     * @var type 
     */
    private $town;

    /**
     *
     * @var type 
     */
    private $telephone1;

    /**
     *
     * @var type 
     */
    private $dateCreated;

    /**
     *
     * @var type 
     */
    private $telephone2;

    /**
     * @var
     */
    private $about;

    /**
     * @var
     */
    private $favorite;

    /**
     * @var
     */
    private $married;

    /**
     * @var
     */
    private $gender;

    /**
     * @var
     */
    private $inerested;

    /**
     * @var
     */
    private $company;

    /**
     * @var
     */
    private $school;

    /**
     * @var
     */
    private $startedSchool;

    /**
     * @var
     */
    private $endedSchool;

    /**
     * @var
     */
    private $college;

    /**
     * @var
     */
    private $startedCollege;

    /**
     * @var
     */
    private $endedCollege;

    /**
     * @var
     */
    private $fromCity;

    /**
     * @var
     */
    private $language;

    /**
     * @var
     */
    private $religion;

    /**
     * @var
     */
    private $politics;

    /**
     * @var
     */
    private $country;

    /**
     * @var
     */
    private $privacy;

    /**
     * @var
     */
    private $skill;

    /**
     * @var
     */
    private $day;

    /**
     * @var
     */
    private $month;

    /**
     *
     * @var type 
     */
    private $year;

    /**
     * @var
     */
    private $studied;
    
    /**
     *
     * @var type 
     */
    private $lastLogin;

    /**
     *
     * @var type 
     */
    private $arrayFieldDefinition = array(
        "telephone1-form" => array("type" => "string", "required" => false, "empty" => false, "accessor" => "getTelephone1", "mutator" => "setTelephone1"),
        "telephone2-form" => array("type" => "string", "required" => true, "empty" => false, "accessor" => "getTelephone2", "mutator" => "setTelephone2"),
        "firstname-form" => array("type" => "string", "required" => false, "empty" => true, "accessor" => "getFirstName", "mutator" => "setFirstName"),
        "lastname-form" => array("type" => "string", "required" => false, "empty" => false, "accessor" => "getLastName", "mutator" => "setLastName"),
        "town-form" => array("type" => "string", "required" => false, "empty" => false, "accessor" => "getTown", "mutator" => "setTown"),
        "address-form" => array("type" => "string", "required" => false, "empty" => false, "accessor" => "getAddress", "mutator" => "setAddress"),
        "postcode-form" => array("type" => "int", "required" => false, "empty" => false, "accessor" => "getPostCode", "mutator" => "setPostCode"),
        "occupation-form" => array("type" => "string", "required" => false, "empty" => true, "accessor" => "getJobTitle", "mutator" => "setJobTitle"),
        "email-form" => array("type" => "string", "required" => false, "empty" => true, "accessor" => "getEmail", "mutator" => "setEmail"),
        "about_us" => array("mutator" => "setAbout", "accessor" => "getAbout", "type" => "int", "required" => "false"),
        "favorite_quotes" => array("mutator" => "setFavorite", "accessor" => "getFavorite", "type" => "date", "required" => "true"),
        "marital_status" => array("mutator" => "setMarried", "accessor" => "getMarried", "type" => "date", "required" => "true"),
        "gender" => array("mutator" => "setGender", "accessor" => "getGender", "type" => "int", "required" => "true"),
        "interested_in" => array("mutator" => "setInerested", "accessor" => "getInerested", "type" => "int", "required" => "true"),
        "day" => array("mutator" => "setDay", "accessor" => "getDay", "type" => "string", "required" => "false"),
        "month" => array("mutator" => "setMonth", "accessor" => "getMonth", "type" => "int", "required" => "true"),
        "year" => array("mutator" => "setYear", "accessor" => "getYear", "type" => "string", "required" => "true"),
        "birth_date_privacy" => array("mutator" => "setPrivacy", "accessor" => "getPrivacy", "type" => "int", "required" => "true"),
        "company" => array("mutator" => "setCompany", "accessor" => "getCompany", "type" => "int", "required" => "true"),
        "job_position" => array("mutator" => "setClaimGroup", "accessor" => "getClaimGroup", "type" => "int", "required" => "false"),
        "professional_skill" => array("mutator" => "setSkill", "accessor" => "getSkill", "type" => "string", "required" => "true"),
        "high_school_name" => array("mutator" => "setSchool", "accessor" => "getSchool", "type" => "int", "required" => "false"),
        "started_high_school_from_date" => array("mutator" => "setStartedSchool", "accessor" => "getStartedSchool", "type" => "int", "required" => "false"),
        "ended_high_school_at_date" => array("mutator" => "setEndedSchool", "accessor" => "getEndedSchool", "type" => "int", "required" => "true"),
        "college_field_of_study" => array("mutator" => "setStudied", "accessor" => "getStudied", "type" => "int", "required" => "true"),
        "college_name" => array("mutator" => "setCollege", "accessor" => "getCollege", "type" => "date", "required" => "true"),
        "started_college_from_date" => array("mutator" => "setStartedCollege", "accessor" => "getStartedCollege", "type" => "int", "required" => "false"),
        "ended_college_at_date" => array("mutator" => "setEndedCollege", "accessor" => "getEndedCollege", "type" => "string", "required" => "false"),
        "from_city_name" => array("mutator" => "setFromCity", "accessor" => "getFromCity", "type" => "string", "required" => "false"),
        "language" => array("mutator" => "setLanguage", "accessor" => "getLanguage", "type" => "string", "required" => "false"),
        "religion" => array("mutator" => "setReligion", "accessor" => "getReligion", "type" => "string", "required" => "false"),
        "politicl_view" => array("mutator" => "setPolitics", "accessor" => "getPolitics", "type" => "string", "required" => "false"),
        "country-form" => array("mutator" => "setCountry", "accessor" => "getCountry", "type" => "string", "required" => "false"),
    );

    /**
     * 
     * @param type $id
     * @throws Exception
     */
    public function __construct ($id)
    {
        $this->id = $id;

        $this->db = new Database();

        $this->db->connect ();

        if ( $this->populateObject () === FALSE )
        {
            throw new Exception ("Unable to populate object");
        }
    }

    /**
     * 
     * @param type $arrDocument
     * @return boolean
     */
    public function loadObject (array $arrData)
    {
        foreach ($arrData as $formField => $formValue) {

            if ( isset ($this->arrayFieldDefinition[$formField]) )
            {
                $mutator = $this->arrayFieldDefinition[$formField]['mutator'];

                if ( method_exists ($this, $mutator) && is_callable (array($this, $mutator)) )
                {
                    if ( isset ($this->arrayFieldDefinition[$formField]) && trim ($formValue) != "" )
                    {
                        call_user_func (array($this, $mutator), $formValue);
                    }
                }
            }
        }

        return true;
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return (int) $this->id;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    public function getEndedSchoolYear ()
    {
        return $this->endedSchoolYear;
    }

    public function setEndedSchoolYear ($endedSchoolYear)
    {
        $this->endedSchoolYear = $endedSchoolYear;
    }

    /**
     * 
     * @return type
     */
    public function getUsername ()
    {
        return $this->username;
    }

    /**
     * 
     * @return type
     */
    public function getEmail ()
    {
        return $this->email;
    }

    /**
     * 
     * @return type
     */
    public function getFirstName ()
    {
        return $this->firstName;
    }

    /**
     * 
     * @return type
     */
    public function getLastName ()
    {
        return $this->lastName;
    }

    /**
     * 
     * @return type
     */
    public function getPassword ()
    {
        return $this->password;
    }

    /**
     * 
     * @return type
     */
    public function getIsActive ()
    {
        return $this->isActive;
    }

    /**
     * 
     * @param type $username
     */
    public function setUsername ($username)
    {
        $this->username = $username;
    }

    /**
     * 
     * @param type $email
     */
    public function setEmail ($email)
    {
        $this->email = $email;
    }

    /**
     * 
     * @return type
     */
    public function getYear ()
    {
        return $this->year;
    }

    /**
     * 
     * @param type $year
     */
    public function setYear ($year)
    {
        $this->year = $year;
    }

    /**
     * 
     * @param type $firstName
     */
    public function setFirstName ($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * 
     * @param type $lastName
     */
    public function setLastName ($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * 
     * @param type $password
     */
    public function setPassword ($password)
    {
        $this->password = $password;
    }

    /**
     * 
     * @param type $isActive
     */
    public function setIsActive ($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * 
     * @return type
     */
    public function getBackgroundImage ()
    {
        return $this->backgroundImage;
    }

    /**
     * 
     * @param type $backgroundImage
     */
    public function setBackgroundImage ($backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
    }

    /**
     * 
     * @return type
     */
    public function getProfileImage ()
    {
        return $this->profileImage;
    }

    /**
     * 
     * @param type $profileImage
     */
    public function setProfileImage ($profileImage)
    {
        $this->profileImage = $profileImage;
    }

    /**
     * 
     * @return type
     */
    public function getBackgroundPosition ()
    {
        return $this->backgroundPosition;
    }

    /**
     * 
     * @param type $backgroundPosition
     */
    public function setBackgroundPosition ($backgroundPosition)
    {
        $this->backgroundPosition = $backgroundPosition;
    }

    /**
     * 
     * @return type
     */
    public function getDob ()
    {
        return $this->dob;
    }

    /**
     * 
     * @return type
     */
    public function getJobTitle ()
    {
        return $this->jobTitle;
    }

    /**
     * 
     * @return type
     */
    public function getTelephone1 ()
    {
        return $this->telephone1;
    }

    /**
     * 
     * @return type
     */
    public function getTelephone2 ()
    {
        return $this->telephone2;
    }

    public function getStartedCollegeYear ()
    {
        return $this->startedCollegeYear;
    }

    public function getStartedCollegeMonth ()
    {
        return $this->startedCollegeMonth;
    }

    public function getEndedCollegeYear ()
    {
        return $this->endedCollegeYear;
    }

    public function getEndedCollegeMonth ()
    {
        return $this->endedCollegeMonth;
    }

    /**
     * 
     * @param type $startedCollegeYear
     */
    public function setStartedCollegeYear ($startedCollegeYear)
    {
        $this->startedCollegeYear = $startedCollegeYear;
    }

    /**
     * 
     * @param type $startedCollegeMonth
     */
    public function setStartedCollegeMonth ($startedCollegeMonth)
    {
        $this->startedCollegeMonth = $startedCollegeMonth;
    }

    /**
     * 
     * @param type $endedCollegeYear
     */
    public function setEndedCollegeYear ($endedCollegeYear)
    {
        $this->endedCollegeYear = $endedCollegeYear;
    }

    /**
     * 
     * @param type $endedCollegeMonth
     */
    public function setEndedCollegeMonth ($endedCollegeMonth)
    {
        $this->endedCollegeMonth = $endedCollegeMonth;
    }

    /**
     * 
     * @param type $telephone1
     */
    public function setTelephone1 ($telephone1)
    {
        $this->telephone1 = $telephone1;
    }

    /**
     * 
     * @param type $telephone2
     */
    public function setTelephone2 ($telephone2)
    {
        $this->telephone2 = $telephone2;
    }

    /**
     * 
     * @return type
     */
    public function getTown ()
    {
        return $this->town;
    }

    /**
     * 
     * @param type $dob
     */
    public function setDob ($dob)
    {
        $this->dob = $dob;
    }

    /**
     * 
     * @param type $jobTitle
     */
    public function setJobTitle ($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * 
     * @param type $town
     */
    public function setTown ($town)
    {
        $this->town = $town;
    }

    /**
     * 
     * @return type
     */
    public function getAddress ()
    {
        return $this->address;
    }

    /**
     * 
     * @return type
     */
    public function getPostCode ()
    {
        return $this->postCode;
    }

    /**
     * 
     * @param type $address
     */
    public function setAddress ($address)
    {
        $this->address = $address;
    }

    /**
     * 
     * @param type $postCode
     */
    public function setPostCode ($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * 
     * @return type
     */
    public function getDateCreated ()
    {
        return $this->dateCreated;
    }

    /**
     * @return mixed
     */
    public function getAbout ()
    {
        return $this->about;
    }

    /**
     * @param mixed $about
     */
    public function setAbout ($about)
    {
        $this->about = $about;
    }

    /**
     * @return mixed
     */
    public function getFavorite ()
    {
        return $this->favorite;
    }

    /**
     * @param mixed $favorite
     */
    public function setFavorite ($favorite)
    {
        $this->favorite = $favorite;
    }

    /**
     * @return mixed
     */
    public function getMarried ()
    {
        return $this->married;
    }

    /**
     * @param mixed $married
     */
    public function setMarried ($married)
    {
        $this->married = $married;
    }

    /**
     * @return mixed
     */
    public function getGender ()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender ($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getInerested ()
    {
        return $this->inerested;
    }

    /**
     * @param mixed $inerested
     */
    public function setInerested ($inerested)
    {
        $this->inerested = $inerested;
    }

    /**
     * @return mixed
     */
    public function getCompany ()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany ($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getSchool ()
    {
        return $this->school;
    }

    /**
     * @param mixed $school
     */
    public function setSchool ($school)
    {
        $this->school = $school;
    }

    /**
     * @return mixed
     */
    public function getStartedSchool ()
    {
        return $this->startedSchool;
    }

    /**
     * @param mixed $startedSchool
     */
    public function setStartedSchool ($startedSchool)
    {
        $this->startedSchool = $startedSchool;
    }

    public function getStartedSchoolDay ()
    {
        return $this->startedSchoolDay;
    }

    public function getEndedSchoolDay ()
    {
        return $this->endedSchoolDay;
    }

    public function getStartedCollegeDay ()
    {
        return $this->startedCollegeDay;
    }

    public function getEndedCollegeDay ()
    {
        return $this->endedCollegeDay;
    }

    /**
     * 
     * @param type $startedSchoolDay
     */
    public function setStartedSchoolDay ($startedSchoolDay)
    {
        $this->startedSchoolDay = $startedSchoolDay;
    }

    /**
     * 
     * @param type $endedSchoolDay
     */
    public function setEndedSchoolDay ($endedSchoolDay)
    {
        $this->endedSchoolDay = $endedSchoolDay;
    }

    /**
     * 
     * @param type $startedCollegeDay
     */
    public function setStartedCollegeDay ($startedCollegeDay)
    {
        $this->startedCollegeDay = $startedCollegeDay;
    }

    /**
     * 
     * @param type $endedCollegeDay
     */
    public function setEndedCollegeDay ($endedCollegeDay)
    {
        $this->endedCollegeDay = $endedCollegeDay;
    }

    /**
     * @return mixed
     */
    public function getEndedSchool ()
    {
        return $this->endedSchool;
    }

    /**
     * @param mixed $endedSchool
     */
    public function setEndedSchool ($endedSchool)
    {
        $this->endedSchool = $endedSchool;
    }

    /**
     * @return mixed
     */
    public function getCollege ()
    {
        return $this->college;
    }

    /**
     * @param mixed $college
     */
    public function setCollege ($college)
    {
        $this->college = $college;
    }

    /**
     * @return mixed
     */
    public function getStartedCollege ()
    {
        return $this->startedCollege;
    }

    /**
     * @param mixed $startedCollege
     */
    public function setStartedCollege ($startedCollege)
    {
        $this->startedCollege = $startedCollege;
    }

    /**
     * @return mixed
     */
    public function getEndedCollege ()
    {
        return $this->endedCollege;
    }

    /**
     * @param mixed $endedCollege
     */
    public function setEndedCollege ($endedCollege)
    {
        $this->endedCollege = $endedCollege;
    }

    /**
     * @return mixed
     */
    public function getFromCity ()
    {
        return $this->fromCity;
    }

    /**
     * @param mixed $fromCity
     */
    public function setFromCity ($fromCity)
    {
        $this->fromCity = $fromCity;
    }

    /**
     * @return mixed
     */
    public function getLanguage ()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage ($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getReligion ()
    {
        return $this->religion;
    }

    /**
     * @param mixed $religion
     */
    public function setReligion ($religion)
    {
        $this->religion = $religion;
    }

    /**
     * @return mixed
     */
    public function getPolitics ()
    {
        return $this->politics;
    }

    /**
     * @param mixed $politics
     */
    public function setPolitics ($politics)
    {
        $this->politics = $politics;
    }

    /**
     * @return mixed
     */
    public function getCountry ()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry ($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getPrivacy ()
    {
        return $this->privacy;
    }

    /**
     * @param mixed $privacy
     */
    public function setPrivacy ($privacy)
    {
        $this->privacy = $privacy;
    }

    /**
     * @return mixed
     */
    public function getSkill ()
    {
        return $this->skill;
    }

    /**
     * @param mixed $skill
     */
    public function setSkill ($skill)
    {
        $this->skill = $skill;
    }

    /**
     * @return mixed
     */
    public function getDay ()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay ($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getMonth ()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth ($month)
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getStudied ()
    {
        return $this->studied;
    }

    /**
     * @param mixed $studied
     */
    public function setStudied ($studied)
    {
        $this->studied = $studied;
    }

    /**
     * 
     * @return type
     */
    public function getStartedSchoolYear ()
    {
        return $this->startedSchoolYear;
    }

    /**
     * 
     * @return type
     */
    public function getEndedSchoolMonth ()
    {
        return $this->endedSchoolMonth;
    }

    /**
     * 
     * @param type $endedSchoolMonth
     */
    public function setEndedSchoolMonth ($endedSchoolMonth)
    {
        $this->endedSchoolMonth = $endedSchoolMonth;
    }

    /**
     * 
     * @return type
     */
    public function getStartedSchoolMonth ()
    {
        return $this->startedSchoolMonth;
    }

    /**
     * 
     * @param type $startedSchoolYear
     */
    public function setStartedSchoolYear ($startedSchoolYear)
    {
        $this->startedSchoolYear = $startedSchoolYear;
    }

    /**
     * 
     * @param type $startedSchoolMonth
     */
    public function setStartedSchoolMonth ($startedSchoolMonth)
    {
        $this->startedSchoolMonth = $startedSchoolMonth;
    }

    /**
     * 
     * @param type $dateCreated
     */
    public function setDateCreated ($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
    
    /**
     * 
     * @return type
     */
    public function getLastLogin ()
    {
        return $this->lastLogin;
    }

    /**
     * 
     * @param type $lastLogin
     */
    public function setLastLogin ($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    

    /**
     * 
     * @param type $fileLocation
     * @return boolean
     */
    public function uploadUserProfileImage ($fileLocation)
    {
        $result = $this->db->update ("users", ["profile_image" => $fileLocation], "uid = :userId", [":userId" => $this->id]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $fileLocation
     * @return boolean
     */
    public function uploadUserBackgroundImage ($fileLocation, $backgroundPosition)
    {
        $result = $this->db->update ("users", ["background_image" => $fileLocation, "background_position" => $backgroundPosition], "uid = :userId", [":userId" => $this->id]);

        if ( $result === FALSE )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function populateObject ()
    {
        $result = $this->db->_select ("users", "uid = :userId", [':userId' => $this->id]);

        if ( empty($result))
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        $this->username = $result[0]['username'];
        $this->lastLogin = $result[0]['last_login'];
        $this->firstName = $result[0]['fname'];
        $this->lastName = $result[0]['lname'];
        $this->backgroundImage = $result[0]['background_image'];
        $this->profileImage = $result[0]['profile_image'];
        $this->backgroundPosition = $result[0]['background_position'];
        $this->dob = $result[0]['date_of_birth'];
        $this->jobTitle = $result[0]['job_title'];
        $this->town = $result[0]['town'];
        $this->telephone1 = $result[0]['telephone_1'];
        $this->telephone2 = $result[0]['telephone_2'];
        $this->postCode = $result[0]['postcode'];
        $this->address = $result[0]['address'];
        $this->email = $result[0]['email'];
        $this->favorite = $result[0]['favorite_quotes'];
        $this->about = $result[0]['about_us'];
        $this->married = $result[0]['marital_status'];
        $this->gender = $result[0]['gender'];
        $this->inerested = $result[0]['interested_in'];
        $this->day = $result[0]['day'];
        $this->month = $result[0]['month'];
        $this->year = $result[0]['year'];
        $this->privacy = $result[0]['birth_date_privacy'];
        $this->company = $result[0]['company'];
        $this->skill = $result[0]['professional_skill'];
        $this->school = $result[0]['high_school_name'];
        $this->startedSchool = $result[0]['started_high_school_from_date'];
        $this->endedSchool = $result[0]['ended_high_school_at_date'];
        $this->studied = $result[0]['college_field_of_study'];
        $this->college = $result[0]['college_name'];
        $this->startedCollege = $result[0]['started_college_from_date'];
        $this->endedCollege = $result[0]['ended_college_at_date'];
        $this->fromCity = $result[0]['from_city_name'];
        $this->language = $result[0]['language'];
        $this->religion = $result[0]['religion'];
        $this->politics = $result[0]['politicl_view'];
        $this->country = $result[0]['country'];


        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function save ()
    {

        $result = $this->db->update ("users", [
            "email" => $this->email,
            "fname" => $this->firstName,
            "lname" => $this->lastName,
            "date_of_birth" => $this->dob,
            "town" => $this->town,
            "job_title" => $this->jobTitle,
            "telephone_1" => $this->telephone1,
            "telephone_2" => $this->telephone2,
            "address" => $this->address,
            "postcode" => $this->postCode,
            'favorite_quotes' => $this->favorite,
            'about_us' => $this->about,
            'marital_status' => $this->married,
            'gender' => $this->gender,
            'interested_in' => $this->inerested,
            'day' => $this->day,
            'month' => $this->month,
            'year' => $this->year,
            'birth_date_privacy' => $this->privacy,
            'company' => $this->company,
            'professional_skill' => $this->skill,
            'high_school_name' => $this->school,
            'started_high_school_from_date' => $this->startedSchool,
            'ended_high_school_at_date' => $this->endedSchool,
            'college_field_of_study' => $this->studied,
            'college_name' => $this->college,
            'started_college_from_date' => $this->startedCollege,
            'ended_college_at_date' => $this->endedCollege,
            'from_city_name' => $this->fromCity,
            'language' => $this->language,
            'religion' => $this->religion,
            'politicl_view' => $this->politics,
            'country' => $this->country
                ], "uid = :userId", [':userId' => $this->id]
        );

        if ( $result === false )
        {
            trigger_error ("DATABASE QUERY FAILED", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objBlockedUser
     * @return boolean
     */
    public function blockUser (User $objBlockedUser)
    {
        $result = $this->db->create ("blocked_friend", ["user_added" => $this->id, "blocked_user" => $objBlockedUser->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @param User $objBlockedUser
     * @return boolean
     */
    public function unBlockUser (User $objBlockedUser)
    {
        $result = $this->db->delete ("blocked_friend", "blocked_user = :blockedUser AND user_added = :userId", [":blockedUser" => $objBlockedUser->getId (), ":userId" => $this->id]);

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
    public function updateLoginTime ()
    {
        $result = $this->db->update ("users", ["last_login" => date ("Y-m-d H:i:s")], "uid = :userId", [":userId" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
