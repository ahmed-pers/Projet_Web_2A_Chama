-- Smart Queue : deux entités liées par une table de jointure
-- services <-> service_counter <-> counters

CREATE DATABASE IF NOT EXISTS smart_queue
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE smart_queue;

CREATE TABLE IF NOT EXISTS services (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(50) NOT NULL UNIQUE,
  description   VARCHAR(255) DEFAULT NULL,
  average_time  INT NOT NULL DEFAULT 10,
  status        ENUM('open', 'closed') NOT NULL DEFAULT 'open',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS counters (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  number        VARCHAR(20) NOT NULL UNIQUE,
  location      VARCHAR(100) DEFAULT NULL,
  status        ENUM('open', 'closed') NOT NULL DEFAULT 'open',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_counter (
  service_id    INT NOT NULL,
  counter_id    INT NOT NULL,
  assigned_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (service_id, counter_id),
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (counter_id) REFERENCES counters(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO services (name, description, average_time, status) VALUES
  ('Customer Service', 'General questions and help', 9, 'open'),
  ('Payments', 'Bills and invoices', 6, 'open'),
  ('Appointments', 'Scheduled meetings', 12, 'open'),
  ('Documents', 'Certificates and permits', 15, 'open'),
  ('Technical Support', 'Technical problems', 18, 'closed'),
  ('New Accounts', 'Open a new account', 20, 'open');

INSERT INTO counters (number, location, status) VALUES
  ('Counter 1', 'Ground floor', 'open'),
  ('Counter 2', 'Ground floor', 'open'),
  ('Counter 3', 'Ground floor', 'closed'),
  ('Counter 4', 'First floor', 'open'),
  ('Counter 5', 'First floor', 'open'),
  ('Counter 6', 'First floor', 'open');

INSERT INTO service_counter (service_id, counter_id) VALUES
  (1, 1), (1, 2), (1, 4),
  (2, 2), (2, 5),
  (3, 1), (3, 4),
  (4, 5), (4, 6),
  (5, 6),
  (6, 4), (6, 6);
