
<?php
class Admin_Actions
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function approve(string $file_id): bool
    {
        $sql = "
        UPDATE barangay_assessment_files
        SET status = 'approved'
        WHERE file_id = :file_id;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':file_id', $file_id);
        return $stmt->execute();
    }

    public function decline(string $file_id): bool
    {
        $sql = "
        UPDATE barangay_assessment_files
        SET status = 'declined'
        WHERE file_id = :file_id;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':file_id', $file_id);
        return $stmt->execute();
    }

    public function viewFile($file_id)
    {
        $query = "SELECT file_path FROM barangay_assessment_files WHERE file_id = :file_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':file_id' => $file_id]);
    
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($file && file_exists($file['file_path'])) {
            return $file['file_path']; // Return file path instead of binary data
        } else {
            throw new Exception("File not found.");
        }
    }
    

    
}
