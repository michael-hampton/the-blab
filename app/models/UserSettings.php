<?php

/**
 * Description of UserSettings
 *
 * @author michael.hampton
 */
class UserSettings extends BaseUserSettings
{

    /**
     *
     * @var type 
     */
    private $objDb;

    /**
     *
     * @var type 
     */
    private $validationFailures = [];

    /**
     * 
     * @param User $objUser
     */
    public function __construct (User $objUser)
    {

        $this->objUser = $objUser;
        $this->objDb = new Database();
        $this->objDb->connect ();

        if ( $this->getUserSettings () === false )
        {
            throw new Exception ('Unable to get user settings');
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

    private function getUserSettings ()
    {
        $arrResults = $this->objDb->_select ("user_settings", "user_id = :userId", [":userId" => $this->objUser->getId ()]);

        if ( $arrResults === false )
        {
            trigger_error ("Query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        if ( trim ($arrResults[0]['feed_groups']) !== "" )
            $this->feedGroup = $arrResults[0]['feed_groups'];

        if ( trim ($arrResults[0]['notification_events']) !== "" )
            $this->notificationEvent = $arrResults[0]['notification_events'];
        if ( trim ($arrResults[0]['notification_groups']) !== "" )
            $this->notificationGroup = $arrResults[0]['notification_groups'];
        if ( trim ($arrResults[0]['notification_pages']) !== "" )
            $this->notificationPage = $arrResults[0]['notification_pages'];

        if ( trim ($arrResults[0]['feed_events']) !== "" )
            $this->feedEvent = $arrResults[0]['feed_events'];
        if ( trim ($arrResults[0]['feed_pages']) !== "" )
            $this->feedPage = $arrResults[0]['feed_pages'];
        if ( trim ($arrResults[0]['visible_groups']) !== "" )
            $this->profileGroup = $arrResults[0]['visible_groups'];
        if ( trim ($arrResults[0]['visible_events']) !== "" )
            $this->profileEvent = $arrResults[0]['visible_events'];
        if ( trim ($arrResults[0]['visible_pages']) !== "" )
            $this->profilePage = $arrResults[0]['visible_pages'];
        if ( trim ($arrResults[0]['visible_birthday']) !== "" )
            $this->profileBirthday = $arrResults[0]['visible_birthday'];
        if ( trim ($arrResults[0]['visible_email']) !== "" )
            $this->profileEmail = $arrResults[0]['visible_email'];
        if ( trim ($arrResults[0]['visible_telephone']) !== "" )
            $this->profileTelephone = $arrResults[0]['visible_telephone'];
        if ( trim ($arrResults[0]['visible_likes']) !== "" )
            $this->profileLikes = $arrResults[0]['visible_likes'];
        if ( trim ($arrResults[0]['email_like']) !== "" )
            $this->emailLike = $arrResults[0]['email_like'];
        if ( trim ($arrResults[0]['email_tag']) !== "" )
            $this->emailTag = $arrResults[0]['email_tag'];
        if ( trim ($arrResults[0]['email_friendRequest']) !== "" )
            $this->emailFriendRequst = $arrResults[0]['email_friendRequest'];
        if ( trim ($arrResults[0]['email_post']) !== "" )
            $this->emailPost = $arrResults[0]['email_post'];

        return true;
    }

    /**
     * 
     * @param User $objUser
     * @return boolean
     */
    private function checkUserExists ()
    {
        $result = $this->objDb->_select ("user_settings", "user_id = :userId", [":userId" => $this->objUser->getId ()]);

        if ( !empty ($result[0]) )
        {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param type $showGroups
     * @param type $showEvents
     * @param type $showPages
     * @param type $birthday
     * @param type $likes
     * @param type $pages
     * @param type $events
     * @param type $groups
     * @param type $telephone
     * @param type $email
     * @return boolean
     */
    private function validate (
    $showGroups, $showEvents, $showPages, $birthday, $likes, $pages, $events, $groups, $telephone, $email, $emailLike, $emailPost, $emailFriendRequest, $emailTag
    )
    {
        if ( trim ($showGroups) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($showEvents) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($showPages) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($birthday) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($likes) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($pages) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }
        if ( trim ($events) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($groups) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($emailFriendRequest) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($emailLike) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($emailPost) === "" )
        {
            $this->validationFailures[] = "Missing required field";
        }

        if ( trim ($emailTag) === "" )
        {
            $this->validationFailures[] = "Missing required field";
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
     * @param type $showGroups
     * @param type $showEvents
     * @param type $showPages
     * @param type $birthday
     * @param type $likes
     * @param type $pages
     * @param type $events
     * @param type $groups
     * @param type $telephone
     * @param type $email
     * @return boolean
     */
    public function saveUserSettings (
    $showGroups, $showEvents, $showPages, $birthday, $likes, $pages, $events, $groups, $telephone, $email, $notificationGroups, $notificationEvents, $notificationPages, $emailLike, $emailPost, $emailFriendRequest, $emailTag
    )
    {

        if ( $this->validate (
                        $showGroups, $showEvents, $showPages, $birthday, $likes, $pages, $events, $groups, $telephone, $email, $emailLike, $emailPost, $emailFriendRequest, $emailTag
                ) === false )
        {
            return false;
        }

        $arrParams = [
            "feed_groups" => $showGroups,
            "feed_events" => $showEvents,
            "feed_pages" => $showPages,
            "visible_groups" => $groups,
            "visible_birthday" => $birthday,
            "visible_email" => $email,
            "visible_telephone" => $telephone,
            "visible_likes" => $likes,
            "visible_pages" => $pages,
            "visible_events" => $events,
            "user_id" => $this->objUser->getId (),
            "notification_pages" => $notificationPages,
            "notification_groups" => $notificationGroups,
            "notification_events" => $notificationEvents,
            "email_like" => $emailLike,
            "email_tag" => $emailTag,
            "email_friendRequest" => $emailFriendRequest,
            "email_post" => $emailPost,
        ];

        if ( $this->checkUserExists () === true )
        {

            $result = $this->objDb->update ("user_settings", $arrParams, "user_id = :userId", [":userId" => $this->objUser->getId ()]);
        }
        else
        {
            $result = $this->objDb->create ("user_settings", $arrParams);
        }

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
