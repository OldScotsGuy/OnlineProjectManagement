<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/02/2019
 * Time: 18:57
 */

namespace Model;


class Task
{
    private $start;
    private $end;
    private $name;
    private $notes;
    private $owner;
    private $taskNo;
    private $percentComplete;

    public function __construct($start, $end, $name, $taskNo, $owner, $notes, $percentComplete)
    {
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
        $this->taskNo = $taskNo;
        $this->owner = $owner;
        $this->notes = $notes;
        $this->percentComplete = $percentComplete;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function __get($var)
    {
        return $this->$var;
    }

}