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
    private $dayInSeconds = 60 * 60 * 24;   //TODO make this a constant
    private $project = null;
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    private $numDays = null;
    private $yearHeader = null;
    private $monthHeader = null;
    private $dayHeader = null;
    private $dateHeader = null;
    private $dayStartTags = array();
    private $html = array();

    // Constructor will have a default set of graphical options that could be overwritten if required (e.g. themes)
    public function __construct($project = null)
    {
        $this->project = $project;

        $this->findDayRange();          // find earliest and latest days
        $this->createDayTagData();    // create array of day classes
        $this->createMonthYearTags();   // create date banner year and month tags
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

    private function createDayTagData()
    {

        for ($i = 0; $i < $this->numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i * $this->dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            $startTag = '<td class="chart-day';
            $startTag .= (($tabDay == 'Sat') || ($tabDay == 'Sun')) ? ' weekend' : '';                             // Test for a weekend
            $startTag .= ($tabYear . "-" . $tabMonth . "-" . $tabDate == date('Y-F-d')) ? ' today' : '';    // Test for today
            $startTag .= $this->isMonthStart($i,$tabMonth) ? ' start' : '';                                        // Test for month start
            $this->dayStartTags[] = $startTag . '">';

            $this->dayHeader .= $this->dayStartTags[$i] . $tabDay . '</td>';
            $this->dateHeader .= $this->dayStartTags[$i] . $tabDate . '</td>';
        }
    }

    private function createMonthYearTags() {
        $this->yearHeader = '<td class="chart-year" colspan = "';
        $this->monthHeader = '<td class="chart-month" colspan = "';
        // Set initial number of days in month and year (for colspan)
        list($currentYear, $currentMonth) = explode("-",date('Y-F', $this->firstDayTimestamp));
        $numYearDays = 0;
        $numMonthDays = 0;

        for ($i = 0; $i < $this->numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i*$this->dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            if ($i == $this->numDays-1) {
                // End of project reached - close month and year table elements
                $this->yearHeader .= ($numYearDays + 1).'">'. $currentYear .'</td>';
                $this->monthHeader .= ($numMonthDays + 1) .'">'. $currentMonth .'</td>';
            } else {
                // Not end of project
                if ($currentYear != $tabYear) {
                    // End of year reached
                    $this->yearHeader .= $numYearDays . '">' . $currentYear . '</td><td class="chart-year" colspan = "';
                    $currentYear = $tabYear;
                    $numYearDays = 1;
                } else {
                    // Not end of year so increment number of days in year
                    $numYearDays = $numYearDays + 1;
                }

                if ($currentMonth != $tabMonth) {
                    // End of Month reached
                    $this->monthHeader .= $numMonthDays . '">' . $currentMonth . '</td><td class="chart-month" colspan = "';
                    $currentMonth = $tabMonth;
                    $numMonthDays = 1;
                } else {
                    $numMonthDays = $numMonthDays + 1;
                }
            }
        }
    }

    private function isMonthStart($index, $month) {
        if ($index == 0) {
            return true;
        } else {
            $yesterdayMonth = date('F', ($this->firstDayTimestamp + ($index - 1) * $this->dayInSeconds));
            return !($month == $yesterdayMonth);
        }
    }

    // Create year, month and day banner
    private function createDateBanner() {
        $this->html[] = "<style>table { width: ". ($this->numDays * 40) . "px; }</style>";
        $this->html[] = "<table>";
        $this->html[] = '<tr>' . $this->yearHeader . '</tr>';
        $this->html[] = '<tr>' . $this->monthHeader . '</tr>';
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
        $this->createChartHeader();
        // Create Side label

        // Creats Gantt bars
        $this->createDateBanner();
        $this->drawTasks();
        $this->createChartFooter();
    }

    public function __toString()
    {
        $this->drawChart();
        return implode("",$this->html);
    }

}