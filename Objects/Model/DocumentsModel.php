<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 09/03/2019
 * Time: 14:14
 */

namespace Model;

require_once("DatabaseConnection.php");

class DocumentsModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertDocument($documentTitle, $documentName) {
        // Store file details in database
        $query = "CREATE TABLE IF NOT EXISTS `Documents` (
                          `docID` int(4) not null auto_increment,
                          `title` nvarchar(128),
                          `name` nvarchar(256),
                           PRIMARY KEY(`docID`));";
        $result = $this->db->query($query);

        $query = "INSERT INTO Documents (`title`, `name`) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $documentTitle, $documentName);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveDocument($docID) {
        $result = array();
        $query = "SELECT title, name FROM Documents WHERE docID = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $docID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($result['docTitle'], $result['docName']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }
}