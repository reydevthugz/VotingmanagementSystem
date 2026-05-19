-- Voting Management System Database Schema
-- Generated for Laragon phpMyAdmin

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    course VARCHAR(255) NOT NULL,
    year VARCHAR(50) NOT NULL,
    section VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    voting_status ENUM('not_voted', 'voted') DEFAULT 'not_voted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Elections table
CREATE TABLE IF NOT EXISTS elections (
    election_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Positions table
CREATE TABLE IF NOT EXISTS positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(255) NOT NULL,
    max_votes INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Party Lists table
CREATE TABLE IF NOT EXISTS party_lists (
    party_id INT AUTO_INCREMENT PRIMARY KEY,
    party_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Candidates table
CREATE TABLE IF NOT EXISTS candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    photo VARCHAR(500),
    motto TEXT,
    position_id INT,
    party_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (party_id) REFERENCES party_lists(party_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Votes table
CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    election_id INT NOT NULL,
    candidate_id INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Sample data
INSERT IGNORE INTO users (fullname, username, password, role) VALUES
('Admin User', 'admin', '$2y$10$examplehashedpassword', 'admin'),
('Student Voter', 'student', '$2y$10$examplehashedpassword', 'student');

INSERT IGNORE INTO students (fullname, email, course, year, section, password) VALUES
('John Doe', 'johndoe@example.com', 'BS Computer Science', '2nd Year', 'A', '$2y$10$examplehashedpassword'),
('Jane Smith', 'janesmith@example.com', 'BS Information Technology', '3rd Year', 'B', '$2y$10$examplehashedpassword'),
('Leo Martinez', 'leomartinez@example.com', 'BS Business Administration', '1st Year', 'C', '$2y$10$examplehashedpassword');

INSERT IGNORE INTO elections (title, start_date, end_date, status) VALUES
('Student Council Election 2026', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 'active'),
('Alumni Officer Election 2025', DATE_SUB(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 85 DAY), 'inactive');

INSERT IGNORE INTO positions (position_name, max_votes) VALUES
('President', 1),
('Vice President', 1),
('Treasurer', 1);

INSERT IGNORE INTO party_lists (party_name, description) VALUES
('Blue Horizon', 'A party focused on student welfare and innovation.'),
('Green Future', 'A party dedicated to sustainability and community service.'),
('United Voices', 'A party committed to inclusive representation and fairness.');

INSERT IGNORE INTO candidates (fullname, photo, motto, position_id, party_id) VALUES
('Ariana Santos', '', 'Leadership through empathy.', 1, 1),
('Diego Reyes', '', 'Every voice matters.', 2, 2),
('Maya Cruz', '', 'A stronger school community.', 3, 3);

INSERT IGNORE INTO votes (student_id, election_id, candidate_id) VALUES
(1, 2, 1);
