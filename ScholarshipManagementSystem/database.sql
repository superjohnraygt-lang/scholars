-- Create Database
CREATE DATABASE IF NOT EXISTS scholarship_management;
USE scholarship_management;

-- Users Table (for authentication) - Only admin and applicant roles
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'applicant') DEFAULT 'applicant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Scholarship Applications Table
CREATE TABLE IF NOT EXISTS scholarship_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name VARCHAR(100) NOT NULL,
    applicant_phone VARCHAR(20),
    gwa DECIMAL(3,2),
    grade_level VARCHAR(50),
    university VARCHAR(150),
    year_level ENUM('1st Year College', '2nd Year College', '3rd Year College', '4th Year College'),
    address TEXT,
    application_status ENUM('Pending', 'Under Review', 'Approved', 'Rejected', 'Requirements Incomplete') DEFAULT 'Pending',
    requirements_complete BOOLEAN DEFAULT 0,
    parent_guardian_name VARCHAR(100),
    parent_guardian_contact VARCHAR(20),
    certificate_of_grades_uploaded BOOLEAN DEFAULT 0,
    certificate_of_registration_uploaded BOOLEAN DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: password123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@scholarship.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample applicants
INSERT INTO users (username, email, password, role) VALUES 
('juan.cruz', 'juan@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'applicant'),
('maria.santos', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'applicant');

-- Insert sample applications (GWA in 0.00 format - Philippine grading system)
INSERT INTO scholarship_applications 
(applicant_name, applicant_phone, gwa, grade_level, university, year_level, address, 
application_status, requirements_complete, parent_guardian_name, parent_guardian_contact, 
certificate_of_grades_uploaded, certificate_of_registration_uploaded, completed_at) VALUES 
('Juan Pedro Dela Cruz', '09171234567', 1.25, 'College Year 3', 'University of the Philippines', '2nd Year College', 
'123 Rizal Street, Quezon City, Metro Manila', 'Approved', 1, 
'Maria Dela Cruz', '09187654321', 1, 1, NOW()),
('Maria Santos Reyes', '09182345678', 1.50, 'College Year 2', 'Ateneo de Manila University', '1st Year College', 
'456 Bonifacio Ave, Makati City, Metro Manila', 'Under Review', 1, 
'Jose Santos', '09198765432', 1, 1, NOW());