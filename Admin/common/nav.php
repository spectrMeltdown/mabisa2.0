<?php
$pathPrepend = isset($isInFolder) ? '../' : '';
$doublePathPrepend = isset($isInFolder) ? '../../' : '../';
if (isset($isInFolder)):
?>
  <div class="d-none" id="isInFolder"></div>
<?php endif; ?>
<script src="<?= $doublePathPrepend ?>js/nav.js" defer></script> <!-- for letting js know that this file is nested in folder -->
<script src="<?= $doublePathPrepend ?>js/read_notif.js" defer></script>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
  <!-- Sidebar Toggle (Topbar) -->
  <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>

  <div class="header-title">
    MABILISANG AKSYON INFORMATION SYSTEM OF ALORAN
  </div>

  <!-- Topbar Navbar -->
  <ul class="navbar-nav ml-auto">
    <!-- Nav Item - Alerts -->
    <li class="nav-item dropdown no-arrow mx-1">
      <a class="nav-link dropdown-toggle" href="#alerts" id="alertsDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
        <?php
        require_once $doublePathPrepend . 'db/db.php';
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notifications WHERE is_read = 0 AND user_id = :user_id');
        $stmt->execute([':user_id' => $userData['id']]);
        $alertsCount = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = ($alertsCount['count'] > 99) ? '99+' : $alertsCount['count'];
        if ($count > 0):
        ?>
          <span class="badge badge-danger badge-counter"><?= $count ?></span>
        <?php endif; ?>
      </a>
      <!-- Dropdown - Alerts -->
      <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
        aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">Notifications</h6>
        <?php
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY is_read LIMIT 5 ');
        $stmt->execute([':user_id' => $userData['id']]);
        /** @var array */
        $alerts = $stmt->fetchAll();

        if ($alerts != false && count($alerts) > 0):
          foreach ($alerts as $alert):
        ?>
            <a class="dropdown-item d-flex align-items-center" href="#read-notif"
              data-toggle="modal"
              data-target="#readNotifModal"
              data-title="<?= $alert['title'] ?>"
              data-message="<?= $alert['message'] ?>"
              data-id="<?= $alert['id'] ?>">
              <div class="mr-3">
                <div class="icon-circle bg-primary">
                  <i class="fas fa-file-alt text-white"></i>
                </div>
              </div>
              <div>
                <div class="small text-gray-500"><?php echo $alert['created_at'] ?></div>
                <span class="<?= $alert['is_read'] ? 'text-gray-500' : 'font-weight-bold' ?>"><?php echo $alert['title'] ?></span>
              </div>
            </a>
          <?php
          endforeach;
        else:
          ?>
          <p class="my-3 text-center">No notifications yet..</p>
        <?php
        endif;
        ?>
        <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
      </div>
    </li>

    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 mt-3 d-none d-lg-inline text-gray-600 small" style="font-size: 15px"><?= $userData['full_name'] ?? '' ?><br>
          <p class="text-right" style="font-size: 12px"><?= $userData['role'] ?? '' ?> &nbsp;</p>
        </span>
        <!-- TODO: render the user profile pic below -->
        <img class="img-profile rounded-circle"
          src="<?= !empty($userData['profile_pic']) && is_file($userData['profile_pic']) ? $userData['profile_pic'] : $doublePathPrepend . 'img/undraw_profile.svg' ?>" />
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <a class="dropdown-item" href="<?= $pathPrepend ?>settings.php">
          <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-600"></i>
          Settings
        </a>
        <a class="dropdown-item" href="<?= $pathPrepend ?>dashboard.php#maintenanceTable">
          <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-600"></i>
          Activity Log
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" id="logoutBtn">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-600"></i>
          <span style="pointer-events: none;">Logout</span>
        </a>
      </div>
    </li>
  </ul>
</nav>
<?php require_once (isset($isInFolder) ? '../' : '') . 'components/confirmation_dialog.php'; ?>
<?php require_once (isset($isInFolder) ? '../' : '') . 'components/read_notif.php'; ?>
<!-- End of Topbar -->