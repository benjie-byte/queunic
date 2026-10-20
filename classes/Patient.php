<?php

class Patient
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addPatient($full_name, $age, $gender, $id_number, $contact_number, $address, $user_id = null)
    {
        try {
            $sql = "INSERT INTO patients
                        (full_name, age, gender, id_number, contact_number, address, user_id)
                    VALUES
                        (:full_name, :age, :gender, :id_number, :contact_number, :address, :user_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
            $stmt->bindValue(":age", $age, PDO::PARAM_INT);
            $stmt->bindValue(":gender", $gender, PDO::PARAM_STR);
            $stmt->bindValue(":id_number", $id_number, PDO::PARAM_STR);
            $stmt->bindValue(":contact_number", $contact_number, PDO::PARAM_STR);
            $stmt->bindValue(":address", $address, PDO::PARAM_STR);
            $stmt->bindValue(":user_id", $user_id, $user_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Add Patient Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPatients()
    {
        try {
            $sql = "SELECT * FROM patients ORDER BY full_name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Patients Error: " . $e->getMessage());
            return [];
        }
    }

    public function getPatient($id)
    {
        try {
            $sql = "SELECT * FROM patients WHERE patient_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Patient Error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePatient($id, $full_name, $age, $gender, $id_number, $contact_number, $address)
    {
        try {
            $sql = "UPDATE patients
                    SET full_name = :full_name,
                        age = :age,
                        gender = :gender,
                        id_number = :id_number,
                        contact_number = :contact_number,
                        address = :address
                    WHERE patient_id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
            $stmt->bindValue(":age", $age, PDO::PARAM_INT);
            $stmt->bindValue(":gender", $gender, PDO::PARAM_STR);
            $stmt->bindValue(":id_number", $id_number, PDO::PARAM_STR);
            $stmt->bindValue(":contact_number", $contact_number, PDO::PARAM_STR);
            $stmt->bindValue(":address", $address, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Update Patient Error: " . $e->getMessage());
            return false;
        }
    }

    public function deletePatient($id)
    {
        try {
            $sql = "DELETE FROM patients WHERE patient_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete Patient Error: " . $e->getMessage());
            return false;
        }
    }

    public function searchPatients($keyword)
    {
        try {
            $sql = "SELECT * FROM patients
                    WHERE full_name LIKE :keyword
                       OR id_number LIKE :keyword
                       OR contact_number LIKE :keyword
                    ORDER BY full_name ASC";

            $stmt = $this->conn->prepare($sql);
            $searchKeyword = "%" . $keyword . "%";
            $stmt->bindValue(":keyword", $searchKeyword, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Search Patient Error: " . $e->getMessage());
            return [];
        }
    }
}
