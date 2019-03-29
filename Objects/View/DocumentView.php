<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/03/2019
 * Time: 14:08
 */

namespace View;

require_once("Objects/Controller/DocumentController.php");

use Controller\DocumentController;
use Utils\Action;
use Utils\Document;
use Utils\Form;
use Utils\PageName;
use Utils\Project;
use Utils\User;

class DocumentView extends DocumentController
{
    private $html = array();

    public function __toString() {
        $this->displayHeader();
        if (isset($this->projectID)) {
            // With a projectID we can either view documents, or upload / delete a document
            switch ($this->action) {
                case Action::View:
                    $this->displayDocuments();
                    break;
                case Action::Upload:
                    $this->displayUploadForm();
                    break;
                case Action::Delete:

                    break;
            }
        } else {
            $this->selectProject();
        }

        return implode("\n", $this->html);
    }

    function displayHeader() {
        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project Documents", $this->message));

        // Navigation links
        if ($_SESSION[User::Role] == User::RoleLead || $_SESSION[User::Role] == User::RoleAdmin) {
            $navigationLinks = array('Upload Project Document' => 'index.php?page='. PageName::Document .'&action=' . Action::Upload,
                'View Project Documents' => 'index.php?page='. PageName::Document .'&action=' . Action::View,
                'Delete Project Document' => 'index.php?page='. PageName::Document .'&action=' . Action::Delete);
            $this->html = array_merge($this->html, $this->formComponents->addNavigationLinks($navigationLinks));
        }
    }

    function selectProject() {
        $this->html[] = '<form action ="index.php?page='. PageName::Document .'&action=' . $this->action . '" method="post">';
        $this->html[] = '<fieldset>';
            // Select Project
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project:', $this->projects));
        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select Project:"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

    function displayDocuments() {
        foreach ($this->documents as $document) {
            $this->html[] = '<p><a href="' . Document::Path . $document[Document::FileName] . '" target="_blank">' . $document[Document::Title] .'</a></p>';
            if ($this->canDeleteDocument) $this->html[] = '<p><a href="index.php?page=document&action='. Action::Delete .'&'. Document::ID .'=' . $document[Document::ID] . '&'. Project::ID .'=' . $this->projectID . '">Delete Document</a></p>';
        }
    }

    function displayUploadForm() {
        $this->html[] = '<form action="index.php?page='. PageName::Document .'&action=' . Action::Upload . '" method="post" enctype="multipart/form-data">';
        $this->html[] = '<fieldset>';
        $this->html = array_merge($this->html, $this->formComponents->uploadDocument($this->projectID));
        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';

    }
}