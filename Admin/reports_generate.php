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
$pdf->AddPage();

$pageWidth = ($orientation == 'L') ? 290 : 190; 
$margin = 10;
$totalWidth = $pageWidth - (2 * $margin);

$barangayColumnWidth = 55;  
$categoryColumnWidth = ($totalWidth - $barangayColumnWidth) / count($categories);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($barangayColumnWidth, 8, 'Barangay', 1, 0, 'C');

foreach ($categories as $category) {
    $pdf->Cell($categoryColumnWidth, 8, $category['cat_code'], 'LRB', 0, 'C');
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

        $displayText = ($submittedCount == $totalCriteria && $totalCriteria != 0) ? '✔' : "$submittedCount / $totalCriteria";
        $pdf->Cell($categoryColumnWidth, 8, $displayText, 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Output('barangay_responses.pdf', 'D');
