<?php global $pathPrepend; ?>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />

<?php
/* 
the following headers allow:
- prevention of user info being leaked
- page refresh when navigating back on browser (to show latest data in tables)

no-cache doesn't really mean totally no cache if you're worried.
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!-- Favicon -->

<link rel="apple-touch-icon" sizes="57x57" href="/mabisa2.0/img/favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/mabisa2.0/img/favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/mabisa2.0/img/favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/mabisa2.0/img/favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/mabisa2.0/img/favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/mabisa2.0/img/favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/mabisa2.0/img/favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/mabisa2.0/img/favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/mabisa2.0/img/favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/mabisa2.0/img/favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/mabisa2.0/img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/mabisa2.0/img/favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/mabisa2.0/img/favicon/favicon-16x16.png">
<link rel="manifest" href="/mabisa2.0/img/favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/mabisa2.0/img/favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

<!-- Font -->
<link
  href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
  rel="stylesheet">

<!-- FontAwesome -->
<link href="<?= $pathPrepend ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<script src="<?= $pathPrepend ?>vendor/fontawesome-free/js/all.min.js" defer></script>

<!-- Jquery -->
<script src="<?= $pathPrepend ?>vendor/jquery/jquery.min.js" defer></script>
<script src="<?= $pathPrepend ?>vendor/jquery-easing/jquery.easing.min.js" defer></script>

<!-- Bootstrap 4.6.0-->
<script src="<?= $pathPrepend ?>vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>

<!-- Chart.js -->
<script src="<?= $pathPrepend ?>vendor/chart.js/Chart.bundle.min.js" defer></script>

<!-- Datatables Bootstrap 4 -->
<link href="<?= $pathPrepend ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
<!-- jquery datatables must come first!! -->
<script src="<?= $pathPrepend ?>vendor/datatables/jquery.datatables.min.js" defer></script>
<script src="<?= $pathPrepend ?>vendor/datatables/dataTables.bootstrap4.min.js" defer></script>
<!-- <script src="vendor/rowsgroup/dataTables.rowsGroup.js" defer></script> -->

<!-- Template css -->
<link href="<?= $pathPrepend ?>css/sb-admin-2.css" rel="stylesheet" />
<script src="<?= $pathPrepend ?>js/sb-admin-2.js" defer></script>

<!-- Other -->
<script src="<?= $pathPrepend ?>js/util/toast.js" defer></script>
<script src="<?= $pathPrepend ?>js/util/confirmation.js" defer></script><!-- keep on all pages, because it is for logout in navbar-->
<script src="<?= $pathPrepend ?>js/util/debounce.js" defer></script>

<script src="<?= $pathPrepend ?>js/app.js" defer></script>

<?= isset($customTitle) ? '' : '<title>mabisa2.0</title>' ?>

<style>
  .unselectable {
    user-select: none;
    /* Standard */
    -webkit-user-select: none;
    /* Safari */
    -moz-user-select: none;
    /* Firefox */
    -ms-user-select: none;
    /* IE/Edge */
  }

  #accordionSidebar {
    position: fixed;
    top: 0;
    left: 0;
    /* Adjust width as needed */
    background-color: #4e73df;
    /* Ensure background color */
    z-index: 1030;
    /* Higher than content */
    overflow-y: auto;
    /* Enable scrolling if needed */
  }

  /* Hide number input controls */
  .no-spinner::-webkit-inner-spin-button,
  .no-spinner::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .no-spinner {
    -moz-appearance: textfield;
    /* Firefox */
  }
</style>