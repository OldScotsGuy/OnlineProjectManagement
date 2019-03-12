<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/03/2019
 * Time: 20:30
 */

namespace View;


class ProjectView
{
    private $html = array();
    private $projects = array();
    private $users = array();
    private $client = null;
    private $displayValues = array();
    private $action;
    private $message;

    function __construct($projects, $users, $client, $displayValues, $action, $message) {
        $this->projects = $projects;
        $this->users = $users;
        $this->client = $client;
        $this->displayValues = $displayValues;
        $this->action = $action;
        $this->message = $message;
    }

    function display() {
        $this->html[] = "<h2>" . ucfirst($this->action) . " Project</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";
        $this->html[] = "<p><a href='index.php?page=project&action=create'>Create User</a></p>";
        $this->html[] = "<p><a href='index.php?page=project&action=update'>Update User</a></p>";
        $this->html[] = "<p><a href='index.php?page=project&action=delete'>Delete User</a></p>";
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';
        if (($this->action == "create") || ($this->action == "update" &&  count($this->displayValues) > 0)) {
            $this->addField("text", "title", "Title:", (isset($this->displayValues['title']) ? $this->displayValues['title'] : null ));
            $this->addTextArea( "description", "Description:", (isset($this->displayValues['description']) ? $this->displayValues['description'] : null));
            $this->addLead(isset($this->displayValues['email']) ? $this->displayValues['email'] : null);
            // TODO add client
            if ($this->action == "create") {
                $this->html[] = '<br><br><input type="submit" value="Create Project"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Update Project"/>';
            }
        } else {
            $this->initialSelection();
            if ($this->action == "update") {
                $this->html[] = '<br><br><input type="submit" value="Select User to Update"/>';
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

    function addTextArea($name, $text, $value) {
        $this->html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="textarea" rows="5" cols="50" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'"/><br>';
        } else {
            $input .= '/><br>';
        }
        $this->html[] = $input;
    }

    function addLead($value) {
        $this->html[] = '<label for="lead">Project Lead: </label>';
        $this->html[] = '<select name = "lead" id="lead">';

        for ($i=0; $i<count($this->users); $i++) {
            $this->html[] = '<option value = "' . $this->users[$i]['email'] . '"' . ($value == $this->users[$i]['email'] ? " selected " : "") . '>' . $this->users[$i]['username'] . '</option>';
        }
        $this->html[] = '</select>';
    }

    function initialSelection() {
        $this->html[] = '<label for="email">Select User to ' . ucfirst($this->action) . ': </label>';
        $select = '<select name = "email" id="email"';
        if (count($this->projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        //$this->html[] = '<select name = "email" id="email">';
        $this->html[] = $select;
        foreach ($this->projects as $user) {
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