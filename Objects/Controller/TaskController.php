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
        $this->nonClientUsers = $this->userModel->retrieveUsersWithRole('lead') + $this->userModel->retrieveUsersWithRole('member');
        $this->projects = $this->projectModel->retrieveProjects();
        if (count($this->projects) == 0) $this->message = "No projects to assign task to";

        switch ($this->action) {
            case "create":
                if (isset($_POST['submit'])) {
                    // Check to see if we have user data to save in the database
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->insertTask($_POST['taskName'], $_POST['startDate'], $_POST['endDate'], $_POST['percent'], $_POST['taskNo'], $_POST['notes'], $_POST['projectID'], $_POST['userOwner'])) {
                            $this->message = "Task information saved";
                        }
                    }
                }
                break;
            case "update":
                // Read taskID and projectID from URL
                $this->taskID = $_GET['taskID'];
                $this->projectID = $_GET['projectID'];

                if (isset($_POST['submit'])) {
                    // Form submitted so check and save data if appropriate
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->updateTask($this->taskID, $_POST['taskName'], $_POST['startDate'], $_POST['endDate'], $_POST['percent'], $_POST['taskNo'], $_POST['notes'], $_POST['projectID'], $_POST['userOwner'])) {
                            $this->message = "Task information updated";
                        }
                    }
                } else {
                    // Read task data and set displayValues for task update
                    $this->displayValues = $this->taskModel->retrieveTask($this->taskID);
                }
                break;
            case "delete" :
                // No information at all, so need to present initial selection of all users
                if (!isset($_POST['email'])) {
                    $this->nonClientUsers = $this->taskModel->retrieveUsers();
                }
                // Email is the Users primary key, hence if no other data we have the user for deletion
                if (isset($_POST['email']) && !isset($_POST['username'])) {
                    if ($this->displayValues = $this->taskModel->deleteUser($_POST['email'])) {
                        $this->message = "User deleted";
                    }
                    $this->nonClientUsers = $this->taskModel->retrieveUsers();
                }
                break;
        }
    }

    function checkFormData() {
        $this->message = "";
        // Check task name entered
        if ($_POST['taskName '] != '') {
            $this->displayValues['taskName'] = $_POST['taskName'];
        } else {
            $this->message .= "<p> Please enter task name </p>";
        }

        // TODO Need to ensure date is in YYYY-MM-DD format
        if ($_POST['startDate'] != '') {
            $this->displayValues['startDate'] = $_POST['startDate'];
        } else {
            $this->message .= "<p> Please enter task start date </p>";
        }

        // TODO Need to ensure date is in YYYY-MM-DD format
        if ($_POST['endDate'] != '') {
            $this->displayValues['endDate'] = $_POST['endDate'];
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
        if ($_POST['percent'] != '') {
            //$percent = (int) $_POST['percent'];
            $_POST['percent'] = max(0, min((int) $_POST['percent'],100));
        } else {
            $_POST['percent'] = 0;
        }
        $this->displayValues['percent'] = $_POST['percent'];

        // Validate taskNo value
        if ($_POST['taskNo'] != '') {
            //$taskNo = (int) $_POST['taskNo'];
            $_POST['taskNo'] = max(-999, min((int) $_POST['taskNo'],999));
        } else {
            $_POST['taskNo'] = 0;
        }
        $this->displayValues['taskNo'] = $_POST['taskNo'];

    }
}