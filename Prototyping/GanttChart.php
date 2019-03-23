<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 16/02/2019
 * Time: 10:09
 */

namespace View;


class GanttChart
{
    // Constant
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant

    // Project data assembled during constructor call
    // ==============================================
    private $project = null;

    // Task data
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    private $numDays = null;
    private $taskData = array();
    private $taskRows = array();

    // Date header data
    private $yearStartData = array();
    private $monthStartData = array();
    private $dayHeader = null;
    private $dateHeader = null;
    private $dayStartTags = array();

    // HTML assembled during draw call
    // ===========================
    private $html = array();

    // Constructor will have a default set of graphical options that could be overwritten if required (e.g. themes)
    public function __construct($project = null)
    {
        $this->project = $project;
        $this->parseTaskData();          // find earliest and latest days
        $this->parseProjectTimeData();      // create array of day classes
    }

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

    private function parseProjectTimeData()
    {
        for ($i = 0; $i < $this->numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i * $this->dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            $startTag = '<td class="chart-day';
            $startTag .= (($tabDay == 'Sat') || ($tabDay == 'Sun')) ? ' weekend' : '';                              // Test for a weekend
            $startTag .= ($tabYear . "-" . $tabMonth . "-" . $tabDate == date('Y-F-d')) ? ' today' : '';     // Test for today
            if ($this->isStart($i,$tabMonth, 'F')) {                                                         // Test for month start
                $startTag .= ' start';
                $this->monthStartData[] = array($tabMonth, $i);     // store month data for constructing month header
            }

            if ($this->isStart($i,$tabYear, 'Y')) {
                $this->yearStartData[] = array($tabYear, $i);       // store year data for constructing month header
            }

            $this->dayStartTags[] = $startTag . '">';               // Store day start tag for task display use
            $this->dayHeader .= $this->dayStartTags[$i] . $tabDay . '</td>';    // Construct Gantt table day header
            $this->dateHeader .= $this->dayStartTags[$i] . $tabDate . '</td>';  // Construct Gantt table date header
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

    // Used to create the Gantt chart month and year headers via the stored parsed data
    private function createHeaderTags($periodStartData, $class) {
        $periodHeader = null;
        $numPeriods = sizeof($periodStartData);
        $periodStartData[] = array('End', $this->numDays);
        for ($period = 0; $period < $numPeriods ; $period++) {
            $periodStartDay = $periodStartData[$period][1];
            $periodEndDay = $periodStartData[$period+1][1];
            $periodHeader .= '<td class="' . $class . '" colspan = "' . ($periodEndDay - $periodStartDay) . '">';
            $periodHeader .= $periodStartData[$period][0] . '</td>';
        }
        return $periodHeader;
    }

     // Plot tasks
    private function createTaskRows() {
        // TODO order $this->taskData according to task number

        // Create task row data
        foreach ($this->taskData as $task) {
            $row = null;
            $row = $this->addPaddingDays(0, $task['start']);                                // Add before task padding days
            $row .= '<td colspan ="' . ($task['end'] - $task['start'] + 1) . '">TASK</td>'; // Add task colspan element
            $row .= $this->addPaddingDays($task['end']+1, $this->numDays);                  // Add after task padding days
            $this->taskRows[] = $row;
        }
    }

    private function addPaddingDays($start, $end) {
        $padding = null;
        for ($i = $start; $i < $end ; $i++ ) {
            $padding .= $this->dayStartTags[$i] . '</td>';
        }
        return $padding;
    }

    // Create chart header
    private function createChartHeader() {
        $this->html[] = "<figure class='chart'>";
        $this->html[] = "<figcaption>Project Title: ". $this->project->title . "</figcaption>";
    }

    // Create year, month and day banner
    private function createGanttTable() {
        $this->html[] = "<style>table { width: ". ($this->numDays * 40) . "px; }</style>";
        $this->html[] = "<table>";
        $this->html[] = '<tr>' . $this->createHeaderTags($this->yearStartData, 'chart-year') . '</tr>';
        $this->html[] = '<tr>' . $this->createHeaderTags($this->monthStartData, 'chart-month') . '</tr>';
        $this->html[] = '<tr>' . $this->dayHeader . '</tr>';
        $this->html[] = '<tr>' . $this->dateHeader . '</tr>';
        foreach ($this->taskRows as $row) {
            $this->html[] = '<tr>' . $row . '</tr>';
        }
        $this->html[] = "</table>";
    }

    // Create chart footer
    private function createChartFooter() {
        $this->html[] = "</figure>";
    }

    // Draw chart function
    private function drawChart() {
        $this->html = array();

        $this->createChartHeader();
        $this->createTaskRows();
        $this->createGanttTable();
        $this->createChartFooter();
    }

    public function __toString()
    {
        $this->drawChart();
        return implode("",$this->html);
    }

}