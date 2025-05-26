-- This file contains mock data and is not to be used in production

-- Users
INSERT INTO users (email, password, role_id) VALUES 
    ('pentester@example.com', 'pentester', 2),
    ('admin@example.com', 'password3', 3),
    ('alice@example.com', 'alice123', 1),
    ('bob@example.com', 'bob123', 1),
    ('carol@example.com', 'carol123', 1),
    ('dave@example.com', 'dave123', 1),
    ('eve@example.com', 'eve123', 1),
    ('frank@example.com', 'frank123', 1),
    ('grace@example.com', 'grace123', 1),
    ('heidi@example.com', 'heidi123', 1),
    ('ivan@example.com', 'ivan123', 1),
    ('judy@example.com', 'judy123', 1);

-- Tests
INSERT INTO tests (user_id, test_name, test_description) VALUES 
    (3, 'Web App Test', 'Testing a web application for security issues.'),
    (4, 'E-Commerce Security Review', 'Audit of online store security.'),
    (5, 'Healthcare System Audit', 'Security testing for hospital software.'),
    (6, 'Online Banking PenTest', 'Penetration test of banking platform.'),
    (7, 'Educational Portal Review', 'Review of security in LMS system.'),
    (3, 'API Security Test', 'Testing API endpoints for vulnerabilities.'),
    (3, 'Mobile App Review', 'Security review of the mobile application.'),
    (4, 'Payment System Audit', 'Audit of payment processing system.'),
    (5, 'Cloud Security Assessment', 'Assessment of cloud infrastructure.'),
    (6, 'Legacy System PenTest', 'Penetration test of legacy systems.'),
    (8, 'IoT Device Security', 'Security review of IoT devices.'),
    (9, 'Network Segmentation Test', 'Testing network segmentation and isolation.');

-- Targets
INSERT INTO targets (test_id, target_name, target_description) VALUES 
    (1, 'Login Page', 'The login page of the web application.'),
    (1, 'Dashboard', 'The dashboard users see after logging in.'),
    (2, 'Web Shop', 'Front-end of the e-commerce site.'),
    (2, 'Payment Gateway', 'Integration with payment processor.'),
    (3, 'Patient Portal', 'Web portal for patients to manage appointments.'),
    (4, 'Medical Database', 'Stores sensitive patient data.'),
    (5, 'Login System', 'Authentication and session management.'),
    (5, 'Transaction Engine', 'Handles all financial transfers.'),
    (6, 'API Gateway', 'Main entry point for API requests.'),
    (6, 'User Service', 'Handles user data and authentication.'),
    (7, 'Mobile Login', 'Login screen of the mobile app.'),
    (7, 'Push Notification Service', 'Handles push notifications.'),
    (8, 'Payment Processor', 'Handles all payment transactions.'),
    (8, 'Fraud Detection', 'Monitors for fraudulent activity.'),
    (9, 'Cloud Storage', 'Stores user files and backups.'),
    (10, 'Legacy DB', 'Old database system.'),
    (10, 'Legacy Web', 'Old web interface.'),
    (11, 'IoT Hub', 'Central hub for IoT devices.'),
    (12, 'Firewall', 'Network firewall.'),
    (12, 'Router', 'Network router.'),
    (12, 'Switch', 'Network switch.');

-- Vulnerabilities
INSERT INTO vulnerabilities (
  target_id, affected_entity, identifier, risk_statement, affected_component,
  residual_risk, classification, identified_controls, cvss_score, 
  likelihood, cvssv3_code, location, vulnerabilities_description, reproduction_steps, impact, remediation_difficulty, recommendations, recommended_reading, response
) VALUES 
    (1, 'Username Field', NULL, 'Potential for brute force attack.', 'Authentication', 'High', 'Confidentiality', 'Rate limiting, account lockout.', 7.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', '/login', 'Login endpoint is susceptible to brute force attacks.', NULL, NULL, NULL, 'Implement CAPTCHA and account lockout policies.', NULL, NULL),
    (1, 'Session Token', NULL, 'Session token can be guessed.', 'Session Management', 'High', 'Integrity', 'Use strong random tokens.', 8.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/dashboard', 'Session tokens are predictable.', NULL, NULL, NULL, 'Use cryptographically secure random session tokens.', NULL, NULL),
    (1, 'Login Form', NULL, 'SQL Injection', 'Input Field', 'High', 'Integrity', 'Use parameterized queries', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '/login', 'Form allows injection of SQL commands.', NULL, NULL, NULL, 'Sanitize and validate all inputs.', NULL, NULL),
    (2, 'Product Page', NULL, 'Cross-Site Scripting (XSS)', 'Script Injection', 'Medium', 'Confidentiality', 'Escape output content', 6.4, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N', '/product?id=1', 'User input rendered without escaping.', NULL, NULL, NULL, 'Escape HTML in user output.', NULL, NULL),
    (2, 'Callback URL', NULL, 'Unvalidated Redirects', 'Redirect Logic', 'Medium', 'Integrity', 'Validate redirect destinations', 5.3, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:L/A:N', '/callback', 'Redirects can be manipulated.', NULL, NULL, NULL, 'Use a whitelist for redirects.', NULL, NULL),
    (2, 'API Auth', NULL, 'Missing Rate Limiting', 'Login Endpoint', 'High', 'Availability', 'Add rate limits to endpoints', 7.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:H', '/api/auth', 'No rate limit allows brute force.', NULL, NULL, NULL, 'Implement request throttling.', NULL, NULL),
    (3, 'Appointment Viewer', NULL, 'Broken Access Control', 'Role Permissions', 'High', 'Confidentiality', 'Check role before page access', 8.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:C/C:H/I:N/A:N', '/appointments', 'Patients can view other records.', NULL, NULL, NULL, 'Enforce permission checks.', NULL, NULL),
    (3, 'Messages', NULL, 'Stored XSS', 'Message Renderer', 'Medium', 'Confidentiality', 'Sanitize user input on save', 6.7, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:R/S:C/C:L/I:L/A:N', '/messages', 'Malicious scripts saved in chat.', NULL, NULL, NULL, 'Escape stored user data.', NULL, NULL),
    (3, 'Backup System', NULL, 'Unencrypted Backup Files', 'Storage', 'High', 'Confidentiality', 'Encrypt backups at rest', 7.9, 'High', 'CVSS:3.1/AV:L/AC:L/PR:H/UI:N/S:U/C:H/I:N/A:N', '/backup', 'Backup files stored unencrypted.', NULL, NULL, NULL, 'Encrypt backup volumes.', NULL, NULL),
    (4, 'DB Admin Panel', NULL, 'Default Credentials', 'Admin UI', 'Critical', 'Integrity', 'Change default passwords', 9.0, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:C/C:H/I:H/A:H', '/admin', 'Login uses default admin/admin.', NULL, NULL, NULL, 'Enforce credential rotation.', NULL, NULL),
    (4, 'Login Field', NULL, 'Credential Stuffing Risk', 'Username Field', 'Medium', 'Confidentiality', 'Add CAPTCHA or 2FA', 6.0, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/login', 'No bot prevention measures.', NULL, NULL, NULL, 'Add CAPTCHA, lockout, 2FA.', NULL, NULL),
    (4, 'JWT Tokens', NULL, 'No Expiration', 'Auth Tokens', 'High', 'Confidentiality', 'Set token expiry', 8.0, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:N/A:N', '/api/token', 'Tokens never expire.', NULL, NULL, NULL, 'Expire tokens and rotate regularly.', NULL, NULL),
    (5, 'Transfer Logic', NULL, 'Logic Flaw in Transfers', 'Transfer Flow', 'Critical', 'Integrity', 'Add transaction verification', 9.2, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', '/transfer', 'Logic allows unauthorized fund movement.', NULL, NULL, NULL, 'Verify all transfer intents.', NULL, NULL),
    (5, 'Logs', NULL, 'Sensitive Info in Logs', 'Logging System', 'Medium', 'Confidentiality', 'Avoid logging PII', 5.9, 'Medium', 'CVSS:3.1/AV:L/AC:L/PR:H/UI:N/S:U/C:H/I:N/A:N', '/logs', 'Logs contain card numbers.', NULL, NULL, NULL, 'Redact or exclude sensitive data.', NULL, NULL),
    -- Example detailed vulnerability
    (6, 'Server', 'ABC-004', 'An attacker might be able to compromise the database server', 'Server', 'High', 'Security Misconfiguration', 'None Identified', 7.5, 'High', 'CVSS:3.1/AV:N/AC:H/PR:L/UI:N/S:U/C:H/I:H/A:H', '10.10.10.10:5443', 'The SQL server has configured one or more database links with other databases. The security team managed to get a set of credentials for one of the SQL servers in the domain, which had configured a DB link with another SQL server. The low privileged user from the 1st database had System Administrator privileges (sa) on the 2nd database.',
    'The following steps can be used for validation and remediation verification:\n• Using a tool such as PowerUpSQL, issue the following command to check if the server has configured links. Check if the current user is a system administrator (SysAdmin) on the remote MSSQL server\nGet-SQLServerLinkCrawl -instance [instance]\n• Using a tool such as HeidiSQL, log in to the instance and run the following query:\nSELECT * FROM OPENQUERY("[ip]",''Select @@version'')\n• Enable RPC OUT, RPC and XP_CMDSHELL\n• Run the OS commands using the same command as below',
    'An attacker could enable all features needed (RPC out, RPC, xp_cmdshell) on the remote MSSQL server and then run OS commands as the service account running the SQL service, through the MSSQL DB link. Compromising the database will provide access to sensitive data within it.',
    'Moderate',
    'Database links must be carefully managed to ensure security, especially public database links. If you do not need database links, remove them all. All the database links should be configured with the least privilege; restrict access to those databases/tables that are really needed.',
    'https://docs.microsoft.com/en-us/sql/relational-databases/security/sql-vulnerability-assessment?view=sql-server-ver15\nhttps://www.upguard.com/blog/11-steps-to-secure-sql\nhttps://blog.quest.com/13-sql-server-security-best-practices/\nhttps://docs.microsoft.com/en-us/sql/relational-databases/security/securing',
    NULL
    );
