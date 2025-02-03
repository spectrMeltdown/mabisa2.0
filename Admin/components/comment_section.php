<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="commentModalLabel">Comments</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Hidden Inputs -->
                <input type="hidden" id="modalFileId" name="file_id">

                <!-- Comments Section -->
                <div id="commentsContainer" class="bg-light border rounded p-3 mb-4">
                    <h6 class="text-muted">Comments</h6>
                    <div id="commentsList">
                        <p class="text-muted">Loading comments...</p>
                    </div>
                </div>

                <!-- Add Comment Form -->
                <?php if ($role === 'Barangay Admin'): ?>
                    <form method="post" action="../bar_assessment/admin_actions/add_comment.php">
                        <input type="hidden" name="file_id">
                        <input type="hidden" name="name">
                        <div class="form-group">
                            <label for="commentText" class="font-weight-bold">Add a Comment</label>
                            <textarea class="form-control" id="commentText" name="commentText" rows="3" placeholder="Write your comment here..." required></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
