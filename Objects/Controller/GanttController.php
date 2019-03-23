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

require_once("Objects/Model/TaskModel.php");
require_once("Objects/Model/ProjectModel.php");

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

    // Project Data
    protected $projectID = null;
    protected $project;

    // Task data
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    protected $numDays = null;          // Total number of days in Gantt Chart
    protected $taskData = array();

    // Date header data
    protected $yearStartData = array();
    protected $monthStartData = array();
    protected $dayClassifications = array();

    public function __construct($projectID = 2)     // TODO change this for project selection
    {
        $this->projectModel = new ProjectModel();
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
        $this->taskData = $this->taskModel->retrieveProjectTasks($this->projectID);
        /*$tasks = array( array('taskID' => 1, 'taskName' => 'Specification', 'startDate' => '2019-01-28', 'endDate' => '2019-02-03', 'percent' => 100, 'taskNo' => 1, 'notes' => 'Understand user requirements', 'projectID' => 2, 'email' => 'asde@asde', 'owner' => 'John'),
                        array('taskID' => 2, 'taskName' => 'panic', 'startDate' => '2019-02-07', 'endDate' => '2019-02-14', 'percent' => 100, 'taskNo' => 2, 'notes' => 'Realised the amount of work required', 'projectID' => 2, 'email' => 'asde@asde', 'owner' => 'John'),
                        array('taskID' => 3, 'taskName' => 'Coding', 'startDate' => '2019-02-15', 'endDate' => '2019-03-07', 'percent' => 50, 'taskNo' => 3, 'notes' => 'The fun bit .. when everything works', 'projectID' => 2, 'email' => 'asde@asde', 'owner' => 'John')
                      ); */

        // Find the start and finish project dates
        foreach ($this->taskData as $task)
        {
            $startTimestamp = strtotime($task['startDate']);
            $endTimestamp = strtotime($task['endDate']);
            if (!$this->firstDayTimestamp || $this->firstDayTimestamp > $startTimestamp) $this->firstDayTimestamp = $startTimestamp;
            if (!$this->lastDayTimestamp || $this->lastDayTimestamp < $endTimestamp) $this->lastDayTimestamp = $endTimestamp;
        }
        $this->numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds + 1;  // Get total number of days in project

        // Now reference each task from the project start date
        for ($index = 0; $index < count($this->taskData); $index++) {
            $startTimestamp = strtotime($this->taskData[$index]['startDate']);
            $startIndex = ($startTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;   // Start day index (Project day 1 has zero)
            $endTimestamp = strtotime($this->taskData[$index]['endDate']);
            $endIndex = ($endTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;       // End day index
            $this->taskData[$index]["start"] = $startIndex;
            $this->taskData[$index]["end"] = $endIndex;
        }

        // Order tasks by task number
        usort($this->taskData, function ($a,$b) {return $a['taskNo'] - $b['taskNo']; });
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