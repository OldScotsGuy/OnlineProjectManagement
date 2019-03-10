<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:42
 */
require("objects/Page/Page.php");
require("objects/View/UserView.php");

use Page\Page;
use View\UserView;

$HomePage = new Page();

$users = array( array('username' => 'Tom', 'email' => 'tom@isp.net', 'role' => 'admin'),
                array('username' => 'Dick', 'email' => 'dick@isp.net', 'role' => 'lead'),
                array('username' => 'Harry', 'email' => 'harry@isp.net', 'role' => 'member'),
                array('username' => 'John', 'email' => 'john@isp.net', 'role' => 'client')
               );
$displayValues = array('username' => 'Tom', 'email' => 'tom@isp.net', 'role' => 'admin');
$UserView = new UserView($users, $displayValues);

$HomePage->content = '<section class="grid-100">'. $UserView . '</section>';

$HomePage->Display();