<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/02/2019
 * Time: 21:10
 */

namespace View;

// This object parses the project object extracting the data from which the HTML will be derived
// The parsed data is stored in the member variables which are inherited
// The child object (not this object) will construct the HTML
// Author: Nick Harle
// Date:   23/02/2019

class GanttData
{
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant
    protected $project = null;

    // Task data
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    protected $numDays = null;
    protected $taskData = array();

    // Date header data
    protected $yearStartData = array();
    protected $monthStartData = array();
    protected $dayClassifications = array();

    public function __construct($project = null)
    {
        $this->project = $project;
        $this->parseTaskData();          // find earliest and latest days
        $this->parseProjectTimeData();      // create array of day classes
    }

    // Finds first and last task dates storing these as timestamps
    // Calculates number of days in project
    // Places task data into array - TODO sort tasks according to task number
    private function parseTaskData()
    {
        $tasks = $this->project->tasks;

        // Find the start and finish project dates
        foreach ($tasks as $task)
        {
            $startTimestamp = strtotime($task->start);
            $endTimestamp = strtotime($task->end);
            if (!$this->firstDayTimestamp || $this->firstDayTimestamp > $startTimestamp) $this->firstDayTimestamp = $startTimestamp;
            if (!$this->lastDayTimestamp || $this->lastDayTimestamp < $endTimestamp) $this->lastDayTimestamp = $endTimestamp;
        }
        $this->numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds + 1;  // Get total number of days in project

        // Now reference each task from the project start date
        foreach ($tasks as $task) {
            $startTimestamp = strtotime($task->start);
            $startIndex = ($startTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;
            $endTimestamp = strtotime($task->end);
            $endIndex = ($endTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds;
            $this->taskData[] = array("start" => $startIndex, "end" => $endIndex, "name" => $task->name , "num" => $task->taskNo);
        }
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