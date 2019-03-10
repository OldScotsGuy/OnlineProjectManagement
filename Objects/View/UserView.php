<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 12:33
 */

namespace View;


class UserView
{
    private $html = array();
    private $initialSelection= array();
    private $displayValues = array();

    function __construct($initialSelection, $displayValues) {
        $this->initialSelection = $initialSelection;
        $this->displayValues = $displayValues;
    }

    function display() {
        
    }

}