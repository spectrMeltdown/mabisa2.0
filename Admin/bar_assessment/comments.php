<?php
require_once __DIR__ . '/../../api/audit_log.php';


class Comments
{
    private $pdo;
    private $auditLog;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->auditLog = new Audit_log($pdo); 
    }

    public function add_comment(
        string $file_id,
        string $commentName,
        string $commentText
    ): array {
        try {
            $this->pdo->beginTransaction();

            $sql = "SELECT comments FROM barangay_assessment_files WHERE file_id = :file_id FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Barangay not found', 'file_id' => $file_id, 'name' => $commentName];
            }

            $comments = json_decode($row['comments'], true) ?: [];
            if (!is_array($comments)) {
                $comments = [];
            }

            $comments[] = [
                'name' => $commentName,
                'comment' => $commentText,
                'timestamp' => time()
            ];

            $updatedComments = json_encode($comments, JSON_PRETTY_PRINT);
            $updateSql = "UPDATE barangay_assessment_files SET comments = :comments WHERE file_id = :file_id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindParam(':comments', $updatedComments, PDO::PARAM_STR);
            $updateStmt->bindParam(':file_id', $file_id, PDO::PARAM_STR);

            if ($updateStmt->execute()) {
                $this->pdo->commit();

                $action = "Added a comment to file ID: $file_id";
                $this->auditLog->userLog($action);

                echo "<script>
                    alert('Comment Added');
                    window.location.href = document.referrer;
                </script>";
                exit;
            } else {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Failed to update comments'];
            }
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function show_comments(string $file_id): array
    {
        try {
            $sql = "SELECT comments FROM barangay_assessment_files WHERE file_id = :file_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['comments'])) {
                $comments = json_decode($row['comments'], true);
                if (is_array($comments)) {
                    return $comments;
                }
            }
            return [];
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return [];
        }
    }

    public function show_all_comments(string $barangay_id): array {
        try {
            $sql = "SELECT file_id, comments FROM barangay_assessment_files WHERE barangay_id = :barangay_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':barangay_id', $barangay_id, PDO::PARAM_STR);
            $stmt->execute();
            
            $allComments = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['comments'])) {
                    $comments = json_decode($row['comments'], true);
                    if (is_array($comments)) {
                        foreach ($comments as $comment) {
                            $comment['file_id'] = $row['file_id'];
                            $allComments[] = $comment;
                        }
                    }
                }
            }
            return $allComments;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return [];
        }
    }
}
