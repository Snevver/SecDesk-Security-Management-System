<?php

namespace Ssms;

class OutputSanitizer
{
    /**
     * Safely escape output for HTML display
     */
    public static function escapeHtml(?string $text): string
    {
        if ($text === null) {
            return '';
        }
        
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Safely escape for HTML attribute values
     */
    public static function escapeAttribute(?string $text): string
    {
        if ($text === null) {
            return '';
        }
        
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Safely escape for JavaScript strings
     */
    public static function escapeJs(?string $text): string
    {
        if ($text === null) {
            return '';
        }
        
        return json_encode($text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }
}
