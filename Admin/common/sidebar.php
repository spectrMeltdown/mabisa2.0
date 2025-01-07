<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index_main.php">
    <div class="sidebar-brand-icon">
      <i><img src="<?= isset($indexMain) ? 'Logo.png' : '../Logo.png' ?>" height="60 px" /></i>
    </div>
    <div class="sidebar-brand-text mx-3">MABISA</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0" />

  <!-- Nav Item - Dashboard -->
  <li class="nav-item">
    <a class="nav-link" href="../index_main.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Criteria Management</div>

  <!-- Nav Item - Criteria -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="Criteria.php">
      <i class="fas fa-fw fa-cog"></i>
      <span>Set-Up Criteria</span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded"></div>
    </div>
  </li>

  <!-- Nav Item - Utilities Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true"
      aria-controls="collapseUtilities">
      <i class="fas fa-fw fa-wrench"></i>
      <span>Criteria Maintenance</span>
    </a>
    <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Settings</h6>
        <a class="collapse-item" href="<?= isset($indexMain) ? 'Admin/area.php' : 'area.php' ?>"">Area</a>
        <a class=" collapse-item"
          href="<?= isset($indexMain) ? 'Admin/area_description.php' : 'area_description.php' ?>">Area Description</a>
        <a class="collapse-item"
          href="<?= isset($indexMain) ? 'Admin/area_indicator.php' : 'area_indicator.php' ?>">Area Indicators</a>
        <a class="collapse-item" href="<?= isset($indexMain) ? 'Admin/min_req.php' : 'min_req.php' ?>">Minimum
          Requirements</a>
        <a class="collapse-item"
          href="<?= isset($indexMain) ? 'Admin/sub_req.php' : 'sub_req.php' ?>">Sub-Requirements</a>
        <a class="collapse-item" href="<?= isset($indexMain) ? 'Admin/category.php' : 'category.php' ?>">Category</a>
        <a class="collapse-item" href="<?= isset($indexMain) ? 'Admin/version.php' : 'version.php' ?>">Version</a>
        <a class="collapse-item" href="<?= isset($indexMain) ? 'Admin/docu_source.php' : 'docu_source.php' ?>">Document
          Source</a>
        <a class="collapse-item"
          href="<?= isset($indexMain) ? 'Admin/governance.php' : 'governance.php' ?>">Governance</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider" />

  <!-- Heading -->
  <div class="sidebar-heading">Barangay Management</div>

  <!-- Nav Item - Tables -->
  <li class="nav-item">
    <a class="nav-link" href="<?= isset($indexMain) ? 'Admin/bar_assessment.php' : 'bar_assessment.php' ?>">
      <i class="fas fa-fw fa-table"></i>
      <span>Barangay Assessment</span></a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="location.php">
      <i class="fas fa-fw fa-table"></i>
      <span>Location</span></a>
  </li>

  <li class="nav-item active">
    <a class="nav-link" href="users.php">
      <i class="fas fa-fw fa-users"></i>
      <span>Users</span></a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="reports.php">
      <i class="fas fa-fw fa-table"></i>
      <span>Generate Report</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block" />

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>
<!-- End of Sidebar -->