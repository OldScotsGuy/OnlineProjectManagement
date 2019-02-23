<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/02/2019
 * Time: 21:24
 */

namespace View;

require_once ("GanttData.php");

// Creates the HTML to display the Gantt chart as a table
// Uses the data parsed from the project object by the GanttData object
// Author: Nick Harle
// Date:   23/02/2019

class GanttTable extends GanttData
{
    private $taskRows = array();
    private $dayHeader = null;
    private $dateHeader = null;
    private $html = array();

    // Create the Gantt chart day and date headers via the stored parsed data
    private function createDayDateHeaders()
    {
        for ($i = 0; $i < $this->numDays; $i++) {
            $startTag = '<td class="' . $this->dayClassifications[$i][0] . '">';
            $this->dayHeader .= $startTag . $this->dayClassifications[$i][1] . '</td>';     // Construct Gantt table day header
            $this->dateHeader .= $startTag . $this->dayClassifications[$i][2] . '</td>';    // Construct Gantt table date header
        }
    }

    // Create the Gantt chart month and year headers via the stored parsed data
    private function createHeaderTags($periodStartData, $class)
    {
        $periodHeader = null;
        $numPeriods = sizeof($periodStartData);
        $periodStartData[] = array('End', $this->numDays);
        for ($period = 0; $period < $numPeriods; $period++) {
            $periodStartDay = $periodStartData[$period][1];
            $periodEndDay = $periodStartData[$period + 1][1];
            $periodHeader .= '<td class="' . $class . '" colspan = "' . ($periodEndDay - $periodStartDay) . '">';
            $periodHeader .= $periodStartData[$period][0] . '</td>';
        }
        return $periodHeader;
    }

    // Create task row data
    private function createTaskRows()
    {
        foreach ($this->taskData as $task) {
            $row = null;
            $row = $this->addPaddingDays(0, $task['start']);                                // Add before task padding days
            $row .= '<td colspan ="' . ($task['end'] - $task['start'] + 1) . '">TASK</td>'; // Add task colspan element
            $row .= $this->addPaddingDays($task['end'] + 1, $this->numDays);                // Add after task padding days
            $this->taskRows[] = $row;
        }
    }

    // Used to add cells before and after the tasks
    private function addPaddingDays($start, $end)
    {
        $padding = null;
        for ($i = $start; $i < $end; $i++) {
            $padding .= '<td class="' . $this->dayClassifications[$i][0] . '">' . '</td>';
        }
        return $padding;
    }

    // Create year, month and day banner
    private function createGanttTable()
    {
        $this->html[] = "<figure class='chart'>";
        $this->html[] = "<figcaption>Project Title: " . $this->project->title . "</figcaption>";
        $this->html[] = "<style>table { width: " . ($this->numDays * 40) . "px; }</style>";
        $this->html[] = "<table>";
        $this->html[] = '<tr>' . $this->createHeaderTags($this->yearStartData, 'chart-year') . '</tr>';
        $this->html[] = '<tr>' . $this->createHeaderTags($this->monthStartData, 'chart-month') . '</tr>';
        $this->html[] = '<tr>' . $this->dayHeader . '</tr>';
        $this->html[] = '<tr>' . $this->dateHeader . '</tr>';
        foreach ($this->taskRows as $row) {
            $this->html[] = '<tr>' . $row . '</tr>';
        }
        $this->html[] = "</table>";
        $this->html[] = "</figure>";
    }

    // Draw chart function
    private function createTable()
    {
        $this->createTaskRows();
        $this->createDayDateHeaders();
        $this->createGanttTable();
    }

    public function __toString()
    {
        $this->createTable();
        return implode("", $this->html);
    }

}
