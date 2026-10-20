<?php

class Service
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addService($service_name, $description, $average_duration)
    {
        try {
            $sql = "INSERT INTO services (service_name, description, average_duration)
                    VALUES (:service_name, :description, :average_duration)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":service_name", $service_name, PDO::PARAM_STR);
            $stmt->bindValue(":description", $description, PDO::PARAM_STR);
            $stmt->bindValue(":average_duration", $average_duration, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Service Error: " . $e->getMessage());
            return false;
        }
    }

    public function getServices()
    {
        try {
            $sql = "SELECT * FROM services ORDER BY service_name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Services Error: " . $e->getMessage());
            return [];
        }
    }

    public function getService($id)
    {
        try {
            $sql = "SELECT * FROM services WHERE service_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Service Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateService($id, $service_name, $description, $average_duration)
    {
        try {
            $sql = "UPDATE services
                    SET service_name = :service_name,
                        description = :description,
                        average_duration = :average_duration
                    WHERE service_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":service_name", $service_name, PDO::PARAM_STR);
            $stmt->bindValue(":description", $description, PDO::PARAM_STR);
            $stmt->bindValue(":average_duration", $average_duration, PDO::PARAM_INT);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Service Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteService($id)
    {
        try {
            $sql = "DELETE FROM services WHERE service_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Service Error: " . $e->getMessage());
            return false;
        }
    }
}
