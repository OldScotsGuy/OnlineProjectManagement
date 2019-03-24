<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 12:33
 */

namespace View;

require_once("Objects/Controller/UserController.php");

use Controller\UserController;
use Utils\Action;
use Utils\Form;
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
        // Title and message
        $this->html[] = "<h2>" . ucfirst($this->action) . " User</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        $this->html[] = '<p><a href="index.php?page=user&action=' . Action::Create . '">Create User</a></p>';
        $this->html[] = '<p><a href="index.php?page=user&action=' . Action::Update . '">Update User</a></p>';
        $this->html[] = '<p><a href="index.php?page=user&action=' . Action::Delete . '">Delete User</a></p>';
    }

    function displayUserForm() {
        // User Form
        $this->html[] = '<form action ="index.php?page=user&action=' . $this->action .'" method="post">';

        // User data: username, password, email, role
        $this->addField("text", User::Username, "Username:", (isset($this->displayValues[User::Username]) ? $this->displayValues[User::Username] : null ));
        $this->addField("password", User::Password, "New Password:", (isset($this->displayValues[User::Password]) ? $this->displayValues[User::Password] : null));
        $this->addField("email", User::Email, "Email:", (isset($this->displayValues[User::Email]) ? $this->displayValues[User::Email] : null));
        $this->addRole(isset($this->displayValues[User::Role]) ? $this->displayValues[User::Role] : null);

        // Submit button
        if ($this->action == Action::Create) {
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Create User"/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Update User"/>';
        }
        $this->html[] = '</form>';
    }

    function addField($type, $name, $text, $value) {
        $this->html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="'. $type . '" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'"/><br>';
        } else {
            $input .= '/><br>';
        }
        $this->html[] = $input;
    }

    function addRole($value) {
        $this->html[] = '<label for="' . User::Role . '">Project Role: </label>';
        $this->html[] = '<select name = "' . User::Role . '" id="' . User::Role . '">';
        $this->html[] = '<option value = "' . User::RoleLead . '" ' . ($value == User::RoleLead ? "selected " : "") . '>Project Lead</option>';
        $this->html[] = '<option value = "' . User::RoleMember . '" ' . ($value == User::RoleMember ? "selected " : "") . '>Project Member</option>';
        $this->html[] = '<option value = "' . User::RoleClient . '" ' . ($value == User::RoleClient ? "selected " : "") . '>Project Client</option>';
        $this->html[] = '<option value = "' . User::RoleAdmin . '" ' . ($value == User::RoleAdmin ? "selected " : "") . '>Admin</option>';
        $this->html[] = '</select>';
    }

    function initialUserSelection() {
        // User Selection Form
        $this->html[] = '<form action ="index.php?page=user&action=' . $this->action .'" method="post">';

        $this->html[] = '<label for="' . User::Email . '">Select User to ' . ucfirst($this->action) . ': </label>';
        $select = '<select name = "' . User::Email . '" id="' . User::Email . '"';
        if (count($this->users) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $this->html[] = $select;
        foreach ($this->users as $user) {
            $this->html[] = '<option value = "'. $user[User::Email] .'">Username: ' . $user[User::Username] . '  Role: ' . $user[User::Role] . '  Email: ' . $user[User::Email] .'</option>';
        }
        $this->html[] = '</select>';

        // Submit button
        if ($this->action == Action::Update) {
            // Disable submit button if no users to update
            $this->html[] = '<br><br><input type="submit" name = "' . Form::SubmitSelection . '" value="Select User to Update"' . (count($this->users) > 0 ? '' : 'disabled') . '/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name = "' . Form::SubmitSelection . '" value="Select User to Delete"/>';
        }
        $this->html[] = '</form>';
    }
}