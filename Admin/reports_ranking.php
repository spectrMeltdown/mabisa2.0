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

$barangayProgress = [];
foreach ($barangayList as $data) {
    $completedAreas = 0;
    $totalAreas = count($categories);

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
                $fileStmt->bindParam(':barangay_id', $data['barangay_id'], PDO::PARAM_INT);
                $fileStmt->execute();
                $fileResult = $fileStmt->fetch(PDO::FETCH_ASSOC);

                $submittedCount = $fileResult['submitted'];
            }
        }

        if ($submittedCount == $totalCriteria && $totalCriteria != 0) {
            $completedAreas++;
        }
    }

    $stmt = $pdo->prepare("SELECT MAX(date_uploaded) AS date_uploaded FROM barangay_assessment_files WHERE barangay_id = :barangay_id");
    $stmt->bindParam(':barangay_id', $data['barangay_id'], PDO::PARAM_INT);
    $stmt->execute();
    $lastModified = $stmt->fetch(PDO::FETCH_ASSOC)['date_uploaded'] ?? '0000-00-00 00:00:00';

    $barangayProgress[] = [
        'barangay_id' => $data['barangay_id'],
        'barangay' => $data['barangay'],
        'completed' => $completedAreas,
        'total_areas' => $totalAreas,
        'date_uploaded' => $lastModified
    ];
}

usort($barangayProgress, function ($a, $b) {
    if ($b['completed'] !== $a['completed']) {
        return $b['completed'] <=> $a['completed'];
    }
    return strtotime($a['date_uploaded']) <=> strtotime($b['date_uploaded']); // Oldest first
});

$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetTitle('Barangay Ranking Report');
$pdf->setPrintHeader(false);
$pdf->AddPage();

$leftLogo = '../img/dilg_logo.jpg'; 
$rightLogo = '../img/bagong_pilipinas.jpg'; 

$logoWidth = 25;
$logoSpacing = 5;
$headerY = 10;
$pageWidth = 190;

$pdf->Image($leftLogo, ($pageWidth / 2) - ($logoWidth + $logoSpacing / 2), $headerY, $logoWidth, $logoWidth);
$pdf->Image($rightLogo, ($pageWidth / 2) + ($logoSpacing / 2), $headerY, $logoWidth, $logoWidth);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(10, $headerY + 30);
$pdf->Cell($pageWidth - 20, 10, 'DEPARTMENT OF INTERIOR AND LOCAL GOVERNMENT', 0, 1, 'C');

$pdf->SetX(10);
$pdf->Cell($pageWidth - 20, 10, 'Barangay Ranking Report', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetX(10);
$pdf->Cell($pageWidth - 20, 8, 'Municipality of Aloran', 0, 1, 'C');

$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetX(10);
$pdf->Cell($pageWidth - 20, 6, 'Report generated on: ' . date('F d, Y'), 0, 1, 'C');

$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, 8, 'Ranking', 1, 0, 'C');
$pdf->Cell(110, 8, 'Barangay', 1, 0, 'C');
$pdf->Cell(60, 8, 'Completed Areas/Total Areas', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$rank = 1;
foreach ($barangayProgress as $data) {
    $pdf->Cell(20, 8, $rank++, 1, 0, 'C');
    $pdf->Cell(110, 8, $data['barangay'], 1, 0, 'L');
    $pdf->Cell(60, 8, "{$data['completed']}/{$data['total_areas']}", 1, 1, 'C');
}

$pdf->Output('barangay_ranking.pdf', 'D');
