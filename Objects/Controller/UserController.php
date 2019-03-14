<?php
namespace Controller;
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 14:55
 */

require("Objects/Model/UserModel.php");

use Model\UserModel;

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
            case "create":
                // Check to see if we have user data to save in the database
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['role'])) {
                    if ($this->userModel->insertUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                        $this->message = "User information saved";
                    }
                }
                break;
            case "update":
                // Step 1: No information at all, so need to present initial selection of all users
                if (!isset($_POST['email'])) {
                    $this->users = $this->userModel->retrieveUsers();
                    if (count($this->users) == 0) $this->message = "No users to update";
                }
                // Step 2: Email is the Users primary key, hence if no other data we only have initial user selection
                if (isset($_POST['email']) && !isset($_POST['username'])) {
                    $this->users = array();
                    $this->displayValues = $this->userModel->retrieveUser($_POST['email']);
                }
                // Step 3: If we have all user data then these are the updated values that need to saved in the Users table
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['role'])) {
                    // Attempt to save user data
                    if ($this->userModel->updateUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                        $this->message = "User information updated";
                    }
                    // Reset UserView users array to offer a second update
                    $this->users = $this->userModel->retrieveUsers();
                }
                break;
            case "delete" :
                // No information at all, so need to present initial selection of all users
                if (!isset($_POST['email'])) {
                    $this->users = $this->userModel->retrieveUsers();
                }
                // Email is the Users primary key, hence if no other data we have the user for deletion
                if (isset($_POST['email']) && !isset($_POST['username'])) {
                    if ($this->displayValues = $this->userModel->deleteUser($_POST['email'])) {
                        $this->message = "User deleted";
                    }
                    $this->users = $this->userModel->retrieveUsers();
                }
                break;
        }
    }
}