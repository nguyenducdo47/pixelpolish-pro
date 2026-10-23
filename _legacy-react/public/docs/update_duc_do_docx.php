<?php

/**
 * Điền dự án từ duc_do_logistics_digital_cv.php vào bảng
 * "THÀNH TÍCH... CÁC DỰ ÁN ĐÃ THAM GIA" và khôi phục bảng kinh nghiệm làm việc (mẫu gốc).
 */

$baseDir = __DIR__;
$docxPath = $baseDir.'/NGUYEN_DAC_DO_cap_nhat.docx';
$extractDir = $baseDir.'/docx_extract';
$documentXmlPath = $extractDir.'/word/document.xml';
$overlay = require dirname(__DIR__, 3).'/database/seeders/data/duc_do_logistics_digital_cv.php';

function xmlText(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/** @return list<array{period: string, company: string, position: string, duties: list<string>}> */
function buildProjectEntries(array $overlay): array
{
    $rows = [];

    foreach ($overlay['projects'] as $project) {
        $rows[] = [
            'period' => $project['period'],
            'company' => $project['title']['vi'],
            'position' => $project['subtitle']['vi'],
            'duties' => array_slice($project['highlights']['vi'], 0, 3),
        ];
    }

    return $rows;
}

function achievementParagraph(string $text, bool $bold = false): string
{
    $b = $bold ? '<w:b/>' : '';

    return '<w:p w14:paraId="'.bin2hex(random_bytes(4)).'" w14:textId="77777777" w:rsidR="009677B3" w:rsidRDefault="009677B3" w:rsidP="0042199F">'
        .'<w:pPr><w:spacing w:before="60" w:after="60"/><w:jc w:val="both"/>'
        .'<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'.$b.'<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:pPr>'
        .'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'.$b.'<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
        .'<w:t>'.xmlText($text).'</w:t></w:r></w:p>';
}

function dutyParagraph(string $text): string
{
    $t = xmlText($text);

    return '<w:p w14:paraId="'.bin2hex(random_bytes(4)).'" w14:textId="77777777" w:rsidR="00602214" w:rsidRDefault="008B7D8E" w:rsidP="008B7D8E">'
        .'<w:pPr><w:pStyle w:val="WW-BodyText2"/><w:numPr><w:ilvl w:val="0"/><w:numId w:val="11"/></w:numPr>'
        .'<w:spacing w:before="120" w:after="120"/><w:ind w:left="316"/>'
        .'<w:rPr><w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman"/>'
        .'<w:bCs/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:pPr>'
        .'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman"/>'
        .'<w:bCs/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>'.$t.'</w:t></w:r></w:p>';
}

function simpleCellParagraph(string $text): string
{
    $t = xmlText($text);

    return '<w:p w14:paraId="'.bin2hex(random_bytes(4)).'" w14:textId="77777777" w:rsidR="00602214" w:rsidRDefault="008B7D8E" w:rsidP="00EF115E">'
        .'<w:pPr><w:pStyle w:val="WW-BodyText2"/><w:spacing w:before="120" w:after="120"/>'
        .'<w:rPr><w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman"/>'
        .'<w:bCs/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:pPr>'
        .'<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman"/>'
        .'<w:bCs/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>'.$t.'</w:t></w:r></w:p>';
}

function buildWorkExperienceRow(array $entry): string
{
    $dutyXml = '';
    foreach (array_slice($entry['duties'], 0, 3) as $duty) {
        $dutyXml .= dutyParagraph($duty);
    }

    return '<w:tr w:rsidR="00602214" w:rsidRPr="008A3314" w14:paraId="'.bin2hex(random_bytes(4)).'" w14:textId="77777777" w:rsidTr="00602214">'
        .'<w:trPr><w:trHeight w:val="1620"/></w:trPr>'
        .'<w:tc><w:tcPr><w:tcW w:w="1548" w:type="dxa"/></w:tcPr>'.simpleCellParagraph($entry['period']).'</w:tc>'
        .'<w:tc><w:tcPr><w:tcW w:w="2790" w:type="dxa"/></w:tcPr>'.simpleCellParagraph($entry['company']).'</w:tc>'
        .'<w:tc><w:tcPr><w:tcW w:w="1800" w:type="dxa"/></w:tcPr>'.simpleCellParagraph($entry['position']).'</w:tc>'
        .'<w:tc><w:tcPr><w:tcW w:w="3600" w:type="dxa"/></w:tcPr>'.$dutyXml.'</w:tc>'
        .'</w:tr>';
}

/** @return list<array{period: string, company: string, position: string, duties: list<string>}> */
function buildEmploymentEntries(array $overlay): array
{
    $rows = [];

    foreach ($overlay['work_experiences'] as $we) {
        $company = $we['company_form']['vi'] ?? $we['company']['vi'];
        $duties = array_slice($we['bullets']['vi'] ?? [], 0, 3);
        $rows[] = [
            'period' => $we['period'],
            'company' => $company,
            'position' => $we['position']['vi'],
            'duties' => $duties,
        ];
    }

    return $rows;
}

function buildAchievementRow(array $entry): string
{
    $descXml = achievementParagraph($entry['company'], true);
    $descXml .= achievementParagraph($entry['position'], false);
    foreach ($entry['duties'] as $duty) {
        $descXml .= achievementParagraph('• '.$duty, false);
    }

    $yearXml = achievementParagraph($entry['period'], false);

    return '<w:tr w:rsidR="009677B3" w:rsidRPr="008A3314" w14:paraId="'.bin2hex(random_bytes(4)).'" w14:textId="77777777" w:rsidTr="009677B3">'
        .'<w:tc><w:tcPr><w:tcW w:w="8280" w:type="dxa"/></w:tcPr>'.$descXml.'</w:tc>'
        .'<w:tc><w:tcPr><w:tcW w:w="1350" w:type="dxa"/></w:tcPr>'.$yearXml.'</w:tc>'
        .'</w:tr>';
}

function replaceWorkExperienceDataRows(string $xml, string $newDataRows): string
{
    $sectionPos = strpos($xml, 'KINH NGHI');
    if ($sectionPos === false) {
        throw new RuntimeException('Work experience section not found.');
    }

    $statusPosFull = strpos($xml, 'Tình trạng', $sectionPos);
    if ($statusPosFull === false) {
        throw new RuntimeException('Work experience end marker not found.');
    }

    $tablePart = substr($xml, $sectionPos, $statusPosFull - $sectionPos);
    if (! preg_match_all('/<w:tr[^>]*>.*?<\/w:tr>/s', $tablePart, $rowMatches) || count($rowMatches[0]) < 2) {
        throw new RuntimeException('Work experience table rows not found.');
    }

    $currentDataRows = implode('', array_slice($rowMatches[0], 1));
    $tablePartNew = str_replace($currentDataRows, $newDataRows, $tablePart);
    if ($tablePartNew === $tablePart) {
        throw new RuntimeException('Could not update work experience rows.');
    }

    return substr($xml, 0, $sectionPos).$tablePartNew.substr($xml, $statusPosFull);
}

function replaceAchievementDataRows(string $xml, string $newDataRows): string
{
    $gridPos = strpos($xml, '<w:gridCol w:w="8280"/>');
    if ($gridPos === false) {
        throw new RuntimeException('Achievements table not found.');
    }

    $tableStart = strrpos(substr($xml, 0, $gridPos), '<w:tbl>');
    $tableEnd = strpos($xml, '</w:tbl>', $gridPos) + 8;
    if ($tableStart === false || $tableEnd === false) {
        throw new RuntimeException('Achievements table bounds not found.');
    }

    $tableXml = substr($xml, $tableStart, $tableEnd - $tableStart);
    if (! preg_match_all('/<w:tr[^>]*>.*?<\/w:tr>/s', $tableXml, $rowMatches) || count($rowMatches[0]) < 2) {
        throw new RuntimeException('Achievements table rows not found.');
    }

    $oldDataRows = implode('', array_slice($rowMatches[0], 1));
    $tableXmlNew = str_replace($oldDataRows, $newDataRows, $tableXml);
    if ($tableXmlNew === $tableXml) {
        throw new RuntimeException('Could not update achievements rows.');
    }

    return substr($xml, 0, $tableStart).$tableXmlNew.substr($xml, $tableEnd);
}

function packDocx(string $extractDir, string $docxPath): void
{
    $tempDocx = $docxPath.'.tmp';
    if (is_file($tempDocx)) {
        unlink($tempDocx);
    }

    $zip = new ZipArchive;
    if ($zip->open($tempDocx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Could not open docx for writing.');
    }

    $root = realpath($extractDir);
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($extractDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if (! $file->isFile()) {
            continue;
        }
        $filePath = $file->getRealPath();
        $relative = substr($filePath, strlen($root) + 1);
        $relative = str_replace('\\', '/', $relative);
        $zip->addFile($filePath, $relative);
    }

    $zip->close();

    if (! rename($tempDocx, $docxPath)) {
        throw new RuntimeException('Could not replace docx (file may be open in Word). Output left at: '.$tempDocx);
    }
}

if (! is_file($documentXmlPath)) {
    fwrite(STDERR, "Missing {$documentXmlPath}. Extract docx to docx_extract first.\n");
    exit(1);
}

$entries = buildProjectEntries($overlay);

$newAchievementRows = '';
foreach ($entries as $entry) {
    $newAchievementRows .= buildAchievementRow($entry);
}

$employmentRows = '';
foreach (buildEmploymentEntries($overlay) as $employment) {
    $employmentRows .= buildWorkExperienceRow($employment);
}

$xml = file_get_contents($documentXmlPath);
$xml = replaceWorkExperienceDataRows($xml, $employmentRows);
$xml = replaceAchievementDataRows($xml, $newAchievementRows);
file_put_contents($documentXmlPath, $xml);

packDocx($extractDir, $docxPath);

echo "Updated: {$docxPath}\n";
echo 'Projects in achievements section: '.count($entries)."\n";
echo 'Work experience rows: '.count(buildEmploymentEntries($overlay))."\n";
