
<?php
class Admin_Actions
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function approve(string $barangay_id): bool
    {
        $sql = "
        UPDATE barangay_assessment
        SET status = 'approved'
        WHERE barangay_id = :barangay_id;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':barangay_id', $barangay_id);
        return $stmt->execute();
    }

    public function decline(string $barangay_id): bool
    {
        $sql = "
        UPDATE barangay_assessment
        SET status = 'declined'
        WHERE barangay_id = :barangay_id;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':barangay_id', $barangay_id);
        return $stmt->execute();
    }

    public function viewFile($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code)
{
    $query = "SELECT file 
              FROM barangay_assessment_files 
              WHERE barangay_id = :barangay_id
              AND req_keyctr = :req_keyctr
              AND desc_keyctr = :desc_ctr
              AND indicator_code = :indicator_code
              AND reqs_code = :reqs_code";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
        ':barangay_id' => $barangay_id,
        ':req_keyctr' => $req_keyctr,
        ':desc_ctr' => $desc_ctr,
        ':indicator_code' => $indicator_code,
        ':reqs_code' => $reqs_code,
    ]);

    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $fileContent = $file['file'];

        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"file.pdf\"");
        echo $fileContent;
    } else {
        throw new Exception("File not found.");
    }
}

    
}
