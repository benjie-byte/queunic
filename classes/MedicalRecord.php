<?php

class MedicalRecord
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addRecord($medical_history, $allergies, $notes, $patient_id)
    {
        try {
            $sql = "INSERT INTO medical_records
                        (medical_history, allergies, notes, patient_id)
                    VALUES
                        (:medical_history, :allergies, :notes, :patient_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":medical_history", $medical_history, PDO::PARAM_STR);
            $stmt->bindValue(":allergies", $allergies, PDO::PARAM_STR);
            $stmt->bindValue(":notes", $notes, PDO::PARAM_STR);
            $stmt->bindValue(":patient_id", $patient_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Medical Record Error: " . $e->getMessage());
            return false;
        }
    }

    public function getRecords()
    {
        try {
            $sql = "SELECT r.*, p.full_name AS patient_name
                    FROM medical_records r
                    INNER JOIN patients p ON r.patient_id = p.patient_id
                    ORDER BY r.date_updated DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Medical Records Error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecord($id)
    {
        try {
            $sql = "SELECT * FROM medical_records WHERE record_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Medical Record Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateRecord($id, $medical_history, $allergies, $notes)
    {
        try {
            $sql = "UPDATE medical_records
                    SET medical_history = :medical_history,
                        allergies = :allergies,
                        notes = :notes
                    WHERE record_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":medical_history", $medical_history, PDO::PARAM_STR);
            $stmt->bindValue(":allergies", $allergies, PDO::PARAM_STR);
            $stmt->bindValue(":notes", $notes, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Medical Record Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRecord($id)
    {
        try {
            $sql = "DELETE FROM medical_records WHERE record_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Medical Record Error: " . $e->getMessage());
            return false;
        }
    }
}
