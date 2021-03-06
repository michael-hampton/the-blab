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

        $objPage = isset ($_GET['pageId']) ? new Page ($_GET['pageId']) : null;

        $arrSalaryRange = $objJobFactory->getSalaryRange ($objPage);

        if ( !empty ($arrSalaryRange) )
        {
            $this->view->salary_min = $arrSalaryRange[0]['min'];
            $this->view->salary_max = $arrSalaryRange[0]['max'];
        }

        $arrLocations = $objJobFactory->getLocations ($objPage);

        $this->view->arrLocations = $arrLocations;
        $this->view->objPage = $objPage;
    }

    /* Search & filter */

    public function searchJobsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( !isset ($_POST['salary_min']) || !isset ($_POST['salary_max']) || !isset ($_POST['duration']) || !isset ($_POST['pageId']) )
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
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get jobs"]
                            ]
            );
        }

        $this->view->arrJobs = $arrJobs;
    }

    /**
     *  View single job- full screen
     * @param type $id
     */
    public function viewAction ($id)
    {

        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        try {
            $objJob = new Job ($id);
            $objUser = new User ($_SESSION['user']['user_id']);
            $this->view->blApplied = $objJob->checkUserHasAppliedForJob ($objUser);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $ex->getMessage ()]
                            ]
            );
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
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => $ex->getMessage ()]
                            ]
            );
        }

        $this->view->partial ("job/addJob", ["objJob" => $objJob, "addType" => "edit", "pageId" => $objJob->getPageId ()]);
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

    /**
     * 
     * @param type $pageId
     */
    public function addJobAction ($pageId)
    {
        $this->view->addType = "add";

        if ( trim ($pageId) === "" )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid page id"]
                            ]
            );
        }

        $this->view->pageId = $pageId;
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
            $objPage = new Page ($_POST['pageId']);
            $objUser = new User ($_SESSION['user']['user_id']);
            $objJob = $objJobFactory->createJob (
                    $objUser, $objPage, $_POST['title'], $_POST['description'], $_POST['salary_min'], $_POST['salary_max'], $_POST['location'], $_POST['responsibilities'], $_POST['perks'], $_POST['duration'], $_POST['expires'], $_POST['skills']
            );

            $objPost = (
                    new PagePost (
                    $objPage, new PostActionFactory (), new UploadFactory (), new CommentFactory (), new ReviewFactory (), new TagUserFactory (), new CommentReplyFactory ()
                    )
                    )->createComment ($_POST['title'] . "<br/>" . $_POST['description'] . "<a href='/blab/job/view/{$objJob->getId ()}'>View</a>", $objUser, new \JCrowe\BadWordFilter\BadWordFilter ());

            if ( $objPost === false )
            {
                $this->ajaxresponse ("error", "Unable to create post on news feed");
            }
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

    public function saveApplicationAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        if ( empty ($_POST['applicationText']) || empty ($_POST['applicationId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $objJob = new Job ($_POST['applicationId']);
            $objJobApplication = new JobApplicationFactory();
            $objPage = (new PageFactory())->getPageById ($objJob->getPageId ());
            $objOwner = new User ($objPage->getUserId ());
            $objBadWordFilter = new \JCrowe\BadWordFilter\BadWordFilter();
            $objNotificationFactory = new NotificationFactory();
            $objEmailFactory = new EmailNotificationFactory();
            $objMessageFactory = new MessageFactory();
            $objPageInboxFactory = new PageInboxFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        // save application
        $blResponse = $objJobApplication->sendJobApplication ($objJob, $objUser, $_POST['applicationText']);

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        // send email
        $blEmailResponse = $objEmailFactory->createNotification ($objOwner, "Someone just responded to your job post {$objJob->getTitle ()}", "{$objUser->getUsername ()} just applied for your job {$objJob->getTitle ()}");

        if ( $blEmailResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        // send to messenger
        $objMessage = $objMessageFactory->sendMessage ("{$objUser->getUsername ()} just applied for your job {$objJob->getTitle ()}", $objBadWordFilter, $objEmailFactory, $objOwner, $objUser, "", "page");

        if ( $objMessage === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $blInboxResponse = $objPageInboxFactory->createMessage ($objPage, $objUser, $objMessage, "IN");

        if ( $blInboxResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        //create notification
        $objNotificationFactory->createNotification ($objOwner, "{$objUser->getUsername ()} just applied for your job {$objJob->getTitle ()}");

        $this->ajaxresponse ("success", "success");
    }

    /**
     * 
     * @param type $jobId
     */
    public function getApplicationsForJobAction ($jobId)
    {


        try {
            $objJob = new Job ($jobId);
            $arrApplications = (new JobApplicationFactory())->getJobApplications ($objJob);
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrApplications === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get applications"]
                            ]
            );
        }

        $this->view->arrApplications = $arrApplications;
    }

    public function updateApplicationStatusAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['jobId']) || empty ($_POST['status']) || empty ($_POST['applicationId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objJob = new Job ($_POST['jobId']);
            $objJobApplication = new JobApplication ($_POST['applicationId']);
            $objUser = new User ($objJobApplication->getUser ());
            $objNotificationFactory = new NotificationFactory();
            $objEmailFactory = new EmailNotificationFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $status = trim ($_POST['status']) === "reject" ? 2 : 1;
        $objJobApplication->setStatus ($status);
        $blResult = $objJobApplication->updateJobApplicationStatus ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $label = trim ($_POST['status']) === "reject" ? "rejected" : "accepted";
        $blNotificationResponse = $objNotificationFactory->createNotification ($objUser, "Your application for the job {$objJob->getTitle ()} has been {$label}");

        if ( $blNotificationResponse === false )
        {
            trigger_error ("Unable to create notification", E_USER_WARNING);
        }

        $blEmailNotificationResponse = $objEmailFactory->createNotification ($objUser, "Job application {$label}", "Your application for the job {$objJob->getTitle ()} has been {$label}");

        if ( $blEmailNotificationResponse === false )
        {
            trigger_error ("Unable to send email", E_USER_WARNING);
        }

        $this->ajaxresponse ("success", "success");
    }

}
