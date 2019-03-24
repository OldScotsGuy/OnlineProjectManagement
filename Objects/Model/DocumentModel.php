<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 09/03/2019
 * Time: 14:14
 */

namespace Model;

use Utils\Document;

require("DatabaseConnection.php");

class DocumentModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query = "CREATE TABLE IF NOT EXISTS Documents (
                          docID int(4) not null auto_increment,
                          title nvarchar(128),
                          filename nvarchar(256),
                          projectID integer(4) not null,
                          PRIMARY KEY(docID),
                          FOREIGN KEY(projectID) REFERENCES Projects(projectID) ON DELETE CASCADE);";
        $result = $this->db->query($query);
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertDocument($documentTitle, $documentFilename, $projectID) {
        $query = "INSERT INTO Documents (title, filename, projectID) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssi', $documentTitle, $documentFilename, $projectID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveDocument($docID) {
        $result = array();
        $query = "SELECT docID, title, filename, projectID FROM Documents WHERE docID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $docID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($result[Document::ID], $result[Document::Title], $result[Document::FileName], $result[Document::ProjectID]);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function deleteDocument($docID) {
        $query = "DELETE FROM Documents WHERE docID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $docID);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }


}