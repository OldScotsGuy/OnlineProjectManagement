<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 14:08
 *
 * This object generates the html required to upload / view / delete documents.
 * Which html form / view is generated based on the object variables inherited from the DocumentController object
 * The presentation can be changed without any requirement to change the Controller object
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
                    // This should not be reached as delete is only called from View page anchor tag
                    // The controller then redirects as a view after the document deletion
                    break;
            }
        } else {
            $this->selectProject();
        }

        return implode("\n", $this->html);
    }

    function displayHeader() {

        // Navigation links
        if ($_SESSION[User::Role] == User::RoleLead || $_SESSION[User::Role] == User::RoleAdmin) {
            $navigationLinks = array('Upload Project Document' => 'index.php?page='. PageName::Document .'&action=' . Action::Upload,
                'View Project Documents' => 'index.php?page='. PageName::Document .'&action=' . Action::View);
            $this->html = array_merge($this->html, $this->formComponents->addNavigationLinks($navigationLinks));
        }
    }

    function selectProject() {
        $this->html[] = '<form action ="index.php?page='. PageName::Document .'&action=' . $this->action . '" method="post">';
        $this->html[] = '<fieldset>';

        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project Documents", $this->message));

        // Select Project
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project:', $this->projects));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select Project:"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

    function displayDocuments() {
        $this->html[] = '<div id="documents">';
        $this->html[] = '<fieldset>';

        if (count($this->documents) == 0) {
            $this->message = '<p>No documents associated with this project</p>';
        }

        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project Documents", $this->message));

        foreach ($this->documents as $document) {
            $this->html[] = '<div class="row">';
            $this->html[] = '<a href="' . Document::Path . $document[Document::FileName] . '" target="_blank">View</a>';
            if ($this->canDeleteDocument) $this->html[] = '<a href="index.php?page='. PageName::Document .'&action='. Action::Delete .'&'. Document::ID .'=' . $document[Document::ID] . '&'. Project::ID .'=' . $this->projectID . '">Delete</a>';
            $this->html[] = '<span>Title:<i> ' . $document[Document::Title].'</i></span>';
            $this->html[] = '</div>';
        }

        $this->html[] = '</fieldset>';
        $this->html[] = '</div>';
    }

    function displayUploadForm() {
        $this->html[] = '<form action="index.php?page='. PageName::Document .'&action=' . Action::Upload . '" method="post" enctype="multipart/form-data">';
        $this->html[] = '<fieldset>';

        // Title and Message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Project Documents", $this->message));
        $this->html = array_merge($this->html, $this->formComponents->uploadDocument($this->projectID));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, "Upload File"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';

    }
}