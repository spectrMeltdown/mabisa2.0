<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

$isInFolder = true;
require_once '../common/auth.php';
if (!userHasPerms('criteria_read', 'gen')) {
  header('Location:' . substr(__DIR__, 0, strrpos(__DIR__, '/')) . 'no_permissions.php');
  exit;
}

include '../script.php';

// Set pathprepend again for the requires/import below
$pathPrepend = isset($isInFolder) ? '../../' : '../';

$data = [];

// Fetch Active Version Keyctr
$stmt = $pdo->prepare("SELECT keyctr FROM maintenance_criteria_version WHERE active_ = 1 LIMIT 1");
$stmt->execute();
$version = $stmt->fetch(PDO::FETCH_ASSOC);
$active_version_keyctr = $version ? $version['keyctr'] : null; // Get the active version or null

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

            // maintenance_criteria_setup with active version filtering
            $maintenance_criteria_setup_query = "
                SELECT 
                    msc.keyctr AS keyctr,
                    mam.description,
                    mam.reqs_code,
                    mam.relevance_definition,
                    msc.movdocs_reqs AS documentary_requirements,
                    msc.template, 
                    mds.srcdesc AS data_source
                FROM `maintenance_criteria_setup` msc 
                LEFT JOIN maintenance_criteria_version AS mcv ON msc.version_keyctr = mcv.keyctr
                LEFT JOIN maintenance_area_mininumreqs AS mam ON msc.minreqs_keyctr = mam.keyctr
                LEFT JOIN maintenance_document_source AS mds ON msc.data_source = mds.keyctr 
                WHERE msc.indicator_keyctr = :indicator_keyctr
                AND msc.version_keyctr = :active_version_keyctr
                ORDER BY mam.reqs_code ASC
            ";

            $stmt = $pdo->prepare($maintenance_criteria_setup_query);
            $stmt->execute([
              'indicator_keyctr' => $maintenance_area_indicators_row['keyctr'],
              'active_version_keyctr' => $active_version_keyctr
            ]);
            $maintenance_criteria_setup_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($maintenance_criteria_setup_result)) {
              foreach ($maintenance_criteria_setup_result as $maintenance_criteria_setup_row) {
                $data[$maintenance_area_description_row['category'] . " " .
                  $maintenance_area_description_row['area_description'] . ": " .
                  $maintenance_area_description_row['desc_keyctr']][] = [
                  'keyctr' => $maintenance_criteria_setup_row['keyctr'],
                  'indicator_code' => $maintenance_area_indicators_row['indicator_code'],
                  'indicator_description' => $maintenance_area_indicators_row['indicator_description'],
                  'relevance_definition' => $maintenance_criteria_setup_row['relevance_definition'],
                  'reqs_code' => $maintenance_criteria_setup_row['reqs_code'],
                  'documentary_requirements' => $maintenance_criteria_setup_row['documentary_requirements'],
                  'description' => $maintenance_criteria_setup_row['description'],
                  'data_source' => $maintenance_criteria_setup_row['data_source'],
                  'template' => $maintenance_criteria_setup_row['template'],
                ];
              }
            }
          }
        }
      }
    }
  }
}


// echo '<pre>';
// print_r($data);
// echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require_once '../common/head.php' ?>
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <style>
    table {
      border-collapse: collapse !important;
      border: 1px solid black !important;
    }

    th,
    td {
      border: 1px solid black !important;
      padding: 10px !important;
      text-align: center !important;
    }
  </style>
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
        <?php require_once '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->

          <!-- Content Row -->
          <!-- <div class="dropdown d-inline-block fluid">
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
         -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Assign Area</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-duration-modal" style="margin-right: 10px;">Edit Duration</a>
                  <a class="btn btn-primary" id="open-add-modal">Add Maintenance Criteria</a>
                </div>

              </div>
            </div>


            <div class="card-body">
              <?php if ($data): ?>
                <?php
                $last_indicator = '';
                foreach ($data as $key => $rows): ?>
                  <div class="card-header bg-primary text-center py-3">
                    <div class="card-body">
                      <h5 class="text-white"><?php echo htmlspecialchars($key); ?></h5>
                    </div>
                  </div>
                  <?php
                  $table_started = false;
                  $req_counts = [];

                  foreach ($rows as $row) {
                    $req_key = $row['relevance_definition'] . " " . $row['reqs_code'] . " " . $row['description'];
                    if (!isset($req_counts[$req_key])) {
                      $req_counts[$req_key] = 0;
                    }
                    $req_counts[$req_key]++;
                  }

                  $printed_reqs = [];

                  foreach ($rows as $row):
                    $current_indicator = $row['indicator_code'] . " " . $row['indicator_description'];

                    if ($current_indicator !== $last_indicator):
                      if ($table_started) {
                        echo "</tbody></table>";
                      }
                  ?>
                      <div class="row bg-info" style="margin: 0; padding: 10px 0;">
                        <h6 class="col-lg-12 text-center text-white" style="margin: 0;">
                          <?php echo htmlspecialchars($current_indicator); ?>
                        </h6>
                      </div>

                      <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                        <thead>
                          <tr>
                            <th style="width: 20%; text-align: center;">Relevant/Definition</th>
                            <th style="width: 20%; text-align: center;">Minimum Requirements</th>
                            <th style="width: 20%; text-align: center;">Documentary Requirements/MOVs</th>
                            <th style="width: 10%; text-align: center;">Data Source</th>
                            <th style="width: 7%; text-align: center;">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $last_indicator = $current_indicator;
                        $table_started = true;
                      endif;

                      $req_key = $row['relevance_definition'] . " " . $row['reqs_code'] . " " . $row['description'];
                        ?>
                        <tr>
                          <?php if (!isset($printed_reqs[$req_key])): ?>
                            <td rowspan="<?= $req_counts[$req_key]; ?>">
                              <span class="short-text">
                                <?= htmlspecialchars(substr($row['relevance_definition'], 0, 300)) . '...'; ?>
                              </span>
                              <span class="full-text" style="display: none;">
                                <?= htmlspecialchars($row['relevance_definition']); ?>
                              </span>
                              <a href="#" class="see-more">See more</a>
                            </td>
                            <td rowspan="<?= $req_counts[$req_key]; ?>">
                              <span class="short-text">
                                <?= htmlspecialchars(substr($row['reqs_code'] . " " . $row['description'], 0, 300)) . '...'; ?>
                              </span>
                              <span class="full-text" style="display: none;">
                                <?= htmlspecialchars($row['reqs_code'] . " " . $row['description']); ?>
                              </span>
                              <a href="#" class="see-more">See more</a>
                            </td>
                            <?php $printed_reqs[$req_key] = true; ?>
                          <?php endif; ?>

                          <td>
                            <?php
                            $link = htmlspecialchars($row['template']);
                            echo htmlspecialchars($row['documentary_requirements']) . '<br> <br>';
                            if (!empty($link)) {
                              echo '<a href="' . $link . '" target="_blank">View Template</a>';
                            } else {
                              echo 'No template available';
                            }
                            ?>
                          </td>

                          <td><?php echo htmlspecialchars($row['data_source']); ?></td>
                          <td>
                            <button class="btn btn-primary open-modal" data-id="<?php echo $row['keyctr']; ?>">
                              Edit
                            </button>
                            <a href="../script.php?delete_id=<?php echo $row['keyctr'] ?>" class="btn btn-danger delete-btn">Delete</a>
                          </td>
                        </tr>
                      <?php endforeach; ?>

                      <?php
                      if ($table_started) {
                        echo "</tbody></table>";
                      }
                      ?>
                    <?php endforeach; ?>

                  <?php else: ?>

                    <div style="display: flex; justify-content: center;">
                      No Requirements Yet
                    </div>
                  <?php endif; ?>
            </div>
          </div>
          <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
          </a>
          <!-- End of Main Content -->
        </div>
      </div>
    </div>
  </div>


  <div id="modalContainer"></div>
  <script>
    $(document).on("click", "#open-add-modal", function() {
      $.ajax({
        url: "add.php",
        type: "GET",
        success: function(response) {
          $("#modalContainer").html(response);
          setTimeout(function() {
            var addMaintenanceCriteriaModal = new bootstrap.Modal(document.getElementById("addMaintenanceCriteriaModal"));
            addMaintenanceCriteriaModal.show();
          }, 200);
        },
        error: function(xhr, status, error) {
          console.log("Error: " + error);
        }
      });
    });

    $(document).on("click", "#open-duration-modal", function() {
      $.ajax({
        url: "duration.php",
        type: "GET",
        success: function(response) {
          $("#modalContainer").html(response);
          setTimeout(function() {
            var addDurationModal = new bootstrap.Modal(document.getElementById("addDurationModal"));
            addDurationModal.show();
          }, 200);
        },
        error: function(xhr, status, error) {
          console.log("Error: " + error);
        }
      });
    });

    $(document).on("click", ".delete-btn", function(e) {
      e.preventDefault();
      var url = $(this).attr("href");
      if (confirm("Are you sure you want to delete this category?")) {
        window.location.href = url;
      }
    });

    $(document).on("click", ".open-modal", function() {
      var edit_id = $(this).data("id");

      $.ajax({
        url: "edit.php",
        type: "POST",
        data: {
          edit_id: edit_id
        },
        success: function(response) {
          $("#modalContainer").html(response);
          setTimeout(function() {
            var displayIdModal = new bootstrap.Modal(document.getElementById("displayIdModal"));
            displayIdModal.show();
          }, 200);
        },
        error: function(xhr, status, error) {
          console.log("Error: " + error);
        }
      });
    });



    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".see-more").forEach(function(link) {
        link.addEventListener("click", function(event) {
          event.preventDefault();
          let parent = this.parentElement;
          parent.querySelector(".short-text").style.display = "none";
          parent.querySelector(".full-text").style.display = "inline";
          this.style.display = "none";
        });
      });
    });
  </script>



  <?php include 'add.php' ?>
  <?php include 'edit.php' ?>
</body>


</html>