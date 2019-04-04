<?php
/**
 * Created by Nick Harle
 * Date: 27/03/2019
 * Time: 08:30
 *
 * This object contains the string constants used to denote page names.
 * These referenced by via html forms as well as keys in numerous associative arrays.
 */

namespace Utils;


abstract class PageName
{
    const Status = 'status';
    const User = 'user';
    const Project = 'project';
    const Task = 'task';
    const Document = 'document';
}