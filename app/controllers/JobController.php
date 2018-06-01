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
//        $id = $_POST['id'];
//        if ( $this->job_model->delete ($id) )
//            $r = true;
//        else
//            $r = false;
//        echo json_encode ($r);
    }
    
    public function addJobAction()
    {
        $this->view->addType = "add";
    }
    
    public function saveJobAction()
    {
        $this->view->disable();
        
//        [title] => test new job
//    [company] => test
//    [location] => 
//    [description] => test description
//    [responsibilities] => <ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li>responsibilities</li></ul></li></ul></li></ul></li></ul>
//    [skills] => <ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li>skills</li></ul></li></ul></li></ul></li></ul>
//    [perks] => <ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li><ul class='bullets'><li>benefits</li></ul></li></ul></li></ul></li></ul>
//    [salary_min] => 500
//    [salary_max] => 2000
//    [duration] => Full time
//    [expires] => 06/28/2018
//    [form_type] => submit
//    [form] => 1
        
        echo '<pre>';
        print_r($_POST);
        die;
    }

}
