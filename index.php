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

// Instance page template
$HomePage = new Page();

// Process user information
$UserController = new UserController();
$UserController->databaseOperations();
$UserView = new UserView($UserController->users, $UserController->displayValues, $UserController->action1, $UserController->action2);

// Place user content on page
$HomePage->content = '<section class="grid-100">'. $UserView . '</section>';

//Display page
$HomePage->Display();