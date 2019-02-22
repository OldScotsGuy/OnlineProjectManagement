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
    private $project;
    private $firstDayTimestamp = false;
    private $lastDayTimestamp = false;
    private $html;

    // Constructor will have a default set of graphical options that could be overwritten if required (e.g. themes)
    public function __construct($project = null)
    {
        $this->project = $project;
        // find earliest and latest days
        $this->findDayRange();
    }

    // Find first and last days in teh project and store in timestamp form
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
    }

    // Create year, month and day banner
    private function createDateBanner() {
        // Get total number of days in project
        $dayInSeconds = 60*60*24;   //TODO make this a constant
        $numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp)/$dayInSeconds + 1;

        // Set initial number of days in month and year (for colspan)
        list($currentYear, $currentMonth) = explode("-",date('Y-F', $this->firstDayTimestamp));
        $numYearDays = 0;
        $numMonthDays = 0;

        // Create Year / Month / Day / Date headers
        $headerYear = '<tr><td class="chart-year" colspan = "';
        $headerMonth = '<tr><td class="chart-month" colspan = "';
        $headerDay = '<tr>';
        $headerDate = '<tr>';

        for($i=0; $i < $numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i*$dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            // Create day headers
            // ==================
            $headerDay .= '<td class="chart-day';
            $headerDate .= '<td class="chart-day';

            // Test for a weekend
            if (($tabDay == 'Sat') || ($tabDay == 'Sun')) {
                $headerDay .= ' weekend';
                $headerDate .= ' weekend';
            }

            // Test for today
            if ($tabYear . "-" . $tabMonth . "-". $tabDate == date('Y-F-d')) {
                $headerDay .= ' today';
                $headerDate .= ' today';
            }


            // Check Year and Month creating required <td></td> elements
            // =========================================================
            if ($i == $numDays-1) {
                // End of project reached - close month and year table elements
                $headerYear = $headerYear . ($numYearDays + 1).'">'. $currentYear .'</td>';
                $headerMonth = $headerMonth . ($numMonthDays + 1) .'">'. $currentMonth .'</td>';
            } else {
                // Not end of project
                if ($currentYear != $tabYear) {
                    // End of year reached
                    $headerYear = $headerYear . $numYearDays . '">' . $currentYear . '</td><td class="chart-year" colspan = "';
                    $currentYear = $tabYear;
                    $numYearDays = 1;
                } else {
                    // Not end of year so increment number of days in year
                    $numYearDays = $numYearDays + 1;
                }

                if ($currentMonth != $tabMonth) {
                    // End of Month reached
                    $headerMonth = $headerMonth . $numMonthDays . '">' . $currentMonth . '</td><td class="chart-month" colspan = "';
                    $currentMonth = $tabMonth;
                    $numMonthDays = 1;
                    $headerDay .= ' start';
                    $headerDate .= ' start';
                } else {
                    $numMonthDays = $numMonthDays + 1;
                }
            }

            $headerDay .= '">' . $tabDay .'</td>';
            $headerDate .= '">'. $tabDate . '</td>';

        }
        $headerYear = $headerYear . "</tr>";
        $headerMonth = $headerMonth . "</tr>";
        $headerDay = $headerDay . "</tr>";
        $headerDate = $headerDate . "</tr>";

        $this->html[] = "<style>table { width: ". ($numDays * 40) . "px; }</style>";
        $this->html[] = "<table>";
        $this->html[] = $headerYear;
        $this->html[] = $headerMonth;
        $this->html[] = $headerDay;
        $this->html[] = $headerDate;
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
        $currentMonth = date('F', $startTimeStamp);
        $tableRow = null;
        for ($i=0; $i < $numDays; $i++) {
            $timestamp = $startTimeStamp + $i*$dayInSeconds;
            $paddingMonth = date('F', $timestamp);
            if ($currentMonth != $paddingMonth) {
                $tableRow .= '<td class="chart-day start"></td>';
                $currentMonth = $paddingMonth;
            } else {
                $tableRow .= '<td class="chart-day"></td>';
            }
            //$tableRow .= '<td class="chart-day"></td>';
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