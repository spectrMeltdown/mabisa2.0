<?php
require_once '../db/db.php';
require_once '../Admin/bar_assessment/responses.php';

$response = new Responses($pdo);

$barangay = $response->show_responses();
$categories = $pdo->query("SELECT * FROM maintenance_governance")->fetchAll(PDO::FETCH_ASSOC);
foreach ($barangay as $bar) {
  echo $bar['barangay'];


  foreach ($categories as $cat) {
    echo '<pre>{';
    echo 'Category: '. $cat['desc_keyctr'];
    $sql1 = 'SELECT * FROM maintenance_area_indicators WHERE desc_keyctr = :desc_keyctr';
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindParam(':desc_keyctr', $cat['desc_keyctr'], PDO::PARAM_INT);
    $stmt1->execute();
    $desc = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    foreach ($desc as $desc_ctr) {
        echo '<pre>{';
        echo 'Area Description'.$desc_ctr['area_description'];
        $sql2 = 'SELECT * FROM maintenance_criteria_setup WHERE indicator_keyctr = :keyctr';
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':keyctr', $desc_ctr['keyctr'], PDO::PARAM_INT);
        $stmt2->execute();
        $indicator = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($indicator as $ind) {
            echo '<pre>{';
            echo 'Criteria Key: '.$ind['keyctr'];
            $sql3 = 'SELECT * FROM barangay_assessment_files WHERE criteria_keyctr = :keyctr';
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->bindParam(':keyctr', $ind['keyctr'], PDO::PARAM_INT);
            $stmt3->execute();
            $criteria = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            foreach ($criteria as $crit) {
                echo '<pre>{';
                echo 'File ID:'. $crit['file_id'];
//here
            }
            echo '}</pre>';
        }
        echo '}</pre>';
    }
    echo '}</pre>';
}


}


