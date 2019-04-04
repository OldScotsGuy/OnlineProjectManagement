<?php
/**
 * Created by Nick Harle
 * Date: 10/03/2019
 * Time: 11:51
 *
 * This object provides a service for all Users table operations in teh MySQL database
 * It could be used to define an interface which any other data storage service could implement
 * In this way the data storage could be altered without affecting the rest of the application code
 */

namespace Model;

use Utils\Password;
use Utils\User;

require_once("DatabaseConnection.php");
require_once("Objects/Utils/Password.php");

class UserModel extends Password
{
    private $db = null;

    function __construct()
    {
        $this->db = new DatabaseConnection();
        $query = "CREATE TABLE IF NOT EXISTS Users (
                          email nvarchar(128) not null,
                          username nvarchar(40),
                          password nvarchar(255),
                          role nvarchar(20),
                          PRIMARY KEY(email))";
        $result = $this->db->query($query);
    }

    function __destruct()
    {
        $this->db->close();
    }

    function insertUser($username, $password, $email, $role) {
        $query = "INSERT INTO Users (email, username, password, role) VALUES (?, ?, ?, ?)";
        //$hashedPassword = $this->password_hash($password, PASSWORD_BCRYPT);
        $hashedPassword = sha1($password);
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssss', $email, $username, $hashedPassword, $role);
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
        $stmt->bind_result( $result[User::Email], $result[User::Username], $result[User::Password], $result[User::Role]);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
    }

    function updateUser($username, $password, $email, $role) {
        $query = "UPDATE Users SET email = ?, username = ?, password = ?, role = ? WHERE email = ?";
        //$hashedPassword = $this->password_hash($password, PASSWORD_BCRYPT);
        $hashedPassword = sha1($password);
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssss', $email, $username, $hashedPassword, $role, $email);
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

    function checkUserCredentials($email, $password) {
        $result = array();
        //$hashedPassword = $this->password_hash($password, PASSWORD_BCRYPT);
        $hashedPassword = sha1($password);
        $query = "SELECT email, username, password, role FROM Users WHERE email = ? AND password = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $email, $hashedPassword);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result( $result[User::Email], $result[User::Username], $result[User::Password], $result[User::Role]);
        $stmt->fetch();
        $stmt->free_result();
        return $result;
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
            $results[$index][User::Email] = $email;
            $results[$index][User::Username] = $username;
            $results[$index][User::Password] = $password;
            $results[$index][User::Role] = $role;
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
        if ($role == User::RoleClient) {
            $results[$index] = array(User::Email => 'none', User::Username => 'none', User::Password => 'none', User::Role => 'none');
            $index += 1;
        }
        while ($stmt->fetch()) {
            $results[$index][User::Email] = $email;
            $results[$index][User::Username] = $username;
            $results[$index][User::Password] = $password;
            $results[$index][User::Role] = $role;
            $index += 1;
        }
        $stmt->free_result();
        return $results;
    }

}