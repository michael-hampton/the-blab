<?php

/**
 * Description of PostUpload
 *
 * @author michael.hampton
 */
class PostUpload
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
    private $objPostActionFactory;

    /**
     *
     * @var type 
     */
    private $objUploadFactory;

    /**
     *
     * @var type 
     */
    private $objCommentFactory;

    /**
     *
     * @var type 
     */
    private $objReviewFactory;

    /**
     *
     * @var type 
     */
    private $objTagUserFactory;

    /**
     *
     * @var type 
     */
    private $objPostFactory;

    /**
     *
     * @var type 
     */
    private $objCommentReplyFactory;

    /**
     *
     * @var type 
     */
    private $objUser;

    /**
     *
     * @var type 
     */
    private $objBadWordFilter;

    /**
     *
     * @var type 
     */
    private $objBannerFactory;

    /**
     *
     * @var type 
     */
    private $objAdvertFactory;

    /**
     *
     * @var type 
     */
    private $objPost;

    /**
     *
     * @var type 
     */
    private $objNotificationFactory;

    /**
     *
     * @var type 
     */
    private $objEmailNotificationFactory;

    /**
     *
     * @var type 
     */
    private $blFeedPost = false;

    /**
     * 
     * @param PostActionFactory $objPostActionFactory
     * @param UploadFactory $objUploadFactory
     * @param CommentFactory $objCommentFactory
     * @param ReviewFactory $objReviewFactory
     * @param TagUserFactory $objTagUserFactory
     * @param CommentReplyFactory $objCommentReplyFactory
     * @param JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter
     * @param User $objUser
     * @param BannerFactory $objBannerFactory
     * @param AdvertFactory $objAdvertFactory
     * @param type $uploadType
     * @param type $uploadId
     */
    public function __construct (
    PostActionFactory $objPostActionFactory, UploadFactory $objUploadFactory, CommentFactory $objCommentFactory, ReviewFactory $objReviewFactory, TagUserFactory $objTagUserFactory, CommentReplyFactory $objCommentReplyFactory, JCrowe\BadWordFilter\BadWordFilter $objBadWordFilter, User $objUser, BannerFactory $objBannerFactory, AdvertFactory $objAdvertFactory, NotificationFactory $objNotificationFactory, EmailNotificationFactory $objEmailNotificationFactory, $uploadType, $uploadId
    )
    {
        $this->objPostActionFactory = $objPostActionFactory;
        $this->objUploadFactory = $objUploadFactory;
        $this->objCommentFactory = $objCommentFactory;
        $this->objCommentReplyFactory = $objCommentReplyFactory;
        $this->objReviewFactory = $objReviewFactory;
        $this->objTagUserFactory = $objTagUserFactory;
        $this->objBadWordFilter = $objBadWordFilter;
        $this->objBannerFactory = $objBannerFactory;
        $this->objAdvertFactory = $objAdvertFactory;
        $this->objUser = $objUser;
        $this->objNotificationFactory = $objNotificationFactory;
        $this->objEmailNotificationFactory = $objEmailNotificationFactory;

        try {
            switch ($uploadType) {
                case "page":
                    $this->objPostFactory = new PagePost (new Page ($uploadId), $this->objPostActionFactory, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);
                    break;

                case "group":
                    $this->objPostFactory = new GroupPost (new Group ($uploadId), $this->objPostActionFactory, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);
                    break;
                case "event":
                    $this->objPostFactory = new EventPost (new Event ($uploadId), $this->objPostActionFactory, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);
                    break;

                default:
                    $this->blFeedPost = true;
                    $this->objPostFactory = new UserPost ($this->objPostActionFactory, $this->objUploadFactory, $this->objCommentFactory, $this->objReviewFactory, $this->objTagUserFactory, $this->objCommentReplyFactory);
                    break;
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
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
    public function getObjPost ()
    {
        return $this->objPost;
    }

    /**
     * 
     * @param type $rootPath
     * @param type $arrFiles
     * @param type $target_dir
     * @param type $comment
     * @param type $arrTags
     * @param UserSettings $objUserSettings
     * @param type $privacy
     * @param type $blAddToStory
     * @return boolean
     */
    public function multipleUploadValidation (
    $rootPath, $arrFiles, $target_dir, $comment, $arrTags = null, UserSettings $objUserSettings, $privacy, $blAddToStory = false
    )
    {
        $arrIds = [];

        if ( !is_dir ($target_dir) )
        {
            mkdir ($target_dir);
        }

        foreach ($arrFiles["pictures"]['name'] as $key => $name) {

            if ( trim ($name) === "" )
            {
                continue;
            }

            $target_file = $target_dir . basename ($name);

            $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

            if ( $arrFiles["pictures"]["size"][$key] > 500000 )
            {
                $this->validationFailures[] = "The file you tried to upload is to big";
            }

// Allow certain file formats
            if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
            {
                $this->validationFailures[] = "The file you tried to upload is in the wrong format.";
            }

            $check = getimagesize ($_FILES["pictures"]["tmp_name"][$key]);

            if ( $check === false )
            {
                $this->validationFailures[] = "Unable to save upload 02";
            }

            if ( count ($this->validationFailures) > 0 )
            {
                return false;
            }

            if ( !$this->compress ($_FILES["pictures"]["tmp_name"][$key], $target_file, 80) )
            {
                $this->validationFailures[] = "Unable to complete upload";
                return false;
            }


            $savedLocation = str_replace ($rootPath, "", $target_file);

            $objUploaded = $this->objUploadFactory->createUpload ($this->objUser, $savedLocation);

            if ( $objUploaded === false )
            {
                $this->validationFailures[] = "Unable to save upload";
            }

            $arrIds[] = $objUploaded->getId ();

            if ( $blAddToStory === true )
            {
                $this->addToStory ($objUploaded);
            }
        }

        if ( count ($this->validationFailures) > 0 )
        {
            return false;
        }


        if ( $this->blFeedPost === true )
        {
            $blResult = $this->createPost ($comment, $arrIds, $privacy);
        }
        else
        {
            $blResult = $this->saveComment ($comment, $arrIds, $privacy);
        }

        if ( $blResult === false )
        {
            return false;
        }

        if ( $arrTags !== null )
        {
            if ( !$this->saveTags ($arrTags, $objUserSettings, $comment) )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @param type $arrTags
     * @param UserSettings $objUserSettings
     * @param type $comment
     * @return boolean
     */
    private function saveTags ($arrTags, UserSettings $objUserSettings, $comment)
    {

        $tags = explode (",", $arrTags);

        $username = $this->objUser->getUsername ();

        $message = $username . " Tagged you in a photo";

        foreach ($tags as $taggedUser) {

            $objNewUser = new User ($taggedUser);

            $blTagResult = $this->objTagUserFactory->createTagForPost ($objNewUser, $this->objPost);

            if ( $blTagResult === false )
            {
                $this->validationFailures[] = "Unable to save tag";
                return false;
            }

            $blResult = $this->objNotificationFactory->createNotification ($objNewUser, $message);

            if ( $objUserSettings->getEmailSetting ('tag') === true )
            {
                $this->objEmailNotificationFactory->createNotification ($objNewUser, $comment, $message);
            }
        }

        return true;
    }

    /**
     * 
     * @param type $objUploaded
     * @return boolean
     */
    private function addToStory ($objUploaded)
    {
        $arrAdvert = $this->objAdvertFactory->getProfileBannerForUser ($this->objUser, $this->objBannerFactory);

        if ( empty ($arrAdvert[0]) )
        {
            $objAdvert = $this->objAdvertFactory->createProfileBanner ($this->objUser, $this->objUser->getFirstName () . ' ' . $this->objUser->getLastName ());

            if ( $objAdvert === false )
            {
                $this->validationFailures[] = "Unable to add uploads to story";
                return false;
            }
        }
        else
        {
            $objAdvert = $arrAdvert[0];
        }

        $blBannerResult = $this->objBannerFactory->addToStory ($this->objUser, $objUploaded, $objAdvert, $this->objNotificationFactory, $this->objEmailNotificationFactory);

        if ( $blBannerResult === false )
        {
            $this->validationFailures[] = "Unable to add picture(s) to story";
            return false;
        }

        return true;
    }

    /**
     * 
     * @param type $source
     * @param type $destination
     * @param type $quality
     * @return boolean
     */
    private function compress ($source, $destination, $quality)
    {

        $info = getimagesize ($source);

        if ( $info['mime'] == 'image/jpeg' )
        {
            $image = imagecreatefromjpeg ($source);
        }
        elseif ( $info['mime'] == 'image/gif' )
        {
            $image = imagecreatefromgif ($source);
        }
        elseif ( $info['mime'] == 'image/png' )
        {
            $image = imagecreatefrompng ($source);
        }

        if ( !isset ($image) )
        {
            return false;
        }

        return imagejpeg ($image, $destination, $quality);
    }

    /**
     * 
     * @param type $comment
     * @param type $arrIds
     * @param type $privacy
     * @return boolean
     */
    private function saveComment ($comment, $arrIds, $privacy)
    {
        $objPost = $this->objPostFactory->createComment ($comment, $this->objUser, $this->objBadWordFilter, $arrIds);

        if ( $objPost === false )
        {
            $this->validationFailures[] = "Unable to save post";
            return false;
        }

        $this->objPost = $objPost;

        return true;
    }

    /**
     * 
     * @param type $comment
     * @param type $arrIds
     * @param type $privacy
     * @return boolean
     */
    private function createPost ($comment, $arrIds, $privacy)
    {

        $objPost = $this->objPostFactory->createPost ($comment, $this->objUser, $this->objBadWordFilter, $arrIds, null, 3, $privacy);

        if ( $objPost === false )
        {
            $this->validationFailures[] = "Unable to save post";
            return false;
        }

        $this->objPost = $objPost;

        return true;
    }

}
