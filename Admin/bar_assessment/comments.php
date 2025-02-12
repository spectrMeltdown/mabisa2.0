<?php

class Comments
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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

            $updatedComments = json_encode($comments);
            $updateSql = "UPDATE barangay_assessment_files SET comments = :comments WHERE file_id = :file_id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindParam(':comments', $updatedComments, PDO::PARAM_STR);
            $updateStmt->bindParam(':file_id', $file_id, PDO::PARAM_STR);

            if ($updateStmt->execute()) {
                $this->pdo->commit();

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
}
