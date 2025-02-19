<?php

$pathPrepend = isset($isInFolder) ? '../' : '';
require_once $pathPrepend . '../db/db.php';
require_once (__DIR__ . "/../api/audit_log.php");
$log = new Audit_log($pdo);


if (isset($_POST['add_maintenance_criteria_setup'])) {
    $version_keyctr = $_POST['version_keyctr'] ?? null;
    $indicator_keyctr = $_POST['indicator_keyctr'] ?? null;
    $minreqs_keyctr = $_POST['minreqs_keyctr'] ?? null;
    $sub_minimumreqs = $_POST['sub_minimumreqs'] ?? null;
    $movdocs_reqs = $_POST['movdocs_reqs'] ?? null;
    $data_source = $_POST['data_source'] ?? null;

    try {
        $pdo->beginTransaction();
        $trail = 'Created at ' . date('Y-m-d H:i:s');
        $query = "INSERT INTO `maintenance_criteria_setup` (
            `version_keyctr`, `indicator_keyctr`, `minreqs_keyctr`, 
            `sub_minimumreqs`, `movdocs_reqs`, `data_source`, `trail`
        ) VALUES (
            :version_keyctr, :indicator_keyctr, :minreqs_keyctr, 
            :sub_minimumreqs, :movdocs_reqs, :data_source, :trail
        )";
         

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':version_keyctr' => $version_keyctr,
            ':indicator_keyctr' => $indicator_keyctr,
            ':minreqs_keyctr' => $minreqs_keyctr,
            ':sub_minimumreqs' => $sub_minimumreqs,
            ':movdocs_reqs' => $movdocs_reqs,
            ':data_source' => $data_source,
            ':trail' => $trail
        ]);

        $pdo->commit();
        $log->userLog('Created a New Criteria with Version ID: '.$version_keyctr.', Indicator ID: '.$indicator_keyctr.', Minimum Requirements ID: '. $minreqs_keyctr.', Sub Minimum Requirements: '.$sub_minimumreqs.', MOV Documents Requirements: '.$movdocs_reqs.', and Document Source: '.$data_source);
        echo "<script>alert('New record created successfully'); window.location.href = document.referrer;</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

if (isset($_POST['update_maintenance_criteria_setup'])) {

    $keyctr = $_POST['keyctr'];
    $version_keyctr = $_POST['version_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $minreqs_keyctr = $_POST['minreqs_keyctr'];
    $sub_minimumreqs = $_POST['sub_minimumreqs'];
    $movdocs_reqs = $_POST['movdocs_reqs'];
    $data_source = $_POST['data_source'];

    try {
        $pdo->beginTransaction();
        $trail = 'Edited at ' . date('Y-m-d H:i:s');
        $sql = "UPDATE maintenance_criteria_setup 
                SET version_keyctr = :version_keyctr, 
                    indicator_keyctr = :indicator_keyctr, 
                    minreqs_keyctr = :minreqs_keyctr, 
                    sub_minimumreqs = :sub_minimumreqs, 
                    movdocs_reqs = :movdocs_reqs, 
                    data_source = :data_source, 
                    trail = :trail 
                WHERE keyctr = :keyctr";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':version_keyctr' => $version_keyctr,
            ':indicator_keyctr' => $indicator_keyctr,
            ':minreqs_keyctr' => $minreqs_keyctr,
            ':sub_minimumreqs' => $sub_minimumreqs,
            ':movdocs_reqs' => $movdocs_reqs,
            ':data_source' => $data_source,
            ':trail' => $trail,
            ':keyctr' => $keyctr
        ]);

        $pdo->commit();
        $log->userLog('Edited a Criteria with id: '.$keyctr.', to Version ID: '.$version_keyctr.', Indicator ID: '.$indicator_keyctr.', Minimum Requirements ID: '. $minreqs_keyctr.', Sub Minimum Requirements: '.$sub_minimumreqs.', MOV Documents Requirements: '.$movdocs_reqs.', and Document Source: '.$data_source);
        echo "<script>alert('Record updated successfully'); window.location.href = document.referrer;</script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_criteria_setup WHERE keyctr = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception("No record found to delete.");
        }

        $pdo->commit();
        $log->userLog('Deleted a Criteria with an ID: '.$id);
        echo "<script>alert('Deleted successfully'); window.location.href = document.referrer;</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

if (isset($_GET['indicator_id'])) {
    $indicator_id = trim($_GET['indicator_id']); 

    try {
        $stmt = $pdo->prepare("
            SELECT 
                mr.keyctr AS req_keyctr,
                mr.indicator_keyctr,
                mr.reqs_code,
                mr.description AS min_requirement_desc,
                mr.sub_mininumreqs
            FROM maintenance_area_mininumreqs mr
            WHERE mr.indicator_keyctr = :indicator_id
            ORDER BY mr.reqs_code
        ");

        $stmt->bindParam(':indicator_id', $indicator_id, PDO::PARAM_STR); 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            error_log("No matching records for indicator_id: " . $indicator_id);
        }

        echo json_encode(['data' => $data], JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        echo json_encode(['error' => htmlspecialchars($e->getMessage())]);
    }
}
