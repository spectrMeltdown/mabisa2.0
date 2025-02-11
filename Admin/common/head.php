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

$pathPrepend = isset($isInFolder) ? '../../' : '../';

if (isset($_COOKIE['id'])) {
  // getting user data
  $customUserID = 'self';
  $userData;
  require $pathPrepend . 'api/get_user.php'; // this will provide userData array
  require $pathPrepend . 'api/util/url_exists.php';
  // unset after using
  unset($customUserID);

  // get the general permissions
  function getPermissions($isBarPerms = false): array | string
  {
    // auto-skip if super admin
    global $userData;
    // writeLog($userData);
    if ($userData['role'] == 'Super Admin') return 'all';
    $sql = '';
    if ($isBarPerms) {
      $sql = 'SELECT b.brgyid, p.* from permissions p 
    INNER JOIN user_roles_barangay urb on urb.permission_id = p.id
    INNER JOIN refbarangay b on b.brgyid = urb.barangay_id
    WHERE urb.user_id = :id;
    ';
    } else {
      $sql = 'SELECT * from permissions p 
    inner join user_roles ur on ur.permissions_id = p.id
    WHERE ur.user_id = :id;
    ';
    }
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->execute([':id' => $userData['id']]);
    $permissions = $query->fetch();
    if ($permissions == false) return [];
    // writeLog('Permissions is');
    // writeLog($permissions);

    // get the permissions that have their value set to 1 (true)
    $permissions = array_keys(array_filter($permissions, function ($value) {
      return $value == 1;
    }));
    return $permissions; //return only column names
  }
  $userGenPerms = getPermissions();
  $userBarPerms = getPermissions(true);
  // get the barangay permissions
}

?>

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
<link href="<?= $pathPrepend ?>css/sb-admin-2.min.css" rel="stylesheet" />
<script src="<?= $pathPrepend ?>js/sb-admin-2.min.js" defer></script>

<!-- Other -->
<script src="<?= $pathPrepend ?>js/util/toast.js" defer></script>
<script src="<?= $pathPrepend ?>js/util/confirmation.js" defer></script><!-- keep on all pages, because it is for logout in navbar-->
<script src="<?= $pathPrepend ?>js/util/debounce.js" defer></script>

<script src="<?= $pathPrepend ?>js/app.js" defer></script>

<title>MABISA</title>