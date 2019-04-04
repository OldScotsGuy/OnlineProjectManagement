<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 15:06
 *
 * This object contains the string constants used to denote task attributes.
 * These referenced by via html forms as well as keys in numerous associative arrays.
 */

namespace Utils;


abstract class Task
{
    const ID = 'taskID';
    const Name = 'taskName';
    const StartDate = 'startDate';
    const EndDate = 'endDate';
    const Percent = 'percent';
    const No = 'taskNo';
    const Notes = 'notes';
    const ProjectID = 'projectID';
    const Email = 'email';
    const Owner = 'owner';
    const Start = 'start';
    const End = 'end';
}