<?php
require_once '../db/db.php';
require_once 'bar_assessment/responses.php';

header('Content-Type: application/json');

$responses = new Responses($pdo);

// Fetch all barangays
$barangayData = $pdo->query('SELECT brgyid, brgyname FROM refbarangay')->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];

foreach ($barangayData as $barangay) {
    $responseCount = $responses->getResponseCount($barangay['brgyid']);

    // Ensure that empty or missing values default to 0
    if ($responseCount === null || $responseCount === '') {
        $submitted = 0;
    } else {
        // Extract the first number from "X/Y" format
        $submitted = explode('/', $responseCount)[0];
    }

    $labels[] = $barangay['brgyname'];
    $data[] = (int)$submitted;
}

// Output JSON
echo json_encode(['labels' => $labels, 'data' => $data]);
exit;
?>
