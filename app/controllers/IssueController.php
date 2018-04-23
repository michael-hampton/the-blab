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

}
