<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 19/03/2019
 * Time: 17:53
 */

namespace Controller;

use Model\TaskModel;
use Model\ProjectModel;
use Model\UserModel;
use Utils\Project;
use Utils\Task;

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

    function __construct($action) {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();
        $this->action = $action;
        $this->databaseOperations();
    }

    // Generalised getter
    public function __get($name)
    {
        return $this->$name;
    }

    function databaseOperations() {
        // Read project list and non-client users for task data entry
        $this->nonClientUsers = array_merge($this->userModel->retrieveUsersWithRole('lead'), $this->userModel->retrieveUsersWithRole('member'));
        $this->projects = $this->projectModel->retrieveProjects();
        if (count($this->projects) == 0) $this->message = "No projects to assign task to";

        switch ($this->action) {
            case "create":
                if (isset($_POST['submit'])) {
                    // Check to see if we have user data to save in the database
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->insertTask($_POST[Task::Name], $_POST[Task::StartDate], $_POST[Task::EndDate], $_POST[Task::Percent], $_POST[Task::No], $_POST[Task::Notes], $_POST[Task::ProjectID], $_POST['taskOwner'])) {
                            $this->message = "Task information saved";
                        }
                    }
                }
                break;
            case "update":
                if (isset($_POST['submit'])) {
                    // Form submitted so check and save data if appropriate
                    $this->taskID = $_POST[Task::ID];
                    $this->projectID = $_POST[Project::ID];
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->updateTask($this->taskID, $_POST[Task::Name], $_POST[Task::StartDate], $_POST[Task::EndDate], $_POST[Task::Percent], $_POST[Task::No], $_POST[Task::Notes], $_POST[Task::ProjectID], $_POST['taskOwner'])) {
                            $this->message = "Task information updated";
                            header('Location: index.php?page=status&projectID=' . $this->projectID);
                        }
                    }
                } else {
                    // Read taskID and projectID from URL
                    $this->taskID = $_GET[Task::ID];
                    $this->projectID = $_GET[Project::ID];

                    // Read task data and set displayValues for task update
                    if (isset($this->taskID)) {
                        $this->displayValues = $this->taskModel->retrieveTask($this->taskID);
                    }
                }
                break;
            case "delete" :
                // Read taskID from URL
                $this->taskID = $_GET[Task::ID];

                // Email is the Users primary key, hence if no other data we have the user for deletion
                if (isset($this->taskID)) {
                    if ($this->taskModel->deleteTask($this->taskID)) {
                        $this->message = "Task deleted";
                    }
                    //$this->action = "create";
                    header('Location: index.php?page=status&projectID=' . $this->projectID);
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
        if ($_POST['taskOwner'] != '') {
            $this->displayValues['taskOwner'] = $_POST['taskOwner'];
        } else {
            $this->message .= "<p> Please enter task owner </p>";
        }

        // Validate percent complete value
        if ($_POST[Task::Percent] != '') {
            //$percent = (int) $_POST['percent'];
            $_POST[Task::Percent] = max(0, min((int) $_POST[Task::Percent],100));
        } else {
            $_POST[Task::Percent] = 0;
        }
        $this->displayValues[Task::Percent] = $_POST[Task::Percent];

        // Validate taskNo value
        if ($_POST[Task::No] != '') {
            //$taskNo = (int) $_POST['taskNo'];
            $_POST[Task::No] = max(-999, min((int) $_POST[Task::No],999));
        } else {
            $_POST[Task::No] = 0;
        }
        $this->displayValues[Task::No] = $_POST[Task::No];

    }
}