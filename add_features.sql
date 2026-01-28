-- Add address column to customer table
ALTER TABLE customer ADD COLUMN address TEXT;

-- Create table for blocked dates
CREATE TABLE room_blocked_dates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_name VARCHAR(100) NOT NULL,
  blocked_date DATE NOT NULL,
  UNIQUE(room_name, blocked_date)
);
