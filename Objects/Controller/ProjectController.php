<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 14/03/2019
 * Time: 19:17
 */

namespace Controller;

require_once("Objects/Model/ProjectModel.php");
require_once("Objects/Model/UserModel.php");

use Model\ProjectModel;
use Model\UserModel;
use Utils\Action;
use Utils\Form;
use Utils\FormComponents;
use Utils\Project;
use Utils\User;

class ProjectController
{
    private $projectModel = null;
    private $userModel = null;

    protected $projects = array();
    protected $projectID = null;
    protected $usersLead = array();
    protected $usersClient = array();
    protected $displayValues = array();
    protected $action = null;
    protected $message = "";
    protected $formComponents = null;

    function __construct($action) {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->formComponents = new FormComponents();
        $this->action = $action;
        $this->databaseOperations();
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        switch ($this->action) {
            case Action::Create:
                // Form arrays of Project Leads and Project Clients
                $this->usersLead = $this->userModel->retrieveUsersWithRole(User::RoleLead);
                $this->usersClient = $this->userModel->retrieveUsersWithRole(User::RoleClient);
                // Check to see if we have project data to save in the database
                if (isset($_POST[Form::SubmitData])) {
                    $this->checkFormData();
                    if ($this->message == '') {
                        $this->saveProjectData();
                    }
                }
                break;

            case Action::Update:
                // Step 1: No information at all, so need to present initial selection of all projects
                if (!isset($_POST[Project::ID])) {
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
                }

                // Step 2: ProjectID is the Projects primary key, hence if no other data we only have initial project selection
                //if (isset($_POST['projectID']) && (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['email']))) {
                if (isset($_POST[Form::SubmitSelection])) {
                    // Form arrays of Project Leads and Project Clients
                    $this->usersLead = $this->userModel->retrieveUsersWithRole(User::RoleLead);
                    $this->usersClient = $this->userModel->retrieveUsersWithRole(User::RoleClient);

                    // Set projectID (hidden form field value) and clear $projects array
                    $this->projects = array();
                    $this->projectID = $_POST[Project::ID];

                    // Retrieve Projects table data
                    $this->displayValues = $this->projectModel->retrieveProjectWithLead($this->projectID);

                    // Retrieve UndertakenFor data if available
                    $temp = $this->projectModel->retrieveProjectClient($this->projectID);
                    if (isset($temp[Project::ClientEmail])) {
                        $this->displayValues[Project::ClientEmail] = $temp[Project::ClientEmail];
                        //$this->displayValues['client'] = $temp['client'];
                    } else {
                        $this->displayValues[Project::ClientEmail] = null;
                    }
                    // If any values were previously set by the user add these in now
                }

                // Step 3: If we have all project data then these are the updated values that need to saved in the Projects table
                //if (isset($_POST['projectID']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST[Project::Lead])) {
                if (isset($_POST[Form::SubmitData])) {
                    $this->checkFormData();
                    // Attempt to save the new project data
                    if ($this->message == '') {
                        $this->updateProjectData();
                    }

                    // Reset ProjectView projects array to offer a second update
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to delete";
                }
                break;

            case Action::Delete :
                // Step 1: No information at all, so need to present initial selection of all users
                if (!isset($_POST[Project::ID])) {
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
                }

                // Step 2: ProjectID is the Projects primary key, hence we have the project for deletion
                if (isset($_POST[Project::ID])) {
                    // First delete the client data if it exists
                    $this->projectModel->deleteProjectClient($_POST[Project::ID]);

                    if ($this->displayValues = $this->projectModel->deleteProject($_POST[Project::ID])) {
                        $this->message = "Project deleted";
                    }
                    // Reset ProjectView projects array to offer a second delete
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";

                }
                break;
        }
    }

    function checkFormData() {
        $this->message = "";
        if ($_POST[Project::Title] != '') {
            $this->displayValues[Project::Title] = $_POST[Project::Title];
        } else {
            $this->message .= "<p> Please enter project title </p>";
        }
        if ($_POST[Project::Description] != '') {
            $this->displayValues[Project::Description] = $_POST[Project::Description];
        } else {
            $this->message .= "<p> Please enter project description </p>";
        }
        if ($_POST[Project::LeadEmail] != '') {
            $this->displayValues[Project::LeadEmail] = $_POST[Project::LeadEmail];
        } else {
            $this->message .= "<p> Please enter project lead </p>";
        }
        if ($_POST[Project::ClientEmail] != '') {
            $this->displayValues[Project::ClientEmail] = $_POST[Project::ClientEmail];
        }
    }

    function saveProjectData() {
        if ($_POST[Project::ClientEmail] != '' && $_POST[Project::ClientEmail] != 'none') {
            // Save Projects and UndertakenFor table data
            if ($this->projectModel->insertProjectWithClient($_POST[Project::Title], $_POST[Project::Description], $_POST[Project::LeadEmail], $_POST[Project::ClientEmail])) {
                $this->message = "Project information saved";
            }
        } else {
            // Save Projects table data only
            if ($this->projectModel->insertProject($_POST[Project::Title], $_POST[Project::Description], $_POST[Project::LeadEmail])) {
                $this->message = "Project information saved";
            }
        }
    }

    function updateProjectData() {
        // Delete the client data if it exists
        $this->projectModel->deleteProjectClient($_POST[Project::ID]);

        // Save Projects table data
        if ($this->projectModel->updateProject($_POST[Project::ID],$_POST[Project::Title], $_POST[Project::Description], $_POST[Project::LeadEmail])) {
            $this->message = "Project information saved";
        }

        // Save UndertakenFor table data
        if ($_POST[Project::ClientEmail] != '' && $_POST[Project::ClientEmail] != 'none') {
            If ($this->projectModel->updateProjectClient($_POST[Project::ID], $_POST[Project::ClientEmail])) {
                // Try to update client information
                $this->message .= "Client Information Saved";
            } else {
                // Assume client is a new addition to the project
                if($this->projectModel->insertProjectClient($_POST[Project::ID], $_POST[Project::ClientEmail])) {
                    $this->message .= "Client Information Saved";
                }
            }
        }
    }
}