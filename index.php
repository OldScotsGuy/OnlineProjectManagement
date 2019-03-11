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

// TODO Get Page and action variables from page url via $_GET
$page = "user";
$action = "create";


// Instance page template
$HomePage = new Page();

// Process user information
$UserController = new UserController($action);
$UserController->databaseOperations();
$UserView = new UserView($UserController->users, $UserController->displayValues, $UserController->action, $UserController->message);

// Place user content on page
$HomePage->content = '<section class="grid-100">'. $UserView . '</section>';

//Display page
$HomePage->Display();