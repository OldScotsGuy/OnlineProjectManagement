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

require_once("Objects/Model/DocumentModel.php");

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

    function __construct($action) {
        $this->documentModel = new DocumentModel();
        $this->projectModel = new ProjectModel();
        $this->action = $action;
        $this->databaseOperations();
    }

    function databaseOperations() {
        if (!isset($_POST[Document::ProjectID])) {
            // $this->projectID not set so load projects for project selection
            $this->projects = $this->projectModel->retrieveProjects();
            if (count($this->projects) == 0) $this->message = "No projects to view the documents of.";
        } else {
            switch ($this->action) {
                case Action::Upload:
                    if ($_POST[Form::SubmitData]) {
                        $this->checkFormData();
                        $this->checkFile();
                        if ($this->message == '') {
                            // do the document upload thing
                            $this->processFile();
                            $this->documentModel->insertDocument($_POST[Document::Title], $this->documentName, $_POST[Document::ProjectID]);
                            $this->message = 'Document Uploaded';
                        }
                    }
                    break;

                case Action::View:
                    // Establish $this->documents
                    break;

                case Action::Delete:

                    break;
            }
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