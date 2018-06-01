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

    public function getJobsAction ()
    {
        $this->view->location = array('Mumbai', 'Chennai', 'Bangalore', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'New Delhi', 'Chandigarh', 'Jaipur', 'Surat', 'Gurgaon', 'Noida');
        $this->view->salary_min = 50;
        $this->view->salary_max = 500;
    }

    /* Search & filter */

    public function searchJobsAction ()
    {
        if ( empty ($_POST['city']) || empty ($_POST['salary_min']) || empty ($_POST['salary_max']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $minSalary = trim ($_POST['salary_min']) !== "" ? $_POST['salary_min'] : null;
        $maxSalary = trim ($_POST['salary_max']) !== "" ? $_POST['salary_max'] : null;
        $location = trim ($_POST['city']) !== "" ? $_POST['city'] : null;

        try {
            $arrJobs = (new JobFactory())->getJobs ($location, $minSalary, $maxSalary);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrJobs === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        //$res = $this->job_model->filter_result ($data);
        //echo json_encode ($res);
    }

    /* View single job- full screen */

    public function viewAction ()
    {
//		if($this->uri->segment(3))
//		{			
//			$job_id = $this->uri->segment(3);	
//			$data = $this->job_model->get_job($job_id);
//			$data = (array) $data;
//			$data['tab'] = 'SINGLE JOB';
//			$this->load->view('header',$data);
//			$this->load->view('single_job',$data);
//		}
//		else
//			redirect('jobs');
    }

    public function homeAction ()
    {
        
    }

    /* Open job form in edit mode */

    public function editAction ()
    {
//        $data['tab'] = 'EDIT';
//        if ( $this->session->userdata ('user') && $this->uri->segment (3) && $this->job_model->validate_author ($this->uri->segment (3)) )
//        {
//            $data['edit'] = $this->job_model->get_job ($this->uri->segment (3));
//            $data['location'] = array('Mumbai', 'Chennai', 'Bangalore', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad', 'New Delhi', 'Chandigarh', 'Jaipur', 'Surat', 'Gurgaon', 'Noida');
//            sort ($data['location']);
//            $this->load->view ('header', $data);
//            $this->load->view ('post_job', $data);
//        }
//        else
//            redirect (base_url ());
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
        ;

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
                !isset ($_POST['page_id']) ||
                !isset ($_POST['location']) ||
                !isset ($_POST['description']) ||
                !isset ($_POST['responsibilities']) ||
                !isset ($_POST['skills']) ||
                !isset ($_POST['perks']) ||
                !isset ($_POST['salary_min']) ||
                !isset ($_POST['salary_max']) ||
                !isset ($_POST['duration']) ||
                !isset ($_POST['expires']) ||
                !isset ($_POST['id'])
        )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objJob = new Job ($_POST['id']);
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
                !isset ($_POST['page_id']) ||
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

        try {
            $blResponse = (new JobFactory())->createJob (
                    $objUser, new Page ($_POST['page_id']), $_POST['title'], $_POST['description'], $_POST['salary_min'], $_POST['salary_max'], $_POST['location'], $_POST['responsibilities'], $_POST['perks'], $_POST['duration'], $_POST['expires'], $_POST['skills']
            );
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objJob->getValidationFailures ()));
        }

        $this->ajaxresponse ("success", "success");
    }

}
