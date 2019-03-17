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

    function __construct($action) {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
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
            case "create":
                // Form arrays of Project Leads and Project Clients
                $this->usersLead = $this->userModel->retrieveUsersWithRole('lead');
                $this->usersClient = $this->userModel->retrieveUsersWithRole('client');
                // Check to see if we have project data to save in the database
                if (isset($_POST['submit'])) {
                    $this->checkFormData();
                    if ($this->message == '') {
                        $this->saveProjectData();
                    }
                }
                break;

            case "update":
                // Step 1: No information at all, so need to present initial selection of all projects
                if (!isset($_POST['projectID'])) {
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
                }

                // Step 2: ProjectID is the Projects primary key, hence if no other data we only have initial project selection
                if (isset($_POST['projectID']) && (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['email']))) {

                    // Form arrays of Project Leads and Project Clients
                    $this->usersLead = $this->userModel->retrieveUsersWithRole('lead');
                    $this->usersClient = $this->userModel->retrieveUsersWithRole('client');

                    // Set projectID (hidden form field value) and clear $projects array
                    $this->projects = array();
                    $this->projectID = $_POST['projectID'];

                    // Retrieve Projects table data
                    $this->displayValues = $this->projectModel->retrieveProjectWithLead($this->projectID);

                    // Retrieve UndertakenFor data if available
                    $temp = $this->projectModel->retrieveProjectClient($this->projectID);
                    if (isset($temp['clientEmail'])) {
                        $this->displayValues['clientEmail'] = $temp['clientEmail'];
                        $this->displayValues['client'] = $temp['client'];
                    } else {
                        $this->displayValues['clientEmail'] = null;
                    }
                    // If any values were previously set by the user add these in now
                }

                // Step 3: If we have all project data then these are the updated values that need to saved in the Projects table
                if (isset($_POST['projectID']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['userLead'])) {
                    if (isset($_POST['submit'])) {
                        $this->checkFormData();
                        // Attempt to save the new project data
                        if ($this->message == '') {
                            $this->updateProjectData();
                        }
                    }

                    // Reset ProjectView projects array to offer a second update
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to delete";
                }
                break;

            case "delete" :
                // Step 1: No information at all, so need to present initial selection of all users
                if (!isset($_POST['projectID'])) {
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
                }

                // Step 2: ProjectID is the Projects primary key, hence we have the project for deletion
                if (isset($_POST['projectID'])) {
                    // First delete the client data if it exists
                    $this->projectModel->deleteProjectClient($_POST['projectID']);

                    if ($this->displayValues = $this->projectModel->deleteProject($_POST['projectID'])) {
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
        if ($_POST['title'] != '') {
            $this->displayValues['title'] = $_POST['title'];
        } else {
            $this->message .= "<p> Please enter project title </p>";
        }
        if ($_POST['description'] != '') {
            $this->displayValues['description'] = $_POST['description'];
        } else {
            $this->message .= "<p> Please enter project description </p>";
        }
        if ($_POST['userLead'] != '') {
            $this->displayValues['leadEmail'] = $_POST['userLead'];
        } else {
            $this->message .= "<p> Please enter project lead </p>";
        }
    }

    function saveProjectData() {
        if ($_POST['userClient'] != '' && $_POST['userClient'] != 'none') {
            // Save Projects and UndertakenFor table data
            if ($this->projectModel->insertProjectWithClient($_POST['title'], $_POST['description'], $_POST['userLead'], $_POST['userClient'])) {
                $this->message = "Project information saved";
            }
        } else {
            // Save Projects table data only
            if ($this->projectModel->insertProject($_POST['title'], $_POST['description'], $_POST['userLead'])) {
                $this->message = "Project information saved";
            }
        }
    }

    function updateProjectData() {
        // Delete the client data if it exists
        $this->projectModel->deleteProjectClient($_POST['projectID']);

        // Save Projects table data
        if ($this->projectModel->updateProject($_POST['projectID'],$_POST['title'], $_POST['description'], $_POST['userLead'])) {
            $this->message = "Project information saved";
        }

        // Save UndertakenFor table data
        if ($_POST['userClient'] != '' && $_POST['userClient'] != 'none') {
            If ($this->projectModel->updateProjectClient($_POST['projectID'], $_POST['userClient'])) {
                // Try to update client information
                $this->message .= "Client Information Saved";
            } else {
                // Assume client is a new addition to teh project
                if($this->projectModel->insertProjectClient($_POST['projectID'], $_POST['userClient'])) {
                    $this->message .= "Client Information Saved";
                }
            }
        }
    }
}