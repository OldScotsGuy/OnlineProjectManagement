<?php
/**
 * Created by PhpStorm.
 * User: P1.NickHarle
 * Date: 28/03/2019
 * Time: 14:48
 */

namespace View;

use Controller\LoginController;
use Utils\User;

require_once("Objects/Controller/LoginController.php");

class LoginView extends LoginController
{
    private $html = array();

    function __toString()
    {
        $this->displayLoginForm();
        return implode("\n", $this->html);
    }

    function displayLoginForm() {
        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header("User Login", $this->message));

        $this->html[] = '<form action ="index.php" method="post">';

        // Login: email, password
        $this->html = array_merge($this->html, $this->formComponents->addField("text", User::Email, "Enter Email:", '', 'required'));
        $this->html =  array_merge($this->html, $this->formComponents->addField("password", User::Password, "Password:", '', 'required'));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, "Login"));
    }

}