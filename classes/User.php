<?php

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
    =========================================
    REGISTER (used by index.php sign-up form)
    =========================================
    */
    public function register($full_name, $username, $password, $role, $contact_number)
    {
        try {

            if ($this->usernameExists($username)) {
                return "taken";
            }

            $sql = "INSERT INTO users
                        (full_name, username, password, role, contact_number)
                    VALUES
                        (:full_name, :username, :password, :role, :contact_number)";

            $stmt = $this->conn->prepare($sql);

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->bindValue(":password", $hashed, PDO::PARAM_STR);
            $stmt->bindValue(":role", $role, PDO::PARAM_STR);
            $stmt->bindValue(":contact_number", $contact_number, PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Register Error: " . $e->getMessage());
            return false;
        }
    }

    /*
    =========================================
    LOGIN (used by login.php)
    =========================================
    */
    public function login($username, $password)
    {
        try {

            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }

            return false;

        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    public function usernameExists($username)
    {
        $sql = "SELECT user_id FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetch();
    }

    public function usernameTakenByOther($username, $excludeId)
    {
        $sql = "SELECT user_id FROM users WHERE username = :username AND user_id != :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":id", $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetch();
    }

    /*
    =========================================
    ADMIN: MANAGE USERS
    =========================================
    */
    public function getUsers()
    {
        try {
            $sql = "SELECT user_id, full_name, username, role, contact_number
                    FROM users ORDER BY user_id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Users Error: " . $e->getMessage());
            return [];
        }
    }

    public function getUser($id)
    {
        try {
            $sql = "SELECT user_id, full_name, username, role, contact_number
                    FROM users WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $full_name, $username, $role, $contact_number)
    {
        try {
            $sql = "UPDATE users
                    SET full_name = :full_name,
                        username = :username,
                        role = :role,
                        contact_number = :contact_number
                    WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->bindValue(":role", $role, PDO::PARAM_STR);
            $stmt->bindValue(":contact_number", $contact_number, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update User Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id)
    {
        try {
            $sql = "DELETE FROM users WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return false;
        }
    }
}
