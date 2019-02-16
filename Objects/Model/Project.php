<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 16/02/2019
 * Time: 10:01
 */

namespace Model;


class Project
{
    private $title = null;
    private $description = null;
    private $tasks;

    public function __construct($title, $description, $tasks=array())
    {
        $this->title = $title;
        $this->description = $description;
        $this->tasks = $tasks;
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