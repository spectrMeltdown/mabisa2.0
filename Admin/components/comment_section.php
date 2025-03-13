<?php
require_once '../../api/audit_log.php';
require_once '../common/auth.php';
require_once '../../db/db.php';

$log = new Audit_log($pdo);

?>

<style>
    .modal-xxl {
        max-width: 95vw;
    }

    .file-preview {
        height: 700px;
    }

    .comments-container {
        height: 500px;
        overflow-y: auto;
        display: flex;
        flex-direction: column-reverse;
    }
</style>

<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="commentModalLabel">File & Comments</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="border rounded p-3 bg-light file-preview">
                            <iframe id="fileDisplay" src="" class="w-100 h-100 border rounded"></iframe>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            Comments
                        </div>
                        <?php if (userHasPerms('comments_read', 'any')): ?>
                            <div class="border rounded p-3 bg-light mb-3 comments-container">
                                <div id="commentsContainer">
                                    <p class="text-muted">Loading comments...</p>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (userHasPerms('comments_create', 'any')): ?>
                            <form method="post" action="../bar_assessment/admin_actions/add_comment.php">
                                <input type="hidden" name="file_id">
                                <input type="hidden" name="name">
                                <input class="bid" type="hidden" name="bid">
                                <input class="iid" type="hidden" name="iid">
                                <input class="expand" type="hidden" name="expand">
                                <div class="form-group">
                                    <textarea class="form-control" id="commentText" name="commentText" rows="2"
                                        placeholder="Write your comment here..." required></textarea>
                                </div>

                            </form>
                            <div id="statusMessage" class="alert alert-info text-center" style="display: none;"></div>
                        <?php endif; ?>
                        <?php if (userHasPerms('submissions_approve', 'any')): ?>
                            <div id="approveForm" style="display: none;">
                                <form method="POST" action="admin_actions/change_status.php">
                                    <input class="bid" type="hidden" name="bid">
                                    <input class="iid" type="hidden" name="iid">
                                    <input class="expand" type="hidden" name="expand">
                                    <input type="hidden" name="file_id" id="approveFileId">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-block">Approve</button>
                                </form>
                            </div>

                            <div id="declineForm" style="display: none;">
                                <form method="POST" action="admin_actions/change_status.php">
                                    <input class="bid" type="hidden" name="bid">
                                    <input class="iid" type="hidden" name="iid">
                                    <input class="expand" type="hidden" name="expand">
                                    <input type="hidden" name="file_id" id="declineFileId">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" class="btn btn-danger btn-block">Return</button>
                                </form>
                            </div>

                            <div id="revertForm" style="display: none;">
                                <form method="POST" action="admin_actions/change_status.php">
                                    <input class="bid" type="hidden" name="bid">
                                    <input class="iid" type="hidden" name="iid">
                                    <input class="expand" type="hidden" name="expand">
                                    <input type="hidden" name="file_id" id="revertFileId">
                                    <input type="hidden" name="action" value="revert">
                                    <button type="submit" class="btn btn-warning btn-block">Revert to Pending</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const commentForm = document.querySelector('form[action*="add_comment.php"]');
        const approveForm = document.querySelector('#approveForm form');
        const declineForm = document.querySelector('#declineForm form');

        function submitWithComment(statusForm) {
            if (commentForm) {
                const commentText = commentForm.querySelector("#commentText").value.trim();
                if (commentText !== "") {
                    const formData = new FormData(commentForm);

                    fetch(commentForm.action, {
                            method: "POST",
                            body: formData,
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log("Comment submitted:", data);
                            statusForm.submit(); // submit the status after comment
                        })
                        .catch(error => console.error("Error submitting comment:", error));
                } else {
                    statusForm.submit(); // just change the status even without comment
                }
            } else {
                statusForm.submit();
            }
        }

        if (approveForm) {
            approveForm.addEventListener("submit", function(event) {
                event.preventDefault();
                submitWithComment(approveForm);
            });
        }

        if (declineForm) {
            declineForm.addEventListener("submit", function(event) {
                event.preventDefault();
                submitWithComment(declineForm);
            });
        }
    });
</script>