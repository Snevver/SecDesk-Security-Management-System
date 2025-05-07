-- This file contains the SQL commands to create the database and the tables for the application in Supabase

-- This table will store all information about the users. There is a difference between the rights of customers, secdesks workers and admins.
-- Customers can only see their own tests and vulnerabilities.
-- Secdesks can see all tests and vulnerabilities, but they cannot delete them. They can only mark them as solved.
-- Admins can see all tests and vulnerabilities, and they can delete them. They can also create new users with customer, secdesk worker and admin rights.
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INTEGER NOT NULL,
    creation_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);



-- The target table will store all information about the targets. Each target is linked to a user (customer), who ownes the target.
-- Each target exists of multiple vulnerabilities. Those are stored in the vulnerabilities table.
CREATE TABLE IF NOT EXISTS target (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    test_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    test_description TEXT NOT NULL,
    test_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



-- The vulnerabilities table will store all information about the vulnerabilities. Each vulnerability is linked to a target.
-- Each vulnerability contains information about the affected entity, risk statement, affected component, residual risk, classification, identified controls, CVSS score, likelihood, CVSSv3 code, location, vulnerabilities description and recommendations.
-- These pieces of information are filled in by the secdesk worker who is working on the target.
-- The vulnerabilities table also contains a solved column, which is set to true when the vulnerability is solved. This is done by the secdesk worker.
CREATE TABLE IF NOT EXISTS vulnerabilities (
    id SERIAL PRIMARY KEY,
    test_id INTEGER NOT NULL,
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
    recommendations TEXT NOT NULL,
    solved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES target(id) ON DELETE CASCADE
);



-- The notes table will store notes that the customer can fill in for each vulnerability.
-- This table is linked to the vulnerabilities table. Each note is linked to a vulnerability.
-- The notes table contains information about the note text and the date it was created.
CREATE TABLE IF NOT EXISTS user_notes (
    id SERIAL PRIMARY KEY,
    vulnerability_id INTEGER NOT NULL,
    note_text TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vulnerability_id) REFERENCES vulnerabilities(id) ON DELETE CASCADE
);


-- The roles table will store all information about the roles. Each role is linked to a user.
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL
);



-- Create a test user with each role
INSERT INTO users (
        email,
        password,
        role_id
    )
VALUES ('admin@example.com', 'admin', 3),
       ('customer@example.com', 'customer', 1),
       ('secdesk@example.com', 'secdesk', 2);


-- Create roles
INSERT INTO roles (name, description)
VALUES ('customer', 'Customer role with limited access to the application.'),
       ('secdesk', 'Secdesk role with access to all tests and vulnerabilities, but cannot delete them.'),
       ('admin', 'Admin role with full access to the application, including the ability to create new users and delete tests and vulnerabilities.');