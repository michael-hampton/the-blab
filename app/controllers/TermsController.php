<?php

use Phalcon\Mvc\View;

/**
 * Description of TermsController
 *
 * @author michael.hampton
 */
class TermsController extends ControllerBase
{

    public function indexAction ()
    {
        
    }

    public function termsAction ()
    {
        
    }

    public function settingsAction ()
    {
        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);
        $objUserSettings = new UserSettings ($objUser);

        if ( $objUserSettings === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $this->view->objUserSettings = $objUserSettings;
    }

    public function privacyAction ()
    {
        
    }

    public function saveSettingsAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if (
                !isset ($_POST['feed-groups']) ||
                !isset ($_POST['feed-events']) ||
                !isset ($_POST['feed-pages']) ||
                !isset ($_POST['birthday']) ||
                !isset ($_POST['likes']) ||
                !isset ($_POST['pages']) ||
                !isset ($_POST['events']) ||
                !isset ($_POST['groups']) ||
                !isset ($_POST['telephone']) ||
                !isset ($_POST['email']) ||
                !isset ($_POST['notification-groups']) ||
                !isset ($_POST['notification-events'])
        )
        {
            trigger_error ("Missing data", E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);

            $blResult = (new UserSettings ($objUser))->saveUserSettings (
                    $_POST['feed-groups'], $_POST['feed-events'], $_POST['feed-pages'], $_POST['birthday'], $_POST['likes'], $_POST['pages'], $_POST['events'], $_POST['groups'], $_POST['telephone'], $_POST['email'], $_POST['notification-groups'], $_POST['notification-events'], $_POST['notification-pages']
            );
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }


        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

}
