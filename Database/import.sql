-- This file contains the SQL commands to create the database and the tables for the application in Supabase

-- First, create the roles table since it's referenced by the users table
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL
);

-- This table will store all information about the users. There is a difference between the rights of customers, secdesks workers and admins.
-- Customers can only see their own tests and vulnerabilities.
-- Secdesks can see all tests and vulnerabilities, but they cannot delete them. They can only mark them as solved.
-- Admins can see all tests and vulnerabilities, and they can delete them. They can also create new users with customer, secdesk worker and admin rights.
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INTEGER NOT NULL,
    creation_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
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

-- Create roles first
INSERT INTO roles (name, description)
VALUES 
    ('customer', 'Customer role with limited access to the application.'),
    ('secdesk', 'Secdesk role with access to all tests and vulnerabilities, but cannot delete them.'),
    ('admin', 'Admin role with full access to the application, including the ability to create new users and delete tests and vulnerabilities.');

-- Create test users with each role
INSERT INTO users (
    email,
    password,
    role_id
)
VALUES 
    ('admin@example.com', 'admin', 3),
    ('customer@example.com', 'customer', 1),
    ('secdesk@example.com', 'secdesk', 2);

-- Add more customers
INSERT INTO users (email, password, role_id)
VALUES 
    ('customer1@example.com', 'password1', 1),
    ('customer2@example.com', 'password2', 1),
    ('customer3@example.com', 'password3', 1),
    ('customer4@example.com', 'password4', 1),
    ('customer5@example.com', 'password5', 1);

-- Add targets for customers
INSERT INTO target (user_id, test_name, status, test_description)
VALUES 
    (2, 'Website Security Test', 'In Progress', 'Testing the security of the customer''s website.'),
    (3, 'API Penetration Test', 'Completed', 'Penetration testing of the customer''s API endpoints.'),
    (4, 'Mobile App Security Test', 'Pending', 'Security testing for the customer''s mobile application.'),
    (5, 'Network Vulnerability Assessment', 'In Progress', 'Assessing vulnerabilities in the customer''s network.'),
    (6, 'Cloud Security Audit', 'Completed', 'Auditing the security of the customer''s cloud infrastructure.');

-- Add vulnerabilities for targets
INSERT INTO vulnerabilities (
    test_id, 
    affected_entity, 
    risk_statement, 
    affected_component, 
    residual_risk, 
    classification, 
    identified_controls, 
    cvss_score, 
    likelihood, 
    cvssv3_code, 
    location, 
    vulnerabilities_description, 
    recommendations
)
VALUES 
    (1, 'Web Server', 'Sensitive data exposure', 'SSL Configuration', 'High', 'Confidentiality', 'Enable HTTPS and enforce strong ciphers', 7.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:N', '/login', 'The login page does not enforce HTTPS, exposing sensitive data.', 'Enforce HTTPS and use strong SSL/TLS configurations.'),
    (2, 'API Gateway', 'Broken authentication', 'Token Validation', 'Medium', 'Integrity', 'Implement token expiration and validation', 6.8, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:H/A:N', '/api/auth', 'The API does not validate tokens properly, allowing unauthorized access.', 'Implement proper token validation and expiration.'),
    (3, 'Mobile App', 'Insecure data storage', 'Local Storage', 'High', 'Confidentiality', 'Encrypt sensitive data before storing', 8.2, 'High', 'CVSS:3.1/AV:L/AC:L/PR:N/UI:N/S:U/C:H/I:L/A:N', '/user/data', 'Sensitive user data is stored in plaintext on the device.', 'Encrypt all sensitive data before storing it locally.'),
    (4, 'Network', 'Open ports', 'Firewall Configuration', 'Medium', 'Availability', 'Restrict access to unnecessary ports', 5.3, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:H', '192.168.1.1', 'Several unnecessary ports are open, increasing the attack surface.', 'Close all unnecessary ports and enforce strict firewall rules.'),
    (5, 'Cloud Storage', 'Misconfigured permissions', 'Bucket Policy', 'High', 'Confidentiality', 'Restrict public access to sensitive buckets', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:N', '/cloud/bucket', 'Sensitive data is publicly accessible due to misconfigured permissions.', 'Restrict public access and enforce least privilege policies.');