<?php

declare(strict_types=1);
require_once '../models/user_model.php';
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $id = $_POST['id'];
  /** @var string */
  $role = $_POST['role'];
  /** @var string */
  $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'N/A';

  // insert to database
  global $pdo;
  $sql = 'update user_policy set role=:role, barangay=:barangay where id=:id;';
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':id' => $id,
      ':role' => $role,
      ':barangay' => $barangay,
    ]);
    echo 'Success! You may now close this tab.';
  } catch (\Throwable $th) {
    echo  $th;
  }
}

<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
session_set_cookie_params(0);
session_start();

// if(!$_SESSION['idno']){ header('location:../actions/logout.php'); }
require_once '../../dbconn.php';
$dbconn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


if (!$dbconn) {
  die("ERROR: Unable to connect to database.");
}

$date_ = date('Y-m-d');
$time_ = date('H:i:s');
$year_ = date('Y');

if (isset($_POST['update'])) {
  $country = $_POST['country'];
  $region = $_POST['region'];
  $province = $_POST['province'];
  $city = $_POST['city'];
  $barangay = $_POST['barangay'];
  $id = $_POST['id'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $account_type = $_POST['account_type'];

  try {

    $dbconn->beginTransaction();
    //add country

    $stmt = $dbconn->prepare("UPDATE account set username=?,password=?,date=?,time=?,year=?,account_type=?,country_code=?,region_code=?,province_code=?,city_code=?,barangay_code=?,email=?,mobile=? where id=?");
    $stmt->bindParam(1, $username);
    $stmt->bindParam(2, $password);
    $stmt->bindParam(3, $date_);
    $stmt->bindParam(4, $time_);
    $stmt->bindParam(5, $year_);
    $stmt->bindParam(6, $account_type);
    $stmt->bindParam(7, $country);
    $stmt->bindParam(8, $region);
    $stmt->bindParam(9, $province);
    $stmt->bindParam(10, $city);
    $stmt->bindParam(11, $barangay);
    $stmt->bindParam(12, $email);
    $stmt->bindParam(13, $phone);
    $stmt->bindParam(14, $id);
    $stmt->execute();
    echo 1;

    $dbconn->commit();
    // echo "Transaction committed successfully!";
  } catch (PDOException $e) {
    $dbconn->rollBack();
    echo "Transaction failed: " . $e->getMessage();
  }
?>

<?php } ?>