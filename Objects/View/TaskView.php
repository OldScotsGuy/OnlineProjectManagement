<?php
/**
 * Created by Nick Harle
 * Date: 18/03/2019
 * Time: 19:35
 *
 * This object generates the html required to create task projects.
 * Which html form is generated based on the object variables inherited from the TaskController object
 * The presentation can be changed without any requirement to change the Controller object
 */

namespace View;

require_once("Objects/Controller/TaskController.php");

use Controller\TaskController;
use Utils\Action;
use Utils\Form;
use Utils\PageName;
use Utils\Project;
use Utils\Task;
use Utils\User;

class TaskView extends TaskController
{
    private $html = array();

    public function __toString()
    {
        $this->displayTaskForm();
        return implode("\n", $this->html);
    }

    function displayTaskForm() {
        // Task Entry Form
        $this->html[] = '<form action ="index.php?page='. PageName::Task .'&action=' . $this->action .'" method="post">';
        $this->html[] = '<fieldset>';

        // Title and message
        $this->html = array_merge($this->html, $this->formComponents->header(ucfirst($this->action) . " Task", $this->message));

        // Select Project Task belongs to
        $this->html = array_merge($this->html, $this->formComponents->selectProject('Select Project:', $this->projects));

        // Enter Task name
        $value = (isset($this->displayValues[Task::Name]) ? $this->displayValues[Task::Name] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("text", Task::Name, "Task Name:", $value, 'required maxlength="80" size="40"'));

        // Enter Task Start Date
        $value = (isset($this->displayValues[Task::StartDate]) ? $this->displayValues[Task::StartDate] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("date", Task::StartDate, "Task Start Date:", $value, 'required'));

        // Enter Task End Date
        $value = (isset($this->displayValues[Task::EndDate]) ? $this->displayValues[Task::EndDate] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("date", Task::EndDate, "Task End Date:", $value, 'required'));

        // Enter Percentage Task Complete
        $value = (isset($this->displayValues[Task::Percent]) ? $this->displayValues[Task::Percent] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("number", Task::Percent, "Task % Complete:", $value, 'required min="0" max="100"'));

        // Enter Task Number - for display purposes only
        $value = (isset($this->displayValues[Task::No]) ? $this->displayValues[Task::No] : null );
        $this->html = array_merge($this->html, $this->formComponents->addField("number", Task::No, "Task No:", $value, 'required min="-100" max="100"'));

        // Enter Task Notes
        $value = (isset($this->displayValues[Task::Notes]) ? $this->displayValues[Task::Notes] : null );
        $this->html = array_merge($this->html, $this->formComponents->addTextArea(Task::Notes, "Task Notes::", $value));

        // Enter Task Owner
        $value = (isset($this->displayValues[Task::Owner]) ? $this->displayValues[Task::Owner] : null);
        $this->html = array_merge($this->html, $this->formComponents->addUserInput(Task::Owner, "Task Owner:", $value, $this->nonClientUsers));

        $this->html[] = '<input type="hidden" name="' . Task::ID . '" value="' . $this->taskID . '"/>';                   // Carry TaskID across

        // Submit button
        $this->html = array_merge($this->html, $this->formComponents->submitButton(Form::SubmitData, ucfirst($this->action) . " Task"));

        $this->html[] = '</fieldset>';
        $this->html[] = '</form>';
    }

}