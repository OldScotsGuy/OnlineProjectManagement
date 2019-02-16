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
    private $html = array();

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
        $dayInSeconds = 60*60*24;
        $numDays = ($this->lastDayTimestamp - $this->firstDayTimestamp)/$dayInSeconds + 1;

        // Set initial number of days in month and year (for colspan)
        list($currentYear, $currentMonth) = explode("-",date('Y-F', $this->firstDayTimestamp));
        $numYearDays = 1;
        $numMonthDays = 1;

        // Create Year / Month / Day / Date headers
        $headerYear = '<tr><td colspan = "';
        $headerMonth = '<tr><td colspan = "';
        $headerDay = "<tr>";
        $headerDate = "<tr>";

        for($i=0; $i < $numDays; $i++) {
            $timestamp = $this->firstDayTimestamp + $i*$dayInSeconds;
            list($tabYear, $tabMonth, $tabDay, $tabDate) = explode("-", date('Y-F-D-d', $timestamp));

            // Create day headers
            $headerDay = $headerDay . "<td>{$tabDay}</td>";
            $headerDate = $headerDate . "<td>{$tabDate}</td>";

            // Check Year and Month creating required <td></td> elements
            if ($i == $numDays-1) {
                // End of project reached - close month and year table elements
                $headerYear = $headerYear . $numYearDays.'">'. $currentYear .'</td>';
                $headerMonth = $headerMonth . $numMonthDays.'">'. $currentMonth .'</td>';
            } else {
                // Not end of project
                if ($currentYear != $tabYear) {
                    // End of year reached
                    $headerYear = $headerYear . $numYearDays . '">' . $currentYear . '</td><td colspan = "';
                    $currentYear = $tabYear;
                    $numYearDays = 1;
                } else {
                    // Not end of year so increment number od days in year
                    $numYearDays = $numYearDays + 1;
                }

                if ($currentMonth != $tabMonth) {
                    // End of Month reached
                    $headerMonth = $headerMonth . $numMonthDays . '">' . $currentMonth . '</td><td colspan = "';
                    $currentMonth = $tabMonth;
                    $numMonthDays = 1;
                } else {
                    $numMonthDays = $numMonthDays + 1;
                }
            }
        }
        $headerYear = $headerYear . "</tr>";
        $headerMonth = $headerMonth . "</tr>";
        $headerDay = $headerDay . "</tr>";
        $headerDate = $headerDate . "</tr>";

        $this->html[] = $headerYear;
        $this->html[] = $headerMonth;
        $this->html[] = $headerDay;
        $this->html[] = $headerDate;
    }

    // Plot tasks

    // Create chart header
    private function createChartHeader() {
        $this->html[] = "<figure>";
        $this->html[] = "<h2>{$this->project->title}</h2>";
        $this->html[] = "<table>";
    }

    // Create chart footer
    private function createChartFooter() {
        $this->html[] = "</table>";
        $this->html[] = "</figure>";
    }

    // Draw chart function
    private function drawChart() {
        $this->html = array();
        $this->createChartHeader();
        $this->createDateBanner();
        // Draw tasks
        $this->createChartFooter();
    }

    public function __toString()
    {
        $this->drawChart();
        return implode('',$this->html);
    }

}