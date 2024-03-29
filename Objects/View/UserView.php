<?php
/**
 * Created by Nick Harle
 * Date: 10/03/2019
 * Time: 12:33
 *
 * This object generates the html required to create / update / delete users.
 * Which html form / view is generated based on the object variables inherited from the UserController object
 * The presentation can be changed without any requirement to change the Controller object
 */

namespace View;

require_once("Objects/Controller/UserController.php");

use Controller\UserController;
use Utils\Action;
use Utils\Form;
use Utils\PageName;
use Utils\User;

class UserView extends UserController
{
    private $html = array();

    public function __toString()
    {
        $this->displayHeader();

        // Select Which Form to Display
        if (($this->action == Action::Create) || ($this->action == Action::Update &&  count($this->displayValues) > 0)) {
            $this->displayUserForm();
        } else {
            $this->initialUserSelection();
        }

        // Create String to Display
        return implode("\n", $this->html);
    }

    function displayHeader() {
        // Navigation links
        $navigationLinks = array(   'Create User' => 'index.php?page='. PageName::User .'&action=' . Action::Create,
                                    'Update User' => 'index.php?page='. PageName::User .'&action=' . Action::Update,
                                    'Delete User' => 'index.php?page='. PageName::User .'&action=' . Action::Delete);
        $this->html = array_merge($this->html, $this->formComponents->addNavigationLinks($navigationLinks));
    }

    function displayUserForm() {
        // User Form
        $this->html[] = '<form action ="index.php?page='. PageName::User .'&action=' . $this->action .'" method="post">';
        $this->html[] = '<fieldset>';

        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " User", $this->message));

        // User data: username, password, email, role
        $value = (isset($this->displayValues[User::Username]) ? $this->displayValues[User::Username] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("text", User::Username, "Username:", $value, 'required'));
        $this->html = array_merge($this->html, $this->formComponents->addField("password", User::Password, "New Password:", '', 'required'));
        $value = (isset($this->displayValues[User::Email]) ? $this->displayValues[User::Email] : null);
        $this->html = array_merge($this->html, $this->formComponents->addField("email", User::Email, "Email:", $value, 'required'));
        $this->html = array_merge($this->html, $this->formComponents->addRole(isset($this->displayValues[User::Role]) ? $this->displayValues[User::Role] : null));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, ucfirst($this->action) . " User"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

    function initialUserSelection() {
        // User Selection Form
        $this->html[] = '<form action ="index.php?page='. PageName::User .'&action=' . $this->action .'" method="post">';
        $this->html[] = '<fieldset>';

        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " User", $this->message));

        // User select
        $this->html = array_merge($this->html, $this->formComponents->selectUser('Select User:', $this->users));
        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select User to ".ucfirst($this->action)));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }
}