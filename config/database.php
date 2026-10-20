<?php

class Database
{
    private $host = "sql304.infinityfree.com";
    private $db_name = "if0_43163218_clinic_management_db";
    private $username = "if0_43163218";
    private $password = "benjiequinio28";

    public function connect()
    {
        $conn = null;

        try {

            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";

            $conn = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $conn;

        } catch (PDOException $e) {

            die(
                "Database connection failed: "
                . $e->getMessage()
            );
        }
    }
}
