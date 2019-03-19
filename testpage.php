<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 19/03/2019
 * Time: 18:46
 */

require ("Objects/View/TaskView.php");
require ("Objects/Model/TaskModel.php");

if (isset($_POST['submit'])) {
    // Echo back form data
/*    $content = "<p>Before database write</p>";
    $content .= "<p>taskName = " . $_POST['taskName'] . "</p>";
    $content .= "<p>startDate = " . $_POST['startDate'] . "</p>";
    $content .= "<p>endDate = " . $_POST['endDate'] . "</p>";
    $content .= "<p>percent = " . $_POST['percent'] . "</p>";
    $content .= "<p>taskNo = " . $_POST['taskNo'] . "</p>";
    $content .= "<p>notes = " . $_POST['notes'] . "</p>";
    $content .= "<p>projectID = " . $_POST['projectID'] . "</p>";
    $content .= "<p>user = " . $_POST['userOwner'] . "</p>"; */

    $TaskModel = new \Model\TaskModel();
    // Save form data
    //if ($TaskModel->insertTask($_POST['taskName'], $_POST['startDate'], $_POST['endDate'], $_POST['percent'], $_POST['taskNo'], $_POST['notes'], $_POST['projectID'], $_POST['userOwner'])) {
    //    $content .= "<p>Data saved to database</p>";
    //}

    // Retrieve data
    $data = $TaskModel->retrieveTask(5);
    $content = "<p>Database data</p>";
    $content .= "<p>taskID = " . $data['taskID'] . "</p>";
    $content .= "<p>taskName = " . $data['taskName'] . "</p>";
    $content .= "<p>startDate = " . $data['startDate'] . "</p>";
    $content .= "<p>endDate = " . $data['endDate'] . "</p>";
    $content .= "<p>percent = " . $data['percent'] . "</p>";
    $content .= "<p>taskNo = " . $data['taskNo'] . "</p>";
    $content .= "<p>notes = " . $data['notes'] . "</p>";
    $content .= "<p>projectID = " . $data['projectID'] . "</p>";
    $content .= "<p>user = " . $data['email'] . "</p>";

} else {
    // Print the form
    $nonClientUsers = array(array('email' => 'asd@asd', 'username' => 'DaveD'),
                            array('email' => 'member2@isp.com', 'username' => 'Not Il Capo'));
    $displayValues = array();
    $action="create";
    $message="Test Run";
    $taskID=1;
    $projects = array(array('projectID' => 1, 'title' => 'Project 1', 'lead' => 'Lead1', 'leadEmail' => 'lead1@isp.com'),
        array('projectID' => 2, 'title' => 'Project 2', 'lead' => 'Lead2', 'leadEmail' => 'lead2@isp.com'));
    $projectID = 2;
    $TaskView = new \View\TaskView($nonClientUsers, $displayValues, $action, $message, $taskID, $projects, $projectID);
    $content = $TaskView;
}

// Boiler plate front end
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Prototyping</title>
    <link rel="stylesheet" href="Assets/CSS/chart-style.css" />
    <link rel="stylesheet" href="Assets/CSS/page.css" />
</head>
<body>
<header>
    <h1>Plan It! Project Tool</h1>
</header>
<main>
    <?php echo $content ?>
</main>
<script type="text/javascript" src="Assets/Javascript/chart-task-notes.js"></script>
</body>
</html>