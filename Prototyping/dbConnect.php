<?php
/**
 * Created by PhpStorm.
 * User: 1813014
 * Date: 25/02/2019
 * Time: 10:20
 */

//define('DB_SERVER', 'CSDM-WEBDEV');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', '1813014');
define('DB_PASSWORD', '1813014');
define('DB_DATABASE', 'db1813014_cmm007');

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if (mysqli_connect_errno()) {
    echo '<p>Error: Could not connect to database.<br/>
       Please try again later.</p>';
    exit;
}