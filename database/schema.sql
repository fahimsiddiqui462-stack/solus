CREATE DATABASE IF NOT EXISTS solus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE solus;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS therapists (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  specialty VARCHAR(160) NOT NULL,
  bio TEXT NOT NULL,
  location VARCHAR(120) NOT NULL DEFAULT 'Online',
  years_experience TINYINT UNSIGNED NOT NULL DEFAULT 1,
  image_url VARCHAR(500) DEFAULT NULL,
  accent VARCHAR(20) NOT NULL DEFAULT '#f6dfc8',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS resources (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL,
  excerpt TEXT NOT NULL,
  content TEXT NOT NULL,
  read_time VARCHAR(40) NOT NULL DEFAULT '5 min read',
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contacts_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  therapist_id INT UNSIGNED DEFAULT NULL,
  preferred_date DATE DEFAULT NULL,
  session_type ENUM('online','in-person') NOT NULL DEFAULT 'online',
  message TEXT DEFAULT NULL,
  status ENUM('new','contacted','confirmed','cancelled') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointment_therapist FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE SET NULL,
  INDEX idx_appointments_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO therapists (name, specialty, bio, location, years_experience, accent) VALUES
('Dr. Maya Chen', 'Anxiety & life transitions', 'A warm, practical approach for people navigating anxiety, change, and the pressure to have everything figured out.', 'Online · California', 12, '#f6dfc8'),
('Jordan Williams, LMFT', 'Relationships & self-worth', 'Jordan helps individuals and couples build clearer communication, stronger boundaries, and more secure connections.', 'Online · New York', 9, '#dcebe7'),
('Priya Nair, LPC', 'Stress & burnout recovery', 'A collaborative space for professionals and caregivers ready to recover their energy and create sustainable routines.', 'Online · Texas', 8, '#ebe4f3')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO resources (title, category, excerpt, content, read_time) VALUES
('A softer way to start a difficult day', 'SELF-CARE', 'Three grounding practices for the moments when your thoughts feel louder than the room.', 'Start by noticing five things you can see, four you can feel, three you can hear, two you can smell, and one you can taste. Then choose one small action that supports the next ten minutes—not the whole day.', '4 min read'),
('What to look for in a therapist', 'GETTING STARTED', 'A simple guide to finding a professional whose approach, experience, and pace feel right for you.', 'Look for someone whose specialty matches what you want help with, then pay attention to how safe and understood you feel during the first conversation. It is okay to meet more than one therapist before deciding.', '6 min read'),
('Building a calmer weekly rhythm', 'MINDFULNESS', 'Small, repeatable habits can make room for steadier energy and more meaningful rest.', 'Choose one anchor habit for morning, one pause in the middle of the day, and one gentle closing ritual at night. Sustainable support is usually built from small actions repeated with compassion.', '5 min read')
ON DUPLICATE KEY UPDATE title = VALUES(title);
