<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailNotification
 *
 * @author michael.hampton
 */
class EmailNotification
{

    /**
     *
     * @var type 
     */
    private $objUser;

    /**
     *
     * @var type 
     */
    private $header;

    /**
     *
     * @var type 
     */
    private $body;

    /**
     *
     * @var type 
     */
    private $sfromEmail = "admin@blab.x10host.com";
    private $noReply = "noreply@blab.x10host.com";

    /**
     * 
     * @param User $objUser
     * @param type $header
     * @param type $body
     */
    public function __construct (User $objUser, $header, $body)
    {
        $this->objUser = $objUser;
        $this->header = $header;
        $this->body = $body;
    }

    private function validateEmail ()
    {
        if ( trim ($this->objUser->getEmail ()) === "" )
        {
            trigger_error ("No email address for user {$this->objUser->getId ()}", E_USER_WARNING);
            return false;
        }

        if ( trim ($this->header) === "" || trim ($this->body) === "" )
        {
            trigger_error ("Required parameters missing", E_USER_WARNING);
            return false;
        }
    }

    private function getHeaders ()
    {
        $headers = "From: " . strip_tags ($this->sfromEmail) . "\r\n";
        $headers .= "Reply-To: " . strip_tags ($this->noReply) . "\r\n";
        $headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return $headers;
    }

    /**
     * 
     * @return boolean
     */
    public function sendEmail ()
    {
        if ( $this->validateEmail () === false )
        {
            return false;
        }

        $headers = $this->getHeaders ();

        $body = $this->getEmailTemplate ();

        if ( !mail (trim ($this->objUser->getEmail ()), $this->header, $body, $headers) )
        {
            //trigger_error ("Unable to send email to {$this->objUser->getEmail ()}", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function getEmailTemplate ()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/blab/app/views/templates/emailTemplate.phtml";

        if ( !file_exists ($filePath) )
        {
            trigger_error ("We tried to get an email template but the file {$filePath} doesnt exist", E_USER_WARNING);
            return false;
        }


        $message = file_get_contents ($filePath);
        $message = str_replace ('{header}', $this->header, $message);
        $message = str_replace ('{body}', $this->body, $message);

        return $message;
    }

    public function mail_attachment ($filename, $path)
    {
        $file = $path . $filename;
        $file_size = filesize ($file);
        $handle = fopen ($file, "r");
        $content = fread ($handle, $file_size);
        fclose ($handle);

        $content = chunk_split (base64_encode ($content));
        $uid = md5 (uniqid (time ()));
        $name = basename ($file);

        $eol = PHP_EOL;

// Basic headers
        $header = "From: " . $this->sfromEmail . " <" . $this->sfromEmail . ">" . $eol;
//$header .= "Reply-To: ".$replyto.$eol;
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"";

// Put everything else in $message
        $message = "--" . $uid . $eol;
        $message .= "Content-Type: text/html; charset=ISO-8859-1" . $eol;
        $message .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
        $message .= $this->body . $eol;
        $message .= "--" . $uid . $eol;
        $message .= "Content-Type: application/pdf; name=\"" . $filename . "\"" . $eol;
        $message .= "Content-Transfer-Encoding: base64" . $eol;
        $message .= "Content-Disposition: attachment; filename=\"" . $filename . "\"" . $eol;
        $message .= $content . $eol;
        $message .= "--" . $uid . "--";

        if ( mail (trim ($this->objUser->getEmail ()), $this->header, $message, $header) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
