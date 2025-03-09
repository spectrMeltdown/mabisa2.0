<?php
require_once '../db/db.php';
require_once 'bar_assessment/responses.php';

header('Content-Type: application/json');

$responses = new Responses($pdo);


$labels = [];
$totalSubmissions = [];
$approvedSubmissions = [];


$barangayData = $pdo->query('SELECT brgyid, brgyname FROM refbarangay')->fetchAll(PDO::FETCH_ASSOC);
$overallTotalSubmissions = $pdo->query('SELECT COUNT(*) FROM maintenance_criteria_setup')->fetchColumn();

$total = ($overallTotalSubmissions === null || $overallTotalSubmissions === '') ? 0 : $overallTotalSubmissions;


foreach ($barangayData as $barangay) {
    $responseCount = $responses->getResponseCount($barangay['brgyid']);
    $approvedCount = $responses->getApprovedCount($barangay['brgyid']);


    $submitted = ($responseCount === null || $responseCount === '') ? 0 : explode('/', $responseCount)[0];
    $approved = ($approvedCount === null || $approvedCount === '') ? 0 : $approvedCount;

    $labels[] = $barangay['brgyname'];
    $totalSubmissions[] = (int)$submitted - (int)$approved;
    $approvedSubmissions[] = (int)$approved;
}

echo json_encode([
    'labels' => $labels,
    'total' => $totalSubmissions,
    'approved' => $approvedSubmissions,
    'maxY' => $total 
]);
exit;

