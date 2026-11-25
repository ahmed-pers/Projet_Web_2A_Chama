-- Lost & Found tables for the shared `fixmycity` database.
-- Import fixmycity/config/fixmycity.sql first, then this file.
CREATE DATABASE IF NOT EXISTS fixmycity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fixmycity;

-- The users table comes from fixmycity.sql. It is repeated here with IF NOT EXISTS
-- so this module also works if you import this file alone.
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO users (id, username, email, password, role) VALUES
  (1, 'user1',  'user1@example.com',  '$2y$10$B9OJgl/Y9A05dLd4TYZtsuZ2.qLbFcnJYdxLHcije9.Z/hhkDxkcq', 'user'),
  (2, 'admin1', 'admin1@example.com', '$2y$10$B9OJgl/Y9A05dLd4TYZtsuZ2.qLbFcnJYdxLHcije9.Z/hhkDxkcq', 'admin');

-- From the database model screenshot.
CREATE TABLE IF NOT EXISTS categories (
  id_category INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
  id_item     INT AUTO_INCREMENT PRIMARY KEY,
  id_category INT NOT NULL,                         -- 1 category -> 0..N items
  owner_id    INT NOT NULL,                         -- user who reported the item
  item_name   VARCHAR(150) NOT NULL,
  description VARCHAR(500) NULL,
  type        ENUM('lost', 'found') NOT NULL,
  status      ENUM('pending', 'matched', 'returned') NOT NULL DEFAULT 'pending',
  date_found  DATE NOT NULL,
  image_url   VARCHAR(255) NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_items_status (status),
  KEY idx_items_type (type),
  CONSTRAINT fk_items_category FOREIGN KEY (id_category) REFERENCES categories(id_category) ON DELETE RESTRICT,
  CONSTRAINT fk_items_owner    FOREIGN KEY (owner_id)    REFERENCES users(id)            ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id_message   INT AUTO_INCREMENT PRIMARY KEY,
  id_item      INT NOT NULL,                        -- messages are about one item
  sender_id    INT NOT NULL,
  receiver_id  INT NOT NULL,
  message_text TEXT NOT NULL,
  date_sent    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read      TINYINT(1) NOT NULL DEFAULT 0,
  KEY idx_messages_receiver (receiver_id, is_read),
  CONSTRAINT fk_messages_item    FOREIGN KEY (id_item)     REFERENCES items(id_item) ON DELETE CASCADE,
  CONSTRAINT fk_messages_sender  FOREIGN KEY (sender_id)   REFERENCES users(id)      ON DELETE CASCADE,
  CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (id_category, name, description) VALUES
  (1, 'Electronics', 'Phones, laptops, tablets, cameras'),
  (2, 'Books',       'Books and notebooks'),
  (3, 'Clothing',    'Jackets, shirts, shoes'),
  (4, 'Bags',        'Backpacks, handbags, luggage'),
  (5, 'Keys',        'Keys and key rings'),
  (6, 'Documents',   'ID cards, tickets, papers'),
  (7, 'Other',       'Anything else');

INSERT IGNORE INTO items
  (id_item, id_category, owner_id, item_name, description, type, status, date_found, image_url) VALUES
  (1, 4, 1, 'Blue backpack', 'Blue backpack with a small key chain.', 'lost', 'pending',  '2026-11-20', NULL),
  (2, 1, 2, 'Black wallet',  'Black leather wallet, found near the entrance.', 'found', 'pending', '2026-11-21', NULL),
  (3, 5, 1, 'Silver keys',   'Three keys on a silver ring.', 'found', 'matched', '2026-11-18', NULL),
  (4, 2, 2, 'Blue notebook', 'Calculus notebook, name on the first page.', 'lost', 'returned', '2026-11-15', NULL);

INSERT IGNORE INTO messages
  (id_message, id_item, sender_id, receiver_id, message_text, is_read) VALUES
  (1, 3, 2, 1, 'I think the silver keys are yours. Claim them at Counter 2.', 0);
