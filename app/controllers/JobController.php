<?php

/**
 * Description of JobController
 *
 * @author michael.hampton
 */
use Phalcon\Mvc\View;

class JobController extends ControllerBase
{

    //https://github.com/sangramjagtap/job_board/tree/master/application/views

    public function indexAction ()
    {
        $data = $this->job_model->get_salary_range ();
        $data['location'] = array('Mumbai', 'Chennai', 'Bangalore', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'New Delhi', 'Chandigarh', 'Jaipur', 'Surat', 'Gurgaon', 'Noida');
        sort ($data['location']);
        $data['tab'] = 'FIND JOBS';

        $this->view->data = $data;
    }

    /**
     * list all jobs
     */
    public function getJobsAction ()
    {
        $objJobFactory = new JobFactory();

        $arrSalaryRange = $objJobFactory->getSalaryRange ();

        if ( !empty ($arrSalaryRange) )
        {
            $this->view->salary_min = $arrSalaryRange[0]['min'];
            $this->view->salary_max = $arrSalaryRange[0]['max'];
        }

        $arrLocations = $objJobFactory->getLocations ();

        $this->view->arrLocations = $arrLocations;
    }

    /* Search & filter */

    public function searchJobsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_POST['salary_min']) || !isset ($_POST['salary_max']) || !isset ($_POST['duration']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $minSalary = trim ($_POST['salary_min']) !== "" ? $_POST['salary_min'] : null;
        $maxSalary = trim ($_POST['salary_max']) !== "" ? $_POST['salary_max'] : null;
        $location = isset ($_POST['city']) && trim ($_POST['city']) !== "" ? $_POST['city'] : null;
        $duration = isset ($_POST['duration']) && trim ($_POST['duration']) !== "" ? $_POST['duration'] : null;

        try {

            $objPage = isset ($_POST['pageId']) && trim ($_POST['pageId']) !== "" ? new Page ($_POST['pageId']) : null;

            //(Page $objPage = null, $locations = null, $salaryMin = null, $salaryMax = null, $duration = null)
            $arrJobs = (new JobFactory())->getJobs ($objPage, $location, $minSalary, $maxSalary, $duration);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrJobs === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrJobs = $arrJobs;
    }

    /**
     *  View single job- full screen
     * @param type $id
     */
    public function viewAction ($id)
    {

        try {
            $objJob = new Job ($id);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->objJob = $objJob;
    }

    public function homeAction ()
    {
        
    }

    /**
     * Open job form in edit mode
     * @param type $id
     */
    public function editAction ($id)
    {
        try {
            $objJob = new Job ($id);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->partial ("job/addJob", ["objJob" => $objJob, "addType" => "edit"]);
    }

    /* Delete job */

    public function deleteAction ()
    {
        if ( empty ($_POST['id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objJob = new Job ($_POST['id']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blResponse = $objJob->delete ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

    public function addJobAction ()
    {
        $this->view->addType = "add";
    }

    public function saveUpdateAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['title']) ||
                !isset ($_POST['pageId']) ||
                !isset ($_POST['location']) ||
                !isset ($_POST['description']) ||
                !isset ($_POST['responsibilities']) ||
                !isset ($_POST['skills']) ||
                !isset ($_POST['perks']) ||
                !isset ($_POST['salary_min']) ||
                !isset ($_POST['salary_max']) ||
                !isset ($_POST['duration']) ||
                !isset ($_POST['expires']) ||
                !isset ($_POST['job_id'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        try {
            $objJob = new Job ($_POST['job_id']);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objJob->setDescription ($_POST['description']);
        $objJob->setDuration ($_POST['duration']);
        $objJob->setExpires ($_POST['expires']);
        $objJob->setLocation ($_POST['location']);
        $objJob->setMaxSalary ($_POST['salary_max']);
        $objJob->setMinSalary ($_POST['salary_min']);
        $objJob->setResponsibilities ($_POST['responsibilities']);
        $objJob->setSkills ($_POST['skills']);
        $objJob->setPerks ($_POST['perks']);
        $objJob->setTitle ($_POST['title']);

        $blResponse = $objJob->save ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objJob->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

    public function saveJobAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['title']) ||
                !isset ($_POST['pageId']) ||
                !isset ($_POST['location']) ||
                !isset ($_POST['description']) ||
                !isset ($_POST['responsibilities']) ||
                !isset ($_POST['skills']) ||
                !isset ($_POST['perks']) ||
                !isset ($_POST['salary_min']) ||
                !isset ($_POST['salary_max']) ||
                !isset ($_POST['duration']) ||
                !isset ($_POST['expires'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        try {
            $objJobFactory = new JobFactory();
            $objJob = $objJobFactory->createJob (
                    new User ($_SESSION['user']['user_id']), new Page ('test_tamara'), $_POST['title'], $_POST['description'], $_POST['salary_min'], $_POST['salary_max'], $_POST['location'], $_POST['responsibilities'], $_POST['perks'], $_POST['duration'], $_POST['expires'], $_POST['skills']
            );
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $objJob === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objJobFactory->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

}
