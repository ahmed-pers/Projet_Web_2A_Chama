-- Run once in phpMyAdmin (Import) or: mysql -u root < config/fixmycity.sql
CREATE DATABASE IF NOT EXISTS fixmycity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixmycity;

-- Teammate's table (same DDL as the workshop). IF NOT EXISTS: harmless if it already exists.
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------- my 3 entities ----------------
CREATE TABLE IF NOT EXISTS department (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL UNIQUE,
  contact_email VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS problem_category (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL UNIQUE,
  department_id INT NOT NULL,
  CONSTRAINT fk_category_department FOREIGN KEY (department_id)
    REFERENCES department(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS report (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,                       -- NEW: author (FK to the teammate's users)
  category_id INT NOT NULL,
  photo       VARCHAR(255) NOT NULL,              -- relative path, e.g. uploads/ab12....jpg
  address     VARCHAR(255) NULL,                  -- NEW: nullable (address mode)
  latitude    DECIMAL(9,6) NULL,                  -- now nullable
  longitude   DECIMAL(9,6) NULL,                  -- now nullable
  description VARCHAR(500) NULL,
  status      ENUM('pending','in_progress','resolved') NOT NULL DEFAULT 'pending',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_report_status (status),
  CONSTRAINT fk_report_user     FOREIGN KEY (user_id)     REFERENCES users(id)            ON DELETE CASCADE,
  CONSTRAINT fk_report_category FOREIGN KEY (category_id) REFERENCES problem_category(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------- seed data ----------------
INSERT IGNORE INTO department (id, name, contact_email) VALUES
  (1, 'Roads',          'roads@city.example'),
  (2, 'Cleaning',       'cleaning@city.example'),
  (3, 'Animal control', 'animals@city.example'),
  (4, 'Lighting',       'lighting@city.example'),
  (5, 'Water',          'water@city.example'),
  (6, 'General',        'contact@city.example');

INSERT IGNORE INTO problem_category (id, name, department_id) VALUES
  (1, 'Hole in the road',        1),
  (2, 'Trash / illegal dumping', 2),
  (3, 'Wild dogs',               3),
  (4, 'Broken street light',     4),
  (5, 'Water leak',              5),
  (6, 'Other',                   6);

-- ---------------- vehicle rental ----------------
CREATE TABLE IF NOT EXISTS rental_vehicle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type ENUM('Car','SUV','Van','Motorcycle','Scooter','Bicycle') NOT NULL,
  location VARCHAR(100) NOT NULL,
  details VARCHAR(255) NULL,
  price_per_day DECIMAL(10,2) NOT NULL,
  status ENUM('available','rented','maintenance') NOT NULL DEFAULT 'available',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- DEV ONLY (delete once the teammate's register/login works): one user and one admin.
-- Both use the workshop's test password: User@1234
INSERT IGNORE INTO users (id, username, email, password, role) VALUES
  (1, 'user1',  'user1@example.com',  '$2y$10$B9OJgl/Y9A05dLd4TYZtsuZ2.qLbFcnJYdxLHcije9.Z/hhkDxkcq', 'user'),
  (2, 'admin1', 'admin1@example.com', '$2y$10$B9OJgl/Y9A05dLd4TYZtsuZ2.qLbFcnJYdxLHcije9.Z/hhkDxkcq', 'admin');
