<?php
namespace Controller;
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 14:55
 */

require_once("Objects/Model/UserModel.php");

use Model\UserModel;
use Utils\Action;
use Utils\Form;
use Utils\User;

class UserController
{
    private $userModel = null;

    protected $users = array();
    protected $displayValues = array();
    protected $action = null;
    protected $message = "";

    function __construct($action) {
        $this->userModel = new UserModel();
        $this->action = $action;
        $this->databaseOperations();
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        switch ($this->action) {
            case Action::Create:
                if (isset($_POST[Form::SubmitData])) {
                    // Check to see if we have user data to save in the database
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->userModel->insertUser($_POST[User::Username], $_POST[User::Password], $_POST[User::Email], $_POST[User::Role])) {
                            $this->message = "User information saved";
                        }
                    }
                }
                break;
            case Action::Update:
                // Step 1: No information at all, so need to present initial selection of all users
                if (empty($_POST[User::Email])) {
                    // Because displayValues has not been set the user selection screen will be presented
                    $this->users = $this->userModel->retrieveUsers();
                    if (count($this->users) == 0) $this->message = "No users to update";
                }
                // Step 2: User selection form has been submitted
                if (isset($_POST[Form::SubmitSelection])) {
                    $this->displayValues = $this->userModel->retrieveUser($_POST[User::Email]);
                    // Force entry of a new password - otherwise we would hash the hash of the old password
                    $this->displayValues[User::Password] = null;
                }
                // Step 3: If we have all user data then these are the updated values that need to saved in the Users table
                if (isset($_POST[Form::SubmitData])) {
                    $this->checkFormData();
                    if ($this->message == '') {
                        if ($this->userModel->updateUser($_POST[User::Username], $_POST[User::Password], $_POST[User::Email], $_POST[User::Role])) {
                            $this->message = "User information updated";
                            $this->displayValues = array();     // Ensures the user selection form is now presented
                        }
                        // Reset UserView users array to offer a second update
                        $this->users = $this->userModel->retrieveUsers();
                    }
                }
                break;
            case Action::Delete :
                // No information at all, so need to present initial selection of all users
                if (!isset($_POST[User::Email])) {
                    $this->users = $this->userModel->retrieveUsers();
                }
                // Email is the Users primary key hence we have required information for deletion
                if (isset($_POST[User::Email])) {
                    if ($this->displayValues = $this->userModel->deleteUser($_POST[User::Email])) {
                        $this->message = "User deleted";
                    }
                    $this->users = $this->userModel->retrieveUsers();
                }
                break;
        }
    }

    function checkFormData() {
        $this->message = "";
        if ($_POST[User::Username] != '') {
            $this->displayValues[User::Username] = $_POST[User::Username];
        } else {
            $this->message .= "<p> Please enter username </p>";
        }
        if ($_POST[User::Password] != '') {
            $this->displayValues[User::Password] = $_POST[User::Password];
        } else {
            $this->message .= "<p> Please enter password </p>";
        }
        if ($_POST[User::Email] != '') {
            $this->displayValues[User::Email] = $_POST[User::Email];
        } else {
            $this->message .= "<p> Please enter user email </p>";
        }
        if ($_POST[User::Role] != '') {
            $this->displayValues[User::Role] = $_POST[User::Role];
        } else {
            $this->message .= "<p> Please enter user email </p>";
        }
    }
}