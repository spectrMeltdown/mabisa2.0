<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require_once 'common/auth.php';
require_once '../db/db.php';
require_once '../Admin/bar_assessment/responses.php';

if (!userHasPerms('reports_read', 'gen')) {
  header('Location:/mabisa2.0/Admin/no_permissions.php');
  exit;
}

$response = new Responses($pdo);
$barangayList = $response->show_responses();
$categories = $pdo->query("SELECT * FROM maintenance_governance")->fetchAll(PDO::FETCH_ASSOC);

$barangayProgress = [];
foreach ($barangayList as $data) {
  $progress = $response->getProgress($data['barangay_id']);

  $stmt = $pdo->prepare("SELECT MAX(date_uploaded) AS date_uploaded FROM barangay_assessment_files WHERE barangay_id = :barangay_id");
  $stmt->bindParam(':barangay_id', $data['barangay_id'], PDO::PARAM_INT);
  $stmt->execute();
  $lastModified = $stmt->fetch(PDO::FETCH_ASSOC)['date_uploaded'] ?? '0000-00-00 00:00:00';

  $barangayProgress[] = [
    'barangay_id' => $data['barangay_id'],
    'progress' => $progress,
    'date_uploaded' => $lastModified
  ];
}


usort($barangayProgress, function ($a, $b) {
  if ($b['progress'] !== $a['progress']) {
    return $b['progress'] <=> $a['progress'];
  }
  return strtotime($a['date_uploaded']) <=> strtotime($b['date_uploaded']);
});

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
if ($limit !== 0) {
  $topBarangays = array_slice($barangayProgress, 0, $limit);
} else {
  $topBarangays = $barangayProgress;
}

$allAreaDescriptions = [];
foreach ($categories as $category) {
  $descQuery = 'SELECT * FROM maintenance_area_indicators WHERE desc_keyctr = :desc_keyctr';
  $descStmt = $pdo->prepare($descQuery);
  $descStmt->bindParam(':desc_keyctr', $category['desc_keyctr'], PDO::PARAM_INT);
  $descStmt->execute();
  $areaDescriptions = $descStmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($areaDescriptions as $area) {
    $allAreaDescriptions[$category['desc_keyctr']][$area['area_description']][] = $area['keyctr'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'common/head.php'; ?>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php
    $isReports = true;
    require_once 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php require_once 'common/nav.php'; ?>
        <div class="container-fluid">
          <div class="card shadow mb-4">

            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <div class="d-flex justify-content-start w-10">
                <h6 class="m-0 font-weight-bold text-primary">Top Performing Barangays</h6>
              </div>
              <div class="d-flex justify-content-end">
                <select id="limitSelect" class="form-control w-auto mx-2">
                  <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                  <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                  <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                  <option value="0" <?= $limit == 0 ? 'selected' : '' ?>>All</option>
                </select>
                <a href="reports_ranking.php" class="btn btn-danger">Download PDF</a>
              </div>
            </div>

            <div class="card-body">
              <ul class="list-group list-group-flush">
                <?php
                $a = 1;
                foreach ($topBarangays as $data) {
                  $stmt = 'SELECT brgyname FROM refbarangay WHERE brgyid = :brgyid';
                  $stmt2 = $pdo->prepare($stmt);
                  $stmt2->bindParam(':brgyid', $data['barangay_id'], PDO::PARAM_INT);
                  $stmt2->execute();
                  $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                  if ($result2) {
                    echo '<li class="list-group-item d-flex align-items-center justify-content-between" style="font-size: 20px;">
                        <span style="min-width: 30px; text-align: right;"><strong>' . $a . '.</strong></span>
                        <span style="flex-grow: 1; padding-left: 10px;">' . htmlspecialchars($result2['brgyname']) . '</span>
                        <span class="badge badge-primary badge-pill">' . $data['progress'] . '%</span>
                      </li>';
                  } else {
                    echo '<li class="list-group-item text-muted d-flex align-items-center justify-content-between">
                        <span style="min-width: 30px; text-align: right;"><strong>' . $a . '.</strong></span>
                        <span style="flex-grow: 1; padding-left: 10px;">No name found</span>
                      </li>';
                  }
                  $a++;
                }
                ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Barangay Responses Table -->
        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h6 class="m-0 font-weight-bold text-primary">Barangay Responses</h6>
              <a href="reports_generate.php" class="btn btn-danger">Download PDF</a>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Barangay</th>
                      <?php
                      $uniqueCategories = [];

                      foreach ($categories as $category) {
                        if (!in_array($category['cat_code'], $uniqueCategories)) {
                          $uniqueCategories[] = $category['cat_code'];
                          $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM maintenance_governance WHERE cat_code = :cat_code");
                          $stmt->bindParam(':cat_code', $category['cat_code'], PDO::PARAM_STR);
                          $stmt->execute();
                          $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                          echo '<th colspan="' . $count . '"  style="text-align: center; vertical-align: middle;">' .
                            htmlspecialchars($category['cat_code']) .
                            '</th>';
                        }
                      }
                      ?>
                    </tr>
                    <tr>
                      <th></th>
                      <?php
                      $uniqueDescriptions = [];

                      foreach ($categories as $category) {
                        if (isset($allAreaDescriptions[$category['desc_keyctr']])) {
                          foreach (array_keys($allAreaDescriptions[$category['desc_keyctr']]) as $description) {
                            if (!in_array($description, $uniqueDescriptions)) {
                              $uniqueDescriptions[] = $description;
                              echo '<th>' . htmlspecialchars($description) . '</th>';
                            }
                          }
                        }
                      }
                      ?>


                    </tr>
                  </thead>


                  <tbody>
                    <?php foreach ($barangayList as $barangay) { ?>
                      <tr>
                        <td><?= htmlspecialchars($barangay['barangay']) ?></td>
                        <?php foreach ($allAreaDescriptions as $governance => $descriptions) {
                          foreach ($descriptions as $description => $indicator_keyctrs) {
                            $totalCriteria = 0;
                            $submittedCount = 0;

                            $criteriaQuery = 'SELECT keyctr FROM maintenance_criteria_setup WHERE indicator_keyctr IN (' . implode(',', $indicator_keyctrs) . ')';
                            $criteriaStmt = $pdo->prepare($criteriaQuery);
                            $criteriaStmt->execute();
                            $criteriaList = $criteriaStmt->fetchAll(PDO::FETCH_COLUMN);

                            $totalCriteria = count($criteriaList);

                            if ($totalCriteria > 0) {
                              $fileQuery = 'SELECT COUNT(*) as submitted FROM barangay_assessment_files 
                                            WHERE criteria_keyctr IN (' . implode(',', $criteriaList) . ') 
                                            AND barangay_id = :barangay_id';
                              $fileStmt = $pdo->prepare($fileQuery);
                              $fileStmt->bindParam(':barangay_id', $barangay['barangay_id'], PDO::PARAM_INT);
                              $fileStmt->execute();
                              $fileResult = $fileStmt->fetch(PDO::FETCH_ASSOC);

                              $submittedCount = $fileResult['submitted'];
                            }
                        ?>
                            <td>

                              <?php
                              if ($submittedCount == $totalCriteria && $submittedCount != 0) {
                                echo '<i class="fa fa-check text-success" aria-hidden="true"></i>';
                              } elseif ($submittedCount == 0 && $totalCriteria == 0) {
                                echo '';
                              } else {
                                echo $submittedCount . '/' . $totalCriteria;
                              }
                              ?>

                            </td>
                        <?php }
                        } ?>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <script>
    document.getElementById('limitSelect').addEventListener('change', function() {
      window.location.href = "?limit=" + this.value;
    });
  </script>
</body>


</html>