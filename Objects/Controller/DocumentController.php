<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/03/2019
 * Time: 15:33
 */

namespace Controller;

use Model\DocumentModel;

require_once("Objects/Model/DocumentModel.php");

class DocumentController
{
    private $documentModel = null;

    protected $projectID = null;
    protected $projects = null;
    protected $documents = array();
    protected $action = null;

    function __construct($action) {
        $this->documentModel = new DocumentModel();
        $this->action = $action;
        $this->databaseOperations();
    }

    function databaseOperations() {

    }

}