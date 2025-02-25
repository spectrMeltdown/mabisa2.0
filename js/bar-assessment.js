$(".delete-user-btn").on("click", (e) => {
  const userID = $(e.target).data("id");
  const username = $(e.target).data("username");

  console.log(`ID: ${userID}, Username: ${username}`);
});


$(document).ready(function () {
  $('#commentModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var fileId = button.data('fileid');
      var name = button.data('name');
      var status = button.data('status'); 
      var modal = $(this);

      console.log('Modal is being shown');
      console.log('File ID:', fileId);
      console.log('Name:', name);
      console.log('Status:', status);

      modal.find('#commentModalLabel').text('File & Comments for File with ID: ' + fileId);


      modal.find('#fileDisplay').attr('src', "../bar_assessment/admin_actions/view.php?file_id=" + fileId);

      modal.find('input[name="file_id"]').val(fileId);
      modal.find('input[name="name"]').val(name);
      modal.find('input[name="status"]').val(status);
      modal.find('#approveFileId').val(fileId);
      modal.find('#declineFileId').val(fileId);
      modal.find('#revertFileId').val(fileId);

      var statusMessage = modal.find('#statusMessage');

      if (status === 'pending') {
          statusMessage.hide();
          modal.find('#approveForm').show();
          modal.find('#declineForm').show();
          modal.find('#revertForm').hide();
      } else if (status === 'approved') {
          statusMessage.text('File is already approved').show();
          modal.find('#approveForm').hide();
          modal.find('#declineForm').hide();
          modal.find('#revertForm').show();
      } else if (status === 'declined') {
          statusMessage.text('File is returned').show();
          modal.find('#approveForm').hide();
          modal.find('#declineForm').hide();
          modal.find('#revertForm').show();
      }

      fetchComments(fileId);
  });

  function fetchComments(fileId) {
      $.ajax({
          url: '../bar_assessment/fetch_comments.php',
          type: 'POST',
          data: { file_id: fileId },
          success: function (response) {
              $('#commentsContainer').html(response);
          },
          error: function () {
              console.error('Failed to fetch comments.');
          }
      });
  }
});


                                                        


//responsive table for barangay_responses
$(document).ready(function () {
  $('#barangayTable').DataTable({
      paging: true,       
      pageLength: 10,       
      lengthMenu: [5, 10, 25, 50],
      searching: true,      
      ordering: true,       
      order: [[0, 'asc']],   
      columnDefs: [
          {
              orderable: false, 
              targets: [2],
          },
      ],
      language: {
          search: "Search Barangay:",
          lengthMenu: "Show _MENU_ entries",
      },
  });
});
