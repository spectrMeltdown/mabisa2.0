<?php

class User_Actions
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function uploadFile($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code, $fileContent) {

        $query = "INSERT INTO barangay_assessment_files 
                  (barangay_id, req_keyctr, desc_keyctr, indicator_code, reqs_code, file, date_uploaded) 
                  VALUES 
                  (:barangay_id, :req_keyctr, :desc_ctr, :indicator_code, :reqs_code, :file, NOW())";
    

        $stmt = $this->pdo->prepare($query);
    
        $result = $stmt->execute([
            ':barangay_id' => $barangay_id,
            ':req_keyctr' => $req_keyctr,
            ':desc_ctr' => $desc_ctr,
            ':indicator_code' => $indicator_code,
            ':reqs_code' => $reqs_code,
            ':file' => $fileContent, 
        ]);
    
        return $result;
    }

    public function deleteFile($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code): bool
    {
        $query = "DELETE FROM barangay_assessment_files
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
            ':reqs_code' => $reqs_code
        ]);
    
        if ($stmt->rowCount() > 0) {
            echo "<script>
                alert('Deleted');
                window.location.href = document.referrer;
            </script>";
            exit;
        } else {
            throw new Exception("No matching record found.");
        }
    }
    
    
    
}
