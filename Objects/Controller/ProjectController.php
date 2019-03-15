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
                if (isset($_POST['projectID']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['email'])) {
                    $this->saveProjectData();
                }
                break;

            case "update":
                // Step 1: No information at all, so need to present initial selection of all projects
                if (!isset($_POST['projectID'])) {
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
                }

                // Step 2: ProjectID is the Projects primary key, hence if no other data we only have initial project selection
                if (isset($_POST['projectID']) && !isset($_POST['title']) && !isset($_POST['description']) && !isset($_POST['email'])) {

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
                    if (isset($temp['email'])) {
                        $this->displayValues['clientEmail'] = $temp['clientEmail'];
                    } else {
                        $this->displayValues['clientEmail'] = null;
                    }
                }

                // Step 3: If we have all project data then these are the updated values that need to saved in the Projects table
                if (isset($_POST['projectID']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['email'])) {
                    // First delete the client data if it exists
                    $this->projectModel->deleteProjectClient($_POST['projectID']);

                    // Attempt to save project data
                    $this->saveProjectData();

                    // Reset ProjectView projects array to offer a second update
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to update";
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

    function saveProjectData() {
        if (isset($_POST['client'])) {
            // Save Projects and UndertakenFor table data
            if ($this->projectModel->insertProjectWithClient($_POST['title'], $_POST['description'], $_POST['email'], $_POST['client'])) {
                $this->message = "Project information saved";
            }
        } else {
            // Save Projects table data only
            if ($this->projectModel->insertProject($_POST['title'], $_POST['description'], $_POST['email'])) {
                $this->message = "Project information saved";
            }
        }
    }
}