<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}
$isInFolder = true;
include '../script.php';

$data = [];

$maintenance_area_description_query = "
    SELECT
        maintenance_governance.*,
        maintenance_category.description AS category,
        maintenance_area.description AS area_description
    FROM `maintenance_governance`
    LEFT JOIN maintenance_category ON maintenance_governance.cat_code = maintenance_category.code
    LEFT JOIN maintenance_area ON maintenance_governance.area_keyctr = maintenance_area.keyctr;
";

$stmt = $pdo->prepare($maintenance_area_description_query);
$stmt->execute();
$maintenance_area_description_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($maintenance_area_description_result)) {
  foreach ($maintenance_area_description_result as $maintenance_area_description_row) {

    // maintenance_governance
    $maintenance_governance_query = "SELECT * FROM `maintenance_governance` WHERE desc_keyctr = :desc_keyctr";
    $stmt = $pdo->prepare($maintenance_governance_query);
    $stmt->execute(['desc_keyctr' => $maintenance_area_description_row['keyctr']]);
    $maintenance_governance_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($maintenance_governance_result)) {
      foreach ($maintenance_governance_result as $maintenance_governance_row) {

        // maintenance_area_indicators
        $maintenance_area_indicators_query = "SELECT * FROM `maintenance_area_indicators` WHERE governance_code = :governance_code";
        $stmt = $pdo->prepare($maintenance_area_indicators_query);
        $stmt->execute(['governance_code' => $maintenance_governance_row['keyctr']]);
        $maintenance_area_indicators_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($maintenance_area_indicators_result)) {
          foreach ($maintenance_area_indicators_result as $maintenance_area_indicators_row) {

            // maintenance_criteria_setup
            $maintenance_criteria_setup_query = "
                            SELECT 
                                msc.keyctr AS keyctr,
                                mam.description,
                                mam.reqs_code,
                                msc.movdocs_reqs AS documentary_requirements,
                                mds.srcdesc AS data_source
                            FROM `maintenance_criteria_setup` msc 
                            LEFT JOIN maintenance_criteria_version AS mcv ON msc.version_keyctr = mcv.keyctr
                            LEFT JOIN maintenance_area_mininumreqs AS mam ON msc.minreqs_keyctr = mam.keyctr
                            LEFT JOIN maintenance_document_source AS mds ON msc.data_source = mds.keyctr 
                            WHERE msc.indicator_keyctr = :indicator_keyctr
                            ORDER BY mam.reqs_code ASC
                        ";
            $stmt = $pdo->prepare($maintenance_criteria_setup_query);
            $stmt->execute(['indicator_keyctr' => $maintenance_area_indicators_row['keyctr']]);
            $maintenance_criteria_setup_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($maintenance_criteria_setup_result)) {
              foreach ($maintenance_criteria_setup_result as $maintenance_criteria_setup_row) {
                $data[$maintenance_area_description_row['category'] . " " .
                  $maintenance_area_description_row['area_description'] . ": " .
                  $maintenance_area_description_row['description']][] = [
                  'keyctr' => $maintenance_criteria_setup_row['keyctr'],
                  'indicator_code' => $maintenance_area_indicators_row['indicator_code'],
                  'indicator_description' => $maintenance_area_indicators_row['indicator_description'],
                  'relevance_definition' => $maintenance_area_indicators_row['relevance_def'],
                  'reqs_code' => $maintenance_criteria_setup_row['reqs_code'],
                  'documentary_requirements' => $maintenance_criteria_setup_row['documentary_requirements'],
                  'description' => $maintenance_criteria_setup_row['description'],
                  'data_source' => $maintenance_criteria_setup_row['data_source'],
                ];
              }
            }
          }
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require '../common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    include '../common/sidebar.php'

      ?>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <?php require '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Criteria</h1>
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <div class="container mt-5"></div>
            </div>
          </div>
          <!-- Content Row -->
          <div class="dropdown d-inline-block fluid">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
              data-bs-toggle="dropdown" aria-expanded="false" style="width: 200px">
              Version
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 1.0')">Version 1.0</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 2.0')">Version 2.0</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 3.0')">Version 3.0</a>
              </li>
            </ul>
          </div>
          <input type="text" id="versionInput" class="form-control d-inline-block" style="width: 200px" readonly />
          <script>
            function updateVersion(selectedVersion) {
              document.getElementById("versionInput").value = selectedVersion;
            }
          </script>
          <div class="dropdown d-inline-block fluid">
            <button class="btn btn-primary dropdown-toggle" type="button" id="governanceDropdown"
              data-bs-toggle="dropdown" aria-expanded="false" style="width: 200px">
              Governance
            </button>
            <ul class="dropdown-menu" aria-labelledby="governanceDropdown">
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 1')">Governance 1</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 2')">Governance 2</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 3')">Governance 3</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 4')">Governance 4</a>
              </li>
            </ul>
          </div>
          <input type="text" id="governanceInput" class="form-control d-inline-block" style="width: 200px" readonly />
          <script>
            function updateGovernance(selectedGovernance) {
              document.getElementById("governanceInput").value =
                selectedGovernance;
            }
          </script>
          <div class="container mt-5" style="padding-bottom: 20px">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#addMaintenanceCriteriaModal">
              Add Maintenance Criteria
            </button>

          </div>
          <?php
          $last_indicator = '';
          foreach ($data as $key => $rows): ?>
            <div class="card-header bg-primary text-center py-3">
              <div class="card-body">
                <h5 class="text-white"><?php echo htmlspecialchars($key); ?></h5>
              </div>
            </div>
            <?php foreach ($rows as $row):
              $current_row = $row['keyctr'];
              ?>
              <?php
              $current_indicator = $row['indicator_code'] . " " . $row['indicator_description'];
              if ($current_indicator !== $last_indicator):
                ?>
                <div class="row bg-info" style="margin: 0; padding: 10px 0;">
                  <h6 class="col-lg-12 text-center text-white" style="margin: 0;">
                    <?php echo htmlspecialchars($current_indicator); ?>
                  </h6>
                </div>
                <?php
                $last_indicator = $current_indicator;
                ?>
              <?php endif; ?>
              <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                <thead class="bg-secondary text-white">
                  <tr>
                    <th style="width: 5%; text-align: center;">Action</th>
                    <th style="width: 20%;text-align: center;">Relevant/Definition</th>
                    <th style="width: 20%;text-align: center;">Minimum Requirements</th>
                    <th style="width: 20%; text-align: center;"> Documentary Requirements/MOVs</th>
                    <th style="width: 10%;text-align: center;"> Data Source</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <button class="btn btn-primary open-modal" data-id="<?php echo $row['keyctr']; ?>">
                        Edit
                      </button>
                      <a href="../script.php?delete_id=<?php echo $row['keyctr'] ?>">Delete</a>
                    </td>
                    <td><?php echo $row['relevance_definition']; ?></td>
                    <td><?php echo $row['reqs_code'] . " " . $row['description']; ?></td>
                    <td><?php echo $row['documentary_requirements']; ?></td>
                    <td><?php echo $row['data_source']; ?></td>
                  </tr>
                </tbody>
              </table>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <!-- End of Main Content -->
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


  <div id="modalContainer"></div>

  <script>
    $(document).on("click", ".open-modal", function () {
      var edit_id = $(this).data("id");

      // Load modal2.php dynamically
      $.ajax({
        url: "edit.php",
        type: "POST",
        data: { edit_id: edit_id },
        success: function (response) {
          $("#modalContainer").html(response); // Append modal HTML

          // Ensure the modal script executes after adding it to DOM
          setTimeout(function () {
            var displayIdModal = new bootstrap.Modal(document.getElementById("displayIdModal"));
            displayIdModal.show();
          }, 200); // Small delay to ensure DOM update
        },
        error: function (xhr, status, error) {
          console.log("Error: " + error);
        }
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


  <?php include 'add.php' ?>
  <?php include 'edit.php' ?>
</body>

</html>