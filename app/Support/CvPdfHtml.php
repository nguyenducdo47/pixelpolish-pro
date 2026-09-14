<?php

namespace App\Support;

class CvPdfHtml
{
    /**
     * Flatten HTML for Dompdf (nested tables break cellmap / cause blank pages).
     */
    public static function prepare(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $html = preg_replace('/<\/?(?:table|thead|tbody|tfoot|colgroup|col)[^>]*>/iu', '', $html) ?? $html;
        $html = preg_replace('/<tr[^>]*>/iu', '<div class="pdf-text-row">', $html) ?? $html;
        $html = preg_replace('/<\/tr>/iu', '</div>', $html) ?? $html;
        $html = preg_replace('/<t[dh][^>]*>/iu', '<span>', $html) ?? $html;
        $html = preg_replace('/<\/t[dh]>/iu', '</span> ', $html) ?? $html;

        return $html;
    }

    public static function rich(?string $html, bool $forPdf = false): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return $forPdf ? self::prepare($html) : $html;
    }
}
