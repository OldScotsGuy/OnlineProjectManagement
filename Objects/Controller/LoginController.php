<?php
/**
 * Created by PhpStorm.
 * User: P1.NickHarle
 * Date: 28/03/2019
 * Time: 14:47
 */

namespace Controller;

require_once("Objects/Model/UserModel.php");

use Model\UserModel;
use Utils\FormComponents;
use Utils\Form;
use Utils\User;

class LoginController
{
    private $userModel = null;
    protected $formComponents = null;
    protected $message = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->formComponents = new FormComponents();
        $this->assessLoginAttempt();
    }

    function assessLoginAttempt() {
        if (isset($_POST[Form::SubmitData])) {

            // Form submitted so read and check form data
            $email = $_POST[User::Email];
            $password = $_POST[User::Password];
            $this->message = $this->checkLoginData($_POST[User::Email], $password);

            if ($this->message == '') {
                // Form filled out so now check login data against the database
                $this->attemptLogin($email, $password);
                if ($this->userLoggedIn()) {
                    header("Location: index.php");
                } else {
                    // Login data did not match that stored in the database
                    $this->message = '<p>User Email and Password do not match</p>';
                }
            }
        }
    }


    function userLoggedIn() {
        return (isset($_SESSION[User::Username]) && isset($_SESSION[User::Email])) && isset($_SESSION[User::Role]);
    }

    function attemptLogin($email, $password) {

        $result = $this->userModel->checkUserCredentials($email, $password);
        if (isset($result[User::Username])) {
            // Match on user email and passwords so set session variables
            $_SESSION[User::Username] = $result[User::Username];
            $_SESSION[User::Email] = $result[User::Email];
            $_SESSION[User::Role] = $result[User::Role];
        }
    }

    function checkLoginData($email, $password) {
        $message = '';
        // Check user email
        if ($email == '') {
            $message .= "<p>Please enter user email</p>";
        }
        // Check user password
        if ($password == '') {
            $message .= "<p>Please enter user password</p>";
        }
        return $message;
    }

    function logout() {
        session_destroy();
    }


}