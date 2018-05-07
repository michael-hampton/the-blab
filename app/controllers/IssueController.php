<?php

use Phalcon\Mvc\View;

/**
 * Description of IssueController
 *
 * @author michael.hampton
 */
class IssueController extends ControllerBase
{

    public function handlerAction ()
    {
        $message = $this->dispatcher->getParam ("message", "string");

        if ( $message === null )
        {
            $message = $this->defaultErrrorMessage;
        }

        $this->view->message = $message;
    }

    public function reportIssueAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['report_post_data']) || empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( !mail (EMAIL_ADDRESS, $objUser->getFirstName () . ' ' . $objUser->getLastName () . ' just reported a problem on The Blab', $_POST['report_post_data']) )
        {
            trigger_error ("Failed to send email bug report", E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");

        echo "<pre>";
        print_r ($_POST);
        die ("Mike");
    }

}
