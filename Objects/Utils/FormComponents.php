<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 26/03/2019
 * Time: 19:42
 */

namespace Utils;


class FormComponents
{
    // =======================
    // General form components
    // =======================

    function header ($title, $message) {
        $html = array();
        $html[] = "<h2>". $title ."</h2>";
        $html[] = "<p>" . $message ."</p>";
        return $html;
    }

    function addField($type, $name, $text, $value, $validationText) {
        $html = array();
        $html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="'. $type . '" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'" ';
        }
        $input .= $validationText .'/><br>';
        $html[] = $input;
        return $html;
    }

    function addNavigationLinks($links) {
        $html = array();
        foreach ($links as $name => $url) {
            $html[] = '<p><a href="'.$url.'">'.$name.'</a></p>';
        }
        return $html;
    }
    function submitButton($name, $value) {
        $html = array();
        $html[] = '<br><br><input type="submit" name="' . $name . '" value="'. $value .'"/>';
        return $html;
    }

    function addTextArea($name, $text, $value) {
        $html = array();
        $html[] = '<label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="textarea" rows="5" cols="50" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'"/><br>';
        } else {
            $input .= '/><br>';
        }
        $html[] = $input;
        return $html;
    }

    // =============================
    // User specific form components
    // =============================

    function addRole($value) {
        $html = array();
        $html[] = '<label for="' . User::Role . '">Project Role: </label>';
        $html[] = '<select name = "' . User::Role . '" id="' . User::Role . '">';
        $html[] = '<option value = "' . User::RoleLead . '" ' . ($value == User::RoleLead ? "selected " : "") . '>Project Lead</option>';
        $html[] = '<option value = "' . User::RoleMember . '" ' . ($value == User::RoleMember ? "selected " : "") . '>Project Member</option>';
        $html[] = '<option value = "' . User::RoleClient . '" ' . ($value == User::RoleClient ? "selected " : "") . '>Project Client</option>';
        $html[] = '<option value = "' . User::RoleAdmin . '" ' . ($value == User::RoleAdmin ? "selected " : "") . '>Admin</option>';
        $html[] = '</select>';
        return $html;
    }

    function selectUser($label, $users) {
        $html = array();
        $html[] = '<label for="' . User::Email . '">' . $label . '</label>';
        $select = '<select name = "' . User::Email . '" id="' . User::Email . '"';
        if (count($users) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $html[] = $select;
        foreach ($users as $user) {
            $html[] = '<option value = "'. $user[User::Email] .'">Username: ' . $user[User::Username] . '  Role: ' . $user[User::Role] . '  Email: ' . $user[User::Email] .'</option>';
        }
        $html[] = '</select>';
        return $html;
    }

    // ================================
    // Project Specific Form Components
    // ================================

    function selectProject($label, $projects) {
        $html = array();
        // Project selection drop down box
        $html[] = '<label for="' . Project::ID . '">' . $label . '</label>';
        $select = '<select name = "' . Project::ID . '" id="' . Project::ID . '"';
        if (count($projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $html[] = $select;
        foreach ($projects as $project) {
            $html[] = '<option value = "'. $project[Project::ID] .'">Title: ' . $project[Project::Title] . ' Project Lead: ' . $project[Project::Lead] . '  Project Lead Email: ' . $project[Project::LeadEmail] .'</option>';
        }
        $html[] = '</select>';
        return $html;
    }

    function addUserInput($name, $label, $value, $userList) {
        $html = array();
        $html[] = '<label for="' . $name .'">' . $label . '</label>';
        $html[] = '<select name = "' . $name .'" id="' . $name .'">';

        for ($i=0; $i<count($userList); $i++) {
            $html[] = '<option value = "' . $userList[$i][User::Email] . '" ' . ($value == $userList[$i][User::Email] ? " selected " : "") . '>' . $userList[$i][User::Username] . '</option>';
        }
        $html[] = '</select><br>';
        return $html;
    }

    // =================================
    // Document Specific Form Components
    // =================================

    function uploadDocument($projectID) {
        $html = array();
        $html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $projectID . '"/>';   // Carry ProjectID across
        $html[] = '<label for="' . Document::Title . '">Document Title:</label>';
        $html[] = '<input type="text" name="' . Document::Title . '" id="' . Document::Title . '" size="100"  maxlength="1280" required />';
        $html[] = '<br><br><input type="file" name="' . Document::FileName . '" required />';
        $html[] = '<input type="submit" name="' . Form::SubmitData . '" value="Upload" />';
        return $html;
    }
}