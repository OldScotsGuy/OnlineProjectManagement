<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/03/2019
 * Time: 14:08
 */

namespace View;

use Utils\Action;
use Utils\Document;
use Utils\Project;

class DocumentView
{
    private $html = array();

    // Variables to be evaluate by the controller
    private $projectID = null;
    private $projects = null;
    private $documents = array();

    private function selectProject() {
        $this->html[] = '<form action ="index.php?page=document&action=' . Action::View . '" method="post">';
        $this->html[] = '<p>' . $this->message . '</p>';
        $this->html[] = '<label for="projectID">Which Project Do You want to View The Documents of? </label>';
        $select = '<select name = "projectID" id="projectID"';
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
        $this->html[] = '<br><br><input type="submit" name="submit" value="Select Project"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';

        $this->html[] = '</form>';
    }

    private function displayDocuments() {
        foreach ($this->documents as $document) {
            echo '<p><a href="documents/' . $document[Document::FileName] . '" target="_blank">' . $document[Document::Title] .'</a></p>';
        }
}

    public function __toString()
    {
        if (isset($this->projectID)) {
            $this->displayDocuments();
        } else {
            $this->selectProject();
        }

        return implode("\n", $this->html);
    }

}