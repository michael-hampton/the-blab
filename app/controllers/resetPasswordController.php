<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of resetPasswordController
 *
 * @author michael.hampton
 */
use Phalcon\Mvc\View;

class resetPasswordController extends ControllerBase
{

    public function resetPasswordAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $objAccountRecovery = new AccountRecovery();

        $blResult = $objAccountRecovery->verifyToken ($_GET['selector'], $_GET['validator']);

        if ( $blResult === false )
        {
            die ("Invalid token");
        }

        $this->view->token = $_GET['selector'];
    }

    public function saveResetPasswordAction ()
    {
        $this->view->disable ();


        if (
                empty ($_POST['password']) ||
                empty ($_POST['confirmPassword']) ||
                trim ($_POST['password']) !== trim ($_POST['confirmPassword']) ||
                empty ($_POST['token']) )
        {
            $this->ajaxresponse ("error", "Please ensure all fields are filled in and both passwords match");
        }

        $blResult = (new AccountRecovery())->resetPasswordAction ($_POST['token'], $_POST['password'], new UserFactory ());

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to reset password");
        }

        $this->ajaxresponse ("success", "success");
    }

}
