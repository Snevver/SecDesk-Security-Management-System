CREATE DATABBASE IF NOT EXISTS pentest_db;
USE pentest_db;


-- This table will store all information about the users. There is a difference between the rights of customers, secdesks workers and admins.
-- Customers can only see their own tests and vulnerabilities.
-- Secdesks can see all tests and vulnerabilities, but they cannot delete them. They can only mark them as solved.
-- Admins can see all tests and vulnerabilities, and they can delete them. They can also create new users with customer, secdesk worker and admin rights.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    isCustomer BOOLEAN DEFAULT FALSE,
    isSecDesk BOOLEAN DEFAULT FALSE,
    isAdmin BOOLEAN DEFAULT FALSE,
    creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- The target table will store all information about the targets. Each target is linked to a user (customer), who ownes the target.
-- Each target exists of multiple vulnerabilities. Those are stored in the vulnerabilities table.
CREATE TABLE IF NOT EXISTS target (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    test_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    test_description TEXT NOT NULL,
    test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- The vulnerabilities table will store all information about the vulnerabilities. Each vulnerability is linked to a target.
-- Each vulnerability contains information about the affected entity, risk statement, affected component, residual risk, classification, identified controls, CVSS score, likelihood, CVSSv3 code, location, vulnerabilities description and recommendations.
-- These pieces of information are filled in by the secdesk worker who is working on the target.
-- The vulnerabilities table also contains a solved column, which is set to true when the vulnerability is solved. This is done by the secdesk worker.
CREATE TABLE IF NOT EXISTS vulnerabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    affected_entity VARCHAR(255) NOT NULL,
    risk_statement TEXT NOT NULL,
    affected_component VARCHAR(255) NOT NULL,
    residual_risk TEXT NOT NULL,
    classification VARCHAR(255) NOT NULL,
    identified_controls TEXT NOT NULL,
    cvss_score FLOAT NOT NULL,
    likelihood VARCHAR(255) NOT NULL,
    cvssv3_code TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    vulnerabilities_description TEXT NOT NULL,
    reccommendations TEXT NOT NULL,
    solved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

-- The notes table will store notes that the customer can fill in for each vulnerability.
-- This table is linked to the vulnerabilities table. Each note is linked to a vulnerability.
-- The notes table contains information about the note text and the date it was created.
CREATE TABLE IF NOT EXISTS user_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vulnerability_id INT NOT NULL,
    note_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vulnerability_id) REFERENCES vulnerabilities(id) ON DELETE CASCADE
);

-- One user with Admin privileges
INSERT INTO users (username, password, isCustomer, isSecDesk, isAdmin) VALUES
('admin', 'admin', FALSE, FALSE, TRUE);