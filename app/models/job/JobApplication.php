<?php

/**
 * Description of JobApplication
 *
 * @author michael.hampton
 */
class JobApplication
{

    /**
     *
     * @var type 
     */
    private $db;

    /**
     *
     * @var type 
     */
    private $id;

    /**
     *
     * @var type 
     */
    private $user;

    /**
     *
     * @var type 
     */
    private $jobId;

    /**
     *
     * @var type 
     */
    private $applicationText;

    /**
     *
     * @var type 
     */
    private $status;

    /**
     *
     * @var type 
     */
    private $dateSent;

    /**
     * 
     * @param type $id
     */
    public function __construct ($id)
    {
        $this->db = new Database();
        $this->db->connect ();
        $this->id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * 
     * @return type
     */
    public function getJobId ()
    {
        return $this->jobId;
    }

    /**
     * 
     * @return type
     */
    public function getApplicationText ()
    {
        return $this->applicationText;
    }

    /**
     * 
     * @return type
     */
    public function getStatus ()
    {
        return $this->status;
    }

    /**
     * 
     * @return type
     */
    public function getDateSent ()
    {
        return $this->dateSent;
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param type $jobId
     */
    public function setJobId ($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * 
     * @return type
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * 
     * @param User $user
     */
    public function setUser (User $user)
    {
        $this->user = $user;
    }

    /**
     * 
     * @param type $applicationText
     */
    public function setApplicationText ($applicationText)
    {
        $this->applicationText = $applicationText;
    }

    /**
     * 
     * @param type $status
     */
    public function setStatus ($status)
    {
        $this->status = $status;
    }

    /**
     * 
     * @param type $dateSent
     */
    public function setDateSent ($dateSent)
    {
        $this->dateSent = $dateSent;
    }

    /**
     * 
     * @return boolean
     */
    public function updateJobApplicationStatus ()
    {
        $result = $this->db->update ("job_application", ["application_status" => $this->status], "id = :id", [":id" => $this->id]);

        if ( $result === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

}
