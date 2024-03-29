<?php
/**
 * Created by Nick Harle
 * Date: 26/03/2019
 * Time: 19:42
 *
 * This object contains all the form input fields used by the View objects
 * Grouping all the input field html generation here helps to ensure:
 * 1) uniform look / feel across all pages
 * 2) only this object needs to be changed if a different application form appearance is required
 *
 */

namespace Utils;


class FormComponents
{
    // =======================
    // General form components
    // =======================

    function header ($title, $message) {
        $html = array();
        $html[] = "<Legend>". $title ."</Legend>";
        if ($message != '') $html[] = "<p>" . $message ."</p>";
        return $html;
    }

    function addNavigationLinks($links) {
        $html = array();
        $html[] = '<div class="subNav"><ul>';
        foreach ($links as $name => $url) {
            $html[] = '<li><a href="'.$url.'"><div><span>'.$name.'</span></div></a></li>';
        }
        $html[] = '</ul></div>';
        return $html;
    }

    function addField($type, $name, $text, $value, $validationText) {
        $html = array();
        $html[] = '<div class="row"><label for="' . $name . '">' . $text . '</label>';
        $input = '<input type="'. $type . '" name="' . $name . '" id="' . $name . '"';
        if ($value != null) {
            $input .= ' value="' . $value .'" ';
        }
        $input .= $validationText .'/></div>';
        $html[] = $input;
        return $html;
    }

    function addTextArea($name, $text, $value) {
        $html = array();
        $html[] = '<div class="row"><label for="' . $name . '">' . $text . '</label>';
        $input = '<textarea rows="5" cols="50" name="' . $name . '" id="' . $name . '">';
        if ($value != null) {
            $input .= $value;
        }
        $input .= '</textarea></div>';
        $html[] = $input;
        return $html;
    }

    function submitButton($name, $value) {
        $html = array();
        $html[] = '<div class="row"><input type="submit" name="' . $name . '" value="'. $value .'" class="submit"/></div>';
        return $html;
    }

    // =============================
    // User specific form components
    // =============================

    function addRole($value) {
        $html = array();
        $html[] = '<div class="row"><label for="' . User::Role . '">Project Role: </label>';
        $html[] = '<select name = "' . User::Role . '" id="' . User::Role . '">';
        $html[] = '<option value = "' . User::RoleLead . '" ' . ($value == User::RoleLead ? "selected " : "") . '>Project Lead</option>';
        $html[] = '<option value = "' . User::RoleMember . '" ' . ($value == User::RoleMember ? "selected " : "") . '>Project Member</option>';
        $html[] = '<option value = "' . User::RoleClient . '" ' . ($value == User::RoleClient ? "selected " : "") . '>Project Client</option>';
        $html[] = '<option value = "' . User::RoleAdmin . '" ' . ($value == User::RoleAdmin ? "selected " : "") . '>Admin</option>';
        $html[] = '</select></div>';
        return $html;
    }

    function selectUser($label, $users) {
        $html = array();
        $html[] = '<div class="row"><label for="' . User::Email . '">' . $label . '</label>';
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
        $html[] = '</select></div>';
        return $html;
    }

    // ================================
    // Project Specific Form Components
    // ================================

    function selectProject($label, $projects) {
        $html = array();
        // Project selection drop down box
        $html[] = '<div class="row"><label for="' . Project::ID . '">' . $label . '</label>';
        $select = '<select name = "' . Project::ID . '" id="' . Project::ID . '"';
        if (count($projects) == 0) {
            $select .= ' disabled>';
        } else {
            $select .= '>';
        }
        $html[] = $select;
        foreach ($projects as $project) {
            $html[] = '<option value = "'. $project[Project::ID] .'">Title: ' . $project[Project::Title] . '--Project Lead: ' . $project[Project::Lead] . '</option>';
        }
        $html[] = '</select></div>';
        return $html;
    }

    function addUserInput($name, $label, $value, $userList) {
        $html = array();
        $html[] = '<div class="row"><label for="' . $name .'">' . $label . '</label>';
        $html[] = '<select name = "' . $name .'" id="' . $name .'">';

        for ($i=0; $i<count($userList); $i++) {
            $html[] = '<option value = "' . $userList[$i][User::Email] . '" ' . ($value == $userList[$i][User::Email] ? " selected " : "") . '>' . $userList[$i][User::Username] . '</option>';
        }
        $html[] = '</select></div>';
        return $html;
    }

    // =================================
    // Document Specific Form Components
    // =================================

    function uploadDocument($projectID) {
        $html = array();
        $html[] = '<input type="hidden" name="' . Project::ID . '" value="' . $projectID . '"/>';   // Carry ProjectID across
        $html[] = '<div class="row"><label for="' . Document::Title . '">Document Title:</label>';
        $html[] = '<input type="text" name="' . Document::Title . '" id="' . Document::Title . '" size="50"  maxlength="128" required /></div>';
        $html[] = '<div class="row"><label for="' . Document::FileName . '">Document Filename:</label>';
        $html[] = '<input type="file" name="' . Document::FileName . '" id="' . Document::FileName . '"required /></div>';
        return $html;
    }
}