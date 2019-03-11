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
    private $users = array();
    private $displayValues = array();
    private $userModel = null;
    private $action;
    private $message;

    function __construct($action) {
        $this->userModel = new UserModel();
        $this->action = $action;
        $this->message = "";
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        switch ($this->action) {
            case "create":
                $this->users = array(); // Ensures pre-selection box does not appear
                $this->displayValues = array('username' => null, 'email' => null, 'role' => null); // ensures no presets in user form
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['role'])) {
                    // Save user details in the database
                    if ($this->userModel->insertUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                        $this->message = "User information saved";
                    }
                }
                break;
            case "update":
                if (!isset($_POST['email'])) {
                    $this->users = $this->userModel->retrieveUsers();
                } else {
                    $this->users = array();
                    $this->displayValues = $this->userModel->retrieveUser($_POST['email']);
                    $_SESSION['action2'] = "form";
                    $this->message = "form";
                }


                break;
        }
    }
}