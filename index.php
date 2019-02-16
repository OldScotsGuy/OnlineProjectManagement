<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/02/2019
 * Time: 19:04
 */

require("Objects/Model/Task.php");

$task = new \Model\Task("2019-02-01", "2019-02-14","Prepare Specification");
$unixStartTimeStamp = strtotime($task->start);
$unixEndTimeStamp = strtotime($task->end);

list($startYear, $startMonth, $startDay) = explode("-",$task->start);
list($endYear, $endMonth, $endDay) = explode("-",$task->end);

$seconds = 60*60*24;

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
