<?php
/**
 * Created by Nick Harle
 * Date: 23/02/2019
 * Time: 21:10
 * This object:
 * 1) reads the project / task data via the Project / Task model objects
 * 2) parses the project data creating the information from which the HTML will be derived
 * 3) The GanttView child object (not this object) generates the HTML
 */

namespace Controller;

use Model\TaskModel;
use Model\ProjectModel;
use Utils\FormComponents;
use Utils\Project;
use Utils\Task;
use Utils\User;

require_once("Objects/Model/TaskModel.php");
require_once("Objects/Model/ProjectModel.php");

class GanttController
{
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant

    // Required Model Objects
    private $taskModel = null;
    private $projectModel = null;
    protected $formComponents = null;

    // Project Data
    protected $projects = null;
    protected $projectID = null;
    protected $project;

    // Task data
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    protected $numDays = null;          // Total number of days in Gantt Chart
    protected $taskData = array();
    protected $canEditTask = false;
    protected $canDeleteTask = false;

    // Date header data
    protected $yearStartData = array();
    protected $monthStartData = array();
    protected $dayClassifications = array();
    protected $dateClassifications = array();

    protected $message = null;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->taskModel = new TaskModel();

        $this->formComponents = new FormComponents();
        if (!isset($_GET[Project::ID])) {
            $this->projects = $this->projectModel->retrieveProjects();
            if (count($this->projects) == 0) $this->message = "No projects to view status of";
        } else {
            $this->projectID = $_GET[Project::ID];
            $this->project = $this->projectModel->retrieveProject($this->projectID);    // Retrieve project data
            $this->parseTaskData();             // Find earliest and latest days
            $this->parseProjectTimeData();      // Create array of day classes
        }
    }

    // Finds first and last task dates storing these as timestamps
    // Calculates number of days in project
    // Places task data into array
    private function parseTaskData()
    {
        $this->taskData = $this->taskModel->retrieveProjectTasks($this->projectID);

        // Find the start and finish project dates
        foreach ($this->taskData as $task)
        {
            $startTimestamp = strtotime($task[Task::StartDate]);
            $endTimestamp = strtotime($task[Task::EndDate]);
            if (!$this->firstDayTimestamp || $this->firstDayTimestamp > $startTimestamp) $this->firstDayTimestamp = $startTimestamp;
            if (!$this->lastDayTimestamp || $this->lastDayTimestamp < $endTimestamp) $this->lastDayTimestamp = $endTimestamp;
        }
        $this->numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds + 1;  // Get total number of days in project

        // Now reference each task from the project start date
        for ($index = 0; $index < count($this->taskData); $index++) {
            $startTimestamp = strtotime($this->taskData[$index][Task::StartDate]);
            $startIndex = ($startTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;   // Start day index (Project day 1 has zero)
            $endTimestamp = strtotime($this->taskData[$index][Task::EndDate]);
            $endIndex = ($endTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;       // End day index
            $this->taskData[$index][Task::Start] = $startIndex;
            $this->taskData[$index][Task::End] = $endIndex;
        }

        // Order tasks by task number
        usort($this->taskData, function ($a,$b) {return $a[Task::No] - $b[Task::No]; });

        // Set tasks delete privilege
        $this->canDeleteTask = ($_SESSION[User::Role] == User::RoleAdmin || $_SESSION[User::Role] == User::RoleLead);
    }

    // Classifies days according to weekend / today / month start
    // Creates the data to build the year and month representation
    private function parseProjectTimeData()
    {
        for ($i = 0; $i < $this->numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i * $this->dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            $dayClass = 'chart-day';
            $dateClass = 'chart-date';

            $additionalClasses = (($tabDay == 'Sat') || ($tabDay == 'Sun')) ? ' weekend' : '';                                 // Test for a weekend
            $additionalClasses .= ($tabYear . "-" . $tabMonth . "-" . $tabDate == date('Y-F-d')) ? ' today' : '';       // Test for today
            if ($this->isStart($i,$tabMonth, 'F')) {                                                                    // Test for month start
                $additionalClasses .= ' start';
                $this->monthStartData[] = array($tabMonth, $i);                         // store month data for constructing month header
            }

            $dayClass .= $additionalClasses;
            $dateClass .= $additionalClasses;

            if ($this->isStart($i,$tabYear, 'Y')) {
                $this->yearStartData[] = array($tabYear, $i);                           // store year data for constructing year header
            }
            $this->dayClassifications[] = array($dayClass, $tabDay);          // Store classification, day for display use
            $this->dateClassifications[] = array($dateClass, $tabDate);        // Store classification, date for display use
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