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
use Utils\Project;

class DocumentView extends DocumentController
{
    private $html = array();

    public function __toString() {
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

    function selectProject() {
        $this->html[] = '<form action ="index.php?page=document&action=' . Action::View . '" method="get">';
        $this->html[] = '<p>' . $this->message . '</p>';
        $this->html[] = '<label for="' . Project::ID . '">Which Project Do You want to View The Documents of? </label>';
        $select = '<select name = "' . Project::ID . '" id="' . Project::ID . '"';
        if (count($this->projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $this->html[] = $select;
        foreach ($this->projects as $project) {
            $this->html[] = '<option value = "'. $project[Project::ID] .'">Title: ' . $project[Project::Title] . ' Project Lead: ' . $project[Project::Lead] . '  Project Lead Email: ' . $project[Project::LeadEmail] .'</option>';
        }
        $this->html[] = '</select>';

        // Disable the submit button if no projects present
        $this->html[] = '<br><br><input type="submit" name="' . Form::SubmitSelection . '" value="Select Project"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';

        $this->html[] = '</form>';
    }

    function displayDocuments() {
        foreach ($this->documents as $document) {
            $this->html[] = '<p><a href="' . Document::Path . $document[Document::FileName] . '" target="_blank">' . $document[Document::Title] .'</a></p>';
            $this->html[] = '<p><a href="index.php?page=document&action='. Action::Delete .'&'. Document::ID .'=' . $document[Document::ID] . '&'. Project::ID .'=' . $this->projectID . '">Delete Task</a></p>';
        }
    }

    function displayUploadForm() {
        $this->html[] = '<form action="index.php?page=document&action=' . Action::Upload . '" method="post" enctype="multipart/form-data">';
        $this->html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $this->projectID . '"/>';   // Carry ProjectID across
        $this->html[] = '<label for="' . Document::Title . '">Document Title:</label>';
        $this->html[] = '<input type="text" name="' . Document::Title . '" id="' . Document::Title . '" size="100"  maxlength="1280" required />';
        $this->html[] = '<br><br><input type="file" name="' . Document::FileName . '" required />';
        $this->html[] = '<input type="submit" name="' . Form::SubmitData . '" value="Upload" />';
        $this->html[] = '</form>';

    }
}