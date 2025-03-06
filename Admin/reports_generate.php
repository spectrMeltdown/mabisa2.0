<?php

require_once '../db/db.php';
require_once '../TCPDF-main/tcpdf.php';
require_once '../Admin/bar_assessment/responses.php';

require_once 'common/auth.php';
if (!userHasPerms('reports_read', 'gen')) {
    die("You don't have permission to download reports.");
}

$response = new Responses($pdo);
$barangayList = $response->show_responses();
$categories = $pdo->query("SELECT * FROM maintenance_governance")->fetchAll(PDO::FETCH_ASSOC);

$orientation = (count($categories) > 5) ? 'L' : 'P';
$pdf = new TCPDF($orientation, 'mm', 'A4');
$pdf->SetTitle('Barangay Responses Report');
$pdf->setPrintHeader(false);
$pdf->AddPage();

// logo
$leftLogo = '../img/dilg_logo.jpg';
$rightLogo = '../img/bagong_pilipinas.jpg';

$logoWidth = 30;
$headerY = 10;
$pageWidth = ($orientation == 'L') ? 290 : 190;

$pdf->Image($leftLogo, 10, $headerY, $logoWidth, $logoWidth);

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(0, $headerY + 5);
$pdf->Cell($pageWidth, 10, 'DEPARTMENT OF INTERIOR AND LOCAL GOVERNMENT', 0, 1, 'C');

$pdf->SetX(0);
$pdf->Cell($pageWidth, 10, 'Barangay Responses Report', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 12);


$pdf->SetX(0);
$pdf->Cell($pageWidth, 8, 'Municipality of Aloran', 0, 1, 'C');

$pdf->SetFont('helvetica', 'I', 10);


$pdf->SetX(0);
$pdf->Cell($pageWidth, 6, 'Report generated on: ' . date('F d, Y'), 0, 1, 'C');





$pdf->Image($rightLogo, $pageWidth - $logoWidth - 10, $headerY, $logoWidth, $logoWidth);

$pdf->Ln(10);



$pageWidth -= 20;
$barangayColumnWidth = 55;
$categoryColumnWidth = ($pageWidth - $barangayColumnWidth) / count($categories);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($barangayColumnWidth, 8, 'Barangay', 1, 0, 'C');
$pdf->SetFont('helvetica', 'B', 6.5);
foreach ($categories as $category) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $desc = $pdo->prepare("SELECT description from maintenance_area_description WHERE keyctr = ?");
    $desc->execute([$category['desc_keyctr']]);
    $description = $desc->fetch(PDO::FETCH_ASSOC);

    $pdf->MultiCell($categoryColumnWidth, 8, $description['description'], 1, 'C');

    $pdf->SetXY($x + $categoryColumnWidth, $y);
}

$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
foreach ($barangayList as $barangay) {
    $pdf->Cell($barangayColumnWidth, 8, $barangay['barangay'], 1, 0, 'L');

    foreach ($categories as $category) {
        $submittedCount = 0;
        $totalCriteria = 0;

        $indicators = $pdo->prepare("SELECT keyctr FROM maintenance_area_indicators WHERE desc_keyctr = ?");
        $indicators->execute([$category['desc_keyctr']]);
        $indicatorIds = $indicators->fetchAll(PDO::FETCH_COLUMN);

        if ($indicatorIds) {
            $criteriaQuery = "SELECT keyctr FROM maintenance_criteria_setup WHERE indicator_keyctr IN (" . implode(',', $indicatorIds) . ")";
            $criteriaStmt = $pdo->prepare($criteriaQuery);
            $criteriaStmt->execute();
            $criteriaList = $criteriaStmt->fetchAll(PDO::FETCH_COLUMN);

            $totalCriteria = count($criteriaList);

            if ($totalCriteria > 0) {
                $fileQuery = 'SELECT COUNT(*) as submitted FROM barangay_assessment_files 
                              WHERE criteria_keyctr IN (' . implode(',', $criteriaList) . ') 
                              AND barangay_id = :barangay_id';
                $fileStmt = $pdo->prepare($fileQuery);
                $fileStmt->bindParam(':barangay_id', $barangay['barangay_id'], PDO::PARAM_INT);
                $fileStmt->execute();
                $fileResult = $fileStmt->fetch(PDO::FETCH_ASSOC);

                $submittedCount = $fileResult['submitted'];
            }
        }

        $displayText = ($submittedCount == $totalCriteria && $totalCriteria != 0) ? 'Complete' : "$submittedCount / $totalCriteria";
        $pdf->Cell($categoryColumnWidth, 8, $displayText, 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Output('barangay_responses.pdf', 'D');
