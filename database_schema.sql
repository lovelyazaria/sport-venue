-- Sports Field Booking Database Schema
-- Run this script in MySQL to create the database and tables

CREATE DATABASE IF NOT EXISTS sports_booking;
USE sports_booking;

-- Users table (for future expansion, e.g., user accounts)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sports table
CREATE TABLE sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    max_players INT,
    duration_hours INT DEFAULT 1
);

-- Fields table (different fields for each sport)
CREATE TABLE fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (sport_id) REFERENCES sports(id)
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    sport_id INT NOT NULL,
    field_id INT,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration_hours INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (sport_id) REFERENCES sports(id),
    FOREIGN KEY (field_id) REFERENCES fields(id)
);

-- Insert sample sports data
INSERT INTO sports (name, description, max_players, duration_hours) VALUES
('Football', 'Grass and turf fields available', 22, 1),
('Basketball', 'Indoor and outdoor courts', 10, 1),
('Tennis', 'Clay and hard courts', 4, 1),
('Volleyball', 'Beach and indoor volleyball', 12, 1),
('Badminton', 'Indoor courts for badminton', 4, 1),
('Swimming', 'Pool reservations', 50, 1);

-- Insert sample fields
INSERT INTO fields (sport_id, field_name, location) VALUES
(1, 'Main Football Field', 'Stadium A'),
(1, 'Training Field 1', 'Field B'),
(2, 'Indoor Court 1', 'Sports Center'),
(2, 'Outdoor Court', 'Park Area'),
(3, 'Clay Court 1', 'Tennis Club'),
(3, 'Hard Court 1', 'Tennis Club'),
(4, 'Beach Volleyball Court', 'Beach Area'),
(4, 'Indoor Volleyball Court', 'Gym'),
(5, 'Badminton Court 1', 'Sports Hall'),
(5, 'Badminton Court 2', 'Sports Hall'),
(6, 'Main Pool', 'Aquatic Center');

-- Create index for better query performance
CREATE INDEX idx_bookings_date_time ON bookings (booking_date, booking_time);
CREATE INDEX idx_bookings_email ON bookings (user_email);
