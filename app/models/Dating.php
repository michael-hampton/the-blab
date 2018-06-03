<?php

/**
 * Description of Dating
 *
 * @author michael.hampton
 */
class Dating
{

    /**
     *
     * @var type 
     */
    private $user;

    /**
     *
     * @var type 
     */
    private $gender;

    /**
     *
     * @var type 
     */
    private $about;

    /**
     *
     * @var type 
     */
    private $interests;

    /**
     *
     * @var type 
     */
    private $nickname;

    /**
     *
     * @var type 
     */
    private $location;

    /**
     *
     * @var type 
     */
    private $age;

    /**
     *
     * @var type 
     */
    private $distance;

    /**
     *
     * @var type 
     */
    private $imageLocation;

    /*
     * @var type 
     */
    private $validationFailures = [];

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
     * @param type $id
     */
    public function __construct (User $objUser)
    {
        $this->db = new Database();
        $this->db->connect ();
        $this->user = $objUser->getId ();

        if ( $this->populateObject () === false )
        {
            throw new Exception ("Unable to populate object");
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
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * 
     * @return type
     */
    public function getGender ()
    {
        return $this->gender;
    }

    /**
     * 
     * @return type
     */
    public function getAbout ()
    {
        return $this->about;
    }

    /**
     * 
     * @return type
     */
    public function getInterests ()
    {
        return $this->interests;
    }

    /**
     * 
     * @return type
     */
    public function getNickname ()
    {
        return $this->nickname;
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
    public function getAge ()
    {
        return $this->age;
    }

    /**
     * 
     * @return type
     */
    public function getDistance ()
    {
        return $this->distance;
    }

    /**
     * 
     * @return type
     */
    public function getImageLocation ()
    {
        return $this->imageLocation;
    }

    /**
     * 
     * @param type $user
     */
    public function setUser ($user)
    {
        $this->user = $user;
    }

    /**
     * 
     * @param type $gender
     */
    public function setGender ($gender)
    {
        $this->gender = $gender;
    }

    /**
     * 
     * @param type $about
     */
    public function setAbout ($about)
    {
        $this->about = $about;
    }

    /**
     * 
     * @param type $interests
     */
    public function setInterests ($interests)
    {
        $this->interests = $interests;
    }

    /**
     * 
     * @param type $nickname
     */
    public function setNickname ($nickname)
    {
        $this->nickname = $nickname;
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
     * @param type $age
     */
    public function setAge ($age)
    {
        $this->age = $age;
    }

    /**
     * 
     * @param type $distance
     */
    public function setDistance ($distance)
    {
        $this->distance = $distance;
    }

    /**
     * 
     * @param type $imageLocation
     */
    public function setImageLocation ($imageLocation)
    {
        $this->imageLocation = $imageLocation;
    }

    /**
     * 
     * @return boolean
     */
    private function validate ()
    {
        if ( trim ($this->gender) === "" || !is_string ($this->gender) )
        {
            $this->validationFailures[] = "Gender is a mandatory field";
        }

        if ( trim ($this->about) === "" || !is_string ($this->about) )
        {
            $this->validationFailures[] = "About is a mandatory field";
        }

        if ( trim ($this->interests) === "" || !is_string ($this->interests) )
        {
            $this->validationFailures[] = "Interests is a mandatory field";
        }

        if ( trim ($this->nickname) === "" || !is_string ($this->nickname) )
        {
            $this->validationFailures[] = "Nickname is a mandatory field";
        }

        if ( trim ($this->location) === "" || !is_string ($this->location) )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

        if ( trim ($this->age) === "" || !is_string ($this->age) )
        {
            $this->validationFailures[] = "Age is a mandatory field";
        }

        if ( trim ($this->distance) === "" || !is_string ($this->distance) )
        {
            $this->validationFailures[] = "Distance is a mandatory field";
        }

        if ( trim ($this->imageLocation) === "" || !is_string ($this->imageLocation) )
        {
            $this->validationFailures[] = "Image is a mandatory field";
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

        $result = $this->db->update ("dating_profile", [
            "gender" => $this->gender,
            "about" => $this->about,
            "interests" => $this->interests,
            "nickname" => $this->nickname,
            "location" => $this->location,
            "age" => $this->age,
            "distance" => $this->distance,
            "image_location" => $this->imageLocation
                ], 
                "user_id = :userId", [
            ":userId" => $this->user
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
     * @return boolean
     */
    private function populateObject ()
    {
        $arrResult = $this->db->_select ("dating_profile", "user_id = :userId", [":userId" => $this->user]);

        if ( $arrResult === false || empty ($arrResult) )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        $this->about = $arrResult[0]['about'];
        $this->age = $arrResult[0]['age'];
        $this->distance = $arrResult[0]['distance'];
        $this->gender = $arrResult[0]['gender'];
        $this->imageLocation = $arrResult[0]['image_location'];
        $this->interests = $arrResult[0]['interests'];
        $this->location = $arrResult[0]['location'];
        $this->nickname = $arrResult[0]['nickname'];
        $this->user;

        return true;
    }

}
