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
     * @param Job $objJob
     * @return type
     */
    public function getJobApplications (Job $objJob)
    {
        $arrResults = $this->db->_select ("job_application", "job_id = :jobId", [":jobId" => $objJob->getId ()]);
        
        return $this->loadObject($arrResults);
    }

    /**
     * 
     * @param type $arrResults
     * @return boolean|\JobApplication
     */
    private function loadObject ($arrResults)
    {

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        if ( empty ($arrResults) )
        {
            return [];
        }

        $arrApplications = [];

        try {
            foreach ($arrResults as $arrResult) {
                $objJobApplication = new JobApplication ($arrResult['id']);
                $objUser = new User($arrResult['user_id']);
                $objJobApplication->setUser($objUser);
                $objJobApplication->setStatus($arrResult['application_status']);
                $objJobApplication->setDateSent($arrResult['date_sent']);
                $objJobApplication->setApplicationText($arrResult['application_text']);
                $objJobApplication->setJobId($arrResult['job_id']);

                $arrApplications[] = $objJobApplication;
            }
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }

        return $arrApplications;
    }

}
