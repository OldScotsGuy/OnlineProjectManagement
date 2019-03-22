<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 18/03/2019
 * Time: 18:46
 */

namespace Model;

require_once("DatabaseConnection.php");

class TaskModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query = "CREATE TABLE IF NOT EXISTS Tasks (
                          taskID integer(4) not null auto_increment,
                          task_name nvarchar(80) not null,
                          start_date date not null,
                          end_date date not null,
                          percent integer(3),
                          task_no integer(3),
                          notes text,
                          projectID integer(4) not null,
                          email nvarchar(128) not null,
                          PRIMARY KEY(taskID),
                          FOREIGN KEY(projectID) REFERENCES Projects(projectID) ON DELETE CASCADE,
                          FOREIGN KEY(email) REFERENCES Users(email))";
        $result = $this->db->query($query);
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertTask($taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email) {
        $query = "INSERT INTO Tasks (task_name, start_date, end_date, percent, task_no, notes, projectID, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssiisis', $taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveTask($taskID) {
        $result = array();
        $query = "SELECT taskID, task_name, start_date, end_date, percent, task_no, notes, projectID, email FROM Tasks WHERE taskID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $taskID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result['taskID'],$result['taskName'], $result['startDate'], $result['endDate'], $result['percent'], $result['taskNo'], $result['notes'], $result['projectID'],$result['taskOwner']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function updateTask($taskID, $taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email) {
        $query = "UPDATE Tasks SET task_name = ?, start_date = ?, end_date = ?, percent = ?, task_no = ?, notes = ?, projectID = ?, email = ? WHERE taskID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssiisisi', $taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email, $taskID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function deleteTask($taskID) {
        $query = "DELETE FROM Tasks WHERE taskID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $taskID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveProjectTasks($projectID) {
        // Variable declarations
        $taskID = null;
        $taskName = null;
        $startDate = null;
        $endDate = null;
        $percent = null;
        $taskNo = null;
        $notes = null;
        $email = null;
        $username = null;
        $results = array();
        $index = 0;

        // Read users from the database
        $query = "SELECT T.taskID, T.task_name, T.start_date, T.end_date, T.percent, T.task_no, T.notes, T.projectID, T.email, U.username FROM Tasks As T, Users As U WHERE T.email = U.email AND projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($taskID,$taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email, $username);
        while ($stmt->fetch()) {
            $results[$index]['taskID'] = $taskID;
            $results[$index]['taskName'] = $taskName;
            $results[$index]['startDate'] = $startDate;
            $results[$index]['endDate'] = $endDate;
            $results[$index]['percent'] = $percent;
            $results[$index]['taskNo'] = $taskNo;
            $results[$index]['notes'] = $notes;
            $results[$index]['projectID'] = $projectID;
            $results[$index]['email'] = $email;
            $results[$index]['owner'] = $username;
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

}