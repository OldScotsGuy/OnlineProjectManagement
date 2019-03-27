<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 12/03/2019
 * Time: 18:43
 */

namespace Model;

require_once("DatabaseConnection.php");

use Utils\Project;

class ProjectModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query1 = "CREATE TABLE IF NOT EXISTS Projects (
                          projectID integer(4) not null auto_increment,
                          title nvarchar(40),
                          description text,
                          email nvarchar(128),
                          PRIMARY KEY(projectID),
                          FOREIGN KEY(email) REFERENCES Users(email))";
        $query2 = "CREATE TABLE IF NOT EXISTS UndertakenFor (
                          projectID integer(4) not null,
                          email nvarchar(128) not null,
                          PRIMARY KEY(projectID),
                          FOREIGN KEY(projectID) REFERENCES Projects(projectID) ON DELETE CASCADE,
                          FOREIGN KEY(email) REFERENCES Users(email) ON DELETE CASCADE)";
        $result = $this->db->query($query1);
        $result = $this->db->query($query2);
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
        $query1 = "INSERT INTO Projects (title, description, email) VALUES (?, ?, ?)";
        $stmt1 = $this->db->prepare($query1);
        $stmt1->bind_param('sss', $title, $description, $leadEmail);
        $stmt1->execute();
        $query2 = "INSERT INTO UndertakenFor (projectID, email) VALUES (LAST_INSERT_ID(), ?)";
        $stmt2 = $this->db->prepare($query2);
        $stmt2->bind_param('s', $clientEmail);
        $stmt2->execute();
        return ($stmt1->affected_rows > 0) && ($stmt1->affected_rows > 0);
    }

    function retrieveProject($projectID) {
        $result = array();
        $query = "SELECT projectID, title, description, email FROM Projects WHERE projectID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $projectID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result[Project::ID], $result[Project::Title], $result[Project::Description], $result[Project::LeadEmail]);
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
        $stmt->bind_result( $result[Project::ID], $result[Project::Title], $result[Project::Description], $result[Project::Lead], $result[Project::LeadEmail]);
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
        $query = "SELECT P.projectID, P.title, U.username as 'lead', U.email FROM Projects as P, Users as U WHERE P.email = U.email";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($projectID,$title, $lead, $email);
        while ($stmt->fetch()) {
            $results[$index][Project::ID] = $projectID;
            $results[$index][Project::Title] = $title;
            $results[$index][Project::Lead] = $lead;
            $results[$index][Project::LeadEmail] = $email;
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
        $stmt->bind_result( $result[Project::ID], $result[Project::Client], $result[Project::ClientEmail]);
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
