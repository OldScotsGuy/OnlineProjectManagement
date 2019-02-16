<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 16/02/2019
 * Time: 10:17
 */

require("Objects/Model/Project.php");
require("Objects/Model/Task.php");
require("Objects/View/GanttChart.php");

// Creat objects to begin with
$task1 = new \Model\Task("2019-01-21","2019-02-07","Prepare Specification");
$task2 = new \Model\Task("2019-02-07","2019-02-14","panic");
$project = new Model\Project("Coding", "CMM007 Assignment", array($task1, $task2));
$chart = new \View\GanttChart($project);

// Boiler plate front end
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Prototyping</title>
</head>
<body>
<header>
    <h1>Plant It! Project Tool</h1>
</header>
<main>
<?php echo $chart ?>
</main>
</body>
