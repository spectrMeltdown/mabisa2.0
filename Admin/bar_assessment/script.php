<?php

$pathPrepend = isset($isBarAss) ? '../../' : '../';
require_once $pathPrepend.'db/db.php';

if (isset($_POST['add_maintenance_criteria_setup'])) {

    $version_keyctr = $_POST['version_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $minreqs_keyctr = $_POST['minreqs_keyctr'];
    $sub_minimumreqs = $_POST['sub_minimumreqs'];
    $movdocs_reqs = $_POST['movdocs_reqs'];
    $trail = $_POST['trail'];
    $data_source = $_POST['data_source'];


    $insert_maintenance_criteria_setup = "
        INSERT INTO `maintenance_criteria_setup`(
            `version_keyctr`,
            `indicator_keyctr`,
            `minreqs_keyctr`,
            `sub_minimumreqs`,
            `movdocs_reqs`,
            `data_source`,
            `trail`
        )
        VALUES(
            '$version_keyctr',
            '$indicator_keyctr',
            '$minreqs_keyctr',
            '$sub_minimumreqs',
            '$movdocs_reqs',
            '$data_source',
            '$trail'
        )
    ";


    try {
        $stmt = $conn->prepare($insert_maintenance_criteria_setup);
        if ($stmt->execute()) {
            echo "<script>alert('New record created successfully'); window.location.href='index.php'</script>";
        } else {
            echo "Error executing query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
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




    $update_maintenance_criteria_setup = "
        UPDATE
            `maintenance_criteria_setup`
        SET
            `version_keyctr` = '$version_keyctr',
            `indicator_keyctr` = '$indicator_keyctr',
            `minreqs_keyctr` = '$minreqs_keyctr',
            `sub_minimumreqs` = '$sub_minimumreqs',
            `movdocs_reqs` = '$movdocs_reqs',
            `data_source` = '$data_source',
            `trail` = '$trail'
        WHERE
            `maintenance_criteria_setup`.`keyctr` =   $keyctr;
    ";




    if (mysqli_query($conn, $update_maintenance_criteria_setup)) {
        echo "<script>alert('Record updated successfully'); window.location.href='index.php'</script>";
    } else {
        echo "Error: " . $update_maintenance_criteria_setup . "<br>" . mysqli_error($conn);
    }

}


if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_criteria_setup WHERE keyctr = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Deleted successfully'); window.location.href='index.php'</script>";
        } else {
            echo "Error executing query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}



if (isset($_GET['indicator_id'])) {
    $indicator_id = $_GET['indicator_id'];



    $maintenance_area_mininumreqs = "SELECT * FROM `maintenance_area_mininumreqs`  
     where reqs_code =  $indicator_id;";
     $maintenance_area_mininumreqs_result = mysqli_query($conn, $maintenance_area_mininumreqs);

    $data = [];
    if (mysqli_num_rows($maintenance_area_mininumreqs_result) > 0) {
        while ($row = mysqli_fetch_assoc($maintenance_area_mininumreqs_result)) {

            $data[] = $row;
        }
    }


    echo json_encode(['data' => $data]);

}
