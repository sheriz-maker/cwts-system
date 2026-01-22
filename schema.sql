-- ==================================================
-- DATABASE: cwts_system
-- Safe schema for multiple imports
-- ==================================================

CREATE DATABASE IF NOT EXISTS cwts_system;
USE cwts_system;

-- DROP TABLES IF THEY EXIST
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS students;

-- =========================
-- TABLE: students
-- =========================
CREATE TABLE students (
    serial_no INT AUTO_INCREMENT PRIMARY KEY,
    id_number VARCHAR(50) NOT NULL UNIQUE,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    classification ENUM('Regular','Irregular','Failed','Dropped') DEFAULT 'Regular',
    previous_school VARCHAR(150),
    nstp_taken VARCHAR(50),
    campus ENUM('PU Urdaneta','PU Tayug') NOT NULL,
    course VARCHAR(255) NOT NULL,
    birthdate DATE,
    region VARCHAR(100),
    municipality VARCHAR(100),
    barangay VARCHAR(100),
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLE: users
-- =========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLE: attendance
-- =========================
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present','Absent','Excused') NOT NULL,
    recorded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (student_id, date),
    FOREIGN KEY (student_id) REFERENCES students(serial_no),
    FOREIGN KEY (recorded_by) REFERENCES users(user_id)
);

-- =========================
-- TABLE: audit_logs
-- =========================
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    entity VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    old_data TEXT,
    new_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- =========================
-- OPTIONAL: Default admin user
-- =========================
INSERT INTO users (username, password_hash, role)
VALUES ('admin', 
        '$2y$10$wHcUBvXYz6b0GkYN9B5Z2Op8OJ6FhxqKMUfp1dHDD7d1UFVb1qG3W', 
        'admin');
-- password is: admin123 (hashed using password_hash in PHP)

ALTER TABLE attendance
ADD deleted_at DATETIME NULL;