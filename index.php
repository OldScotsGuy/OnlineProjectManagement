<?php
/**
 * Created by Nick Harle
 * Date: 10/03/2019
 * Time: 11:42
 *
 * This object is the single entry point from which all pages are generated.
 * The server could be configured to allow internet access to this page ONLY
 * An authorisation check is made for pages that have user restricted content
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

use Page\Page;
use View\UserView;
use View\ProjectView;
use View\TaskView;
use View\DocumentView;
use View\GanttView;
use View\LoginView;
use Utils\Action;
use Utils\PageName;
use Utils\User;

session_start();

// Instance page template
$HomePage = new Page();

if (isset($_SESSION[User::Username]) && isset($_SESSION[User::Email]) && isset($_SESSION[User::Role])) {
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

    // Check for User logout
    if ($action == Action::Logout) {
        session_destroy();
        header('Location: index.php');
    }

    // Generate Page Content
    $enhancedPrivileges = ($_SESSION[User::Role] == User::RoleLead || $_SESSION[User::Role] == User::RoleAdmin);
    $authorisationErrorContent = '<div id="error"><h2>This page content is unavailable</h2></div>';
    switch ($page) {
        case PageName::User:
            if ($enhancedPrivileges) {
                $UserView = new UserView($action);
                $HomePage->content =$UserView;
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Project:
            if ($enhancedPrivileges) {
                $ProjectView = new ProjectView($action);
                $HomePage->content =$ProjectView;
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Task:
            if (($_SESSION[User::Role] == User::RoleMember && $action == Action::Update) || $enhancedPrivileges) {
                $TaskView = new TaskView($action);
                $HomePage->content = $TaskView;
            } else {
                $HomePage->content = $authorisationErrorContent;
            }
            break;

        case PageName::Document:
            $DocumentView = new DocumentView($action);
            $HomePage->content = $DocumentView;
            break;

        case PageName::Status:
            $GanttView = new GanttView();
            $HomePage->content = $GanttView;
            break;
    }

} else {
    // ==================
    // User not logged in
    // ==================
    $LoginView = new LoginView();
    $HomePage->content = $LoginView;
}

// ============
// Display Page
// ============
$HomePage->Display();