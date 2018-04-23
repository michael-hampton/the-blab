<?php

/**
 * Description of EmailLog
 *
 * @author michael.hampton
 */
class EmailLog
{

    /**
     *
     * @var type 
     */
    private $emailAddress;

    public function __construct ($arrEmailAddresses = array())
    {
        if ( empty ($arrEmailAddresses) )
        {
            trigger_error ("No email address defined", E_USER_WARNING);
            return false;
        }

        $this->emailAddress = implode (",", $arrEmailAddresses);
    }

    /**
     * 
     * @param type $message
     * @param type $errno
     */
    public function logError ($message, $errno)
    {
        $this->saveLog ($message, $errno);
        $this->sendMail ($message, $errno);
    }

    /**
     * 
     * @param type $message
     * @param type $errno
     */
    private function saveLog ($message, $errno)
    {
        $logFile = $_SERVER['DOCUMENT_ROOT'] . "/blab/app/logs";

        $objLogger = new Katzgrau\KLogger\Logger ($logFile, Psr\Log\LogLevel::DEBUG, ["filename" => "debug_" . date ('m-d-Y') . ".log"]);

        $objLogger->error ($message);
        $this->sendMail ($message, $errno);
    }

    /**
     * 
     * @param type $message
     * @param type $subject
     */
    private function sendMail ($message, $subject)
    {
        $to = "mike@blab.x10host.com";

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <test@theblab.com>' . "\r\n";
        //$headers .= 'Cc: bluetiger_ua' . "\r\n";

        mail ($to, $subject, $message, $headers);
    }

}
