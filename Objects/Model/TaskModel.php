<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 18/03/2019
 * Time: 18:46
 */

namespace Model;


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
                          percent integer(2),
                          task_no integer(2),
                          notes text,
                          projectID integer(4) not null,
                          email nvarchar(128) not null,
                          PRIMARY KEY(taskID),
                          FOREIGN KEY(projectID) REFERENCES Projects(projectID),
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
        $stmt->bind_result( $result['taskID'],$result['taskName'], $result['startDate'], $result['endDate'], $result['percent'], $result['taskNo'], $result['notes'], $result['projectID'],$result['email']);
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
        $projectID = null;
        $email = null;
        $results = array();
        $index = 0;

        // Read users from the database
        $query = "SELECT taskID, task_name, start_date, end_date, percent, task_no, notes, projectID, email FROM Tasks WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($taskID,$taskName, $startDate, $endDate, $percent, $taskNo, $notes, $projectID, $email);
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
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

}