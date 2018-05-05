<?php

use Phalcon\Mvc\View;

/**
 * Description of NotificationController
 *
 * @author michael.hampton
 */
class NotificationController extends ControllerBase
{

    public function getAllNotificationsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrNotifications = (new NotificationFactory())->getNotificationsForUser (new User ($_SESSION['user']['user_id']));

        if ( $arrNotifications === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $this->view->arrNotifications = $arrNotifications;
 
    }

    public function getNotificationCountAction ()
    {
        $this->view->disable ();

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        try {
            $objUser = new User ($userId);
            $count = (new Notification())->getUnreadCountForUser ($objUser);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $count === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success", ["unseen_notification" => $count]);
    }

    public function fetchAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $objNotification = new NotificationFactory();

        if ( isset ($_POST['view']) && $_POST['view'] == 'yes' )
        {
            $objNotification->markAsRead ($objUser);
        }
        
        $arrNotifications = $objNotification->getNotificationsForUser ($objUser);

        if ( $arrNotifications === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrNotifications = $arrNotifications;
    }

    public function testEmailAction ()
    {
        $this->view->disable ();

        $objEmail = new EmailNotification (new User ($_SESSION['user']['user_id']), "Test notification", "test body");
        $blResponse = $objEmail->sendEmail ();

        if ( $blResponse === false )
        {
            echo "FAILED";
            return;
        }

        echo "SUCCESS";
    }

}
