<?php

class Responses
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function show_responses(): array
    {
        // Join barangay_assessment_files with refbarangay to fetch brgyname
        $sql = "SELECT 
                    refbarangay.brgyname AS barangay, 
                    barangay_assessment.barangay_id, 
                    barangay_assessment.last_modified AS last_modified,
                    barangay_assessment.status 
                FROM barangay_assessment
                INNER JOIN refbarangay 
                ON barangay_assessment.barangay_id = refbarangay.brgyid";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(); // Execute the query
    
        $responses = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $responses[] = [
                'barangay' => $row['barangay'] ?? null,
                'barangay_id' => $row['barangay_id'] ?? null,
                'dateUploaded' => $row['last_modified'] ?? null,
                'status' => $row['status'] ?? null,
            ];
        }
    
        return $responses;
    }

    public function getStatus($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code): bool
    {
        $query = "SELECT 1 FROM barangay_assessment_files
                  WHERE barangay_id = :barangay_id 
                  AND req_keyctr = :req_keyctr
                  AND desc_keyctr = :desc_ctr
                  AND indicator_code = :indicator_code 
                  AND reqs_code = :reqs_code 
                  LIMIT 1";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':barangay_id' => $barangay_id,
            ':req_keyctr' => $req_keyctr,
            ':desc_ctr' => $desc_ctr,
            ':indicator_code' => $indicator_code,
            ':reqs_code' => $reqs_code
        ]);
    
        
        return (bool) $stmt->fetch();
    }
    
    

    
    
}
