<?php

class Appointment
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    /*
    =========================================
    ADD APPOINTMENT
    =========================================
    */

    public function addAppointment(
        $appointment_date,
        $appointment_time,
        $status,
        $patient_id,
        $service_id
    ) {
        try {

            $sql = "INSERT INTO appointments
                    (
                        appointment_date,
                        appointment_time,
                        status,
                        patient_id,
                        service_id
                    )
                    VALUES
                    (
                        :appointment_date,
                        :appointment_time,
                        :status,
                        :patient_id,
                        :service_id
                    )";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ":appointment_date",
                $appointment_date,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":appointment_time",
                $appointment_time,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":status",
                $status,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":patient_id",
                $patient_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ":service_id",
                $service_id,
                PDO::PARAM_INT
            );

            return $stmt->execute();

        } catch (PDOException $e) {

            error_log(
                "Add Appointment Error: "
                . $e->getMessage()
            );

            return false;
        }
    }


    /*
    =========================================
    GET ALL APPOINTMENTS
    =========================================
    */

    public function getAppointments()
    {
        try {

            $sql = "SELECT
                        a.appointment_id,
                        a.appointment_date,
                        a.appointment_time,
                        a.status,
                        a.patient_id,
                        a.service_id,
                        p.full_name AS patient_name,
                        s.service_name

                    FROM appointments a

                    INNER JOIN patients p
                        ON a.patient_id = p.patient_id

                    INNER JOIN services s
                        ON a.service_id = s.service_id

                    ORDER BY
                        a.appointment_date ASC,
                        a.appointment_time ASC";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {

            error_log(
                "Get Appointments Error: "
                . $e->getMessage()
            );

            return [];
        }
    }


    /*
    =========================================
    GET ONE APPOINTMENT
    =========================================
    */

    public function getAppointment($id)
    {
        try {

            $sql = "SELECT
                        appointment_id,
                        appointment_date,
                        appointment_time,
                        status,
                        patient_id,
                        service_id

                    FROM appointments

                    WHERE appointment_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {

            error_log(
                "Get Appointment Error: "
                . $e->getMessage()
            );

            return false;
        }
    }


    /*
    =========================================
    UPDATE APPOINTMENT
    =========================================
    */

    public function updateAppointment(
        $id,
        $appointment_date,
        $appointment_time,
        $status,
        $patient_id,
        $service_id
    ) {
        try {

            $sql = "UPDATE appointments

                    SET
                        appointment_date = :appointment_date,
                        appointment_time = :appointment_time,
                        status = :status,
                        patient_id = :patient_id,
                        service_id = :service_id

                    WHERE appointment_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ":appointment_date",
                $appointment_date,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":appointment_time",
                $appointment_time,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":status",
                $status,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":patient_id",
                $patient_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ":service_id",
                $service_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            return $stmt->execute();

        } catch (PDOException $e) {

            error_log(
                "Update Appointment Error: "
                . $e->getMessage()
            );

            return false;
        }
    }


    /*
    =========================================
    DELETE APPOINTMENT
    =========================================
    */

    public function deleteAppointment($id)
    {
        try {

            $sql = "DELETE FROM appointments
                    WHERE appointment_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ":id",
                $id,
                PDO::PARAM_INT
            );

            return $stmt->execute();

        } catch (PDOException $e) {

            error_log(
                "Delete Appointment Error: "
                . $e->getMessage()
            );

            return false;
        }
    }


    /*
    =========================================
    SEARCH APPOINTMENTS
    =========================================
    */

    public function searchAppointments($keyword)
    {
        try {

            $sql = "SELECT
                        a.appointment_id,
                        a.appointment_date,
                        a.appointment_time,
                        a.status,
                        a.patient_id,
                        a.service_id,
                        p.full_name AS patient_name,
                        s.service_name

                    FROM appointments a

                    INNER JOIN patients p
                        ON a.patient_id = p.patient_id

                    INNER JOIN services s
                        ON a.service_id = s.service_id

                    WHERE
                        CAST(a.appointment_id AS CHAR)
                        LIKE :keyword

                        OR a.appointment_date
                        LIKE :keyword

                        OR a.appointment_time
                        LIKE :keyword

                        OR a.status
                        LIKE :keyword

                        OR p.full_name
                        LIKE :keyword

                        OR s.service_name
                        LIKE :keyword

                    ORDER BY
                        a.appointment_date ASC,
                        a.appointment_time ASC";

            $stmt = $this->conn->prepare($sql);

            $searchKeyword = "%" . $keyword . "%";

            $stmt->bindValue(
                ":keyword",
                $searchKeyword,
                PDO::PARAM_STR
            );

            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {

            error_log(
                "Search Appointment Error: "
                . $e->getMessage()
            );

            return [];
        }
    }
}
?>