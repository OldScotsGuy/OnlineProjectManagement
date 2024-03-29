<?php
/**
 * Created by Nick Harle
 * Date: 19/03/2019
 * Time: 17:53
 * This object:
 * 1) Handles task creation / update / deletion database operations via the TaskModel object
 * 2) Sets arrays and variables data required to display the task information
 * 3) The TaskView child object generates html, not this object
 */

namespace Controller;

use Model\TaskModel;
use Model\ProjectModel;
use Model\UserModel;
use Utils\Action;
use Utils\Form;
use Utils\FormComponents;
use Utils\PageName;
use Utils\Project;
use Utils\Task;
use Utils\User;

require_once("Objects/Model/TaskModel.php");
require_once("Objects/Model/ProjectModel.php");
require_once("Objects/Model/UserModel.php");

class TaskController
{
    private $taskModel = null;
    private $projectModel = null;
    private $userModel = null;

    protected $taskID;                      // Obtained by $_GET when arriving from project status page
    protected $projectID;                   // Obtained by $_GET when arriving from project status page
    protected $projects;                    // List of available projects to assign task to
    protected $nonClientUsers = array();    // List users the task can be assigned to
    protected $displayValues = array();     // Used for task update when arriving from project status page
    protected $action = null;               // create: when arriving from navigation bar, update: when arriving from project status page
    protected $message = "";                // Set by controller
    protected $formComponents = null;

    function __construct($action) {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();
        $this->action = $action;
        $this->formComponents = new FormComponents();
        $this->databaseOperations();
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        // Read project list and non-client users for task data entry
        $this->nonClientUsers = array_merge($this->userModel->retrieveUsersWithRole(User::RoleLead), $this->userModel->retrieveUsersWithRole(User::RoleMember));
        $this->projects = $this->projectModel->retrieveProjects();
        if (count($this->projects) == 0) $this->message = "No projects to assign task to";

        switch ($this->action) {
            case Action::Create:
                if (isset($_POST[Form::SubmitData])) {
                    // Check to see if we have user data to save in the database
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->insertTask($_POST[Task::Name], $_POST[Task::StartDate], $_POST[Task::EndDate], $_POST[Task::Percent], $_POST[Task::No], $_POST[Task::Notes], $_POST[Task::ProjectID], $_POST[Task::Owner])) {
                            $this->message = "Information saved for task: " . $_POST[Task::Name];
                        }
                    }
                }
                break;
            case Action::Update:
                if (isset($_POST[Form::SubmitData])) {
                    // Form submitted so check and save data if appropriate
                    $this->taskID = $_POST[Task::ID];
                    $this->projectID = $_POST[Project::ID];
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->updateTask($this->taskID, $_POST[Task::Name], $_POST[Task::StartDate], $_POST[Task::EndDate], $_POST[Task::Percent], $_POST[Task::No], $_POST[Task::Notes], $_POST[Project::ID], $_POST[Task::Owner])) {
                            $this->message = "Information updated for task: " . $_POST[Task::Name];
                            header('Location: index.php?page='. PageName::Status .'&'. Project::ID .'=' . $this->projectID);
                        }
                    }
                } else {
                    // Read taskID and projectID from URL - we are coming from the project status page
                    $this->taskID = $_GET[Task::ID];
                    $this->projectID = $_GET[Project::ID];

                    // Read task data and set displayValues for task update
                    if (isset($this->taskID)) {
                        $this->displayValues = $this->taskModel->retrieveTask($this->taskID);
                    }
                }
                break;
            case Action::Delete :
                // Read taskID and projectID from URL - we are coming from the project status page
                $this->taskID = $_GET[Task::ID];
                $this->projectID = $_GET[Project::ID];

                // Email is the Users primary key, hence if no other data we have the user for deletion
                if (isset($this->taskID)) {
                    if ($this->taskModel->deleteTask($this->taskID)) {
                        $this->message = "Deleted Task";
                    }
                    header('Location: index.php?page='. PageName::Status .'&'. Project::ID .'=' . $this->projectID);
                } else {
                    $this->message .= '<p>Select Task to delete</p>';
                }
                break;
        }
    }

    function checkFormData() {
        $this->message = "";
        // Check task name entered
        if ($_POST[Task::Name] != '') {
            $this->displayValues[Task::Name] = $_POST[Task::Name];
        } else {
            $this->message .= "<p> Please enter task name </p>";
        }

        // TODO Need to ensure date is in YYYY-MM-DD format
        if ($_POST[Task::StartDate] != '') {
            $this->displayValues[Task::StartDate] = $_POST[Task::StartDate];
        } else {
            $this->message .= "<p> Please enter task start date </p>";
        }

        // TODO Need to ensure date is in YYYY-MM-DD format
        if ($_POST[Task::EndDate] != '') {
            $this->displayValues[Task::EndDate] = $_POST[Task::EndDate];
        } else {
            $this->message .= "<p> Please enter task end date </p>";
        }

        // Ensure task owner is entered
        if ($_POST[Task::Owner] != '') {
            $this->displayValues[Task::Owner] = $_POST[Task::Owner];
        } else {
            $this->message .= "<p> Please enter task owner </p>";
        }

        // Validate percent complete value
        if ($_POST[Task::Percent] != '') {
            $_POST[Task::Percent] = max(0, min((int) $_POST[Task::Percent],100));
        } else {
            $_POST[Task::Percent] = 0;
        }
        $this->displayValues[Task::Percent] = $_POST[Task::Percent];

        // Validate taskNo value
        if ($_POST[Task::No] != '') {
            $_POST[Task::No] = max(-999, min((int) $_POST[Task::No],999));
        } else {
            $_POST[Task::No] = 0;
        }
        $this->displayValues[Task::No] = $_POST[Task::No];

    }
}