<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/02/2019
 * Time: 21:10
 */

namespace Controller;

use Model\TaskModel;
use Model\ProjectModel;
use Model\UserModel;

require_once("Objects/Model/TaskModel.php");
require_once("Objects/Model/ProjectModel.php");
require_once("Objects/Model/UserModel.php");

// This object parses the project object extracting the data from which the HTML will be derived
// The parsed data is stored in the member variables which are inherited
// The child object (not this object) will construct the HTML
// Author: Nick Harle
// Date:   23/02/2019

class GanttController
{
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant

    // Required Model Objects
    private $taskModel = null;
    private $projectModel = null;
    private $userModel = null;

    // Project Data
    protected $projectID = null;
    protected $project;

    // Task data
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    protected $numDays = null;
    protected $taskData = array();

    // Date header data
    protected $yearStartData = array();
    protected $monthStartData = array();
    protected $dayClassifications = array();

    public function __construct($projectID = 2)     // TODO change this for project selection
    {
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();

        $this->projectID = $projectID;
        $this->project = $this->projectModel->retrieveProject($this->projectID);    // Retrieve project data
        $this->parseTaskData();             // Find earliest and latest days
        $this->parseProjectTimeData();      // Create array of day classes
    }

    // Finds first and last task dates storing these as timestamps
    // Calculates number of days in project
    // Places task data into array
    private function parseTaskData()
    {
        //$tasks = $this->project->tasks;
        $tasks = $this->taskModel->retrieveProjectTasks($this->projectID);

        // Find the start and finish project dates
        foreach ($tasks as $task)
        {
            $startTimestamp = strtotime($task['startDate']);
            $endTimestamp = strtotime($task['endDate']);
            if (!$this->firstDayTimestamp || $this->firstDayTimestamp > $startTimestamp) $this->firstDayTimestamp = $startTimestamp;
            if (!$this->lastDayTimestamp || $this->lastDayTimestamp < $endTimestamp) $this->lastDayTimestamp = $endTimestamp;
        }
        $this->numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds + 1;  // Get total number of days in project

        // Now reference each task from the project start date
        foreach ($tasks as $task) {
            $startTimestamp = strtotime($task['startDate']);
            $startIndex = ($startTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;
            $endTimestamp = strtotime($task['endDate']);
            $endIndex = ($endTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;
            $this->taskData[] = array("start" => $startIndex, "end" => $endIndex, "name" => $task['taskName'], "num" => $task['taskNo'], "owner" => $task['owner'], "notes" => $task['notes'], "percent" => $task['percent']);
        }

        // Order tasks by task number
        usort($this->taskData, function ($a,$b) {return $a['num'] - $b['num']; });
    }

    // Classifies days according to weekend / today / month start
    // Creates the data to build the year and month representation
    private function parseProjectTimeData()
    {
        for ($i = 0; $i < $this->numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i * $this->dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            $dayClass = 'chart-day';
            $dayClass .= (($tabDay == 'Sat') || ($tabDay == 'Sun')) ? ' weekend' : '';                              // Test for a weekend
            $dayClass .= ($tabYear . "-" . $tabMonth . "-" . $tabDate == date('Y-F-d')) ? ' today' : '';     // Test for today
            if ($this->isStart($i,$tabMonth, 'F')) {                                                         // Test for month start
                $dayClass .= ' start';
                $this->monthStartData[] = array($tabMonth, $i);                         // store month data for constructing month header
            }

            if ($this->isStart($i,$tabYear, 'Y')) {
                $this->yearStartData[] = array($tabYear, $i);                           // store year data for constructing year header
            }
            $this->dayClassifications[] = array($dayClass, $tabDay, $tabDate); // Store classification, day, date for display use
        }
    }

    // Used to test if a month or year has changed when iterating through days
    private function isStart($index, $current, $format) {
        if ($index == 0) {
            return true;
        } else {
            $previous = date($format, ($this->firstDayTimestamp + ($index - 1) * $this->dayInSeconds));
            return !($current == $previous);
        }
    }

}