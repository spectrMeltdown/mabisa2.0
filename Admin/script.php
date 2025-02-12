<?php

$pathPrepend = isset($isInFolder) ? '../' : '';
require_once $pathPrepend . '../db/db.php';


if (isset($_POST['add_maintenance_criteria_setup'])) {
    $version_keyctr = $_POST['version_keyctr'] ?? null;
    $indicator_keyctr = $_POST['indicator_keyctr'] ?? null;
    $minreqs_keyctr = $_POST['minreqs_keyctr'] ?? null;
    $sub_minimumreqs = $_POST['sub_minimumreqs'] ?? null;
    $movdocs_reqs = $_POST['movdocs_reqs'] ?? null;
    $trail = $_POST['trail'] ?? null;
    $data_source = $_POST['data_source'] ?? null;

    $query = "
        INSERT INTO `maintenance_criteria_setup` (
            `version_keyctr`,
            `indicator_keyctr`,
            `minreqs_keyctr`,
            `sub_minimumreqs`,
            `movdocs_reqs`,
            `data_source`,
            `trail`
        ) VALUES (
            :version_keyctr,
            :indicator_keyctr,
            :minreqs_keyctr,
            :sub_minimumreqs,
            :movdocs_reqs,
            :data_source,
            :trail
        )
    ";

    try {
        $stmt = $pdo->prepare($query);

        $success = $stmt->execute([
            ':version_keyctr' => $version_keyctr,
            ':indicator_keyctr' => $indicator_keyctr,
            ':minreqs_keyctr' => $minreqs_keyctr,
            ':sub_minimumreqs' => $sub_minimumreqs,
            ':movdocs_reqs' => $movdocs_reqs,
            ':data_source' => $data_source,
            ':trail' => $trail
        ]);

        if ($success) {
            echo "<script>alert('New record created successfully');window.location.href = document.referrer;</script>";
        } else {
            echo "Error executing query.";
        }
    } catch (PDOException $e) {
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
    $trail = $_POST['trail'];
    $data_source = $_POST['data_source'];

    try {
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

        $stmt->bindParam(':version_keyctr', $version_keyctr, PDO::PARAM_STR);
        $stmt->bindParam(':indicator_keyctr', $indicator_keyctr, PDO::PARAM_STR);
        $stmt->bindParam(':minreqs_keyctr', $minreqs_keyctr, PDO::PARAM_STR);
        $stmt->bindParam(':sub_minimumreqs', $sub_minimumreqs, PDO::PARAM_STR);
        $stmt->bindParam(':movdocs_reqs', $movdocs_reqs, PDO::PARAM_STR);
        $stmt->bindParam(':data_source', $data_source, PDO::PARAM_STR);
        $stmt->bindParam(':trail', $trail, PDO::PARAM_STR);
        $stmt->bindParam(':keyctr', $keyctr, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully');   window.location.href = document.referrer;</script>";
        } else {
            echo "<script>alert('Error updating record');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_criteria_setup WHERE keyctr = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Deleted successfully');window.location.href = document.referrer;</script>";
        } else {
            echo "Error executing query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_GET['indicator_id'])) {
    $indicator_id = $_GET['indicator_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM maintenance_area_mininumreqs WHERE indicator_code = :indicator_id");
        $stmt->execute(['indicator_id' => $indicator_id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $data]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
