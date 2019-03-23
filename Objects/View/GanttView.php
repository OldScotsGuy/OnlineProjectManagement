<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/02/2019
 * Time: 21:24
 */

namespace View;

require_once("Objects/Controller/GanttController.php");

use Controller\GanttController;

// Creates the HTML to display the Gantt chart as a table
// Uses the data parsed from the project object by the GanttController object
// Author: Nick Harle
// Date:   23/02/2019

class GanttView extends GanttController
{
    private $taskSideBarRows = array();
    private $taskRows = array();
    private $dayHeader = null;
    private $dateHeader = null;
    private $html = array();

/*    private function createTaskSideBarRows() {

        $row = '<tr><td class="side-heading">Task Name</td>';
        $row .= '<td class="side-heading">Owner</td></tr>';
        $this->taskSideBarRows[] = $row;

        foreach ($this->taskData as $task) {
            $row = '<tr><td class="side-name">' . $task['name'] . '</td>';
            $row .= '<td class="side-owner">' . $task['owner'] . '</td></tr>';
            $this->taskSideBarRows[] = $row;
        }
    } */


    // Create the Gantt chart day and date headers via the stored parsed data
    private function createDayDateHeaders()
    {
        $this->dayHeader = '<th></th>';
        $this->dateHeader = '<th class="side-heading">Task : Owner</th>';
        //$startTag = '';
        for ($i = 0; $i < $this->numDays; $i++) {
            $startTag = '<th class="' . $this->dayClassifications[$i][0] . '">';
            $this->dayHeader .= $startTag . $this->dayClassifications[$i][1] . '</th>';     // Construct Gantt table day header
            $this->dateHeader .= $startTag . $this->dayClassifications[$i][2] . '</th>';    // Construct Gantt table date header
        }
    }

    // Create the Gantt chart month and year headers via the stored parsed data
    private function createHeaderTags($periodStartData, $class)
    {
        $periodHeader = '<th></th>';
        //$periodHeader = null;
        $numPeriods = sizeof($periodStartData);
        $periodStartData[] = array('End', $this->numDays);
        for ($period = 0; $period < $numPeriods; $period++) {
            $periodStartDay = $periodStartData[$period][1];
            $periodEndDay = $periodStartData[$period + 1][1];
            $periodHeader .= '<th class="' . $class . '" colspan = "' . ($periodEndDay - $periodStartDay) . '">';
            $periodHeader .= $periodStartData[$period][0] . '</th>';
        }
        return $periodHeader;
    }

    // Create task row data
    private function createTaskRows()
    {
        foreach ($this->taskData as $task) {

            // Add task details
            $row = '<tr><td class="side-name">' . $task['name'] . ' : ' . $task['owner'] . '</td>';
            //$row = null;

            // Add before task padding days
            $row .= $this->addPaddingDays(0, $task['start']);

            // Add task details
            $row .= '<td colspan ="' . ($task['end'] - $task['start'] + 1) . '">';
            $row .= '<div class ="chart-task"><div class="chart-fill" style="width: ' . $task['percent'] .'%">';
            $row .= '</div></div></td>';

            // Add after task padding days
            $row .= $this->addPaddingDays($task['end'] + 1, $this->numDays)  . '</tr>';

            // Add notes row under task detail
            $row .= '<tr class="task-notes"><td colspan ="'. $this->numDays . '">' . $task['notes'] . '</td></tr>';

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
        $this->html[] = "<figcaption>Project Title: " . $this->project['title'] . "</figcaption>";

        $this->html[] = "<figure class='chart'>";
        // Gantt Task Side Bar
        //$this->html[] = "<aside><table>";
        //foreach ($this->taskSideBarRows as $row) {
        //    $this->html[] = $row;
        //}
        //$this->html[] = "</table></aside>";

        // Gantt Bars
        $this->html[] = '<section><table style ="width: ' . ($this->numDays * 40) . 'px;">';
        $this->html[] = '<thead>';
        $this->html[] = '<tr>' . $this->createHeaderTags($this->yearStartData, 'chart-year') . '</tr>';
        $this->html[] = '<tr>' . $this->createHeaderTags($this->monthStartData, 'chart-month') . '</tr>';
        $this->html[] = '<tr>' . $this->dayHeader . '</tr>';
        $this->html[] = '<tr>' . $this->dateHeader . '</tr>';
        $this->html[] = '</thead>';
        $this->html[] = '<tbody>';
        foreach ($this->taskRows as $row) {
            $this->html[] = $row;
        }
        $this->html[] = "</tbody></table></section>";
        $this->html[] = "</figure>";
    }

    // Draw chart function
    private function createTable()
    {
        //$this->createTaskSideBarRows();
        $this->createTaskRows();
        $this->createDayDateHeaders();
        $this->createGanttTable();
    }

    public function __toString()
    {
        $this->createTable();
        return implode("\n", $this->html);
    }

}
