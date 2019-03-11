<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:42
 */
require("Objects/Page/Page.php");
require("Objects/View/UserView.php");
require("Objects/Controller/UserController.php");

use Page\Page;
use View\UserView;
use Controller\UserController;

// Get page and action variables
// =============================
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
// ======================
$HomePage = new Page();


// Generate Page Content
// =====================
switch ($page) {
    case "user":    // Process user information
        $UserController = new UserController($action);
        $UserController->databaseOperations();
        $UserView = new UserView($UserController->users, $UserController->displayValues, $UserController->action, $UserController->message);
        $HomePage->content = '<section>'. $UserView . '</section>';
        break;
}


// Display Page
// ============
$HomePage->Display();