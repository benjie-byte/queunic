<?php

class Notification
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addNotification($message, $patient_id)
    {
        try {
            $sql = "INSERT INTO notifications (message, patient_id)
                    VALUES (:message, :patient_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":message", $message, PDO::PARAM_STR);
            $stmt->bindValue(":patient_id", $patient_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Notification Error: " . $e->getMessage());
            return false;
        }
    }

    public function getNotifications()
    {
        try {
            $sql = "SELECT n.*, p.full_name AS patient_name
                    FROM notifications n
                    INNER JOIN patients p ON n.patient_id = p.patient_id
                    ORDER BY n.date_sent DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Notifications Error: " . $e->getMessage());
            return [];
        }
    }

    public function markAsRead($id)
    {
        try {
            $sql = "UPDATE notifications SET status = 'Read' WHERE notification_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Mark Notification Read Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteNotification($id)
    {
        try {
            $sql = "DELETE FROM notifications WHERE notification_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Notification Error: " . $e->getMessage());
            return false;
        }
    }
}
