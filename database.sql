-- Sports Club Management System
-- Database: sports_club_db

CREATE DATABASE IF NOT EXISTS sports_club_db;
USE sports_club_db;

-- Table 1: users (Stores admin and player login accounts)

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'player') NOT NULL DEFAULT 'player'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table 2: teams (Stores sports club teams)

CREATE TABLE IF NOT EXISTS teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    sport VARCHAR(50) NOT NULL,
    category ENUM('Men', 'Women', 'Mixed') NOT NULL,
    coach_name VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table 3: players (Stores registered club players)

CREATE TABLE IF NOT EXISTS players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    contact VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sport VARCHAR(50) NOT NULL,
    team_id INT NULL,
    join_date DATE NOT NULL,
    CONSTRAINT fk_players_team
        FOREIGN KEY (team_id) REFERENCES teams(team_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table 4: events (Stores club matches and events)

CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    sport VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('Upcoming', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Sample Data: Default Admin Account
-- Username: admin
-- Password: admin123

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$12$vm0WSghn2jQ5VgnOdfX7bOSXEa1xWu2i85k5zkS9SkgFUqzqq31gm', 'admin');


-- Sample Data: Teams

INSERT INTO teams (team_name, sport, category, coach_name, description) VALUES
('Titans FC', 'Football', 'Men', 'Coach Marcus Reid', 'Senior men competitive football squad competing in the regional league.'),
('Blaze Hoops', 'Basketball', 'Women', 'Coach Elena Vance', 'Women varsity and club basketball team focusing on fast-break play.'),
('Apex Strikers', 'Volleyball', 'Mixed', 'Coach David Chen', 'Mixed recreational and tournament indoor volleyball team.'),
('Ace Racquets', 'Tennis', 'Mixed', 'Coach Sophia Patel', 'Club tennis training and inter-club singles/doubles squad.');


-- Sample Data: Players

INSERT INTO players (full_name, dob, gender, contact, email, sport, team_id, join_date) VALUES
('Liam Carter', '2001-05-14', 'Male', '555-0101', 'liam.carter@example.com', 'Football', 1, '2025-01-15'),
('Noah Bennett', '2002-09-22', 'Male', '555-0102', 'noah.bennett@example.com', 'Football', 1, '2025-02-03'),
('Maya Lin', '2003-03-11', 'Female', '555-0103', 'maya.lin@example.com', 'Basketball', 2, '2025-02-18'),
('Chloe Brooks', '2001-11-30', 'Female', '555-0104', 'chloe.brooks@example.com', 'Basketball', 2, '2025-03-05'),
('Ethan Morales', '2000-07-08', 'Male', '555-0105', 'ethan.morales@example.com', 'Volleyball', 3, '2025-03-12'),
('Aria Singh', '2002-12-19', 'Female', '555-0106', 'aria.singh@example.com', 'Tennis', 4, '2025-04-01');


-- Sample Data: Events

INSERT INTO events (event_name, sport, event_date, start_time, location, description, status) VALUES
('Inter-University Football Cup', 'Football', '2026-11-28', '15:00:00', 'Main Club Stadium', 'Quarter-final match between Titans FC and Metro Rovers.', 'Upcoming'),
('City Basketball Invitational', 'Basketball', '2026-12-05', '18:30:00', 'Indoor Arena Court A', 'Evening tournament showcase featuring Blaze Hoops.', 'Upcoming'),
('Autumn Volleyball Open', 'Volleyball', '2026-11-14', '10:00:00', 'Sports Complex Hall 2', 'All-day mixed doubles and team bracket matches.', 'Ongoing'),
('Annual Club Tennis Gala', 'Tennis', '2026-10-12', '09:00:00', 'North Clay Courts', 'Annual singles and mixed doubles championship.', 'Completed'),
('Summer Charity Friendly', 'Football', '2026-09-20', '16:00:00', 'Community Pitch', 'Friendly exhibition match postponed due to heavy rain.', 'Cancelled');