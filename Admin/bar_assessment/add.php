<?php
include 'script.php';

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
        <h4>Add Maintenance Criteria Setup</h4>


        <form action="script.php" method="post">
            <div class="mb-3">
                <label class="form-label">Version</label>
                <select class="form-control" name="version_keyctr">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_criteria_version_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_criteria_version_result)) { ?>
                            <option value="<?php echo $row['keyctr']; ?>"><?php echo $row['short_def']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Indicator</label>
                <select class="form-control" name="indicator_keyctr">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_area_indicators_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_area_indicators_result)) { ?>
                            <option data-indicator-code="<?php echo  $row['indicator_code'];  ?>" value="<?php echo $row['keyctr']; ?>"><?php echo $row['indicator_code']." ". $row['indicator_description']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">minreqs_keyctr</label>
                <select class="form-control" name="minreqs_keyctr">
                    <!-- <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_area_mininumreqs_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_area_mininumreqs_result)) { ?>
                            <option value="<?php echo $row['keyctr']; ?>">
                                <?php echo $row['reqs_code'] . " " . $row['description']; ?>
                            </option>
                        <?php }
                    }
                    ?>   -->

                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">sub_minimumreqs</label>

                <input type="number" class="form-control" name="sub_minimumreqs" />

            </div>

            <div class="mb-3">
                <label class="form-label">DOCUMENTARY REQUIREMENTS/MOVs</label>
                <textarea class="form-control" name="movdocs_reqs" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Data Source</label>
                <select class="form-control" name="data_source">
                    <option value="">Select</option>
                    <?php
                    if (mysqli_num_rows($maintenance_document_source_result) > 0) {
                        while ($row = mysqli_fetch_assoc($maintenance_document_source_result)) { ?>
                            <option value="<?php echo $row['keyctr']; ?>"><?php echo $row['srcdesc']; ?></option>
                        <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Trail</label>
                <textarea class="form-control" name="trail" rows="3"></textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary btn-block" name="add_maintenance_criteria_setup" ">Submit</button>
            </div>


        </form>

    </div>  

    <script src=" https://code.jquery.com/jquery-3.7.1.min.js"></script>


                    <script>

                        $(document).ready(function () {



                            $('select[name="indicator_keyctr"]').change(function () {
                                // Get the selected option
                                    const selectedOption = $(this).find(':selected');

                                // Access the data-indicator-code attribute
                                const indicatorCode = selectedOption.data('indicator-code');
 
                                $.ajax({
                                    url: './script.php',
                                    type: 'GET',
                                    data: {
                                        'indicator_id': indicatorCode
                                    },
                                    async: true,
                                    dataType: 'JSON',
                                    success: function (response) {
                                        // Check if `response.data` exists and is an array
                                        if (response.data && Array.isArray(response.data)) {
                                            // Clear current options in `minreqs_keyctr`
                                            // $('select[name="minreqs_keyctr"]').empty().append('<option value="">Select</option>');

                                            // Append new options from `response.data`
                                            $.each(response.data, function (index, item) {
                                                $('select[name="minreqs_keyctr"]').append(
                                                    $('<option>', {
                                                        value: item.keyctr,
                                                        text: item.reqs_code + " " + item.description
                                                    })
                                                );
                                            });
                                        } else {
                                            console.error("Unexpected response format:", response);
                                        }
                                    }
                                });
                            });


                        });



                    </script>

</body>

</html>
