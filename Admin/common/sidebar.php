<?php
global $userBarPerms;
global $userGenPerms;
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="dashboard.php">
    <div class="sidebar-brand-icon">
      <i><img src="<?= $pathPrepend ?>img/logo.png" height="60px" /></i>
    </div>
    <div class="sidebar-brand-text mx-3">mabisa2.0</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-2" />

  <!-- Nav Item - Dashboard -->
  <li class="nav-item">
    <a class="nav-link" href="<?php echo isset($isInFolder) ? '../' : ''; ?>dashboard.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <?php
  // check if gen permissions has any criteria* permissions, or if 'all' is specified
  if ((is_string($userGenPerms) && $userGenPerms == 'all') || !empty(array_filter($userGenPerms ?? [], function ($value) {
    return str_contains($value, 'criteria');
  }))):
  ?>
    <!-- Divider -->
    <hr class="sidebar-divider" />

    <!-- Heading -->
    <div class="sidebar-heading">Criteria Management</div>

    <!-- Nav Item - Criteria -->
    <li class="nav-item <?= isset($isSetupCriteriaPhp) ? 'active' : '' ?>">
      <a class="nav-link collapsed" href="<?php echo isset($isInFolder) ? '../' : ''; ?>criteria/">
        <i class="fas fa-fw fa-cog"></i>
        <span>Set-Up Criteria</span>
      </a>
      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded"></div>
      </div>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item <?= isset($isCriteriaPhp) ? 'active' : '' ?>">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true"
        aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-wrench"></i>
        <span>Criteria Maintenance</span>
      </a>
      <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Settings</h6>

          <a class="collapse-item" href="<?php echo isset($isInFolder) ? '../' : ''; ?>version/">Version</a>
          <a class="collapse-item" href="<?php echo isset($isInFolder) ? '../' : ''; ?>category/">Category</a>
          <a class="collapse-item" href="<?php echo isset($isInFolder) ? '../' : ''; ?>area/">Area</a>

          <a class="collapse-item"
            href="<?php echo isset($isInFolder) ? '../' : ''; ?>governance/">Governance</a>
          <a class="collapse-item"
            href="<?php echo isset($isInFolder) ? '../' : ''; ?>area_indicator/">Area Indicators</a>
          <a class="collapse-item" href="<?php echo isset($isInFolder) ? '../' : ''; ?>min_req/">Minimum
            Requirements</a>
          <!-- <a class="collapse-item"
            href="<?php echo isset($isInFolder) ? '../' : ''; ?>sub_req/">Sub-Requirements</a> -->
          <a class="collapse-item" href="<?php echo isset($isInFolder) ? '../' : ''; ?>docu_source/">Document
            Source</a>
        </div>
      </div>
    </li>

  <?php endif; ?>
  <!-- Divider -->
  <hr class="sidebar-divider" />
  <!-- Heading -->
  <div class="sidebar-heading">Barangay Management</div>
  <?php
  // writeLog('Gen perms was');
  // writeLog($userGenPerms);
  // writeLog('bar perms was');
  // writeLog($userBarPerms);
  // check if gen permissions has any assessment* permissions, or if 'all' is specified, or if barPermissions has entries
  if (userHasPerms('assessment', 'any')):
  ?>
    <!-- Nav Item - Tables -->
    <li class="nav-item <?= isset($isInFolderessmentPhp) ? 'active' : '' ?>">
      <a class="nav-link" href="<?php echo isset($isInFolder) ? '../' : ''; ?>bar_assessment.php">
        <i class="fas fa-fw fa-edit"></i>
        <span>Barangay Assessment</span></a>
    </li>
  <?php
  endif;
  ?>
  <?php
  // check if gen permissions has any map* permissions, or if 'all' is specified
  if ((is_string($userGenPerms) && $userGenPerms == 'all') || !empty(array_filter($userGenPerms ?? [], function ($value) {
    return str_contains($value, 'map');
  }))):
  ?>
    <!-- <li class="nav-item <?= isset($isLocationPhp) ? 'active' : '' ?>">
      <a class="nav-link" href="<?php echo isset($isInFolder) ? '../' : ''; ?>location.php">
        <i class="fas fa-fw fa-map-marker-alt"></i>
        <span>Location</span></a>
    </li> -->
  <?php
  endif;
  ?>
  <?php
  // check if gen permissions has any user* permissions, or if 'all' is specified
  if ((is_string($userGenPerms) && $userGenPerms == 'all') || !empty(array_filter($userGenPerms ?? [], function ($value) {
    return str_contains($value, 'user');
  }))):
  ?>
    <li class="nav-item <?= isset($isUsersPhp) ? 'active' : '' ?>">
      <a class="nav-link" href="<?php echo isset($isInFolder) ? '../' : ''; ?>users.php">
        <i class="fas fa-fw fa-users"></i>
        <span>Users</span></a>
    </li>
  <?php
  endif;
  ?>
  <?php
  global $userGenPerms;
  // check if gen permissions has any user* permissions, or if 'all' is specified
  if ((is_string($userGenPerms) && $userGenPerms == 'all') || !empty(array_filter($userGenPerms ?? [], function ($value) {
    return str_contains($value, 'user');
  }))):
  ?>
    <li class="nav-item <?= isset($isReports) ? 'active' : '' ?>">
      <a class="nav-link" href="<?php echo isset($isInFolder) ? '../' : ''; ?>reports.php">
        <i class="fas fa-fw fa-chart-line"></i>
        <span>Reports</span></a>
    </li>
  <?php
  endif;
  ?>
  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block" />

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>
<!-- End of Sidebar -->

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="dashboard.php">
    <div class="sidebar-brand-icon">
      <i><img src="<?= $pathPrepend ?>img/logo.png" height="60px" /></i>
    </div>
    <div class="sidebar-brand-text mx-3">mabisa2.0</div>
  </a>
  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>