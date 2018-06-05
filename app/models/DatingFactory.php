<?php

/**
 * Description of DatingFactory
 *
 * @author michael.hampton
 */
class DatingFactory
{

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     *
     * @var type 
     */
    private $db;

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
     * @param type $gender
     * @param type $about
     * @param type $interests
     * @param type $nickname
     * @param type $location
     * @param type $age
     * @param type $distance
     * @return boolean
     */
    private function validate ($gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation)
    {
        if ( trim ($gender) === "" || !is_string ($gender) )
        {
            $this->validationFailures[] = "Gender is a mandatory field";
        }

        if ( trim ($about) === "" || !is_string ($about) )
        {
            $this->validationFailures[] = "About is a mandatory field";
        }

        if ( trim ($interests) === "" || !is_string ($interests) )
        {
            $this->validationFailures[] = "Interests is a mandatory field";
        }

        if ( trim ($nickname) === "" || !is_string ($nickname) )
        {
            $this->validationFailures[] = "Nickname is a mandatory field";
        }
        
        if($this->checkIfNicknameExists($nickname) === true) {
            $this->validationFailures[] = "The nickname is already being used";            
        }

        if ( trim ($location) === "" || !is_string ($location) )
        {
            $this->validationFailures[] = "Location is a mandatory field";
        }

        if ( trim ($age) === "" || !is_string ($age) )
        {
            $this->validationFailures[] = "Age is a mandatory field";
        }

        if ( trim ($distance) === "" || !is_string ($distance) )
        {
            $this->validationFailures[] = "Distance is a mandatory field";
        }

        if ( trim ($imageLocation) === "" || !is_string ($imageLocation) )
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
     * @param User $objUser
     * @return type
     * @throws Exception
     */
    private function checkIfUserHasProfile (User $objUser)
    {
        $arrResult = $this->db->_select ("dating_profile", "user_id = :userId", [":userId" => $objUser->getId ()]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }

        return !empty ($arrResult);
    }
    
    private function checkIfNicknameExists ($nickname)
    {        
        $arrResult = $this->db->_query ("SELECT * FROM dating_profile WHERE LOWER(nickname) = :nickname", [":nickname" => strtolower ($nickname)]);

        if ( $arrResult === false )
        {
            throw new Exception ("Db query failed");
        }

        return !empty ($arrResult);
    }

    /**
     * 
     * @param User $objUser
     * @param type $gender
     * @param type $about
     * @param type $interests
     * @param type $nickname
     * @param type $location
     * @param type $age
     * @param type $distance
     * @return boolean
     */
    public function createProfile (User $objUser, $gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation)
    {
        if ( $this->validate ($gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation) === false )
        {
            return false;
        }

        if ( $this->checkIfUserHasProfile ($objUser) === true )
        {
            return $this->updateProfile ($objUser, $gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation);
        }

        return $this->saveNewProfile ($objUser, $gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation);
    }

    /**
     * 
     * @param User $objUser
     * @param type $gender
     * @param type $about
     * @param type $interests
     * @param type $nickname
     * @param type $location
     * @param type $age
     * @param type $distance
     * @param type $imageLocation
     * @return boolean
     */
    private function updateProfile (User $objUser, $gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation)
    {
        $objDating = new Dating ($objUser);
        $objDating->setAbout ($about);
        $objDating->setAge ($age);
        $objDating->setGender ($gender);
        $objDating->setInterests ($interests);
        $objDating->setNickname ($nickname);
        $objDating->setLocation ($location);
        $objDating->setDistance ($distance);
        $objDating->setImageLocation ($imageLocation);
        $blResult = $objDating->save ();

        if ( $blResult === false )
        {
            return false;
        }

        return true;
    }

    public function getProfileByNickname(UserFactory $objUserFactory, $nickname)
    {
        $arrProfile = $this->getDatingProfiles($objUserFactory, $nickname);
        
        if(empty($arrProfile)) {
            return false;
        }
        
        return $arrProfile[0];
    }

        /**
     * 
     * @param UserFactory $objUserFactory
     * @return \Dating|boolean
     */
    public function getDatingProfiles (UserFactory $objUserFactory, $nickname = null, $gender = null, $age = null, $location = null)
    {

        $sqlWhere = ' is_active = 1';
        $arrWhere = [];

        if ( $nickname !== null )
        {
            $sqlWhere .= " AND nickname = :nickname";
            $arrWhere[":nickname"] = $nickname;
        }
        
        if ( $gender !== null )
        {
            $sqlWhere .= " AND gender = :gender";
            $arrWhere[":gender"] = $gender;
        }
        
        if ( $age !== null )
        {
            $sqlWhere .= " AND age = :age";
            $arrWhere[":age"] = $age;
        }
        
        if ( $location !== null )
        {
            $sqlWhere .= " AND location = :location";
            $arrWhere[":location"] = $location;
        }

        $arrResults = $this->db->_select ("dating_profile", $sqlWhere, $arrWhere);

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrProfiles = [];

        foreach ($arrResults as $arrResult) {

            try {

                $objUser = $objUserFactory->getUserById ($arrResult['user_id']);

                $objDating = new Dating ($objUser);
                $objDating->setAbout ($arrResult['about']);
                $objDating->setAge ($arrResult['age']);
                $objDating->setGender ($arrResult['gender']);
                $objDating->setInterests ($arrResult['interests']);
                $objDating->setNickname ($arrResult['nickname']);
                $objDating->setLocation ($arrResult['location']);
                $objDating->setDistance ($arrResult['distance']);
                $objDating->setImageLocation ($arrResult['image_location']);
                $objDating->setUser ($objUser);

                $arrProfiles[] = $objDating;
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage (), E_USER_WARNING);
                return false;
            }
        }

        return $arrProfiles;
    }

    /**
     * 
     * @param User $objUser
     * @param type $gender
     * @param type $about
     * @param type $interests
     * @param type $nickname
     * @param type $location
     * @param type $age
     * @param type $distance
     * @param type $imageLocation
     * @return boolean
     */
    private function saveNewProfile (User $objUser, $gender, $about, $interests, $nickname, $location, $age, $distance, $imageLocation)
    {
        $result = $this->db->create ("dating_profile", [
            "is_active" => 1,
            "user_id" => $objUser->getId (),
            "gender" => $gender,
            "about" => $about,
            "interests" => $interests,
            "nickname" => $nickname,
            "location" => $location,
            "age" => $age,
            "distance" => $distance,
            "image_location" => $imageLocation
                ]
        );

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return new Dating ($objUser);
    }

}
