-- ==========================================================
-- QUEUNIC CLINIC MANAGEMENT SYSTEM
-- Database schema (generated from clinic_management_db_vuerd.json)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS clinic_management_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE clinic_management_db;

-- ==========================================================
-- USERS  (system accounts: Admin, Clinic Staff, Employee, Patient)
-- ==========================================================
CREATE TABLE users (
    user_id         INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            VARCHAR(30)  NOT NULL,
    contact_number  VARCHAR(20)
);

-- ==========================================================
-- PATIENTS
-- ==========================================================
CREATE TABLE patients (
    patient_id      INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    age             TINYINT,
    gender          VARCHAR(20),
    id_number       VARCHAR(30),
    contact_number  VARCHAR(20),
    address         VARCHAR(255),
    user_id         INT NULL,
    CONSTRAINT fk_patients_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE SET NULL
);

-- ==========================================================
-- SERVICES
-- ==========================================================
CREATE TABLE services (
    service_id        INT AUTO_INCREMENT PRIMARY KEY,
    service_name       VARCHAR(100) NOT NULL,
    description         TEXT,
    average_duration    INT COMMENT 'in minutes'
);

-- ==========================================================
-- APPOINTMENTS
-- ==========================================================
CREATE TABLE appointments (
    appointment_id     INT AUTO_INCREMENT PRIMARY KEY,
    appointment_date   DATE NOT NULL,
    appointment_time   TIME NOT NULL,
    status              VARCHAR(30) NOT NULL DEFAULT 'Pending',
    patient_id          INT NOT NULL,
    service_id          INT NOT NULL,
    CONSTRAINT fk_appt_patient
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_appt_service
        FOREIGN KEY (service_id) REFERENCES services(service_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- QUEUE
-- ==========================================================
CREATE TABLE queue (
    queue_id       INT AUTO_INCREMENT PRIMARY KEY,
    queue_number   INT NOT NULL,
    status         VARCHAR(30) NOT NULL DEFAULT 'Waiting',
    time_in        DATETIME,
    time_called    DATETIME NULL,
    time_out       DATETIME NULL,
    patient_id     INT NOT NULL,
    service_id     INT NOT NULL,
    CONSTRAINT fk_queue_patient
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_queue_service
        FOREIGN KEY (service_id) REFERENCES services(service_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- CONSULTATIONS
-- ==========================================================
CREATE TABLE consultations (
    consultation_id   INT AUTO_INCREMENT PRIMARY KEY,
    diagnosis         TEXT,
    prescription      TEXT,
    notes             TEXT,
    date              DATE NOT NULL,
    user_id           INT NOT NULL,
    queue_id          INT NOT NULL,
    CONSTRAINT fk_consult_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_consult_queue
        FOREIGN KEY (queue_id) REFERENCES queue(queue_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- MEDICAL RECORDS
-- ==========================================================
CREATE TABLE medical_records (
    record_id         INT AUTO_INCREMENT PRIMARY KEY,
    medical_history    TEXT,
    allergies          TEXT,
    notes              TEXT,
    date_updated       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    patient_id         INT NOT NULL,
    CONSTRAINT fk_records_patient
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- NOTIFICATIONS
-- ==========================================================
CREATE TABLE notifications (
    notification_id   INT AUTO_INCREMENT PRIMARY KEY,
    message            TEXT NOT NULL,
    date_sent          DATETIME DEFAULT CURRENT_TIMESTAMP,
    status              VARCHAR(30) NOT NULL DEFAULT 'Unread',
    patient_id          INT NOT NULL,
    CONSTRAINT fk_notif_patient
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- ACTIVITY LOGS
-- ==========================================================
CREATE TABLE activity_logs (
    log_id       INT AUTO_INCREMENT PRIMARY KEY,
    action        VARCHAR(100) NOT NULL,
    timestamp     DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id       INT NOT NULL,
    CONSTRAINT fk_log_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- ==========================================================
-- Seed an initial admin account so you can log in immediately.
-- Username: admin   Password: admin123
-- (hash below is password_hash('admin123', PASSWORD_DEFAULT))
-- ==========================================================
INSERT INTO users (full_name, username, password, role, contact_number)
VALUES (
    'System Administrator',
    'admin',
    '$2y$10$vDUJSjpIp6voMZ43t4le1O5h.7NVLEeQgbdoiBTfl1aZET87mvuLq',
    'Admin',
    '09000000000'
);
