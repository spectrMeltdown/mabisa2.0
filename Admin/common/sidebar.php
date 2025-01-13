<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="<?= isset($root) ? 'dashboard.php' : '../dashboard.php' ?>">
    <div class="sidebar-brand-icon">
      <i><img src="<?= isset($root) ? 'Logo.png' : '../Logo.png' ?>" height="60 px" /></i>
    </div>
    <div class="sidebar-brand-text mx-3">MABISA</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0" />

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?= isset($root) ? 'active' : '' ?>">
    <a class="nav-link" href="<?= isset($root) ? 'dashboard.php' : '../dashboard.php' ?>">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Criteria Management</div>

  <!-- Nav Item - Criteria -->
  <li class="nav-item <?= isset($isSetupCriteriaPhp) ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="<?= isset($root) ? 'Admin/criteria.php' : 'criteria.php' ?>">
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
        <a class="collapse-item" href="<?= isset($root) ? 'Admin/area.php' : 'area.php' ?>"">Area</a>
        <a class=" collapse-item"
          href="<?= isset($root) ? 'Admin/area_description.php' : 'area_description.php' ?>">Area Description</a>
        <a class="collapse-item"
          href="<?= isset($root) ? 'Admin/area_indicator.php' : 'area_indicator.php' ?>">Area Indicators</a>
        <a class="collapse-item" href="<?= isset($root) ? 'Admin/min_req.php' : 'min_req.php' ?>">Minimum
          Requirements</a>
        <a class="collapse-item"
          href="<?= isset($root) ? 'Admin/sub_req.php' : 'sub_req.php' ?>">Sub-Requirements</a>
        <a class="collapse-item" href="<?= isset($root) ? 'Admin/category.php' : 'category.php' ?>">Category</a>
        <a class="collapse-item" href="<?= isset($root) ? 'Admin/version.php' : 'version.php' ?>">Version</a>
        <a class="collapse-item" href="<?= isset($root) ? 'Admin/docu_source.php' : 'docu_source.php' ?>">Document
          Source</a>
        <a class="collapse-item"
          href="<?= isset($root) ? 'Admin/governance.php' : 'governance.php' ?>">Governance</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Barangay Management</div>

  <!-- Nav Item - Tables -->
  <li class="nav-item <?= isset($isBarAssessmentPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?= isset($root) ? 'Admin/bar_assessment.php' : 'bar_assessment.php' ?>">
      <i class="fas fa-fw fa-edit"></i>
      <span>Barangay Assessment</span></a>
  </li>

  <li class="nav-item <?= isset($isLocationPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?= isset($root) ? 'Admin/location.php' : 'location.php' ?>">
      <i class="fas fa-fw fa-map-marker-alt"></i>
      <span>Location</span></a>
  </li>

  <li class="nav-item <?= isset($isUsersPhp) ? 'active' : '' ?>">
    <a class="nav-link" href="<?= isset($root) ? 'Admin/users.php' : 'users.php' ?>">
      <i class="fas fa-fw fa-users"></i>
      <span>Users</span></a>
  </li>

  <li class="nav-item <?= isset($isReports) ? 'active' : '' ?>">
    <a class="nav-link" href="<?= isset($root) ? 'Admin/reports.php' : 'reports.php' ?>">
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