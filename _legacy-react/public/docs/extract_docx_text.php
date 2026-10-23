<?php

$xml = file_get_contents(__DIR__ . '/docx_extract/word/document.xml');
$xml = preg_replace('/<w:tab[^>]*\/>/', "\t", $xml);
$xml = preg_replace('/<w:br[^>]*\/>/', "\n", $xml);
preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $xml, $m);
echo implode('', $m[1]);
