<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 19/03/2019
 * Time: 17:53
 */

namespace Controller;

use Model\TaskModel;

require_once("Objects/Model/TaskModel.php");

class TaskController
{
    private $taskModel = null;

    protected $taskID;
    protected $projects;
    protected $projectID;   //Project task belongs too
    protected $nonClientUsers = array();
    protected $displayValues = array();
    protected $action = null;
    protected $message = "";

    function __construct($action) {
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
        switch ($this->action) {
            case "create":
                if (isset($_POST['submit'])) {
                    // Check to see if we have user data to save in the database
                    $this->checkFormData();
                    if ($this->message == "") {
                        if ($this->taskModel->insertUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                            $this->message = "User information saved";
                        }
                    }
                }
                break;
            case "update":
                // Step 1: No information at all, so need to present initial selection of all users
                if (!isset($_POST['email'])) {
                    $this->nonClientUsers = $this->taskModel->retrieveUsers();
                    if (count($this->nonClientUsers) == 0) $this->message = "No users to update";
                }
                // Step 2: Email is the Users primary key, hence if no other data we only have initial user selection
                if (isset($_POST['email']) && !isset($_POST['username'])) {
                    $this->nonClientUsers = array();
                    $this->displayValues = $this->taskModel->retrieveUser($_POST['email']);
                    // Force entry of a new password - otherwise we would hash the hash of teh old password
                    $this->displayValues['password'] = null;
                }
                // Step 3: If we have all user data then these are the updated values that need to saved in the Users table
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['role'])) {
                    // Attempt to save user data
                    if ($this->taskModel->updateUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'])) {
                        $this->message = "User information updated";
                    }
                    // Reset UserView users array to offer a second update
                    $this->nonClientUsers = $this->taskModel->retrieveUsers();
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
            $percent = (int) $_POST['percent'];
            $percent = max(0, min($percent,100));
        } else {
            $percent = 0;
        }
        $this->displayValues['percent'] = $percent;

        // Validate taskNo value
        if ($_POST['taskNo'] != '') {
            $taskNo = (int) $_POST['taskNo'];
            $taskNo = max(-999, min($taskNo,999));
        } else {
            $taskNo = 0;
        }
        $this->displayValues['taskNo'] = $taskNo;

    }
}