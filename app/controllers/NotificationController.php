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
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrNotifications = (new NotificationFactory())->getNotificationsForUser (new User ($_SESSION['user']['user_id']));

        if ( $arrNotifications === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrNotifications as $arrNotification) {
            echo '<div style="padding:10px;" id="vpb_display_vnotifications"><script type="text/javascript">$("#v_no_new_notification").show();</script><div class="input-group vpb_popup_fb_box">
							<div style="display:inline-block;vertical-align:middle !important;">
								<div style="display:inline-block;vertical-align:top !important;margin-right:8px; cursor:pointer; max-width:50px; width:auto;"><img src="" class="" border="0" align="absmiddle" onclick="window.location.href=\'http://www.vasplus.info/wall/victor\';"></div>
								<div class="vpb_popup_fb_box_acbd"><span class="vpb_hover" title="Victor Olu" onclick="window.location.href=\'http://www.vasplus.info/wall/victor\';">' . $arrNotification->getMessage () . '</span><br>
	<i class="fa fa-clock-o"></i> <span class="fb_box_acbd" title="Wednesday 28th of February 2018 08:22:53 am">' . $arrNotification->getDateAdded () . '</span></div>
								
								<div style="clear:both;"></div>
							</div>
								
								<div style="clear:both;"></div>
							  </div></div>';
        }
    }

    public function fetchAction ()
    {
        $this->view->disable ();

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

        $count = 0;
        $html = '';

        if ( !empty ($arrNotifications) )
        {
            $html = '<div id="notificationTitle">Notifications '
                    . ' <div style="cursor:pointer;" class="pull-right closeNotification">x</div>'
                    . '<div style="float:right; font-weight:normal !important; font-size:13px !important;">
<span id="view_all_notifications" style="" class="vpb_hover_b" onclick="vpb_show_notifications_details(\'michaelhampton\');">View All</span>
</div>'
                    . '</div>';

            //$html .= '<div id="notificationsBody" class="notifications">';


            foreach ($arrNotifications as $arrNotification) {
                $html .= '<li>
				<a href="#">
					<strong>' . $arrNotification->getMessage () . '</strong><br>
					<small><em>' . $arrNotification->getDateAdded () . '</em></small>
				</a>
			</li><li class="divider"></li>';

                if ( $arrNotification->getHasRead () === 0 )
                {
                    $count++;
                }
            }

            //$html .= '</div>';

            $html .= "<button id='prev' >Prev</button>";
            $html .= '<div id="next" class="text-center link-block">
                                <a href="#">
                                    <i class="fa fa-envelope"></i> <strong>Next</strong>
                                </a>
                            </div>';
        }

        $arrTest = array("notification" => $html, "unseen_notification" => $count);

        echo json_encode ($arrTest);
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
