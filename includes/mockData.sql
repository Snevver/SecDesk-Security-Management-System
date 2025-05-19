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
  target_id, affected_entity, risk_statement, affected_component,
  residual_risk, classification, identified_controls, cvss_score, 
  likelihood, cvssv3_code, location, vulnerabilities_description, recommendations
) VALUES 
    (1, 'Username Field', 'Potential for brute force attack.', 'Authentication', 'High', 'Confidentiality', 'Rate limiting, account lockout.', 7.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', '/login', 'Login endpoint is susceptible to brute force attacks.', 'Implement CAPTCHA and account lockout policies.'),
    (1, 'Session Token', 'Session token can be guessed.', 'Session Management', 'High', 'Integrity', 'Use strong random tokens.', 8.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/dashboard', 'Session tokens are predictable.', 'Use cryptographically secure random session tokens.'),
    (1, 'Login Form', 'SQL Injection', 'Input Field', 'High', 'Integrity', 'Use parameterized queries', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '/login', 'Form allows injection of SQL commands.', 'Sanitize and validate all inputs.'),
    (2, 'Product Page', 'Cross-Site Scripting (XSS)', 'Script Injection', 'Medium', 'Confidentiality', 'Escape output content', 6.4, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N', '/product?id=1', 'User input rendered without escaping.', 'Escape HTML in user output.'),
    (2, 'Callback URL', 'Unvalidated Redirects', 'Redirect Logic', 'Medium', 'Integrity', 'Validate redirect destinations', 5.3, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:L/A:N', '/callback', 'Redirects can be manipulated.', 'Use a whitelist for redirects.'),
    (2, 'API Auth', 'Missing Rate Limiting', 'Login Endpoint', 'High', 'Availability', 'Add rate limits to endpoints', 7.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:H', '/api/auth', 'No rate limit allows brute force.', 'Implement request throttling.'),
    (3, 'Appointment Viewer', 'Broken Access Control', 'Role Permissions', 'High', 'Confidentiality', 'Check role before page access', 8.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:C/C:H/I:N/A:N', '/appointments', 'Patients can view other records.', 'Enforce permission checks.'),
    (3, 'Messages', 'Stored XSS', 'Message Renderer', 'Medium', 'Confidentiality', 'Sanitize user input on save', 6.7, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:R/S:C/C:L/I:L/A:N', '/messages', 'Malicious scripts saved in chat.', 'Escape stored user data.'),
    (3, 'Backup System', 'Unencrypted Backup Files', 'Storage', 'High', 'Confidentiality', 'Encrypt backups at rest', 7.9, 'High', 'CVSS:3.1/AV:L/AC:L/PR:H/UI:N/S:U/C:H/I:N/A:N', '/backup', 'Backup files stored unencrypted.', 'Encrypt backup volumes.'),
    (4, 'DB Admin Panel', 'Default Credentials', 'Admin UI', 'Critical', 'Integrity', 'Change default passwords', 9.0, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:C/C:H/I:H/A:H', '/admin', 'Login uses default admin/admin.', 'Enforce credential rotation.'),
    (4, 'Login Field', 'Credential Stuffing Risk', 'Username Field', 'Medium', 'Confidentiality', 'Add CAPTCHA or 2FA', 6.0, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/login', 'No bot prevention measures.', 'Add CAPTCHA, lockout, 2FA.'),
    (4, 'JWT Tokens', 'No Expiration', 'Auth Tokens', 'High', 'Confidentiality', 'Set token expiry', 8.0, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:N/A:N', '/api/token', 'Tokens never expire.', 'Expire tokens and rotate regularly.'),
    (5, 'Transfer Logic', 'Logic Flaw in Transfers', 'Transfer Flow', 'Critical', 'Integrity', 'Add transaction verification', 9.2, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', '/transfer', 'Logic allows unauthorized fund movement.', 'Verify all transfer intents.'),
    (5, 'Logs', 'Sensitive Info in Logs', 'Logging System', 'Medium', 'Confidentiality', 'Avoid logging PII', 5.9, 'Medium', 'CVSS:3.1/AV:L/AC:L/PR:H/UI:N/S:U/C:H/I:N/A:N', '/logs', 'Logs contain card numbers.', 'Redact or exclude sensitive data.'),
    (6, 'Assignment Uploader', 'Unrestricted File Upload', 'Uploader', 'High', 'Integrity', 'Check MIME type and extension', 8.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:C/C:N/I:H/A:N', '/upload', 'Any file can be uploaded.', 'Restrict uploads to safe file types.'),
    (6, 'Dashboard Widgets', 'Clickjacking', 'UI Layer', 'Medium', 'Confidentiality', 'Use frame-busting headers', 6.0, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:U/C:L/I:N/A:N', '/dashboard', 'App can be embedded in iframe.', 'Set X-Frame-Options headers.'),
    (6, 'Grade API', 'Insecure Direct Object Reference', 'API Access', 'High', 'Integrity', 'Use access tokens per student', 7.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:H/A:N', '/api/grades', 'Grades can be accessed by modifying ID.', 'Enforce secure ID referencing.'),
    (6, 'API Key', 'Key leakage risk.', 'API Gateway', 'Medium', 'Confidentiality', 'Rotate keys regularly.', 6.5, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/api', 'API keys may be exposed.', 'Use environment variables and rotate keys.'),
    (6, 'Rate Limiting', 'No rate limiting on endpoints.', 'API Gateway', 'High', 'Availability', 'Implement rate limiting.', 7.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:H', '/api', 'Endpoints can be abused.', 'Add rate limiting.'),
    (7, 'Password Field', 'Weak password policy.', 'Mobile Login', 'Medium', 'Integrity', 'Enforce strong passwords.', 5.9, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:L/A:N', '/login', 'Passwords are too simple.', 'Require complex passwords.'),
    (8, 'Payment API', 'Improper input validation.', 'Payment Processor', 'High', 'Integrity', 'Validate all inputs.', 8.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/pay', 'Inputs not validated.', 'Sanitize and validate inputs.'),
    (8, 'Fraud Alerts', 'Alerts can be bypassed.', 'Fraud Detection', 'High', 'Integrity', 'Improve alert logic.', 7.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/fraud', 'Bypass possible.', 'Enhance detection logic.'),
    (9, 'Storage Bucket', 'Publicly accessible bucket.', 'Cloud Storage', 'Critical', 'Confidentiality', 'Restrict access.', 9.0, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '/storage', 'Bucket is public.', 'Set proper permissions.'),
    (10, 'DB User', 'Default credentials.', 'Legacy DB', 'High', 'Integrity', 'Change default passwords.', 8.7, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/db', 'Default user/pass.', 'Change credentials.'),
    (11, 'IoT Device', 'Unpatched firmware.', 'IoT Hub', 'High', 'Integrity', 'Update firmware.', 7.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/iot', 'Firmware outdated.', 'Patch devices.'),
    (12, 'Firewall Rules', 'Overly permissive rules.', 'Firewall', 'Medium', 'Confidentiality', 'Restrict rules.', 6.2, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/firewall', 'Too many open ports.', 'Tighten firewall rules.'),
    (12, 'Router Config', 'Weak SNMP community.', 'Router', 'Medium', 'Confidentiality', 'Change SNMP community.', 5.8, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/router', 'Default SNMP.', 'Use strong SNMP community.'),
    (13, 'Switch Mgmt', 'No management password.', 'Switch', 'High', 'Integrity', 'Set management password.', 7.0, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/switch', 'No password set.', 'Set a strong password.');
