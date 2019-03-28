<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 23/03/2019
 * Time: 14:14
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