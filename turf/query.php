-- Create the database
CREATE DATABASE IF NOT EXISTS turf;
USE turf;

-- Address Table
CREATE TABLE tbl_address (
  address_id INT AUTO_INCREMENT PRIMARY KEY,
  landmark VARCHAR(50),
  street VARCHAR(50),
  city VARCHAR(50),
  district VARCHAR(50),
  state VARCHAR(50),
  pin_code VARCHAR(6)
);

-- Admin Table
CREATE TABLE admin (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

-- Owner Table
CREATE TABLE owner (
  owner_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  address_id INT,
  is_deleted BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (address_id) REFERENCES tbl_address(address_id)
);

-- Customer Table
CREATE TABLE customer (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(15) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_deleted BOOLEAN DEFAULT FALSE
);

-- Turf Table
CREATE TABLE turf (
  turf_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category VARCHAR(50),
  size VARCHAR(20),
  image_path VARCHAR(255),
  map_url VARCHAR(255),
  grass_type VARCHAR(50),
  owner_id INT,
  address_id INT,
  is_approved BOOLEAN DEFAULT FALSE,
  approved_by_admin_id INT,
  is_deleted BOOLEAN DEFAULT FALSE,
  price_day DECIMAL(10,2),
  price_night DECIMAL(10,2),
  FOREIGN KEY (owner_id) REFERENCES owner(owner_id),
  FOREIGN KEY (address_id) REFERENCES tbl_address(address_id),
  FOREIGN KEY (approved_by_admin_id) REFERENCES admin(admin_id)
);


-- Schedule Table (Time slots)
CREATE TABLE schedule (
  schedule_id INT AUTO_INCREMENT PRIMARY KEY,
  start_time TIME,
  end_time TIME
);

-- Turf Availability
CREATE TABLE turf_availability (
  turf_id INT,
  schedule_id INT,
  day_of_week ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
  is_available BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (turf_id, schedule_id, day_of_week),
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id),
  FOREIGN KEY (schedule_id) REFERENCES schedule(schedule_id)
);

-- Booking Status
CREATE TABLE booking_status (
  status_id INT AUTO_INCREMENT PRIMARY KEY,
  status_name VARCHAR(20) -- e.g., Booked, Cancelled, Completed
);

-- Booking Table
CREATE TABLE booking (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  turf_id INT NOT NULL,
  booking_date DATE NOT NULL,
  status_id INT NOT NULL,
  is_deleted BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id),
  FOREIGN KEY (status_id) REFERENCES booking_status(status_id)
);

-- Feedback Table
CREATE TABLE feedback (
  feedback_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  turf_id INT,
  rating INT CHECK (rating BETWEEN 1 AND 10),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id)
);

-- Reviews Table
CREATE TABLE reviews (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  turf_id INT,
  username VARCHAR(50),
  review_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id)
);

-- Rate Table
CREATE TABLE rate (
  rate_id INT AUTO_INCREMENT PRIMARY KEY,
  turf_id INT,
  day_rate DECIMAL(10,2),
  night_rate DECIMAL(10,2),
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id)
);

-- Cancellation Table
CREATE TABLE cancellation (
  cancellation_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,
  refund_amount DECIMAL(10,2),
  cancellation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES booking(booking_id)
);

-- Payment Table
CREATE TABLE payment (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,
  amount DECIMAL(10,2),
  payment_method VARCHAR(50), -- e.g. UPI, Card, Cash
  payment_status ENUM('Pending', 'Success', 'Failed') DEFAULT 'Pending',
  payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES booking(booking_id)
);

-- Notification Table
CREATE TABLE notification (
  notification_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,
  ndate DATETIME,
  message TEXT,
  FOREIGN KEY (booking_id) REFERENCES booking(booking_id)
);

-- Turf Images (Multiple images per turf)
CREATE TABLE turf_images (
  image_id INT AUTO_INCREMENT PRIMARY KEY,
  turf_id INT,
  image_path VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id)
);

-- Wishlist Table
CREATE TABLE wishlist (
  wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  turf_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id)
);

-- Contact Queries Table
CREATE TABLE contact_queries (
  query_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  subject VARCHAR(100),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Actions Log Table
CREATE TABLE admin_actions (
  action_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT,
  action_type ENUM('DELETE', 'APPROVE', 'BLOCK', 'EDIT'),
  target_table VARCHAR(50),
  target_id INT,
  action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  description TEXT,
  FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
);

CREATE TABLE slots (
  slot_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,  -- Link to booking table
  turf_id INT NOT NULL,
  customer_id INT NOT NULL,  -- New column for customer
  slot_date DATE NOT NULL,
  slot_time TIME NOT NULL,
  is_booked BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (booking_id) REFERENCES booking(booking_id) ON DELETE CASCADE,
  FOREIGN KEY (turf_id) REFERENCES turf(turf_id),
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id)  -- Add this FK
);




INSERT INTO status (status_id, status_name) VALUES 
(1, 'Pending'),
(2, 'Approved'),
(3, 'cancelled'),
(4, 'rejected');