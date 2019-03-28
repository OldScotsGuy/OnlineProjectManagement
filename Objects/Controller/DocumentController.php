<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/03/2019
 * Time: 15:33
 */

namespace Controller;

use Model\DocumentModel;
use Model\ProjectModel;
use Utils\Action;
use Utils\Document;
use Utils\Form;
use Utils\FormComponents;
use Utils\PageName;
use Utils\Project;
use Utils\User;

require_once("Objects/Model/DocumentModel.php");
require_once("Objects/Model/ProjectModel.php");

class DocumentController
{
    private $documentModel = null;
    private $projectModel = null;

    protected $projectID = null;
    protected $projects = null;
    protected $documents = array();
    protected $action = null;
    protected $message = null;
    protected $documentName = null;
    protected $formComponents = null;
    protected $canDeleteDocument = false;

    function __construct($action) {
        $this->documentModel = new DocumentModel();
        $this->projectModel = new ProjectModel();
        $this->action = $action;
        $this->formComponents = new FormComponents();
        $this->databaseOperations();
    }

    function databaseOperations() {
        // Set tasks delete privilege
        $this->canDeleteDocument = ($_SESSION[User::Role] == User::RoleAdmin || $_SESSION[User::Role] == User::RoleLead);
        switch ($this->action) {
            case Action::Upload:
                // Step 1 - Need to select project
                if (!isset($_POST[Project::ID])) {
                    // $this->projectID not set so load projects for project selection
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to view the documents of.";
                } else {
                    $this->projectID = $_POST[Project::ID];
                }

                // Step 2 - Form data has been submitted so check and upload document
                if (isset($_POST[Form::SubmitData])) {
                    $this->projectID = $_POST[Project::ID];
                    $this->checkFormData();
                    $this->checkFile();
                    if ($this->message == '') {
                        // do the document upload thing
                        $this->processFile();
                        $this->documentModel->insertDocument($_POST[Document::Title], $this->documentName, $this->projectID);
                        $this->message = 'Document Uploaded';
                    }
                }
                break;

            case Action::View:
                // If coming from the project selection form, ProjectID can be obtained via $_POST
                // if coming from the document delete action, ProjectID can be obtained via $_GET
                if (!isset($_POST[Project::ID]) && !isset($_GET[Project::ID])) {
                    // Step 1 - Need to select project
                    $this->projects = $this->projectModel->retrieveProjects();
                    if (count($this->projects) == 0) $this->message = "No projects to view the documents of.";
                } else {
                    // Step 2 - Now list project documents
                    if (isset($_POST[Project::ID])) {
                        $this->projectID = $_POST[Project::ID];
                    } else {
                        $this->projectID = $_GET[Project::ID];
                    }
                    $this->documents = $this->documentModel->retrieveProjectDocuments($this->projectID);
                }
                break;

            case Action::Delete:
                if (isset($_GET[Document::ID])) {
                    // Delete document from documents directory
                    $temp = $this->documentModel->retrieveDocument($_GET[Document::ID]);
                    unlink(Document::Path . $temp[Document::FileName]);

                    // Delete from database
                    if ($this->documentModel->deleteDocument($_GET[Document::ID])) {
                        $this->message = "<p>Document deleted</p>";
                    }
                    header('Location: index.php?page='. PageName::Document .'&action='. Action::View .'&'. Project::ID .'='.$_GET[Project::ID]);
                } else {
                    $this->message .= '<p>Select Task to delete</p>';
                }
                break;
        }

    }

    function checkFormData() {
        $this->message='';
        if ($_POST[Document::Title] == '') {
            $this->message .= '<p>Please enter document title</p>';
        }
        if ($_POST[Document::ProjectID] == '') {
            $this->message .= '<p>Please select project</p>';
        }
    }

    function checkFile() {
        // File upload error messages
        $phpFileUploadErrors = array(
            0 => 'File uploaded successfully',
            1 => 'File exceeds the upload_max_filesize directive in php.ini',
            2 => 'File exceeds the MAX_FILE_SIZE directive specified in the HTML form',
            3 => 'File only partially uploaded',
            4 => 'File not uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped teh file upload'
        );

        if (isset($_FILES[Document::FileName])) {
            // Check File Extension
            $file_ext = explode('.', $_FILES[Document::FileName]['name']);
            $file_ext = end($file_ext);
            if ($file_ext != 'pdf') {
                $this->message .= '<p>PLease upload a PDF file</p>';
            }

            // Check for file upload errors
            if ($_FILES[Document::FileName]['error']) {
                $this->message .= '<p>' . $phpFileUploadErrors[$_FILES[Document::FileName]['error']] . '</p>';
            }
        } else {
            $this->message .= '<p>Please select a file to upload</p>';
        }
    }

    function processFile() {
        // Can now move and then store file details
        $this->message .= "<p>File uploaded </p>>";
        $this->documentName = $_FILES[Document::FileName]['name'];

        // Prepend with time() to try an avoid filename conflicts
        $this->documentName = time() . str_replace(' ', '_', $this->documentName);

        // Move file to storage location
        if (move_uploaded_file($_FILES[Document::FileName]['tmp_name'], Document::Path . $this->documentName)) {
            $this->message .= "<p>File successfully moved</p>>";
        } else {
            $this->message .= "<p>File move failed</p>>";
        }
    }
}