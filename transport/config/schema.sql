USE fixmycity;

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
