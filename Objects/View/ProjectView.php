<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/03/2019
 * Time: 20:30
 */

namespace View;

use Controller\ProjectController;

class ProjectView extends ProjectController
{
    private $html = array();

    function display() {

        // Title and Message
        $this->html[] = "<h2>" . ucfirst($this->action) . " Project</h2>";
        $this->html[] = "<p>" . $this->message ."</p>";

        // Navigation links
        $this->html[] = "<p><a href='index.php?page=project&action=create'>Create Project</a></p>";
        $this->html[] = "<p><a href='index.php?page=project&action=update'>Update Project</a></p>";
        $this->html[] = "<p><a href='index.php?page=project&action=delete'>Delete Project</a></p>";

        // Project Form
        $this->html[] = '<form action ="index.php?page=project&action=' . $this->action .'" method="post">';
        if (($this->action == "create") || ($this->action == "update" &&  count($this->displayValues) > 0)) {

            // Display project title and description fields
            $this->addField("text", "title", "Title:", (isset($this->displayValues['title']) ? $this->displayValues['title'] : null ));
            $this->addTextArea( "description", "Description:", (isset($this->displayValues['description']) ? $this->displayValues['description'] : null));

            // Display project lead Options
            $this->addUserInput("Lead", $this->usersLead, isset($this->displayValues['leadEmail']) ? $this->displayValues['leadEmail'] : null );

            // Display project client options
            $this->addUserInput("Client", $this->usersClient, isset($this->displayValues['clientEmail']) ? $this->displayValues['clientEmail'] : null );

            // Submit button
            if ($this->action == "create") {
                // Submit button disabled if no Project Lead users
                $this->html[] = '<br><br><input type="submit" value="Create Project"' . (count($this->usersLead) > 0 ? '' : 'disabled') . '/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Update Project"/>';
            }
        } else {

            // Project selection drop down box
            $this->initialSelection();

            // Submit button
            if ($this->action == "update") {
                // Disable the submit button if no projects present
                $this->html[] = '<br><br><input type="submit" value="Select User to Update"' . (count($this->projects) > 0 ? '' : 'disabled') . '/>';
            } else {
                $this->html[] = '<br><br><input type="submit" value="Select User to Delete"/>';
            }
        }
        $this->html[] = '</form>';
    }

    function addField($type, $name, $text, $value) {
        $this->html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="'. $type . '" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'"/><br>';
        } else {
            $input .= '/><br>';
        }
        $this->html[] = $input;
    }

    function addTextArea($name, $text, $value) {
        $this->html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="textarea" rows="5" cols="50" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'"/><br>';
        } else {
            $input .= '/><br>';
        }
        $this->html[] = $input;
    }

    function addUserInput($roleDescription, $value, $userList) {
        $this->html[] = '<label for="user' . $roleDescription .'">Project Lead: </label>';
        $this->html[] = '<select name = "user' . $roleDescription .'" id="user' . $roleDescription .'">';

        for ($i=0; $i<count($userList); $i++) {
            $this->html[] = '<option value = "' . $userList[$i]['email'] . '"' . ($value == $userList[$i]['email'] ? " selected " : "") . '>' . $userList[$i]['username'] . '</option>';
        }
        $this->html[] = '</select>';
    }

    function initialSelection() {
        $this->html[] = '<label for="projectID">Select Project to ' . ucfirst($this->action) . ': </label>';
        $select = '<select name = "projectID" id="projectID"';
        if (count($this->projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $this->html[] = $select;
        foreach ($this->projects as $project) {
            $this->html[] = '<option value = "'. $project['projectID'] .'">Title: ' . $project['title'] . ' Project Lead: ' . $project['lead'] . '  Project Lead Email: ' . $project['leadEmail'] .'</option>';
        }
        $this->html[] = '</select>';
    }

    public function __toString()
    {
        $this->display();
        return implode("\n", $this->html);
    }
}