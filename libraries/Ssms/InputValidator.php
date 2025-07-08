<?php

namespace Ssms;

class InputValidator
{
    /**
     * Sanitize string input to prevent XSS
     */
    public static function sanitizeString(?string $input, int $maxLength = 1000): string
    {
        if ($input === null) {
            return '';
        }
        
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Limit length
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        
        // Remove potentially dangerous HTML tags and encode special characters
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Validate and sanitize test title
     */
    public static function validateTestTitle(?string $title): array
    {
        if (empty($title)) {
            return ['valid' => false, 'error' => 'Test title is required'];
        }
        
        $sanitized = self::sanitizeString($title, 255);
        
        if (empty($sanitized)) {
            return ['valid' => false, 'error' => 'Test title contains invalid characters'];
        }
        
        return ['valid' => true, 'value' => $sanitized];
    }
    
    /**
     * Validate and sanitize test description
     */
    public static function validateTestDescription(?string $description): array
    {
        $sanitized = self::sanitizeString($description, 2000);
        return ['valid' => true, 'value' => $sanitized];
    }
    
    /**
     * Validate and sanitize target name
     */
    public static function validateTargetName(?string $name): array
    {
        if (empty($name)) {
            return ['valid' => false, 'error' => 'Target name is required'];
        }
        
        $sanitized = self::sanitizeString($name, 255);
        
        if (empty($sanitized)) {
            return ['valid' => false, 'error' => 'Target name contains invalid characters'];
        }
        
        return ['valid' => true, 'value' => $sanitized];
    }
    
    /**
     * Validate and sanitize target description
     */
    public static function validateTargetDescription(?string $description): array
    {
        $sanitized = self::sanitizeString($description, 2000);
        return ['valid' => true, 'value' => $sanitized];
    }
    
    /**
     * Validate and sanitize vulnerability field
     */
    public static function validateVulnerabilityField(?string $value, string $fieldName, int $maxLength = 1000): array
    {
        $sanitized = self::sanitizeString($value, $maxLength);
        return ['valid' => true, 'value' => $sanitized];
    }
    
    /**
     * Validate integer ID
     */
    public static function validateId($id, string $fieldName): array
    {
        if (!is_numeric($id) || $id <= 0) {
            return ['valid' => false, 'error' => $fieldName . ' must be a valid positive integer'];
        }
        
        return ['valid' => true, 'value' => (int)$id];
    }
    
    /**
     * Validate and return integer value
     */
    public static function validateInteger($value): ?int
    {
        if (!is_numeric($value) || $value <= 0) {
            return null;
        }
        
        return (int)$value;
    }
    
    /**
     * Validate and return boolean value
     */
    public static function validateBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $value = strtolower($value);
            return in_array($value, ['true', '1', 'yes', 'on']);
        }
        
        if (is_numeric($value)) {
            return (bool)$value;
        }
        
        return false;
    }
}
