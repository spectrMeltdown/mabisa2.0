<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require 'common/head.php' ?>
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
      border: none;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card text-center p-4">
          <div class="card-body">
            <h1 class="card-title mb-3">No permissions</h1>
            <p class="card-text text-muted mb-4">You do not have the permissions to view this page.</p>
            <button id="goBackBtn" class="btn btn-danger btn-lg"><i class="fas fa-arrow-circle-left mr-2"></i>Go back</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script>
  document.getElementById('goBackBtn').addEventListener('click', () => {
    // console.log('backing up');
    history.back();
  });
</script>

</html>