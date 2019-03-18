<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 18/03/2019
 * Time: 19:35
 */

namespace View;


class TaskView
{
    private $html = array();

    // Values to be defined in the controller
    private $nonClientUsers = array();
    private $displayValues = array();
    private $action = null;
    private $message;
    private $taskID;
    private $projects;

    function display() {
        // Title and message
        $this->html[] = "<h2>" . ucfirst($this->action) . " User</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        $this->html[] = "<p><a href='index.php?page=task&action=create'>Create Task</a></p>";
        $this->html[] = "<p><a href='index.php?page=task&action=update'>Update Task</a></p>";
        $this->html[] = "<p><a href='index.php?page=task&action=delete'>Delete Task</a></p>";

        // Task Entry Form
        $this->html[] = '<form action ="index.php?page=task&action=' . $this->action .'" method="post">';
        if (($this->action == "create") || ($this->action == "update" &&  count($this->displayValues) > 0)) {

            // Task data: taskID, taskName, startDate, endDate, percent, taskNo, notes, projectID, email
            $this->addField("text", "taskName", "Task Name:", (isset($this->displayValues['taskName']) ? $this->displayValues['taskName'] : null ));
            $this->addField("date", "startDate", "Start Date:", (isset($this->displayValues['startDate']) ? $this->displayValues['startDate'] : null ));
            $this->addField("date", "endDate", "End Date:", (isset($this->displayValues['endDate']) ? $this->displayValues['endDate'] : null ));
            $this->addField("number", "percent", "Percent Complete:", (isset($this->displayValues['percent']) ? $this->displayValues['percent'] : null));
            $this->addField("number", "taskNo", "Task no:", (isset($this->displayValues['taskNo']) ? $this->displayValues['taskNo'] : null));
            $this->addTextArea( "notes", "Task Notes:", (isset($this->displayValues['notes']) ? $this->displayValues['notes'] : null));
            $this->addUserInput("Owner", (isset($this->displayValues['taskOwner']) ? $this->displayValues['taskOwner'] : null), $this->nonClientUsers);

            // Carry TaskID across
            $this->html[] = '<input type="hidden" name="taskID" value="' . $this->taskID . '"/>';

            // Submit button
            if ($this->action == "create") {
                $this->html[] = '<br><br><input type="submit" name="submit" value="Create Task"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" name="submit" value="Update Task"/>';
            }
        } else {
            // Task selection drop down box
            $this->initialSelection();

            // Submit button
            if ($this->action == "update") {
                // Disable submit button if no users to update
                $this->html[] = '<br><br><input type="submit" name = "submit" value="Select Task to Update"' . (count($this->users) > 0 ? '' : 'disabled') . '/>';
            } else {
                $this->html[] = '<br><br><input type="submit" name = "submit" value="Select Task to Delete"/>';
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

    function addUserInput($roleDescription, $value, $userList) {
        $this->html[] = '<label for="user' . $roleDescription .'">Task ' . ucfirst($roleDescription) . ': </label>';
        $this->html[] = '<select name = "user' . $roleDescription .'" id="user' . $roleDescription .'">';

        for ($i=0; $i<count($userList); $i++) {
            $this->html[] = '<option value = "' . $userList[$i]['email'] . '"' . ($value == $userList[$i]['email'] ? " selected " : "") . '>' . $userList[$i]['username'] . '</option>';
        }
        $this->html[] = '</select><br>';
    }

    function initialSelection() {
        $this->html[] = '<label for="projectID">Select Project to ' . ucfirst($this->action) . ': </label>';
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