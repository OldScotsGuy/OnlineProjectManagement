<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 09/03/2019
 * Time: 15:45
 */

namespace Model;

//define('DB_SERVER', 'CSDM-WEBDEV');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', '1813014');
define('DB_PASSWORD', '1813014');
define('DB_DATABASE', 'db1813014_cmm007');

class DatabaseConnection extends \mysqli
{

    function __construct()
    {
        parent::__construct(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    }

}