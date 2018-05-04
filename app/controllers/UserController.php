<?php

use Phalcon\Mvc\View;

/**
 * Description of UserController
 *
 * @author michael.hampton
 */
class UserController extends ControllerBase
{

    public function searchForSuggestedUserAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['system_username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrUsers = (new UserFactory())->getUsers ($_POST['system_username']);

        if ( $arrUsers === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        foreach ($arrUsers as $arrUser) {
            echo '<div id="vpb_user_selected_Kampit267Beras" class="vpb_users_wraper" onclick="vpb_add_new_wall_user_to_group(\'' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '\', \'' . $arrUser->getId () . '\', \'/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg\');">
            
					<div class="vpb_users_wraper_left" style="width:100% !important;">
					
					<div class="vpb_users_wraper_photos">
					<img src="/blab/public/uploads/profile/' . $arrUser->getUsername () . '.jpg" border="0">
					</div>
					
					<div class="vpb_users_wraper_name">
					<span class="vpb_users_wraper_name_left" style="margin-bottom:4px !important;">
					' . $arrUser->getFirstName () . ' ' . $arrUser->getLastName () . '
					</span>
					 
					</div>
					</div>
					</div>';
        }
    }

    /**
     * login
     */
    public function doLoginAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['username']) || empty ($_POST['password']) )
        {
            trigger_error ("Missing username or password params in login", E_USER_WARNING);
            echo "ERROR";
            die;
        }

        if ( empty ($_POST['token']) || $_SESSION['token'] != $_POST['token'] )
        {
            trigger_error ("Possible CSRF attack {$_POST['username']}", E_USER_WARNING);
            die ("ERROR");
        }
        else
        {
            
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $objLogin = new LoginModel();
            $blResult = (new UserSession())->createUserSession ($objLogin, $username, $password, new UserFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            die ("ERROR");
        }

        if ( $blResult === FALSE )
        {
            echo 'ERROR';
            die;
        }

        $key = 'fc4d57ed55a78de1a7b31e711866ef5a2848442349f52cd470008f6d30d47282';
        $key = pack ("H*", $key); // They key is used in binary form
        $objRememberMe = new RememberMe ($key);
        $objRememberMe->remember ($username);
    }

    public function loginAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $username = '';
        $password = '';

        if ( !empty ($_COOKIE['blab_rememberme']) )
        {

            $objUser = json_decode ($_COOKIE['blab_rememberme']);
            $username = $objUser->username;
            $password = $objUser->password;

            $objLogin = new LoginModel();
            $username = $objLogin->decryptCookie ($username);
            $password = $objLogin->decryptCookie ($password);
        }

        $this->view->username = $username;
        $this->view->password = $password;

        if ( empty ($_SESSION['token']) )
        {
            if ( function_exists ('mcrypt_create_iv') )
            {
                $_SESSION['token'] = bin2hex (mcrypt_create_iv (32, MCRYPT_DEV_URANDOM));
            }
            else
            {
                $_SESSION['token'] = bin2hex (openssl_random_pseudo_bytes (32));
            }
        }

        $this->view->token = $_SESSION['token'];

        if ( !empty ($_SESSION['user']['username']) && !empty ($_SESSION['user']['user_id']) )
        {
            header ("Location: /blab/index/index");
            exit;
        }
    }

    /*     * **************8 Forgot Password **************** */

    public function forgotPasswordAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);
    }

    public function sendForgotPasswordAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['email']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objAccountRecovery = new AccountRecovery();

        $blResult = $objAccountRecovery->forgotPassword ($_POST['email']);

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objAccountRecovery->getArrValidationFailures ()));
        }

        $this->ajaxresponse ("success", "SUCCESS");
    }

    public function updateProfileDataAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $objUser = new User ($userId);

        $objUser->loadObject ($_POST);
        $blResult = $objUser->save ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Cant save");
        }


        $this->ajaxresponse ("success", "Success");
    }

    public function uploadProfileAction ()
    {
        $this->view->disable ();

        if ( isset ($_FILES['profilepic']) && !isset ($_FILES['userImage']) )
        {
            $_FILES['userImage'] = $_FILES['profilepic'];
            unset ($_FILES['profilepic']);
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        $target_dir = $this->rootPath . "/blab/public/uploads/profile/";

        $target_file = $target_dir . basename ($_FILES["userImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
        if ( isset ($_POST["submit"]) )
        {
            $check = getimagesize ($_FILES["userImage"]["tmp_name"]);
            if ( $check !== false )
            {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            }
            else
            {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
// Check if file already exists
        if ( file_exists ($target_file) )
        {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
// Check file size
        if ( $_FILES["userImage"]["size"] > 500000 )
        {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
// Allow certain file formats
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
// Check if $uploadOk is set to 0 by an error
        if ( $uploadOk == 0 )
        {
            echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
        }
        else
        {

            $newFile = $target_dir . $_SESSION['user']['username'] . ".jpg";

            if ( move_uploaded_file ($_FILES["userImage"]["tmp_name"], $newFile) )
            {
                echo "The file " . basename ($_FILES["userImage"]["name"]) . " has been uploaded.";
            }
            else
            {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $objUser->uploadUserProfileImage (str_replace ($this->rootPath, "", $newFile));
    }

    public function saveNewUserAction ()
    {
        $this->view->disable ();

        $objUserFactory = new UserFactory();

        if (
                !isset ($_POST['firstname']) ||
                !isset ($_POST['lastname']) ||
                !isset ($_POST['password']) ||
                !isset ($_POST['youremail'])
        )
        {
            $this->ajaxresponse ("error", "missing fields");
        }

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['youremail'];
        $password = $_POST['password'];

        $user_name = strtolower ($firstname) . '.' . strtolower ($lastname);
        $user_name = str_replace (' ', '', $user_name);

        $objUser = $objUserFactory->createUser ($user_name, $password, $firstname, $lastname, $email);

        if ( $objUser === FALSE )
        {
            $this->ajaxresponse ("error", implode ("<br/>", $objUserFactory->getValidationFailures ()));
            return false;
        }

        $_SESSION['user']['user_id'] = $objUser->getId ();
        $_SESSION['user']['username'] = $objUser->getUsername ();

        $this->ajaxresponse ("success", "SUCCESS", ["username" => $objUser->getUsername ()]);
    }

    public function logoutAction ()
    {
        $this->view->disable ();

        unset ($_SESSION['user']);
        header ("Location: /blab/user/login");
        exit;
    }

    public function resetPasswordAction ()
    {
        $this->view->disable ();

        if (
                empty ($_POST['my_identity']) ||
                empty ($_POST['oldpasswd']) ||
                empty ($_POST['newpasswd']) ||
                empty ($_POST['verifynewpasswd']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {

            $objLoginModel = new LoginModel();
            $blResult = $objLoginModel->validatePassword ($_SESSION['user']['username'], $_POST['oldpasswd']);

            if ( $blResult === false )
            {
                $this->ajaxresponse ("error", "Invalid password");
            }

            $objAccountRecovery = new AccountRecovery();
            $blResult2 = $objAccountRecovery->resetPasswordInDb ($_POST['newpasswd'], new UserFactory (), new User ($_SESSION['user']['user_id']));
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult2 === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "The password has been reset successfully");
    }

    public function saveBackgroundImageAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['position']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $position = $_POST['position'];

        $objUser = new User ($_SESSION['user']['user_id']);

        $path = $this->rootPath . "/blab/public/uploads/background_image/";

        if ( !is_dir ($path) )
        {
            mkdir ($path);
        }

        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
        if ( isset ($_POST) && $_SERVER['REQUEST_METHOD'] == "POST" )
        {
            $name = $_FILES['photoimg']['name'];
            $size = $_FILES['photoimg']['size'];

            if ( strlen ($name) )
            {
                $ext = pathinfo ($name, PATHINFO_EXTENSION);


                if ( in_array ($ext, $valid_formats) )
                {
                    if ( $size < (1024 * 1024) )
                    {

                        $actual_image_name = $_SESSION['user']['username'] . "." . $ext;
                        $tmp = $_FILES['photoimg']['tmp_name'];
                        $bgSave = '<div id="uX' . '" class="bgSave wallbutton blackButton">Save Cover</div>';
                        if ( move_uploaded_file ($tmp, $path . $actual_image_name) )
                        {
                            $imagePath = str_replace ($this->rootPath, "", $path);

                            $data = $objUser->uploadUserBackgroundImage ($imagePath . $actual_image_name, $position);
                            if ( $data )
                            {

                                echo $bgSave . '<img src="' . $imagePath . $actual_image_name . '"  id="timelineBGload" class="headerimage ui-corner-all" style="top:' . $position . 'px"/>';
                            }
                        }
                        else
                        {
                            echo "Fail upload folder with read access.";
                        }
                    }
                    else
                    {
                        echo "Image file size max 1 MB";
                    }
                }
                else
                {
                    echo "Invalid file format.";
                }
            }
            else
            {
                echo "Please select image..!";
            }

            exit;
        }
    }

    public function getUserDetailsAction ($userId = null)
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $userId = ($userId !== null) ? $userId : ((!empty ($_POST['userId'])) ? $_POST['userId'] : '');

        if ( empty ($userId) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $objUser = new User ($userId);

        $arrCountries = (new Location())->getCountries ();

        if ( $arrCountries === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $arrLanguages = (new Location())->getLangauges ();

        if ( $arrLanguages === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->view->arrLanguages = $arrLanguages;
        $this->view->objUser = $objUser;
        $this->view->arrCountries = $arrCountries;

        $arrUserSettings = (new UserSettings ($objUser));

        $this->view->arrUserSettings = $arrUserSettings;
    }

    /**
     * creates a compressed zip file
     * @param type $file
     * @param type $destination
     * @param type $overwrite
     * @return boolean
     */
    private function create_zip ($file, $destination, $overwrite = true)
    {
        $zip = new ZipArchive;
        $result = $zip->open ($destination, ZipArchive::CREATE);

        if ( $result === false )
        {
            trigger_error ("Unable to open zip file", E_USER_WARNING);
            return false;
        }

        if ( !file_exists ($file) )
        {
            trigger_error ("User download sheet doesnt exist", E_USER_WARNING);
            return false;
        }

        $content = file_get_contents ($file);
        $r = $zip->addFromString (pathinfo ($file, PATHINFO_BASENAME), $content);

        if ( $r === false )
        {
            trigger_error ("Unable to add file to zip", E_USER_WARNING);
            return false;
        }

        $r = $zip->close ();

        if ( $r === false )
        {
            return false;
        }

        return true;
    }

    public function downloadUserDataAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) || empty ($_SESSION['user']['username']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $userId = $_SESSION['user']['user_id'];

        $filename = $_SESSION['user']['username'] . '.html';
        $path = $this->rootPath . "/blab/public/downloads/";
        $destination = $path . $filename;
        $zipDestination = $this->rootPath . "/blab/public/downloads/" . $_SESSION['user']['username'] . '.zip';

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/blab/user/getUserDetails/' . $userId;


        //fopen opens webpage in Binary
        $handle = fopen ($url, "rb");
// initialize
        $lines_string = "";
// read content line by line
        do {
            $data = fread ($handle, 1024);
            if ( strlen ($data) == 0 )
            {
                break;
            }
            $lines_string.=$data;
        }
        while (true);
//close handle to release resources
        fclose ($handle);
        $myfile = fopen ($destination, "w") or die ("Unable to open file!");
        fwrite ($myfile, $lines_string);
        fclose ($myfile);
        $this->create_zip ($destination, $zipDestination);

        $objEmail = new EmailNotification (new User ($_SESSION['user']['user_id']), "Your Personal Data download", "Please find attached your personal data download");
        $objEmail->mail_attachment ($_SESSION['user']['username'] . '.zip', $this->rootPath . "/blab/public/downloads/");
    }

}
