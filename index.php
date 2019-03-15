<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:42
 */
require("Objects/Page/Page.php");
require_once("Objects/View/UserView.php");
require_once("Objects/View/ProjectVIew.php");

use Page\Page;
use View\UserView;
use View\ProjectView;

// Get page and action variables
if (empty($_GET['page'])) {
    $page = "user";
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
}

// Display Page
$HomePage->Display();