<?php

/**
 * Email Configuration
 * 
 * Configure your SMTP settings here
 */

return [
    // SMTP Host (examples: smtp.gmail.com, smtp.outlook.com, smtp.mailtrap.io)
    'smtp_host' => 'smtp.gmail.com',
    
    // SMTP Port (587 for TLS, 465 for SSL, 25 for non-encrypted)
    'smtp_port' => 587,
    
    // SMTP Username
    'smtp_username' => 'svenhoeksema@gmail.com', // Change this to the company email address
    
    // SMTP Password (for Gmail, use App Password)
    'smtp_password' => 'kpsa jwls cwdc csys',
    
    // Encryption type (tls, ssl, or false for none)
    'smtp_encryption' => 'tls',
    
    // From email address
    'from_email' => 'svenhoeksema@gmail.com', // Once again, change this to the company email address
    
    // From name
    'from_name' => 'SecDesk Security Management',
    
    // Reply-to email
    'reply_to' => 'noreply@secdesk.com',
];
