<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 14:55
 */

namespace Controller;


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
        $this->action1 = $_GET['action1'];
        $this->action2 = $_GET['action2'];
    }

    function update() {
        switch ($this->action1) {
            case "create":
                $this->users = array();
                $this->displayValues = array();
                if ($this->action2 == "save") {
                    $this->userModel->insertUser($_POST['email'], $_POST['username'], $_POST['password'], $_POST['role']);
                }
                break;
            case "update":

                break;
            case "delete":

                break;
        }

    }


}