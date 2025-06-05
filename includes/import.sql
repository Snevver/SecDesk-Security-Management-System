-- This file contains the SQL commands to create the database and the tables for the application in Supabase

-- The roles table will store all information about the roles. Each role has a unique ID, a name and a description.
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL
);

-- This table will store all information about the users.
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INTEGER NOT NULL,
    creation_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- The tests table will store all information about the tests. Each test is linked to a customer and a pentester.
CREATE TABLE IF NOT EXISTS tests (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    pentester_id INTEGER NOT NULL,
    test_name VARCHAR(255) NULL,
    test_description TEXT NULL,
    test_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pentester_id) REFERENCES users(id) ON DELETE CASCADE
);

-- The targets table will store all information about the targets. Each target is linked to a specific test.
CREATE TABLE IF NOT EXISTS targets (
    id SERIAL PRIMARY KEY,
    test_id INTEGER NOT NULL,
    target_name VARCHAR(255) NULL,
    target_description TEXT NULL,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

-- The vulnerabilities table will store all information about the vulnerabilities. Each vulnerability is linked to a specific target.
CREATE TABLE IF NOT EXISTS vulnerabilities (
    id SERIAL PRIMARY KEY,
    target_id INTEGER NOT NULL,
    affected_entity VARCHAR(255) NULL,
    identifier VARCHAR(255),
    risk_statement TEXT NULL,
    affected_component VARCHAR(255) NULL,
    residual_risk TEXT NULL,
    classification VARCHAR(255) NULL,
    identified_controls TEXT NULL,
    cvss_score FLOAT NULL,
    likelihood VARCHAR(255) NULL,
    cvssv3_code TEXT NULL,
    location VARCHAR(255) NULL,
    vulnerabilities_description TEXT NULL,
    reproduction_steps TEXT,
    impact TEXT,
    remediation_difficulty VARCHAR(255),
    recommendations TEXT NULL,
    recommended_reading TEXT,
    response TEXT,
    solved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (target_id) REFERENCES targets(id) ON DELETE CASCADE
);

-- Per default, the database will be created with the following roles:
-- 1. customer: Regular user with limited access to the system.
-- 2. pentester: User with access to all users tests and view results.
-- 3. admin: User with full access to the system, including user and test management.
INSERT INTO roles (id, name, description) VALUES (1, 'customer', 'Customers have limited access to the application.'), (2, 'pentester', 'Pentesters have alot more priviledges, but are not almighty.' ), (3, 'admin', 'Admins have full access to the application.' );

-- One admin account is created by default.
INSERT INTO users (email, password, role_id)
VALUES ('email@placeholder.com', 'password', 3);
