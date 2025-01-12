<?php
require_once './responses.php';
require_once '../../db/db.php';


$responsesObj = new Responses($pdo);
$responses = $responsesObj->show_responses();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="../">


    <title>MABISA - Admin</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--sidebar start  -->
        <?php include '../common/sidebar.php' ?>
        <!-- sidebar end -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include '../common/nav.php' ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <?php
                    $headingText = 'Barangay Assesment';
                    include '../common/page_heading.php'
                        ?>
                    <!-- End of Page Heading -->

                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <h6> Barangay Assessment Responses</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Barangay</th>
                                            <th>Date Uploaded</th>
                                            <th>Actions</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                       <tbody>
                                        <?php
                                        if (!empty($responses)) {
                                            foreach ($responses as $row) {


                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['barangay']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['dateUploaded']) . "</td>";
                                                echo "<td>";
                                                echo "<a href='bar_assessment/show_bar_response.php?barangay_id=" . htmlspecialchars($row['barangay_id']) . "' class='btn btn-primary btn-sm'>View</a>";
                                                echo "</td>";
                                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <tbody>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->


            </div>
            <!-- End of Main Content -->
        </div>

    </div>
    </div>
</body>










</body>

</body>

</html>