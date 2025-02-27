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

$barangayProgress = [];
foreach ($barangayList as $data) {
    $responseCount = $response->getResponseCount($data['barangay_id']);
    [$submitted, $total] = explode('/', $responseCount);
    $submitted = (int)$submitted;
    $total = (int)$total;

    $stmt = $pdo->prepare("SELECT MAX(date_uploaded) AS date_uploaded FROM barangay_assessment_files WHERE barangay_id = :barangay_id");
    $stmt->bindParam(':barangay_id', $data['barangay_id'], PDO::PARAM_INT);
    $stmt->execute();
    $lastModified = $stmt->fetch(PDO::FETCH_ASSOC)['date_uploaded'] ?? '0000-00-00 00:00:00';

    $barangayProgress[] = [
        'barangay_id' => $data['barangay_id'],
        'barangay' => $data['barangay'],
        'submitted' => $submitted,
        'total' => $total,
        'date_uploaded' => $lastModified
    ];
}

usort($barangayProgress, function ($a, $b) {
    if ($b['submitted'] !== $a['submitted']) {
        return $b['submitted'] <=> $a['submitted'];
    }
    return strtotime($a['date_uploaded']) <=> strtotime($b['date_uploaded']); // Oldest first
});

$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetTitle('Barangay Ranking Report');
$pdf->setPrintHeader(false);
$pdf->AddPage();

$leftLogo = '../img/dilg_logo.jpg'; 
$rightLogo = '../img/mabisa-logo.jpg'; 

$logoWidth = 25;
$headerY = 10;
$pageWidth = 190;

$pdf->Image($leftLogo, 10, $headerY, $logoWidth, $logoWidth);
$pdf->Image($rightLogo, 170, $headerY, $logoWidth, $logoWidth); 

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(10, $headerY + 5);
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
$pdf->Cell(60, 8, 'Submitted/Total', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$rank = 1;
foreach ($barangayProgress as $data) {
    $pdf->Cell(20, 8, $rank++, 1, 0, 'C');
    $pdf->Cell(110, 8, $data['barangay'], 1, 0, 'L');
    $pdf->Cell(60, 8, "{$data['submitted']}/{$data['total']}", 1, 1, 'C');
}

$pdf->Output('barangay_ranking.pdf', 'D');
