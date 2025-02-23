<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  // adding path prepend here, because it is defined in auth.php, which we don't use here
  $pathPrepend = isset($isInFolder) ? '../../' : '../';
  require_once 'common/head.php';
  ?>
  <script src="../js/forgot_password.js" defer></script>
  <script src="../js/util/confirmation.js" defer></script>
  <style>
    .step {
      display: none;
    }

    .active {
      display: block;
    }

    .buttons {
      margin-top: 10px;
    }

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
            <h3 class="mb-4">Account Recovery</h3>
            <div class="step active form-group">
              <form id="email-form" class="needs-validation" novalidate>
                <label for="email" class="form-label">Enter your email:</label>
                <input class="form-control" style="margin:auto; max-width: 20rem;" name="email" id="email" type="email" maxlength="100" minlength="7" required>
                <div class="invalid-feedback">testing</div>
              </form>
            </div>
            <div class="step form-group">
              <form id="code-form" class="needs-validation" novalidate>
                <label for="email" class="form-label">A code was sent to your email address.<br>Please visit your email and enter the code below:<br></label>
                <input class="form-control mt-2 no-spinner" style="transform: scale(1.2, 1.2); max-width: 5.3rem; margin: auto;" name="code" id="code" type="number" maxlength="6" minlength="6" placeholder="Code" required>
                <div class="invalid-feedback">testing</div>
              </form>
            </div>
            <div class="step form-group">
              <form id="new-pass-form" class="needs-validation" novalidate>
                <label for="email" class="form-label">New password:</label>
                <input class="form-control" name="password" id="password" type="password" maxlength="100" minlength="8" required>
                <div class="invalid-feedback"></div>
              </form>
            </div>
            <div class="buttons mt-4">
              <button id="cancel" class="btn btn-danger">Cancel</button>
              <button id="prevStep" class="btn btn-secondary d-none">Previous</button>
              <button id="nextStep" class="btn btn-primary">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once 'components/confirmation_dialog.php' ?>
</body>

</html>