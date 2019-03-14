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

class UserView extends UserController
{
    private $html = array();

/*    private $users = array();
    private $displayValues = array();
    private $action;
    private $message;

    function __construct($users, $displayValues, $action, $message) {
        $this->users = $users;
        $this->displayValues = $displayValues;
        $this->action = $action;
        $this->message = $message;
    } */

    function display() {
        // Title and message
        $this->html[] = "<h2>" . ucfirst($this->action) . " User</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        $this->html[] = "<p><a href='index.php?page=user&action=create'>Create User</a></p>";
        $this->html[] = "<p><a href='index.php?page=user&action=update'>Update User</a></p>";
        $this->html[] = "<p><a href='index.php?page=user&action=delete'>Delete User</a></p>";

        // User Form
        $this->html[] = '<form action ="index.php?page=user&action=' . $this->action .'" method="post">';
        if (($this->action == "create") || ($this->action == "update" &&  count($this->displayValues) > 0)) {

            // User data: username, password, email, role
            $this->addField("text", "username", "Username:", (isset($this->displayValues['username']) ? $this->displayValues['username'] : null ));
            $this->addField("password", "password", "Password:", (isset($this->displayValues['password']) ? $this->displayValues['password'] : null));
            $this->addField("email", "email", "Email:", (isset($this->displayValues['email']) ? $this->displayValues['email'] : null));
            $this->addRole(isset($this->displayValues['role']) ? $this->displayValues['role'] : null);

            // Submit button
            if ($this->action == "create") {
                $this->html[] = '<br><br><input type="submit" value="Create User"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Update User"/>';
            }
        } else {
            // User selection drop down box
            $this->initialSelection();

            // Submit button
            if ($this->action == "update") {
                // Disable submit button if no users to update
                $this->html[] = '<br><br><input type="submit" value="Select User to Update"' . (count($this->users) > 0 ? '' : 'disabled') . '/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Select User to Delete"/>';
            }
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
        $this->html[] = '<label for="role">Project Role: </label>';
        $this->html[] = '<select name = "role" id="role">';
        $this->html[] = '<option value = "lead"' . ($value == "lead" ? " selected " : "") . '>Project Lead</option>';
        $this->html[] = '<option value = "member"' . ($value == "member" ? " selected " : "") . '>Project Member</option>';
        $this->html[] = '<option value = "client"' . ($value == "client" ? " selected " : "") . '>Project Client</option>';
        $this->html[] = '<option value = "admin"' . ($value == "admin" ? " selected " : "") . '>Admin</option>';
        $this->html[] = '</select>';
    }

    function initialSelection() {
        $this->html[] = '<label for="email">Select User to ' . ucfirst($this->action) . ': </label>';
        $select = '<select name = "email" id="email"';
        if (count($this->users) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        //$this->html[] = '<select name = "email" id="email">';
        $this->html[] = $select;
        foreach ($this->users as $user) {
            $this->html[] = '<option value = "'. $user['email'] .'">Username: ' . $user['username'] . '  Role: ' . $user['role'] . '  Email: ' . $user['email'] .'</option>';
        }
        $this->html[] = '</select>';
    }

    public function __toString()
    {
        $this->display();
        return implode("\n", $this->html);
    }
}