<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 14:38
 *
 * * This object contains the string constants used to denote project attributes.
 * These referenced by via html forms as well as keys in numerous associative arrays.
 */

namespace Utils;


abstract class Project
{
    const ID = 'projectID';
    const Title = 'title';
    const Description = 'description';
    const LeadEmail = 'leadEmail';
    const ClientEmail = 'clientEmail';
    const Lead = 'lead';
    const Client = 'client';
}