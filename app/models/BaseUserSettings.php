<?php

class BaseUserSettings
{

    /**
     *
     * @var type 
     */
    protected $profileBirthday = 'private';

    /**
     *
     * @var type 
     */
    protected $profileTelephone = 'private';

    /**
     *
     * @var type 
     */
    protected $profileEmail = 'private';

    /**
     *
     * @var type 
     */
    protected $profileGroup = 'public';

    /**
     *
     * @var type 
     */
    protected $profileEvent = 'public';

    /**
     *
     * @var type 
     */
    protected $profilePage = 'public';

    /**
     *
     * @var type 
     */
    protected $profileFriends = 'public';

    /**
     *
     * @var type 
     */
    protected $profileLikes = 'public';

    /**
     *
     * @var type 
     */
    protected $objUser;

    /**
     *
     * @var type 
     */
    protected $feedPage = 'yes';

    /**
     *
     * @var type 
     */
    protected $feedGroup = 'yes';

    /**
     *
     * @var type 
     */
    protected $notificationEvent;

    /**
     *
     * @var type 
     */
    protected $notificationGroup;

    /**
     *
     * @var type 
     */
    protected $notificationPage;

     /**
     *
     * @var type 
     */
    protected $emailFriendRequest = 'yes';

     /**
     *
     * @var type 
     */

    protected $emailLike = 'yes';

     /**
     *
     * @var type 
     */
    protected $emailTag = 'yes';

     /**
     *
     * @var type 
     */
    protected $emailPost = 'yes';

    /**
     *
     * @var type 
     */
    protected $feedEvent = 'yes';

    public function getProfileBirthday ()
    {
        return $this->profileBirthday;
    }

    public function getProfileTelephone ()
    {
        return $this->profileTelephone;
    }

    public function getProfileEmail ()
    {
        return $this->profileEmail;
    }

    public function getProfileGroup ()
    {
        return $this->profileGroup;
    }

    public function getNotificationEvent ()
    {
        return $this->notificationEvent;
    }

    public function getNotificationGroup ()
    {
        return $this->notificationGroup;
    }

    public function getNotificationPage ()
    {
        return $this->notificationPage;
    }

    /**
     * 
     * @param type $notificationEvent
     */
    public function setNotificationEvent ($notificationEvent)
    {
        $this->notificationEvent = $notificationEvent;
    }

    /**
     * 
     * @param type $notificationGroup
     */
    public function setNotificationGroup ($notificationGroup)
    {
        $this->notificationGroup = $notificationGroup;
    }

    /**
     * 
     * @param type $notificationPage
     */
    public function setNotificationPage ($notificationPage)
    {
        $this->notificationPage = $notificationPage;
    }

    public function getProfileEvent ()
    {
        return $this->profileEvent;
    }

    public function getProfilePage ()
    {
        return $this->profilePage;
    }

    public function getProfileFriends ()
    {
        return $this->profileFriends;
    }

    public function getProfileLikes ()
    {
        return $this->profileLikes;
    }

    public function getFeedPage ()
    {
        return $this->feedPage;
    }

    public function getFeedGroup ()
    {
        return $this->feedGroup;
    }

    public function getFeedEvent ()
    {
        return $this->feedEvent;
    }

    /**
     * 
     * @param type $profileBirthday
     */
    protected function setProfileBirthday ($profileBirthday)
    {
        $this->profileBirthday = $profileBirthday;
    }

    /**
     * 
     * @param type $profileTelephone
     */
    protected function setProfileTelephone ($profileTelephone)
    {
        $this->profileTelephone = $profileTelephone;
    }

    /**
     * 
     * @param type $profileEmail
     */
    protected function setProfileEmail ($profileEmail)
    {
        $this->profileEmail = $profileEmail;
    }

    /**
     * 
     * @param type $profileGroup
     */
    protected function setProfileGroup ($profileGroup)
    {
        $this->profileGroup = $profileGroup;
    }

    /**
     * 
     * @param type $profileEvent
     */
    protected function setProfileEvent ($profileEvent)
    {
        $this->profileEvent = $profileEvent;
    }

    /**
     * 
     * @param type $profilePage
     */
    protected function setProfilePage ($profilePage)
    {
        $this->profilePage = $profilePage;
    }

    /**
     * 
     * @param type $profileFriends
     */
    protected function setProfileFriends ($profileFriends)
    {
        $this->profileFriends = $profileFriends;
    }

    /**
     * 
     * @param type $profileLikes
     */
    protected function setProfileLikes ($profileLikes)
    {
        $this->profileLikes = $profileLikes;
    }

    /**
     * 
     * @param type $feedPage
     */
    protected function setFeedPage ($feedPage)
    {
        $this->feedPage = $feedPage;
    }

    /**
     * 
     * @param type $feedGroup
     */
    protected function setFeedGroup ($feedGroup)
    {
        $this->feedGroup = $feedGroup;
    }
    
    /**
     * 
     * @return type
     */
    public function getEmailFriendRequest ()
    {
        return $this->emailFriendRequest;
    }

    /**
     * 
     * @return type
     */
    public function getEmailLike ()
    {
        return $this->emailLike;
    }

    /**
     * 
     * @return type
     */
    public function getEmailTag ()
    {
        return $this->emailTag;
    }

    /**
     * 
     * @return type
     */
    public function getEmailPost ()
    {
        return $this->emailPost;
    }

    /**
     * 
     * @param type $emailFriendRequest
     */
    public function setEmailFriendRequest ($emailFriendRequest)
    {
        $this->emailFriendRequest = $emailFriendRequest;
    }

    /**
     * 
     * @param type $emailLike
     */
    public function setEmailLike ($emailLike)
    {
        $this->emailLike = $emailLike;
    }

    /**
     * 
     * @param type $emailTag
     */
    public function setEmailTag ($emailTag)
    {
        $this->emailTag = $emailTag;
    }

    /**
     * 
     * @param type $emailPost
     */
    public function setEmailPost ($emailPost)
    {
        $this->emailPost = $emailPost;
    }

    
    /**
     * 
     * @param type $feedEvent
     */
    protected function setFeedEvent ($feedEvent)
    {
        $this->feedEvent = $feedEvent;
    }

    public function getProfileSetting ($action)
    {
        $getter = 'getProfile' . ucfirst ($action);
        
        $value = $this->$getter ();

        $arrFriendList = (new UserFactory())->getFriendList($this->objUser);

        if ( strtolower (trim ($value)) === 'public' || (strtolower (trim ($value)) === 'friends' && in_array ($this->objUser->getId (), $arrFriendList)) )
        {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param type $action
     * @return boolean
     */
    public function getFeedSetting ($action)
    {
        $getter = 'getFeed' . $action;
        $value = $this->$getter ();

        if ( strtolower (trim ($value)) === 'yes' )
        {
            return true;
        }

        return false;
    }

     /**
     * 
     * @param type $action
     * @return boolean
     */
    public function getEmailSetting ($action)
    {
        $getter = 'getEmail' . $action;
        
        $value = $this->$getter ();

        if ( strtolower (trim ($value)) === 'yes' )
        {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param UserFactory $objUserFactory
     * @param type $action
     * @return boolean
     */
    public function getNotificationSetting ($action)
    {
        $getter = 'getNotification' . $action;
        $value = $this->$getter ();

        if ( strtolower (trim ($value)) === 'yes' )
        {
            return true;
        }

        return false;
    }

}