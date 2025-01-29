<?php
include 'script.php';


$id = $_GET['edit_id'];

// get maintenance criteria setup where id = $Id

$maintenance_criteria_setup = "SELECT * FROM `maintenance_criteria_setup` where keyctr =  $id";
$maintenance_criteria_setup_result = mysqli_query($conn, $maintenance_criteria_setup);

if (mysqli_num_rows($maintenance_criteria_setup_result) > 0) {
    $maintenance_criteria_setup_row = mysqli_fetch_assoc($maintenance_criteria_setup_result);
}

// get maintenance criteria version

$maintenance_criteria_version = "SELECT * FROM `maintenance_criteria_version`";
$maintenance_criteria_version_result = mysqli_query($conn, $maintenance_criteria_version);



// get maintenance area indicators

$maintenance_area_indicators = "SELECT * FROM `maintenance_area_indicators`";
$maintenance_area_indicators_result = mysqli_query($conn, $maintenance_area_indicators);



// get maintenance area mininumreqs

$maintenance_area_mininumreqs = "SELECT * FROM `maintenance_area_mininumreqs`";
$maintenance_area_mininumreqs_result = mysqli_query($conn, $maintenance_area_mininumreqs);


// get maintenance area mininumreqs

$maintenance_document_source = "SELECT * FROM `maintenance_document_source`";
$maintenance_document_source_result = mysqli_query($conn, $maintenance_document_source);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />

    <title>Add</title>
</head>

<body>

    <div class="container">
        <h4>Edit Maintenance Criteria Setup</h4>


        <form action="script.php" method="post">
            <input type="hidden" name="keyctr" value="<?php echo $maintenance_criteria_setup_row['keyctr']; ?>" />
            <br />
            <div class="mb-3">
                <label class="form-label">Version</label>
                <select class="form-control" name=" version_keyctr">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_criteria_version_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_criteria_version_result)) { ?>
                            <option <?php echo $maintenance_criteria_setup_row['version_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['short_def']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Indicator</label>
                <select class="form-control" name=" indicator_keyctr">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_area_indicators_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_area_indicators_result)) { ?>
                            <option <?php echo $maintenance_criteria_setup_row['indicator_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['indicator_description']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">minreqs_keyctr</label>
                <select class="form-control" name=" minreqs_keyctr">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_area_mininumreqs_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_area_mininumreqs_result)) { ?>
                            <option <?php echo $maintenance_criteria_setup_row['minreqs_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['reqs_code'] ." ". $row['description']; ?></option>
                        <?php }
                    }
                    ?>

                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">sub_minimumreqs</label>

                <input type="number" class="form-control" name="sub_minimumreqs"
                    value="<?php echo $maintenance_criteria_setup_row['sub_minimumreqs']; ?>" />

            </div>

            <div class="mb-3">
                <label class="form-label">DOCUMENTARY REQUIREMENTS/MOVs</label>
                <textarea class="form-control" name="movdocs_reqs" rows="3">
                <?php echo trim($maintenance_criteria_setup_row['movdocs_reqs']); ?>
                </textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Data Source</label>
                <select class="form-control" name=" data_source">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_document_source_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_document_source_result)) { ?>
                            <option <?php echo $maintenance_criteria_setup_row['data_source'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['srcdesc']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Trail</label>
                <textarea class="form-control" name="trail" rows="3">
                <?php echo trim($maintenance_criteria_setup_row['trail']); ?>
                </textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary btn-block" name="update_maintenance_criteria_setup" ">Submit</button>
            </div>


        </form>

    </div>  

</body>
</html>