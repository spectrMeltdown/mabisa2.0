<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalFileId" name="file_id">
                <div class="mb-3 p-3 bg-light border rounded" id="commentsContainer">
                    <p>Loading comments...</p>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($role === 'Admin'): ?>
                    <form method="post" action="./bar_assessment/admin_actions/comments.php" class="mt-4">
                        <input type="hidden" name="file_id" value="<?= htmlspecialchars($file_id) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Your Comment</label>
                            <textarea class="form-control" id="commentText" name="commentText" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </div>
                    </form>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>