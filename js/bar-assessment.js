$('.delete-user-btn').on('click', e => {
  const userID = $(e.target).data('id');
  const username = $(e.target).data('username');

  console.log(`ID: ${userID}, Username: ${username}`);
});

$(document).ready(function() {
  $('#commentModal').on('show.bs.modal', function(event) {
    let button = $(event.relatedTarget);
    let fileId = button.data('fileid');
    let name = button.data('name');
    let status = button.data('status');
    let bid = button.data('bid');
    let iid = button.data('iid');
    let expand = button.data('expand');
    let modal = $(this);

    console.log('Modal is being shown');
    console.log('File ID:', fileId);
    console.log('Name:', name);
    console.log('Status:', status);
    console.log('brgy id:', bid);
    console.log('indicator :', iid);
    console.log('expand :', expand);

    modal
      .find('#commentModalLabel')
      .text('File & Comments for File with ID: ' + fileId);

    modal
      .find('#fileDisplay')
      .attr(
        'src',
        '../bar_assessment/admin_actions/view.php?file_id=' + fileId,
      );

    modal.find('.bid').val(bid);
    modal.find('.iid').val(iid);
    modal.find('.expand').val(expand);
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
      success: function(response) {
        $('#commentsContainer').html(response);
      },
      error: function() {
        console.error('Failed to fetch comments.');
      },
    });
  }
});

//responsive table for barangay_responses
$(document).ready(function() {
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
      search: 'Search Barangay:',
      lengthMenu: 'Show _MENU_ entries',
    },
  });
});

const indicator = window.location.hash.substring(1);

const params = new URLSearchParams(location.search);
const governance = params.get('expand');

console.log(indicator + governance);
if (indicator && governance) {
  console.log('Yes');

  // expand the govenance
  $(governance).collapse('show');

  // focus on the indicator
  let targetElement = document.getElementById(indicator);
  if (targetElement) {
    setTimeout(function() {
      targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' }); // Scroll into view
    }, 100);
  }
}
