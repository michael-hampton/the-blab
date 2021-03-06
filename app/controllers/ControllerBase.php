<?php

use Phalcon\Mvc\Controller;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ControllerBase extends \Phalcon\Mvc\Controller
{

    /**
     *
     * @var type 
     */
    protected $arrUserSettings;

    /**
     * The
     * @var type 
     */
    protected $totalFriendsPerLoad = 50;

    /**
     *
     * @var type 
     */
    private $totalChatPerLoad = 20;

    /**
     *
     * @var type 
     */
    protected $defaultErrrorMessage = "Unable to complete request";

    /**
     *
     * @var type 
     */
    protected $postsPerPage = 20;

    /**
     * 
     * @param type $haystack
     * @param type $needle
     * @param type $offset
     * @return boolean
     */
    function strposa ($haystack, $needles = array(), $offset = 0)
    {
        $chr = array();
        foreach ($needles as $needle) {
            $res = strpos ($haystack, $needle, $offset);
            if ( $res !== false )
                $chr[$needle] = $res;
        }
        return !empty ($chr);
    }
    
    /**
     * 
     * @param type $array
     * @return type
     */
    protected function array_flatten ($array)
    {
        $return = array();
        foreach ($array as $key => $value) {
            if ( is_array ($value) )
            {
                $return = array_merge ($return, $this->array_flatten ($value));
            }
            else
            {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     *
     * @var type 
     */
    protected $rootPath;

    public function initialize ()
    {
        $this->view->rootPath = $_SERVER['DOCUMENT_ROOT'];
        $this->rootPath = $_SERVER['DOCUMENT_ROOT'];

        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ( empty ($_SESSION['user']['user_id']) )
        {
            try {
                $key = 'fc4d57ed55a78de1a7b31e711866ef5a2848442349f52cd470008f6d30d47282';
                $key = pack ("H*", $key); // They key is used in binary form

                $objRememberMe = new RememberMe ($key);

                // Check if remember me is present
                if ( $objRememberMe->auth () === true )
                {
                    $_SESSION['user']['username'] = $objRememberMe->getUsername ();
                    $_SESSION['user']['user_id'] = $objRememberMe->getUserId ();
                }
                else
                {



                    if ( strpos ($url, 'user') === false &&
                            strpos ($url, 'resetPassword') === false && strpos ($url, 'terms') === false )
                    {
                        header ("Location: /blab/user/login");
                        exit;
                    }
                }
            } catch (Exception $ex) {
                trigger_error ($ex->getMessage (), E_USER_WARNING);
            }
        }

        $this->view->totalChatPerLoad = $this->totalChatPerLoad;
        $this->view->totalFriendsPerLoad = $this->totalFriendsPerLoad;
        $this->view->startPage = 0;
        $this->view->totalPerLoad = 4;
        $this->view->totalComments = 2;

        $arrGroupCategories = (new GroupCategoryFactory())->getAllCategories ();

        if ( $arrGroupCategories === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }
        
        $this->view->arrGroupCategories = $arrGroupCategories;
    }

    /**
     * 
     * @param User $objUser
     */
    public function getSettingsForUser (User $objUser)
    {
        $arrResults = (new UserSettings())->getUserSettings ();

        if ( $arrResults === false )
        {
            
        }

        $this->arrUserSettings = $arrResults;
    }

    /**
     * 
     * @param type $type
     * @param type $message
     * @param type $data
     */
    public function ajaxresponse ($type, $message, $data = [])
    {
        if ( $type == "success" || $type == "sucess" )
        {
            $success = 1;
        }
        elseif ( $type == "error" )
        {
            $success = 0;
        }

        echo json_encode (array("sucess" => $success, "message" => $message, "data" => $data));
        exit;
    }

    protected function validateUpload ()
    {
        
    }

   

    /**
     * 
     * @param type $uploadType
     * @param type $title
     * @param type $arrImages
     * @return type
     */
    protected function singleUploadAction ($uploadType, $title, $arrImages)
    {

        switch ($uploadType) {
            case "page":
                $target_dir = $this->rootPath . "/blab/public/uploads/pages/";
                break;
            case "group":
                $target_dir = $this->rootPath . "/blab/public/uploads/groups/";
                break;
            case "event":
                $target_dir = $this->rootPath . "/blab/public/uploads/events/";
                break;
            default:
                die ("Unknown image type");
                break;
        }

        $target_file = $target_dir . basename ($arrImages["userImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
        if ( isset ($arrImages["userImage"]) )
        {
            $check = getimagesize ($arrImages["userImage"]["tmp_name"]);
            if ( $check !== false )
            {
                $uploadOk = 1;
            }
            else
            {
                $uploadOk = 0;
            }
        }
// Check if file already exists
        if ( file_exists ($target_file) )
        {
            $uploadOk = 0;
        }
// Check file size
        if ( $arrImages["userImage"]["size"] > 500000 )
        {
            $uploadOk = 0;
        }
// Allow certain file formats
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            $uploadOk = 0;
        }
// Check if $uploadOk is set to 0 by an error
        if ( $uploadOk == 0 )
        {
            return false;
// if everything is ok, try to upload file
        }
        else
        {

            $newFile = $target_dir . strtolower (str_replace (" ", "_", $title)) . "." . $imageFileType;

            if ( file_exists ($newFile) )
            {
                unlink ($newFile);
            }

            if ( move_uploaded_file ($arrImages["userImage"]["tmp_name"], $newFile) )
            {
                
            }
            else
            {
                return false;
            }
        }

        $imageLocation = str_replace ($this->rootPath, "", $newFile);

        return $imageLocation;
    }
}
