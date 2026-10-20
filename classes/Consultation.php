<?php

class Consultation
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addConsultation($diagnosis, $prescription, $notes, $date, $user_id, $queue_id)
    {
        try {
            $sql = "INSERT INTO consultations
                        (diagnosis, prescription, notes, date, user_id, queue_id)
                    VALUES
                        (:diagnosis, :prescription, :notes, :date, :user_id, :queue_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":diagnosis", $diagnosis, PDO::PARAM_STR);
            $stmt->bindValue(":prescription", $prescription, PDO::PARAM_STR);
            $stmt->bindValue(":notes", $notes, PDO::PARAM_STR);
            $stmt->bindValue(":date", $date, PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":queue_id", $queue_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Consultation Error: " . $e->getMessage());
            return false;
        }
    }

    public function getConsultations()
    {
        try {
            $sql = "SELECT c.*, u.full_name AS staff_name, p.full_name AS patient_name
                    FROM consultations c
                    INNER JOIN users u ON c.user_id = u.user_id
                    INNER JOIN queue q ON c.queue_id = q.queue_id
                    INNER JOIN patients p ON q.patient_id = p.patient_id
                    ORDER BY c.date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Consultations Error: " . $e->getMessage());
            return [];
        }
    }

    public function getConsultation($id)
    {
        try {
            $sql = "SELECT * FROM consultations WHERE consultation_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Consultation Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateConsultation($id, $diagnosis, $prescription, $notes, $date)
    {
        try {
            $sql = "UPDATE consultations
                    SET diagnosis = :diagnosis,
                        prescription = :prescription,
                        notes = :notes,
                        date = :date
                    WHERE consultation_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":diagnosis", $diagnosis, PDO::PARAM_STR);
            $stmt->bindValue(":prescription", $prescription, PDO::PARAM_STR);
            $stmt->bindValue(":notes", $notes, PDO::PARAM_STR);
            $stmt->bindValue(":date", $date, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Consultation Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteConsultation($id)
    {
        try {
            $sql = "DELETE FROM consultations WHERE consultation_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Consultation Error: " . $e->getMessage());
            return false;
        }
    }
}
