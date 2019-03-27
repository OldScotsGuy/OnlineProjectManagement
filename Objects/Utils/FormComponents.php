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
}