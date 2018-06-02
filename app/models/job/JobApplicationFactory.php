<?php

/**
 * Description of JobApplication
 *
 * @author michael.hampton
 */
class JobApplicationFactory
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     * 
     */
    public function __construct ()
    {
        $this->db = new Database();
        $this->db->connect ();
    }

    /**
     * 
     * @param Job $objJob
     * @param User $objUser
     * @param type $applicationText
     * @return boolean
     */
    public function sendJobApplication (Job $objJob, User $objUser, $applicationText)
    {
        $result = $this->db->create ("job_application", ["user_id" => $objUser->getId (), "date_sent" => date ("Y-m-d H:i:s"), "application_text" => $applicationText, "application_status" => 0, "job_id" => $objJob->getId ()]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function getJobApplications ()
    {
        $arrResults = [];

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }
        
        if(empty($arrResults)) {
            return [];
        }
        
        $arrApplications = [];
        
        foreach ($arrResults as $arrResult) {
            
        }
        
        return $arrApplications;
    }

}
