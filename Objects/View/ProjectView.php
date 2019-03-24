<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/03/2019
 * Time: 20:30
 */

namespace View;

require_once("Objects/Controller/ProjectController.php");

use Controller\ProjectController;
use Utils\Action;
use Utils\Form;
use Utils\Project;
use Utils\User;

class ProjectView extends ProjectController
{
    private $html = array();

    public function __toString() {

        $this->displayHeader();

        // Select form to Display
        if (($this->action == Action::Create) || ($this->action == Action::Update &&  count($this->displayValues) > 0)) {
            $this->displayProjectForm();
        } else {
            $this->displayProjectSelectionForm();
        }

        return implode("\n", $this->html);
    }

    function displayHeader() {

        // Title and Message
        $this->html[] = "<h2>" . ucfirst($this->action) . " Project</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        $this->html[] = '<p><a href="index.php?page=project&action=' . Action::Create . '">Create Project</a></p>';
        $this->html[] = '<p><a href="index.php?page=project&action=' . Action::Update . '">Update Project</a></p>';
        $this->html[] = '<p><a href="index.php?page=project&action=' . Action::Delete . '">Delete Project</a></p>';
    }

    function displayProjectSelectionForm() {
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';

        // Project selection drop down box
        $this->html[] = '<label for="' . Project::ID . '">Select Project to ' . ucfirst($this->action) . ': </label>';
        $select = '<select name = "' . Project::ID . '" id="' . Project::ID . '"';
        if (count($this->projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $this->html[] = $select;
        foreach ($this->projects as $project) {
            $this->html[] = '<option value = "'. $project[Project::ID] .'">Title: ' . $project[Project::Title] . ' Project Lead: ' . $project[Project::Lead] . '  Project Lead Email: ' . $project[Project::LeadEmail] .'</option>';
        }
        $this->html[] = '</select>';

        // Submit button
        if ($this->action == Action::Update) {
            // Disable the submit button if no projects present
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitSelection . '" value="Select Project to Update"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitSelection . '" value="Select Project to Delete"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';
        }
        $this->html[] = '</form>';
    }

    function displayProjectForm() {
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';

        // Display project title and description fields
        $this->addField("text", Project::Title, "Title:", (isset($this->displayValues[Project::Title]) ? $this->displayValues[Project::Title] : null ));
        $this->addTextArea( Project::Description, "Description:", (isset($this->displayValues[Project::Description]) ? $this->displayValues[Project::Description] : null));

        // Display project lead Options
        $this->addUserInput(Project::Lead, (isset($this->displayValues[Project::LeadEmail]) ? $this->displayValues[Project::LeadEmail] : null), $this->usersLead);

        // Display project client options
        $this->addUserInput(Project::Client, (isset($this->displayValues[Project::ClientEmail]) ? $this->displayValues[Project::ClientEmail] : null), $this->usersClient);

        // Carry ProjectID across
        $this->html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $this->projectID . '"/>';

        // Submit button
        if ($this->action == Action::Create) {
            // Submit button disabled if no Project Lead users
            $this->html[] = '<br><br><input type="submit" name="submit" value="Create Project"' . (count($this->usersLead) > 0 ? '' : 'disabled') . '/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name="submit" value="Update Project"/>';
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

    function addUserInput($name, $value, $userList) {
        $this->html[] = '<label for="' . $name .'">Project ' . ucfirst($name) . ': </label>';
        $this->html[] = '<select name = "' . $name .'" id="' . $name .'">';

        for ($i=0; $i<count($userList); $i++) {
            $this->html[] = '<option value = "' . $userList[$i][User::Email] . '"' . ($value == $userList[$i][User::Email] ? " selected " : "") . '>' . $userList[$i][User::Username] . '</option>';
        }
        $this->html[] = '</select><br>';
    }
}