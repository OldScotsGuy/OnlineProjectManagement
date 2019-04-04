<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 14:14
 *
 * This object contains the string constants used to denote actions on the stored data and user logout
 */

namespace Utils;


abstract class Action
{
    const Create = 'create';
    const Update = 'update';
    const Delete = 'delete';
    const Upload = 'upload';
    const View = 'view';
    const Logout = 'logout';
}