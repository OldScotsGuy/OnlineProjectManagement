<?php
namespace Controller;
session_start();
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
    private $action1;
    private $action2;

    function __construct() {
        $this->userModel = new UserModel();
        if (isset($_SESSION['action1']))  {
            $this->action1 = $_SESSION['action1'];
        } else {
            $this->action1 = "update";
        }
        if (isset($_SESSION['action2'])) {
            $this->action2 = $_SESSION['action2'];
        } else {
            $this->action2 = "select";
        }
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        switch ($this->action1) {
            case "create":
                $this->users = array(); // Ensures pre-selection box does not appear
                $this->displayValues = array('username' => null, 'email' => null, 'role' => null); // ensures no presets in user form
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['role'])) {
                    // Save user details in the database
                    if ($this->userModel->insertUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                        $_SESSION['action2'] = "save";
                        $this->action2 = "save";
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
                    $this->action2 = "form";
                }


                break;
        }
    }
}