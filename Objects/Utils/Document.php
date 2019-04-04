<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 14:18
 *
 * This object contains the string constants used to denote documents attributes.
 * These referenced by via html forms as well as keys in numerous associative arrays.
 */

namespace Utils;


abstract class Document
{
    const ID = 'documentID';
    const Title = 'documentTitle';
    const FileName = 'fileName';
    const ProjectID = 'projectID';
    const Path = 'documents/';
}