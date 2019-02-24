<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/02/2019
 * Time: 19:04
 */

require("Objects/Model/Task.php");

$task = new \Model\Task("2019-02-01", "2019-02-14","Prepare Specification",3,"NH", "Need to understand the user requirements");
$unixStartTimeStamp = strtotime($task->start);
$unixEndTimeStamp = strtotime($task->end);

list($startYear, $startMonth, $startDay) = explode("-",$task->start);
list($endYear, $endMonth, $endDay) = explode("-",$task->end);

$seconds = 60*60*24;

$task1 = new \Model\Task("2019-01-28", "2019-02-03", "Specification",3,"NH", "Need to understand the user requirements");
$task2 = new \Model\Task("2019-02-07","2019-02-14","Panic",2, "NH", "Realising the amount of work required in the time given produced panic");
$task3 = new \Model\Task("2019-02-15", "2019-03-7", "Coding",1, "NH", "The most fun bit");
$taskData = array();
$taskData[] = array("start" => $task1->start, "end" => $task1->end, "name" => $task1->name , "num" => $task1->taskNo, "owner" => $task1->owner, "notes" => $task1->notes);
$taskData[] = array("start" => $task2->start, "end" => $task2->end, "name" => $task2->name , "num" => $task2->taskNo, "owner" => $task2->owner, "notes" => $task2->notes);
$taskData[] = array("start" => $task3->start, "end" => $task3->end, "name" => $task3->name , "num" => $task3->taskNo, "owner" => $task3->owner, "notes" => $task3->notes);
usort($taskData, function ($a,$b) {return $a['num'] - $b['num']; });

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Prototyping</title>
</head>
<body>
    <main>
        <h2>Default Values</h2>
        <p><?php echo "Task start ". $task->start ?></p>
        <p><?php echo "Task end ". $task->end ?></p>
        <p><?php echo "Task name ". $task->name ?></p>
        <h2>Unix Time</h2>
        <p><?php echo "Unix start: ".$unixStartTimeStamp ?></p>
        <p><?php echo "Unix end: ".$unixEndTimeStamp ?></p>
        <p><?php echo "No. of days: ".(($unixEndTimeStamp - $unixStartTimeStamp)/$seconds + 1) ?></p>
        <h2>Date Function Values</h2>
        <p><?php echo "Apply date function to start Unix timestamp: ".date('Y-m-d', $unixStartTimeStamp) ?></p>
        <p><?php echo "Apply date function to end Unix timestamp: ".date('Y-m-d', $unixEndTimeStamp) ?></p>
        <p><?php echo "Day code: ".date('w', $unixStartTimeStamp) ?></p>
        <p><?php echo "Day code: ".date('w', $unixEndTimeStamp) ?></p>
        <p><?php echo "Today's date: ".date('Y-m-d', time()) ?></p>
        <p><?php echo "Is start date today? ".(date ('Y-m-d',$unixStartTimeStamp) ==  date ('Y-m-d') ? "yes" : "no") ?></p>
        <p><?php echo "Is end date today? ".(date ('Y-m-d',$unixEndTimeStamp) ==  date ('Y-m-d') ? "yes" : "no") ?></p>
        <h2>Exploded Values</h2>
        <p><?php echo "Start year: ".$startYear ?></p>
        <p><?php echo "Start month: ".$startMonth ?></p>
        <p><?php echo "Start day: ".$startDay ?></p>
        <p><?php echo "End year: ".$endYear ?></p>
        <p><?php echo "End month: ".$endMonth ?></p>
        <p><?php echo "End day: ".$endDay ?></p>
        <h2>Task Array Output</h2>
        <p><?php echo "Task1: " . $taskData[0]['name'] . "Task no: " . $taskData[0]['num'] ?></p>
        <p><?php echo "Task2: " . $taskData[1]['name'] . "Task no: " . $taskData[1]['num'] ?></p>
        <p><?php echo "Task3: " . $taskData[2]['name'] . "Task no: " . $taskData[2]['num'] ?></p>
        <h2>Table Output</h2>
<?php
    $numDays = ($unixEndTimeStamp - $unixStartTimeStamp)/$seconds + 1;
    echo "<table>";
    echo "<tr><th>Year: 2019</th></tr>";
    echo "<tr><th>Month: February</th></tr>";
    echo "<tr>";
    for($i=0; $i < $numDays; $i++) {
        $timestamp = $unixStartTimeStamp + $i*$seconds;
        list($tabYear, $tabMonth, $tabDay) = explode("-", date('Y-F-D', $timestamp));
        echo '<td>'.$tabDay."</td>";
    }
    echo "</tr>";
    echo "</table>";
?>
    </main>


</body>
</html>
