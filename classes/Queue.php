<?php

class QueueManager
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addToQueue($patient_id, $service_id)
    {
        try {
            $queue_number = $this->getNextQueueNumber();

            $sql = "INSERT INTO queue
                        (queue_number, status, time_in, patient_id, service_id)
                    VALUES
                        (:queue_number, 'Waiting', NOW(), :patient_id, :service_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":queue_number", $queue_number, PDO::PARAM_INT);
            $stmt->bindValue(":patient_id", $patient_id, PDO::PARAM_INT);
            $stmt->bindValue(":service_id", $service_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Queue Error: " . $e->getMessage());
            return false;
        }
    }

    private function getNextQueueNumber()
    {
        $sql = "SELECT COALESCE(MAX(queue_number), 0) + 1 AS next_number
                FROM queue WHERE DATE(time_in) = CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetch()['next_number'];
    }

    public function getQueue()
    {
        try {
            $sql = "SELECT q.*, p.full_name AS patient_name, s.service_name
                    FROM queue q
                    INNER JOIN patients p ON q.patient_id = p.patient_id
                    INNER JOIN services s ON q.service_id = s.service_id
                    ORDER BY q.time_in ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Queue Error: " . $e->getMessage());
            return [];
        }
    }

    public function getQueueEntry($id)
    {
        try {
            $sql = "SELECT * FROM queue WHERE queue_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Queue Entry Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status)
    {
        try {
            $timeField = null;
            if ($status === 'Called') {
                $timeField = 'time_called';
            } elseif ($status === 'Done') {
                $timeField = 'time_out';
            }

            $sql = "UPDATE queue SET status = :status" .
                   ($timeField ? ", $timeField = NOW()" : "") .
                   " WHERE queue_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":status", $status, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Queue Status Error: " . $e->getMessage());
            return false;
        }
    }

    public function getCalledQueue()
    {
        try {
            $sql = "SELECT q.queue_id, p.full_name AS patient_name, s.service_name
                    FROM queue q
                    INNER JOIN patients p ON q.patient_id = p.patient_id
                    INNER JOIN services s ON q.service_id = s.service_id
                    WHERE q.status = 'Called'
                    ORDER BY q.time_called ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Called Queue Error: " . $e->getMessage());
            return [];
        }
    }

    public function deleteQueueEntry($id)
    {
        try {
            $sql = "DELETE FROM queue WHERE queue_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Queue Entry Error: " . $e->getMessage());
            return false;
        }
    }
}
