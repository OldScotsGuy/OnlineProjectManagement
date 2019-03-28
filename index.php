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
require("Objects/Utils/Form.php");
require("Objects/Utils/PageName.php");

// Load View Objects
require("Objects/Utils/FormComponents.php");
require("Objects/View/UserView.php");
require("Objects/View/ProjectVIew.php");
require("Objects/View/TaskView.php");
require("Objects/View/GanttView.php");
require("Objects/View/DocumentView.php");
require("Objects/View/LoginView.php");

// Load Login object
//require("Objects/Utils/LoginManagement.php");

use Page\Page;
use View\UserView;
use View\ProjectView;
use View\TaskView;
use View\DocumentView;
use View\GanttView;
use View\LoginView;
use Utils\Action;
use Utils\PageName;
use Utils\Form;
use Utils\User;
use Utils\LoginManagement;

session_start();

// Instance page template
$HomePage = new Page();

// Instance Login tracking
//$trackLogin = new LoginManagement();
$LoginView = new LoginView();

if ($LoginView->userLoggedIn()) {
//if (true) {
    // ==============
    // User logged in
    // ==============

    // Get page and action variables
    if (empty($_GET['page'])) {
        $page = PageName::Status;
    } else {
        $page = $_GET['page'];
    }
    if (empty($_GET['action'])) {
        $action = Action::Create;
    } else {
        $action = $_GET['action'];
    }

    // Generate Page Content
    $enhancedPrivileges = ($_SESSION[User::Role] == User::RoleLead || $_SESSION[User::Role] == User::RoleAdmin);
    $authorisationErrorContent = '<section><p>This page content is unavailable</p></section>';
    switch ($page) {
        case PageName::User:
            if ($enhancedPrivileges) {
                $UserView = new UserView($action);
                $HomePage->content = '<section>'. $UserView . '</section>';
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Project:
            if ($enhancedPrivileges) {
                $ProjectView = new ProjectView($action);
                $HomePage->content = '<section>'. $ProjectView . '</section>';
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Task:
            if (($_SESSION[User::Role] == User::RoleMember && $action == Action::Update) || $enhancedPrivileges) {
                $TaskView = new TaskView($action);
                $HomePage->content = '<section>' . $TaskView . '</section>';
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Document:
            $DocumentView = new DocumentView($action);
            $HomePage->content = '<section>' . $DocumentView . '</section>';
            break;

        case PageName::Status:
            $GanttView = new GanttView();
            $HomePage->content = '<section>' . $GanttView . '</section>';
            break;
    }

} else {
    // ==================
    // User not logged in
    // ==================
    $HomePage->content = '<section>' . $LoginView . '</section>';

/*    $message = '';
    if (isset($_POST[Form::SubmitData])) {

        // Form submitted so read and check form data
        $email = $_POST[User::Email];
        $password = $_POST[User::Password];
        $message .= $trackLogin->checkLoginData($_POST[User::Email], $password);

        if ($message == '') {
            // Form filled out so now check login data against the database
            $trackLogin->attemptLogin($email, $password);
            if ($trackLogin->userLoggedIn()) {
                header("Location: index.php");
            } else {
                // Login data did not match that stored in the database
                $message .= '<p>User Email and Password do not match</p>';
            }
        }
    }
    $HomePage->content = '<section>' . $trackLogin->displayLoginForm($message) . '</section>'; */
}

// Display Page
$HomePage->Display();