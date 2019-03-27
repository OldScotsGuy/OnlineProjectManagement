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
use Utils\PageName;
use Utils\Project;
use Utils\User;

class ProjectView extends ProjectController
{
    private $html = array();

    public function __toString() {

        $this->displayHeader();

        // Select form to Display
        if (($this->action == Action::Create) || ($this->action == Action::Update &&  isset($this->projectID))) {
            $this->displayProjectForm();
        } else {
            $this->displayProjectSelectionForm();
        }

        return implode("\n", $this->html);
    }

    function displayHeader() {

        // Title and Message
        $this->html[] = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project", $this->message));

        // Navigation links
        $navigationLinks = array(   'Create Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Create,
                                    'Update Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Update,
                                    'Delete Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Delete);
        $this->html[] = array_merge($this->html, $this->formComponents->addNavigationLinks($navigationLinks));

        //$this->html[] = "<h2>" . ucfirst($this->action) . " Project</h2>";
        //$this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        //$this->html[] = '<p><a href="index.php?page=project&action=' . Action::Create . '">Create Project</a></p>';
        //$this->html[] = '<p><a href="index.php?page=project&action=' . Action::Update . '">Update Project</a></p>';
        //$this->html[] = '<p><a href="index.php?page=project&action=' . Action::Delete . '">Delete Project</a></p>';
    }

    function displayProjectSelectionForm() {
        $this->html[] = '<form action ="index.php?page='. PageName::Project .'&action=' . $this->action .'" method="post">';

        // Project selection drop down box
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project to ' . ucfirst($this->action) . ':', $this->projects));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select Project to ".ucfirst($this->action)));

        /*$this->html[] = '<label for="' . Project::ID . '">Select Project to ' . ucfirst($this->action) . ': </label>';
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
        $this->html[] = '</select>'; */

        /*if ($this->action == Action::Update) {
            // Disable the submit button if no projects present
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitSelection . '" value="Select Project to Update"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitSelection . '" value="Select Project to Delete"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';
        } */
        $this->html[] = '</form>';
    }

    function displayProjectForm() {
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';

        // Display project description
        $value = (isset($this->displayValues[Project::Title]) ? $this->displayValues[Project::Title] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("text", Project::Title, "Title:", $value, 'required'));

        // Display project description
        $value = (isset($this->displayValues[Project::Description]) ? $this->displayValues[Project::Description] : null );
        $this->html = array_merge($this->html, $this->formComponents->addTextArea(Project::Description, "Description:", $value));

        //$this->addField("text", Project::Title, "Title:", (isset($this->displayValues[Project::Title]) ? $this->displayValues[Project::Title] : null ));
        //$this->addTextArea( Project::Description, "Description:", (isset($this->displayValues[Project::Description]) ? $this->displayValues[Project::Description] : null));

        // Display project lead Options
        $label = 'Project ' . ucfirst(Project::LeadEmail) . ': ';
        $value = (isset($this->displayValues[Project::LeadEmail]) ? $this->displayValues[Project::LeadEmail] : null);
        $this->html = array_merge($this->html, $this->formComponents->addUserInput(Project::LeadEmail, $label, $value, $this->usersLead));
        //$this->addUserInput(Project::LeadEmail, (isset($this->displayValues[Project::LeadEmail]) ? $this->displayValues[Project::LeadEmail] : null), $this->usersLead);

        // Display project client options
        $label = 'Project ' . ucfirst(Project::ClientEmail) . ': ';
        $value = (isset($this->displayValues[Project::ClientEmail]) ? $this->displayValues[Project::ClientEmail] : null);
        $this->html = array_merge($this->html, $this->formComponents->addUserInput(Project::ClientEmail, $label, $value, $this->usersClient));

        //$this->addUserInput(Project::ClientEmail, (isset($this->displayValues[Project::ClientEmail]) ? $this->displayValues[Project::ClientEmail] : null), $this->usersClient);

        // Carry ProjectID across
        $this->html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $this->projectID . '"/>';

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, ucfirst($this->action) . " Project"));

        /*if ($this->action == Action::Create) {
            // Submit button disabled if no Project Lead users
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Create Project"' . (count($this->usersLead) > 0 ? '' : 'disabled') . '/>';
        } else {
            $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitData . '" value="Update Project"/>';
        } */
        $this->html[] = '</form>';
    }

/*    function addField($type, $name, $text, $value) {
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
    } */
}