CREATE DATABASE IF NOT EXISTS booking_system;
USE booking_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    faculty_name VARCHAR(50) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    UNIQUE (faculty_name, time_slot)
);
