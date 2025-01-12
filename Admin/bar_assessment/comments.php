<?php


class Comments
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function add_comment(
        string $barangay_id,
        string $commentName,
        string $commentText
    ): array {
    
        try {
            $sql = "SELECT comments FROM barangay_assessment WHERE barangay_id = :barangay_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':barangay_id', $barangay_id, PDO::PARAM_STR);
            $stmt->execute();
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return ['success' => false, 'message' => 'Barangay not found'];
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
            $updateSql = "UPDATE barangay_assessment SET comments = :comments WHERE barangay_id = :barangay_id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindParam(':comments', $updatedComments, PDO::PARAM_STR);
            $updateStmt->bindParam(':barangay_id', $barangay_id, PDO::PARAM_STR);
    
            if ($updateStmt->execute()) {
                echo "<script>
                    
                    // Notify the user
                    alert('Comment Added');
    
                    // Reload the page
                    window.location.href = document.referrer;
    
                </script>";
                exit;
            } else {
                return ['success' => false, 'message' => 'Failed to update comments'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
      

    public function show_comments(string $barangay_id): array
{
    try {
        $sql = "SELECT comments FROM barangay_assessment WHERE barangay_id = :barangay_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':barangay_id', $barangay_id, PDO::PARAM_STR);
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
