<?php
require 'admin_actions.php';
require_once '../../../db/db.php';

try {
    $admin = new Admin_Actions($pdo);

    $barangay_id = $_POST['barangay_id'];
    $req_keyctr = $_POST['req_keyctr'];
    $desc_ctr = $_POST['desc_ctr'];
    $indicator_code = $_POST['indicator_code'];
    $reqs_code = $_POST['reqs_code'];

 
    $admin->viewFile($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code);

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}