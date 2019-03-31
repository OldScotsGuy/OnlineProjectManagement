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
use Utils\Action;
use Utils\Form;
use Utils\PageName;
use Utils\Project;
use Utils\Task;
use Utils\User;

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

    public function __toString()
    {
        if (isset($this->projectID)) {
            $this->createGanttChart();
        } else {
            $this->selectProject();
        }
        return implode("\n", $this->html);
    }

    // Draw Gantt chart function
    private function createGanttChart()
    {
        //$this->createTaskSideBarRows();
        $this->createDayDateHeaders();
        $this->createTaskRows();
        $this->createGanttTable();
    }

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
        $this->dayHeader = '<th class="side-heading"></th>';
        $this->dateHeader = '<th class="side-heading">Task : <span class = "emphasis">Owner</span></th>';
        //$startTag = '';
        for ($i = 0; $i < $this->numDays; $i++) {
            $startTag = '<th class="' . $this->dayClassifications[$i][0] . '">';
            $this->dayHeader .= $startTag . $this->dayClassifications[$i][1] . '</th>';     // Construct Gantt table day header
            $this->dateHeader .= $startTag . $this->dayClassifications[$i][2] . '</th>';    // Construct Gantt table date header
        }
    }

    // Create task row data
    private function createTaskRows()
    {
        $index = 0;
        foreach ($this->taskData as $task) {

            // Add task details
            $row = '<tr><td class="side-name">' . $task[Task::Name] . ' : <span class = "emphasis">' . $task[Task::Owner] . '</span></td>';

            // Add before task padding days
            $row .= $this->addPaddingDays(0, $task[Task::Start]);

            // Add task details
            if ($index == 0) {
                $row .= '<td colspan ="' . ($task[Task::End] - $task[Task::Start] + 1) . '" class="task-start">';
            } else {
                $row .= '<td colspan ="' . ($task[Task::End] - $task[Task::Start] + 1) . '">';
            }
            $index += 1;
            //$row .= '<td colspan ="' . ($task[Task::End] - $task[Task::Start] + 1) . '">';
            $row .= '<div class ="chart-task"><div class="chart-fill" style="width: ' . $task[Task::Percent] .'%">';
            $row .= '</div></div></td>';

            // Add after task padding days
            $row .= $this->addPaddingDays($task[Task::End] + 1, $this->numDays)  . '</tr>';

            // Add Task update / delete / notes in row under task detail
            $this->canEditTask = ($this->canDeleteTask || ($_SESSION[User::Role] == User::RoleMember) && $_SESSION[User::Email] == $task[Task::Email]);
            $row .= '<tr class="task-notes"><td colspan ="'. ($this->numDays+1) . '">';
            if ($this->canEditTask) $row .= '<a href="index.php?page='. PageName::Task .'&action='. Action::Update .'&'. Task::ID . '=' . $task[Task::ID] . '&'. Project::ID .'=' . $this->projectID . '">Update</a>';
            if ($this->canDeleteTask) $row .= '<a href="index.php?page='. PageName::Task .'&action='. Action::Delete .'&'. Task::ID . '=' . $task[Task::ID] . '&'. Project::ID .'=' . $this->projectID . '">Delete</a>';
            $row .= '<span>Task Notes: ' . $task[Task::Notes] . '</span>';
            $row .= '</td></tr>';

            $this->taskRows[] = $row;
        }
    }

    // Used to add cells before and after the tasks
    private function addPaddingDays($start, $end) {
        $padding = null;
        for ($i = $start; $i < $end; $i++) {
            $padding .= '<td class="' . $this->dayClassifications[$i][0] . '">' . '</td>';
        }
        return $padding;
    }

    // Create year, month and day banner
    private function createGanttTable()
    {
        $this->html[] = "<figure>";

        $this->html[] = "<figcaption>Project Title: " . $this->project[Project::Title] . "</figcaption>";
        // Gantt Task Side Bar
        //$this->html[] = "<aside><table>";
        //foreach ($this->taskSideBarRows as $row) {
        //    $this->html[] = $row;
        //}
        //$this->html[] = "</table></aside>";

        // Centred Gantt Chart
        $this->html[] = "<div class='chart'>";
        $this->html[] = '<div id="scrollable">';
        $this->html[] = '<table style ="width: ' . (300 + ($this->numDays * 40)) . 'px;">';
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
        $this->html[] = "</tbody>";
        $this->html[] = "</table>";
        $this->html[] = "</div>";

        $this->html[] = "</div>";
        $this->html[] = "</figure>";
    }

    // Create the Gantt chart month and year headers via the stored parsed data
    private function createHeaderTags($periodStartData, $class)
    {
        $periodHeader = '<th class="side-blank"></th>';
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

    private function selectProject() {
        $this->html[] = '<form action ="index.php?page=status" method="get">';
        $this->html[] = '<fieldset>';

        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header("Project Status", $this->message));

        // Project selection drop down box
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project: ', $this->projects));

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitSelection, "Select Project"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }
}
