<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/03/2019
 * Time: 18:43
 */

namespace Model;

require("DatabaseConnection.php");

class ProjectModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query = "CREATE TABLE IF NOT EXISTS Projects (
                          projectID integer(4) not null auto_increment,
                          title nvarchar(40),
                          description text,
                          email nvarchar(128),
                          PRIMARY KEY(projectID),
                          FOREIGN KEY(email) REFERENCES Users(email));
                  CREATE TABLE IF NOT EXISTS UndertakenFor (
                          projectID integer(4) not null,
                          email nvarchar(128) not null,
                          PRIMARY KEY(projectID),
                          FOREIGN KEY(projectID) REFERENCES Projects(projectID),
                          FOREIGN KEY(email) REFERENCES Users(email));";
        $result = $this->db->query($query);
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertProject($title, $description, $email) {
        $query = "INSERT INTO Projects (title, description, email) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sss', $title, $description, $email);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function insertProjectWithClient($title, $description, $leadEmail, $clientEmail) {
        $query = "INSERT INTO Projects (title, description, email) VALUES (?, ?, ?); INSERT INTO UndertakenFor (projectID, email) VALUES (projectID = LAST_INSERT_ID(), ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssss', $title, $description, $leadEmail, $clientEmail);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveProject($email) {
        $result = array();
        $query = "SELECT projectID, title, description, email FROM Projects WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result['projectID'], $result['title'], $result['description'], $result['email']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function retrieveProjectWithLead($projectID) {
        $result = array();

        $query = "SELECT P.projectID, P.title, P.description, U.username as 'lead', U.email FROM Projects as P, Users as U WHERE P.email = U.email AND P.projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result['projectID'], $result['title'], $result['description'], $result['lead'], $result['email']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function updateProject($projectID, $title, $description, $email) {
        $query = "UPDATE Projects SET projectID = ?, title = ?, description = ?, email = ? WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('isssi', $projectID, $title, $description, $email, $projectID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function deleteProject($projectID) {
        $query = "DELETE FROM Projects WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveProjects() {
        // Variable declarations
        $projectID = null;
        $title = null;
        $description = null;
        $email = null;
        $results = array();
        $index = 0;

        // Read users from the database
        $query = "SELECT projectID, title, description, email FROM Projects";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($projectID,$title, $description, $email);
        while ($stmt->fetch()) {
            $results[$index]['projectID'] = $projectID;
            $results[$index]['title'] = $title;
            $results[$index]['description'] = $description;
            $results[$index]['email'] = $email;
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

    function insertProjectClient($projectID, $email) {
        $query = "INSERT INTO UndertakenFor (projectID, email) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('is', $projectID, $email);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveProjectClient($projectID) {
        $result = array();
        $query = "SELECT P.projectID, U.username as 'client', U.email FROM Projects as P, UndertakenFor as F, Users as U WHERE P.projectID = F.projectID AND F.email = U.email AND P.projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result['projectID'], $result['client'], $result['email']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function updateProjectClient($projectID, $email) {
        $query = "UPDATE UndertakenFor SET projectID = ?, email = ? WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('isi', $projectID, $email, $projectID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function deleteProjectClient($projectID) {
        $query = "DELETE FROM UndertakenFor WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
}