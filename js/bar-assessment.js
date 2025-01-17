$(".delete-user-btn").on("click", (e) => {
  const userID = $(e.target).data("id");
  const username = $(e.target).data("username");
  const role = $(e.target).data("role");

  console.log(`ID: ${userID}, Username: ${username}, Role: ${role}`);
});

$(document).ready(function () {
  $('#commentModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const file_id = button.data("fileid");
    const role = button.data("role");
    const name = button.data("name");

    console.log('File ID:', file_id);
    console.log('Role:', role);
    console.log('Name:', name);


    $('#modalFileId').val(file_id);

    $('#commentModal form input[name="file_id"]').val(file_id);
    $('#commentModal form input[name="name"]').val(name);

    fetchComments(file_id);
  });

  function fetchComments(file_id) {
    $.ajax({
      url: 'bar_assessment/fetch_comments.php',
      type: 'POST',
      data: { file_id: file_id },
      success: function (response) {
        $('#commentsContainer').html(response);
      },
      error: function () {
        console.error('Failed to fetch comments.');
      }
    });
  }
});


async function confirmDelete(button) {
  console.log("Delete button clicked"); // Debugging log
  const confirmed = confirm("Are you sure you want to delete this content?");
  if (!confirmed) return;

  const form = button.closest('form');
  const file_id = form.getAttribute('data-id');

  try {
    const response = await fetch('./bar_assessment/user_actions/delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ file_id })
    });

    const text = await response.text();
    console.log('Raw server response:', text);

    try {
      const result = JSON.parse(text);
      if (response.ok && result.success) {
        alert('Content deleted successfully.');

        // Clear the specific cells
        const row = button.closest('tr');
        if (row) {
          row.querySelector('.data-cell-status').innerHTML = ''; // Clear status
          row.querySelector('.data-cell-comments').innerHTML = ''; // Clear comments
          row.querySelector('.data-cell-date-uploaded').innerHTML = ''; // Clear date uploaded
          const formHtml = `
                     <form method="POST" action="./bar_assessment/user_actions/upload.php"
                                                                                enctype="multipart/form-data"
                                                                                id="uploadForm_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="barangay_id"
                                                                                    value="<?php echo htmlspecialchars($barangay_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="req_keyctr"
                                                                                    value="<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="desc_ctr"
                                                                                    value="<?php echo htmlspecialchars($area_desc['keyctr'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="indicator_code"
                                                                                    value="<?php echo htmlspecialchars($indicator['indicator_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="reqs_code"
                                                                                    value="<?php echo htmlspecialchars($minReq['reqs_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="file" name="file"
                                                                                    id="fileInput_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>"
                                                                                    style="display: none;" required>
                                                                                <button type="button" class="btn btn-primary" title="Upload"
                                                                                    onclick="document.getElementById('fileInput_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>').click();">
                                                                                    <i class="fa fa-upload"></i>
                                                                                </button>
                                                                            </form>`; // Show upload button
          row.querySelector('.data-cell-upload-view').innerHTML = formHtml;
          const uploadButton = document.getElementById("uploadButton_${<?php echo json_encode($current_req_keyctr); ?>}");
          if (uploadButton) {
            uploadButton.addEventListener('click', function () {
              document.getElementById("fileInput_${<?php echo json_encode($current_req_keyctr); ?>}").click();
            });
          }
          const fileInput = document.getElementById("fileInput_${<?php echo json_encode($current_req_keyctr); ?>}");
          if (fileInput) {
            fileInput.addEventListener('change', function () {
              if (this.files.length > 0) {
                document.getElementById("uploadForm_${<?php echo json_encode($current_req_keyctr); ?>}").submit();
              }
            });
          }
        } else {
          console.error("Row not found. Please check the DOM structure.");
        }
      } else {
        alert(result.message || 'Failed to delete the content.');
      }
    } catch (parseError) {
      console.error('JSON parse error:', parseError, 'Response:', text);
      alert('Invalid server response. Please contact support.');
    }
  } catch (error) {
    console.error('Error during deletion:', error);
    alert('An unexpected error occurred. Please try again.');
  }
}
