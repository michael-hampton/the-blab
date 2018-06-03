<?php

/**
 * Description of DatingController
 *
 * @author michael.hampton
 */
use Phalcon\Mvc\View;

class DatingController extends ControllerBase
{

    public function testAction ()
    {
        try {
            $arrProfiles = (new DatingFactory())->getDatingProfiles (new UserFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $ex->getMessage ()]
                            ]
            );
        }

        if ( $arrProfiles === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get profiles"]
                            ]
            );
        }

        $this->view->arrProfiles = $arrProfiles;
    }

    public function profileAction ($userId = null)
    {
        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        if ( $userId !== null && (int) $userId !== (int) $_SESSION['user']['user_id'] )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objDating = $userId !== null ? new Dating ($objUser) : null;
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $ex->getMessage ()]
                            ]
            );
        }

        $this->view->objDating = $objDating;
        $this->view->objUser = $objUser;
    }

    public function saveProfileAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['userId']) || !isset ($_POST['gender']) || !isset ($_POST['about-you']) || !isset ($_POST['your-interests']) || !isset ($_POST['nickname']) || !isset ($_POST['location']) || !isset ($_POST['age']) || !isset ($_POST['distance']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        try {
            $objDatingFactory = new DatingFactory();
            $longPath = $this->rootPath . "/blab/public/uploads/dating/";
            $shortPath = "/blab/public/uploads/dating/";

            $objFileUpload = new FileUpload ($longPath);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objFileUpload->prepareUpload ($_FILES['avatar-2']);
        $blUploadValidationResponse = $objFileUpload->validateUpload ();

        if ( $blUploadValidationResponse === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objFileUpload->getValidationFailures ()));
        }

        $blUploadResponse = $objFileUpload->saveUpload ();

        if ( $blUploadResponse === false )
        {
            $this->ajaxresponse ("error", "Unable to save upload");
        }

        $blResult = $objDatingFactory->createProfile (new User ($_POST['userId']), $_POST['gender'], $_POST['about-you'], $_POST['your-interests'], $_POST['nickname'], $_POST['location'], $_POST['age'], $_POST['distance'], $objFileUpload->getTargetFile ());

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    /**
     * 
     * @param type $nickname
     */
    public function viewProfileAction ($nickname)
    {
        try {
            $objProfile = (new DatingFactory())->getProfileByNickname (new UserFactory (), $nickname);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $ex->getMessage ()]
                            ]
            );
        }

        if ( $objProfile === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get profile for user"]
                            ]
            );
        }
        
        $this->view->objProfile = $objProfile;
    }

}
