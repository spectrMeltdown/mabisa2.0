'use strict';
let page = 0;
const notificationList = $('#notificationList');
const notifLoading = $('#loading');

// when attempting to show
$('#allNotifModal').on('show.bs.modal', async e => {
  // const id = $(e.relatedTarget).data('id');
  const title = $(e.relatedTarget).data('title');
  const message = $(e.relatedTarget).data('message');

  page = 0;
  notificationList.html(''); // Clear old notifications
  loadNotifications();
  notificationList.on('scroll', onScroll);

  if (title) $('#notifModalLabel').text(title);
  if (message) $('#notifModalMessage').text(message);
});

// refresh page when closed
$('#allNotifModal')
  .off('hidden.bs.modal')
  .on('hidden.bs.modal', () => {
    notificationList.off('scroll');
    location.reload();
  });

function loadNotifications() {
  if (notifLoading.is(':visible')) return; // Prevent duplicate calls
  notifLoading.show();

  let isInFolder = $('#isInFolder').length != 0;

  $.ajax({
    url: (isInFolder ? '../../' : '../') + 'api/get_all_notifs.php',
    method: 'GET',
    data: {
      page: page,
    },
    success: data => {
      console.log(data);
      if (!data || data.trim() === '') {
        // No more notifications
        notificationList.off('scroll');
      } else {
        data = JSON.parse(data);
        for (const entry of data) {
          // console.log(entry);
          notificationList.append(addNotifEntry(entry));
        }
        page++;
      }
      notifLoading.hide();
    },
    error: err => {
      console.error('Error fetching notifications');
      console.error(err.responseText);
      notifLoading.hide();
    },
  });
}

function addNotifEntry(data) {
  console.log(data);
  /*
  data.id
data.user_id
data.title
data.message
data.is_read
data.created_at
data.file_link
*/
  let link = '';
  if (data.file_link) {
    link = `<a href="${data.file_link}" class="card-link">Go to submission</a>`;
  }
  return `
  <a
          title="View"
          data-toggle="modal"
          data-target="#readNotifModal">
          <div class="card mb-2">
              <div class="card-body">
              <h5 class="card-title">${data.title}</h5>
                <h6 class="card-subtitle mt-2">${data.created_at}</h6>
                <p class="card-text">${data.message}</p>
                  ${link}
              </div>
            </div>
          </a>
  `;
}

function onScroll() {
  if (
    notificationList[0].scrollTop + notificationList[0].clientHeight >=
    notificationList[0].scrollHeight - 10
  ) {
    loadNotifications();
  }
}
