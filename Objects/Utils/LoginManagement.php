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

    function validLogin($email, $password) {
        return $this->userModel->checkUserCredentials($email, $password);
        //return $this->userModel->retrieveUser($email);
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
        $this->html[] = "<h2>User Login</h2>";
        $this->html[] = "<p>" . $message ."</p>";

        $this->html[] = '<form action ="index.php" method="post">';

        // User data: username, password, email, role
        $this->html = array_merge($this->html, $this->formComponents->addField("text", User::Email, "User Email:", '', ''));
        $this->html =  array_merge($this->html, $this->formComponents->addField("password", User::Password, "Password:", '', ''));

        // Submit button
        $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Login"/>';
        $this->html[] = '</form>';
        return implode("\n", $this->html);
    }
}