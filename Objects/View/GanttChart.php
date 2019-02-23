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
    // Project data assembled during constructor call
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant
    private $project = null;
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    private $numDays = null;
    private $yearStartData = array();
    private $monthStartData = array();

    // HTML Strings
    private $dayHeader = null;
    private $dateHeader = null;
    private $dayStartTags = array();
    private $html = array();

    // Constructor will have a default set of graphical options that could be overwritten if required (e.g. themes)
    public function __construct($project = null)
    {
        $this->project = $project;

        $this->findDayRange();          // find earliest and latest days
        $this->parseProjectTimeData();      // create array of day classes
        // order tasks
    }

    private function findDayRange()
    {
        $tasks = $this->project->tasks;
        foreach ($tasks as $task)
        {
            $startTimestamp = strtotime($task->start);
            $endTimestamp = strtotime($task->end);
            if (!$this->firstDayTimestamp || $this->firstDayTimestamp > $startTimestamp) $this->firstDayTimestamp = $startTimestamp;
            if (!$this->lastDayTimestamp || $this->lastDayTimestamp < $endTimestamp) $this->lastDayTimestamp = $endTimestamp;
        }
        $this->numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp) / $this->dayInSeconds + 1;  // Get total number of days in project
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
            $this->dayHeader .= $this->dayStartTags[$i] . $tabDay . '</td>';
            $this->dateHeader .= $this->dayStartTags[$i] . $tabDate . '</td>';
        }
    }

    private function isStart($index, $current, $format) {
        if ($index == 0) {
            return true;
        } else {
            $previous = date($format, ($this->firstDayTimestamp + ($index - 1) * $this->dayInSeconds));
            return !($current == $previous);
        }
    }

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

    // Create year, month and day banner
    private function createDateBanner() {
        $this->html[] = "<style>table { width: ". ($this->numDays * 40) . "px; }</style>";
        $this->html[] = "<table>";
        $this->html[] = '<tr>' . $this->createHeaderTags($this->yearStartData, 'chart-year') . '</tr>';
        $this->html[] = '<tr>' . $this->createHeaderTags($this->monthStartData, 'chart-month') . '</tr>';
        $this->html[] = '<tr>' . $this->dayHeader . '</tr>';
        $this->html[] = '<tr>' . $this->dateHeader . '</tr>';
    }

     // Plot tasks
    private function drawTasks() {
        $dayInSeconds = 60*60*24;   //TODO make this a constant
        foreach ($this->project->tasks as $task) {
            $taskStartTimestamp = strtotime($task->start);
            $taskEndTimestamp = strtotime($task->end);
            $daysToTaskStart = ($taskStartTimestamp - $this->firstDayTimestamp) / $dayInSeconds;
            $daysAfterTaskEnd = ($this->lastDayTimestamp - $taskEndTimestamp) / $dayInSeconds;
            $taskLength = ($taskEndTimestamp - $taskStartTimestamp) / $dayInSeconds + 1;

            $taskRow = '<tr>';
            // Test for front end padding
            if ($daysToTaskStart > 0) {
                $taskRow .= $this->addPaddingdays($this->firstDayTimestamp, $daysToTaskStart);
            }
            // Add task
            $taskRow .= '<td colspan ="' . $taskLength . '">TASK</td>';

            // Test for padding after task
            if ($daysAfterTaskEnd > 0) {
                $taskRow .= $this->addPaddingdays($taskEndTimestamp + $dayInSeconds, $daysAfterTaskEnd);
            }
            $taskRow = $taskRow . '</tr>';
            $this->html[] = $taskRow;
        }
        $this->html[] = "</table>";
    }

    private function addPaddingdays($startTimeStamp, $numDays) {

        $dayInSeconds = 60*60*24;   //TODO make this a constant
        list($currentMonth, $currentDay) = explode("-", date('F-D', $startTimeStamp));
        //$currentMonth = date('F', $startTimeStamp);
        $tableRow = null;
        for ($i=0; $i < $numDays; $i++) {
            $timestamp = $startTimeStamp + $i*$dayInSeconds;
            list($paddingMonth, $paddingDay) = explode("-", date('F-D', $timestamp));

            $tableRow .= '<td class="chart-day';

            // Test for month start
            if ($currentMonth != $paddingMonth) {
                $tableRow .= ' start';
                $currentMonth = $paddingMonth;
            }

            // Test for weekend
            if (($paddingDay == 'Sat') || ($paddingDay == 'Sun')) {
                $tableRow .= ' weekend';
                $tableRow .= ' weekend';
            }

            $tableRow .= '"></td>';
        }
        return $tableRow;
    }

    // Create chart header
    private function createChartHeader() {
        $this->html[] = "<figure class='chart'>";
        $this->html[] = "<figcaption>Project Title: ". $this->project->title . "</figcaption>";
    }

    // Create chart footer
    private function createChartFooter() {
        $this->html[] = "</figure>";
    }

    // Draw chart function
    private function drawChart() {
        $this->html = array();
        
        // Create Header
        $this->createChartHeader();
        // Create Side label

        // Create Gantt chart
        $this->createDateBanner();
        $this->drawTasks();

        // Create Footer
        $this->createChartFooter();
    }

    public function __toString()
    {
        $this->drawChart();
        return implode("",$this->html);
    }

}