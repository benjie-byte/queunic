<?php

class ActivityLog
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Call this from any module after a create/update/delete action, e.g.:
     * (new ActivityLog($conn))->log($_SESSION['user_id'], "Added new patient: John Doe");
     */
    public function log($user_id, $action)
    {
        try {
            $sql = "INSERT INTO activity_logs (action, user_id) VALUES (:action, :user_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":action", $action, PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Activity Log Error: " . $e->getMessage());
            return false;
        }
    }

    public function getLogs()
    {
        try {
            $sql = "SELECT l.*, u.full_name AS user_name
                    FROM activity_logs l
                    INNER JOIN users u ON l.user_id = u.user_id
                    ORDER BY l.timestamp DESC
                    LIMIT 200";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Logs Error: " . $e->getMessage());
            return [];
        }
    }
}
