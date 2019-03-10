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

    function __construct($users, $displayValues) {
        $this->users = $users;
        $this->displayValues = $displayValues;
    }

    function display() {

        if (count($this->users) == 0) {
            $this->html[] = '<form action ="index.php?page=user&action=update" method="post">';
            $this->addField("text", "username", "Username:", $this->displayValues['username']);
            $this->addField("password", "password", "Password:", null);
            $this->addField("email", "email", "Email:", $this->displayValues['email']);
            $this->addRole($this->displayValues['role']);
        } else {
            $this->html[] = '<form action ="index.php?page=user&action=selected" method="post">';
            $this->initialSelection();
        }
        $this->html[] = '<br><br><input type="submit" value="Submit"/>';
        $this->html[] = '</form>';
    }

    function addField($type, $name, $text, $value) {
        $this->html[] = '<label for="' . $name . '">' . $text . '</label>';
        //$this->html[] = '<input type="'. $type . '" name="' . $name . '" id="' . $name . '"/><br>';
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
        $this->html[] = '<label for="user">Select User to Modify: </label>';
        $this->html[] = '<select name = "user" id="user">';
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