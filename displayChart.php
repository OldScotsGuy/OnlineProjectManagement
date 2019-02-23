<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 16/02/2019
 * Time: 10:17
 */

require_once ("Objects/Model/Project.php");
require_once ("Objects/Model/Task.php");
//require("Objects/View/GanttChart.php");
require_once ("Objects/View/GanttTable.php");

// Creat objects to begin with
$task1 = new \Model\Task("2019-01-28", "2019-02-03", "Specification",1);
$task2 = new \Model\Task("2019-02-07","2019-02-14","panic",2);
$task3 = new \Model\Task("2019-02-15", "2019-03-7", "Coding",3);
$project = new Model\Project("Coding", "CMM007 Assignment", array($task1, $task2, $task3));
//$chart = new \View\GanttChart($project);
$chart = new \View\GanttTable($project);

// Boiler plate front end
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Prototyping</title>
    <link rel="stylesheet" href="CSS/chart-style.css" />
    <link rel="stylesheet" href="CSS/page.css" />
</head>
<body>
<header>
    <h1>Plan It! Project Tool</h1>
</header>
<main>
<?php echo $chart ?>
</main>
</body>
