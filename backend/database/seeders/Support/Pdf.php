<?php

namespace Database\Seeders\Support;

/**
 * Minimal one-page PDF (Helvetica, WinAnsi) so demo lessons and documents can
 * really be opened and downloaded.
 */
class Pdf
{
    public static function make(string $title, array $lines): string
    {
        $text = "BT /F1 18 Tf 60 780 Td (" . self::esc($title) . ") Tj ET\n"
            . "BT /F1 10 Tf 60 760 Td (" . self::esc('INSFP Mohamed Tayeb Boucenna - Horrimet') . ") Tj ET\n";
        $y = 720;
        foreach ($lines as $line) {
            foreach (explode("\n", wordwrap($line, 95)) as $part) {
                $text .= "BT /F1 11 Tf 60 $y Td (" . self::esc($part) . ") Tj ET\n";
                $y -= 16;
            }
            $y -= 6;
        }

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            "<< /Length " . strlen($text) . " >>\nstream\n$text\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n$object\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";
    }

    private static function esc(string $s): string
    {
        $s = str_replace(['’', '—', '–', '«', '»'], ["'", '-', '-', '"', '"'], $s);
        $s = mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }
}
