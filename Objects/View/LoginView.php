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
use Utils\Form;

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

        $this->html[] = '<form action ="index.php" method="post">';
        $this->html[] = '<fieldset>';

        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header("User Login", $this->message));

        // Login: email, password
        $this->html = array_merge($this->html, $this->formComponents->addField("text", User::Email, "Email:", '', 'required'));
        $this->html =  array_merge($this->html, $this->formComponents->addField("password", User::Password, "Password:", '', 'required'));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, "Login"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

}