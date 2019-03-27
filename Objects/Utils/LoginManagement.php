<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 26/03/2019
 * Time: 19:08
 */

namespace Utils;

require_once("Objects/Model/UserModel.php");

use Model\UserModel;

class LoginManagement
{
    private $userModel = null;
    private $formComponents = null;
    private $html = array();

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->formComponents = new FormComponents();
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

    function displayLoginForm($message) {
        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header("User Login", $message));
        //$this->html[] = "<h2>User Login</h2>";
        //$this->html[] = "<p>" . $message ."</p>";

        $this->html[] = '<form action ="index.php" method="post">';

        // Login: email, password
        $this->html = array_merge($this->html, $this->formComponents->addField("text", User::Email, "Enter Email:", '', 'required'));
        $this->html =  array_merge($this->html, $this->formComponents->addField("password", User::Password, "Password:", '', 'required'));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, "Login"));
        //$this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Login"/>';
        //$this->html[] = '</form>';
        return implode("\n", $this->html);
    }
}