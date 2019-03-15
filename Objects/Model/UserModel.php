<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:51
 */

namespace Model;

require_once("DatabaseConnection.php");

class UserModel
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query = "CREATE TABLE IF NOT EXISTS Users (
                          email nvarchar(128) not null,
                          username nvarchar(40),
                          password nvarchar(60),
                          role nvarchar(20),
                          PRIMARY KEY(email));";
        $result = $this->db->query($query);
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertUser($username, $password, $email, $role) {
        $query = "INSERT INTO Users (email, username, password, role) VALUES (?, ?, SHA('?'), ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssss', $email, $username, $password, $role);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveUser($email) {
        $result = array();
        $query = "SELECT email, username, password, role FROM Users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result['email'], $result['username'], $result['password'], $result['role']);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function updateUser($username, $password, $email, $role) {
        $query = "UPDATE Users SET email = ?, username = ?, password = SHA('?'), role = ? WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssss', $email, $username, $password, $role, $email);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function deleteUser($email) {
        $query = "DELETE FROM Users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    function retrieveUsers() {
        // Variable declarations
        $email = null;
        $username = null;
        $password = null;
        $role = null;
        $results = array();
        $index = 0;

        // Read users from the database
        $query = "SELECT email, username, password, role FROM Users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email,$username, $paswword, $role);
        while ($stmt->fetch()) {
            $results[$index]['email'] = $email;
            $results[$index]['username'] = $username;
            $results[$index]['paswword'] = $password;
            $results[$index]['role'] = $role;
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

    function retrieveUsersWithRole($role) {
        // Variable declarations
        $email = null;
        $username = null;
        $password = null;
        $results = array();
        $index = 0;

        // Read users from the database
        $query = "SELECT email, username, password, role FROM Users WHERE role = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email,$username, $paswword, $role);
        while ($stmt->fetch()) {
            $results[$index]['email'] = $email;
            $results[$index]['username'] = $username;
            $results[$index]['paswword'] = $password;
            $results[$index]['role'] = $role;
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

}