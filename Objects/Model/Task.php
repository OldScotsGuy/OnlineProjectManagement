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
    private $description;
    private $comments;
    private $taskNo;

    public function __construct($start, $end, $name)
    {
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
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