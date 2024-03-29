<?php
/**
 * Created by Nick Harle
 * Date: 12/03/2019
 * Time: 20:30
 *
 * This object generates the html required to create / update / delete projects.
 * Which html form / view is generated based on the object variables inherited from the ProjectController object
 * The presentation can be changed without any requirement to change the Controller object
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

        // Navigation links
        $navigationLinks = array(   'Create Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Create,
                                    'Update Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Update,
                                    'Delete Project' => 'index.php?page='. PageName::Project .'&action=' . Action::Delete);
        $this->html = array_merge($this->html, $this->formComponents->addNavigationLinks($navigationLinks));
    }

    function displayProjectSelectionForm() {
        $this->html[] = '<form action ="index.php?page='. PageName::Project .'&action=' . $this->action .'" method="post">';
        $this->html[] = '<fieldset>';

        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project", $this->message));

        // Project selection drop down box
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project:', $this->projects));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select Project to ".ucfirst($this->action)));
        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

    function displayProjectForm() {
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';
        $this->html[] = '<fieldset>';

        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project", $this->message));

        // Display project description
        $value = (isset($this->displayValues[Project::Title]) ? $this->displayValues[Project::Title] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("text", Project::Title, "Title:", $value, 'required size="40" maxlength="40"'));

        // Display project description
        $value = (isset($this->displayValues[Project::Description]) ? $this->displayValues[Project::Description] : null );
        $this->html = array_merge($this->html, $this->formComponents->addTextArea(Project::Description, "Description:", $value));

        // Display project lead Options
        $label = 'Project Lead: ';
        $value = (isset($this->displayValues[Project::LeadEmail]) ? $this->displayValues[Project::LeadEmail] : null);
        $this->html = array_merge($this->html, $this->formComponents->addUserInput(Project::LeadEmail, $label, $value, $this->usersLead));

        // Display project client options
        $label = 'Project Client: ';
        $value = (isset($this->displayValues[Project::ClientEmail]) ? $this->displayValues[Project::ClientEmail] : null);
        $this->html = array_merge($this->html, $this->formComponents->addUserInput(Project::ClientEmail, $label, $value, $this->usersClient));

        // Carry ProjectID across
        $this->html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $this->projectID . '"/>';

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, ucfirst($this->action) . " Project"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }
}