<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 18/03/2019
 * Time: 19:35
 */

namespace View;

require_once("Objects/Controller/TaskController.php");

use Controller\TaskController;
use Utils\Action;
use Utils\Project;
use Utils\Task;
use Utils\User;

class TaskView extends TaskController
{
    private $html = array();

    function display() {
        if (($this->action == Action::Create) || ($this->action == Action::Update &&  count($this->displayValues) > 0)) {
            // Title and message
            $this->html[] = "<h2>" . ucfirst($this->action) . " Task</h2>";
            $this->html[] = "<p>" . $this->message ."</p>";

            // Task Entry Form
            $this->html[] = '<form action ="index.php?page=task&action=' . $this->action .'" method="post">';

            // Task data: taskID, taskName, startDate, endDate, percent, taskNo, notes, projectID, email
            $this->projectSelection();
            $this->addField("text", Task::Name, "Task Name:", (isset($this->displayValues[Task::Name]) ? $this->displayValues[Task::Name] : null ));
            $this->addField("date", Task::StartDate, "Start Date:", (isset($this->displayValues[Task::StartDate]) ? $this->displayValues[Task::StartDate] : null ));
            $this->addField("date", Task::EndDate, "End Date:", (isset($this->displayValues[Task::EndDate]) ? $this->displayValues[Task::EndDate] : null ));
            $this->addField("number", Task::Percent, "Percent Complete:", (isset($this->displayValues[Task::Percent]) ? $this->displayValues[Task::Percent] : null));
            $this->addField("number", "taskNo", "Task no:", (isset($this->displayValues[Task::No]) ? $this->displayValues[Task::No] : null));
            $this->addTextArea( "notes", "Task Notes:", (isset($this->displayValues[Task::Notes]) ? $this->displayValues[Task::Notes] : null));
            $this->addUserInput("Owner", (isset($this->displayValues['taskOwner']) ? $this->displayValues['taskOwner'] : null), $this->nonClientUsers);
            $this->html[] = '<input type="hidden" name="taskID" value="' . $this->taskID . '"/>';                   // Carry TaskID across

            // Submit button
            if ($this->action == Action::Create) {
                $this->html[] = '<br><br><input type="submit" name="submit" value="Create Task"/>';
            } else {
                $this->html[] = '<br><br><input type="submit" name="submit" value="Update Task"/>';
                //$this->html[] = '<br><br><a href = "index.php?page=task&action=delete&taskID=' . $this->taskID . '">Delete Task</a>';
            }
        $this->html[] = '</form>';
        } else {
            // Handle task delete

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
        $this->html[] = '<label for="task' . $roleDescription .'">Task ' . ucfirst($roleDescription) . ': </label>';
        $this->html[] = '<select name = "task' . $roleDescription .'" id="task' . $roleDescription .'">';

        for ($i=0; $i<count($userList); $i++) {
            $this->html[] = '<option value = "' . $userList[$i][User::Email] . '"' . ($value == $userList[$i][User::Email] ? " selected " : "") . '>' . $userList[$i][User::Username] . '</option>';
        }
        $this->html[] = '</select><br>';
    }

    function projectSelection() {
        $this->html[] = '<label for="' . Project::ID .'">Select Project Task Belongs to: </label>';
        $select = '<select name = "' . Project::ID .'" id="' . Project::ID .'"';
        if (count($this->projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $this->html[] = $select;
        foreach ($this->projects as $project) {
            $this->html[] = '<option value = "'. $project['projectID'] .'"' . ($project['projectID'] == $this->projectID ? " selected " : "") . '>Project Title: ' . $project['title'] . '  Lead: ' . $project['lead'] . '  Email: ' . $project['leadEmail'] .'</option>';
        }
        $this->html[] = '</select><br>';
    }

    public function __toString()
    {
        $this->display();
        return implode("\n", $this->html);
    }
}