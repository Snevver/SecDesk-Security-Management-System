-- WARNING: This is mock data and should not be used in production

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Clear existing mock data if any exists (but preserve admin user)
DELETE FROM vulnerabilities WHERE id > 0;
DELETE FROM targets WHERE id > 0;  
DELETE FROM tests WHERE id > 0;
DELETE FROM users WHERE role_id IN (1, 2); -- Remove customers and pentesters, keep admin

-- Reset sequences to ensure consistent IDs
-- After deleting mock users, reset the sequence to start from 2 (next after admin)
SELECT setval('users_id_seq', 1, true);
SELECT setval('tests_id_seq', 1, false);
SELECT setval('targets_id_seq', 1, false);
SELECT setval('vulnerabilities_id_seq', 1, false);

-- Users
INSERT INTO users (email, password, role_id) VALUES 
    ('customer@example.com', crypt('password123', gen_salt('bf')), 1),
    ('pentester@example.com', crypt('password123', gen_salt('bf')), 2),
    ('admin@example.com', crypt('password123', gen_salt('bf')), 3);

-- Tests
INSERT INTO tests (customer_id, pentester_id, test_name, test_description, test_date, completed) VALUES
    (2, 3, 'E-commerce Platform Security Assessment', 'Comprehensive security testing of online retail platform', '2024-01-15 09:00:00', true),
    (2, 3, 'Mobile Banking App Penetration Test', 'Security assessment of iOS and Android banking applications', '2024-01-20 10:00:00', false),
    (2, 3, 'Corporate Network Infrastructure Test', 'Internal network security assessment and penetration testing', '2024-02-01 08:30:00', false);

-- Targets for each test
INSERT INTO targets (test_id, target_name, target_description) VALUES 
    -- E-commerce Platform Security Assessment (Test ID 1)
    (1, 'User Authentication System', 'Login and registration functionality with password reset capabilities'),
    (1, 'Customer Dashboard', 'Main user interface after successful authentication'),
    (1, 'Payment Processing Module', 'Credit card processing and payment gateway integration'),
    
    -- Mobile Banking App Penetration Test (Test ID 2)
    (2, 'iOS Application Binary', 'Compiled iOS application for reverse engineering analysis'),
    (2, 'Android APK Package', 'Android application package for static and dynamic analysis'),
    (2, 'Mobile API Gateway', 'Backend API services specifically designed for mobile consumption'),

    -- Corporate Network Infrastructure Test (Test ID 3)
    (3, 'Domain Controller Servers', 'Active Directory domain controllers managing user authentication'),
    (3, 'File Share Servers', 'Network attached storage systems containing corporate documents'),
    (3, 'Email Exchange Servers', 'Microsoft Exchange email infrastructure');

-- Vulnerabilities
INSERT INTO vulnerabilities (target_id, affected_entity, identifier, risk_statement, affected_component, residual_risk, classification, identified_controls, cvss_score, likelihood, cvssv3_code, location, vulnerabilities_description, reproduction_steps, impact, remediation_difficulty, recommendations, recommended_reading, response, solved) VALUES
    (1, 'Login Form', 'VULN-001', 'SQL injection vulnerability allows unauthorized database access', 'Authentication Module', 'High', 'High', 'Input validation', 9.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '/login.php', 'SQL injection vulnerability in username parameter allows full database compromise', '1. Navigate to login page\n2. Enter '' OR 1=1 -- in username field\n3. Observe unauthorized access', 'Complete database compromise, unauthorized access to all user accounts', 'Medium', 'Implement prepared statements and parameterized queries', 'OWASP SQL Injection Prevention Cheat Sheet', 'Acknowledged - Fix scheduled for next release', false),
    (2, 'Dashboard Widgets', 'VULN-003', 'Cross-site scripting in dashboard widgets', 'Dashboard Rendering', 'Medium', 'Medium', 'Output encoding', 6.1, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:R/S:C/C:L/I:L/A:N', '/dashboard', 'Reflected XSS in widget name parameter', '1. Add widget with <script>alert(1)</script>\n2. Script executes on dashboard load', 'Session hijacking, user impersonation', 'Low', 'Sanitize and encode all user input', 'OWASP XSS Prevention Cheat Sheet', 'Open', false),
    (2, 'Session Management', 'VULN-004', 'Session ID not rotated after login', 'Session Management', 'Low', 'Low', 'Session rotation', 4.3, 'Low', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:L/I:N/A:N', '/dashboard', 'Session fixation vulnerability due to missing session ID rotation', '1. Obtain session ID before login\n2. Login with same session\n3. Session remains valid', 'Potential session fixation attacks', 'Low', 'Rotate session ID after authentication', 'OWASP Session Management Cheat Sheet', 'Open', false),
    (3, 'Payment Gateway', 'VULN-005', 'Insecure TLS configuration', 'Payment Processing', 'High', 'High', 'TLS hardening', 7.4, 'High', 'CVSS:3.1/AV:N/AC:H/PR:N/UI:N/S:U/C:H/I:H/A:N', '/payment', 'TLS 1.0 enabled, weak ciphers accepted', '1. Connect using SSL Labs\n2. Observe weak ciphers and protocols', 'Sensitive data exposure', 'Medium', 'Disable weak protocols and ciphers', 'OWASP Transport Layer Protection Cheat Sheet', 'Open', false),
    (3, 'Payment Form', 'VULN-006', 'Credit card number not masked in logs', 'Payment Logging', 'Medium', 'Medium', 'Log redaction', 5.3, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:L/I:N/A:N', '/payment', 'Full credit card numbers are logged in plaintext', '1. Submit payment\n2. Review logs\n3. Card number visible', 'PCI DSS violation, data breach risk', 'Low', 'Mask or redact sensitive fields in logs', 'PCI DSS Requirement 10', 'Open', false),
    (4, 'iOS App Binary', 'VULN-007', 'Debug symbols present in production build', 'iOS Application', 'Low', 'Low', 'Release build process', 3.7, 'Low', 'CVSS:3.1/AV:L/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', 'N/A', 'App binary contains debug symbols', '1. Extract IPA\n2. Analyze binary\n3. Debug symbols found', 'Information disclosure', 'Low', 'Strip debug symbols before release', 'Apple Secure Coding Guide', 'Open', false),
    (4, 'iOS App Transport Security', 'VULN-008', 'ATS disabled for all domains', 'iOS Networking', 'Medium', 'Medium', 'ATS enforcement', 6.5, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:L/A:N', 'N/A', 'App allows insecure HTTP connections', '1. Review Info.plist\n2. NSAllowsArbitraryLoads set to true', 'Sensitive data exposure', 'Low', 'Enable ATS and restrict exceptions', 'Apple ATS Documentation', 'Open', false),
    (5, 'Android APK', 'VULN-009', 'App allows backup by default', 'Android Manifest', 'Low', 'Low', 'Backup flag', 4.0, 'Low', 'CVSS:3.1/AV:L/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', 'N/A', 'android:allowBackup="true" in manifest', '1. Decompile APK\n2. Check manifest\n3. allowBackup is true', 'User data can be extracted from device', 'Low', 'Set allowBackup to false', 'Android Security Best Practices', 'Open', false),
    (5, 'Android WebView', 'VULN-010', 'WebView loads content over HTTP', 'WebView Implementation', 'Medium', 'Medium', 'Secure WebView usage', 6.1, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:R/S:U/C:L/I:L/A:N', 'N/A', 'WebView loads insecure content', '1. Analyze code\n2. WebView loads http:// URLs', 'Sensitive data exposure', 'Low', 'Enforce HTTPS in WebView', 'OWASP Mobile Top 10', 'Open', false),
    (6, 'API Gateway', 'VULN-011', 'Missing rate limiting on login endpoint', 'API Security', 'Medium', 'Medium', 'Rate limiting', 5.0, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:L', '/api/login', 'No rate limiting allows brute-force attacks', '1. Send multiple login attempts\n2. No lockout or delay', 'Account brute-force risk', 'Low', 'Implement rate limiting', 'OWASP API Security Top 10', 'Open', false),
    (6, 'API Gateway', 'VULN-012', 'Verbose error messages reveal stack traces', 'API Error Handling', 'Low', 'Low', 'Error message sanitization', 3.3, 'Low', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '/api/', 'API returns stack traces in error responses', '1. Trigger error\n2. Observe stack trace in response', 'Information disclosure', 'Low', 'Return generic error messages', 'OWASP API Security Top 10', 'Open', false),
    (7, 'Domain Controller', 'VULN-013', 'SMB signing not enforced', 'Active Directory', 'High', 'High', 'SMB signing', 8.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:N', '\\\\domain-controller', 'SMB signing disabled allows MITM attacks', '1. Scan SMB config\n2. Signing not required', 'Credential theft, MITM', 'Medium', 'Enforce SMB signing', 'Microsoft Security Baselines', 'Open', false),
    (7, 'Domain Controller', 'VULN-014', 'Weak Kerberos pre-authentication', 'Kerberos', 'Medium', 'Medium', 'Kerberos hardening', 6.8, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:L/A:N', '\\\\domain-controller', 'Kerberos pre-auth allows AS-REP roasting', '1. Request TGT without pre-auth\n2. Receive encrypted response', 'Password cracking risk', 'Medium', 'Enforce pre-authentication', 'Microsoft Kerberos Guide', 'Open', false),
    (8, 'File Share Server', 'VULN-015', 'Anonymous access enabled', 'File Share', 'High', 'High', 'Access control', 7.5, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', '\\\\fileshare', 'Shares accessible without authentication', '1. Connect as guest\n2. Access files', 'Data leakage', 'Low', 'Disable anonymous access', 'Microsoft File Sharing Security', 'Open', false),
    (8, 'File Share Server', 'VULN-016', 'Sensitive files world-readable', 'File Permissions', 'Medium', 'Medium', 'Permission hardening', 5.5, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:L/I:N/A:N', '\\\\fileshare', 'Confidential files have world-readable permissions', '1. List files\n2. Read confidential.txt', 'Unauthorized data access', 'Low', 'Restrict file permissions', 'CIS Windows Benchmark', 'Open', false),
    (9, 'Exchange Server', 'VULN-017', 'Outdated Exchange version with known RCE', 'Exchange', 'High', 'High', 'Patch management', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', 'mail.company.com', 'Exchange version vulnerable to CVE-2021-26855', '1. Identify version\n2. Exploit ProxyLogon', 'Remote code execution', 'High', 'Apply latest security patches', 'Microsoft Exchange Security Updates', 'Open', false),
    (9, 'Exchange Server', 'VULN-018', 'Autodiscover exposes credentials', 'Autodiscover', 'Medium', 'Medium', 'Autodiscover hardening', 6.0, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:L/A:N', 'mail.company.com', 'Autodiscover leaks credentials via HTTP', '1. Intercept Autodiscover traffic\n2. Credentials sent in cleartext', 'Credential theft', 'Low', 'Enforce HTTPS and restrict Autodiscover', 'Microsoft Exchange Deployment Guide', 'Open', false);