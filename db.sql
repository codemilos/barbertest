CREATE DATABASE barbershop_booking;

USE barbershop_booking;

CREATE TABLE chairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chair_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    UNIQUE KEY unique_booking (chair_id, booking_date, booking_time),
    FOREIGN KEY (chair_id) REFERENCES chairs(id)
);

INSERT INTO chairs (name) VALUES ('Chair 1'), ('Chair 2');
