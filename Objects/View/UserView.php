<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 12:33
 */

namespace View;

class UserView
{
    private $html = array();
    private $users = array();
    private $displayValues = array();
    private $action;
    private $message;

    function __construct($users, $displayValues, $action, $message) {
        $this->users = $users;
        $this->displayValues = $displayValues;
        $this->action = $action;
        $this->message = $message;
    }

    function display() {
        $this->addTitle();
        $this->html[] = '<form action ="index.php?page=user" method="post">';
        if (($this->action == "create") || ($this->action == "update" &&  count($this->displayValues) > 0)) {
            $this->addField("text", "username", "Username:", $this->displayValues['username']);
            $this->addField("password", "password", "Password:", $this->displayValues['password']);
            $this->addField("email", "email", "Email:", $this->displayValues['email']);
            $this->addRole($this->displayValues['role']);
            if ($this->action = "create") {
                $this->html[] = '<br><br><input type="submit" value="Create User"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Update User"/>';
            }
        } else {
            $this->initialSelection();
            if ($this->action = "update") {
                $this->html[] = '<br><br><input type="submit" value="Select User to Update"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Select User to Delete"/>';
            }
        }
        $this->html[] = '</form>';
    }

    function addTitle() {
        if ($this->action == "create") {
            if ($this->message == "form") {
                $this->html[] = "<h2>Create User</h2>";
            } else {
                $this->html[] = "<h2>User Details Saved</h2>";
                $this->html[] = "<h2>Create User</h2>";
            }
        }
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
        $this->html[] = '<label for="email">Select User to Modify: </label>';
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