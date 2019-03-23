<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:42
 */
require("Objects/Page/Page.php");

// Load Enumerations
require("Objects/Utils/Action.php");
require("Objects/Utils/Document.php");
require("Objects/Utils/Project.php");
require("Objects/Utils/Task.php");
require("Objects/Utils/User.php");

// Load View Objects
require_once("Objects/View/UserView.php");
require_once("Objects/View/ProjectVIew.php");
require_once("Objects/View/TaskView.php");
require_once("Objects/View/GanttView.php");

use Page\Page;
use View\UserView;
use View\ProjectView;
use View\TaskView;
use View\GanttView;

// Get page and action variables
if (empty($_GET['page'])) {
    $page = "status";
} else {
    $page = $_GET['page'];
}
if (empty($_GET['action'])) {
    $action = "create";
} else {
    $action = $_GET['action'];
}

// Instance page template
$HomePage = new Page();

// Generate Page Content
switch ($page) {
    case "user":    // Process user information
        $UserView = new UserView($action);
        $HomePage->content = '<section>'. $UserView . '</section>';
        break;

    case "project":
        $ProjectView = new ProjectView($action);
        $HomePage->content = '<section>'. $ProjectView . '</section>';
        break;

    case "task":
        $TaskView = new TaskView($action);
        $HomePage->content = '<section>' . $TaskView . '</section>';
        break;

    case "document":

        break;

    case "status":
        $GanttView = new GanttView();
        $HomePage->content = '<section>' . $GanttView . '</section>';
        break;
}

// Display Page
$HomePage->Display();