<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="dashboard.php">
    <div class="sidebar-brand-icon">
      <i><img src="<?= $pathPrepend ?>img/logo.png" height="60px" /></i>
    </div>
    <div class="sidebar-brand-text mx-3">MABISA</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0" />

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?= '' ?>">
    <a class="nav-link" href="<?php echo isset($isBarAss) ? '../' : ''; ?>dashboard.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Criteria Management</div>

  <!-- Nav Item - Criteria -->
  <li class="nav-item <?= isset($isSetupCriteriaPhp) ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="<?php echo isset($isBarAss) ? '../' : ''; ?>criteria.php">
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
        <a class="collapse-item" href="<?php echo isset($isBarAss) ? '../' : ''; ?>area.php">Area</a>
        <a class=" collapse-item"
          href="<?php echo isset($isBarAss) ? '../' : ''; ?>area_description.php">Area Description</a>
        <a class="collapse-item"
          href="area_indicator.php">Area Indicators</a>
        <a class="collapse-item" href="<?php echo isset($isBarAss) ? '../' : ''; ?>min_req.php">Minimum
          Requirements</a>
        <a class="collapse-item"
          href="<?php echo isset($isBarAss) ? '../' : ''; ?>sub_req.php">Sub-Requirements</a>
        <a class="collapse-item" href="<?php echo isset($isBarAss) ? '../' : ''; ?>category.php">Category</a>
        <a class="collapse-item" href="<?php echo isset($isBarAss) ? '../' : ''; ?>version.php">Version</a>
        <a class="collapse-item" href="<?php echo isset($isBarAss) ? '../' : ''; ?>docu_source.php">Document
          Source</a>
        <a class="collapse-item"
          href="<?php echo isset($isBarAss) ? '../' : ''; ?>governance.php">Governance</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Barangay Management</div>

  <!-- Nav Item - Tables -->
  <li class="nav-item <?= isset($isBarAssessmentPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?php echo isset($isBarAss) ? '../' : ''; ?>bar_assessment.php">
      <i class="fas fa-fw fa-edit"></i>
      <span>Barangay Assessment</span></a>
  </li>

  <li class="nav-item <?= isset($isLocationPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?php echo isset($isBarAss) ? '../' : ''; ?>location.php">
      <i class="fas fa-fw fa-map-marker-alt"></i>
      <span>Location</span></a>
  </li>

  <li class="nav-item <?= isset($isUsersPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?php echo isset($isBarAss) ? '../' : ''; ?>users.php">
      <i class="fas fa-fw fa-users"></i>
      <span>Users</span></a>
  </li>

  <li class="nav-item <?= isset($isReports) ? 'active' : '' ?>">
    <a class="nav-link" href="<?php echo isset($isBarAss) ? '../' : ''; ?>reports.php">
      <i class="fas fa-fw fa-chart-line"></i>
      <span>Reports</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block" />

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>
<!-- End of Sidebar -->