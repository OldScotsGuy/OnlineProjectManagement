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

    function __construct($action) {
        $this->documentModel = new DocumentModel();
        $this->projectModel = new ProjectModel();
        $this->action = $action;
        $this->databaseOperations();
    }

    function databaseOperations() {
        $this->projects = $this->projectModel->retrieveProjects();
        if (count($this->projects) == 0) $this->message = "No projects to assign task to";

        switch ($this->action) {
            case Action::Upload:

                break;

            case Action::View:

                break;

            case Action::Delete:

                break;
        }
    }

}