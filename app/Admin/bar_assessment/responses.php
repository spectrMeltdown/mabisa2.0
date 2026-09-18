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
        $sql = "SELECT 
                    refbarangay.brgyname AS barangay, 
                    barangay_assessment.barangay_id, 
                    barangay_assessment.last_modified AS last_modified
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

    public function getData($barangay_id, $criteria_keyctr)
    {
        $query = "SELECT * FROM barangay_assessment_files
                  WHERE barangay_id = :barangay_id 
                  AND criteria_keyctr = :criteria_keyctr
                  LIMIT 1";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':barangay_id' => $barangay_id,
            ':criteria_keyctr' => $criteria_keyctr           
        ]);
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
      
        return $result;
    }
    
    public function getProgress($barangay_id) {
        try {
            $query1 = "SELECT COUNT(*) as totalRequirements FROM maintenance_criteria_setup";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    
            $totalRequirements = (int)$result1['totalRequirements'];
    
            if ($totalRequirements === 0) {
                return 0;
            }
    
            $query2 = "SELECT COUNT(*) as barangayCount FROM barangay_assessment_files WHERE barangay_id = :barangay_id";
            $stmt2 = $this->pdo->prepare($query2);
            $stmt2->bindParam(':barangay_id', $barangay_id, PDO::PARAM_INT);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
            $barangayCount = (int)$result2['barangayCount'];
    
            $progress = ($barangayCount / $totalRequirements) * 100;
    
            return round($progress, 2); 
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function passedOrFail($barangay_id) {
        try {
            $query1 = "SELECT COUNT(*) as totalRequirements FROM maintenance_criteria_setup";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    
            $totalRequirements = (int)$result1['totalRequirements'];
    
            if ($totalRequirements === 0) {
                return 0;
            }
    
            $query2 = "SELECT COUNT(*) as barangayCount FROM barangay_assessment_files WHERE barangay_id = :barangay_id AND status = 'approved'";
            $stmt2 = $this->pdo->prepare($query2);
            $stmt2->bindParam(':barangay_id', $barangay_id, PDO::PARAM_INT);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
            $barangayCount = (int)$result2['barangayCount'];
    
            $progress = ($barangayCount / $totalRequirements) * 100;
    
            return round($progress, 2); 
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function getResponseCount($barangay_id) {
        try {
            $query1 = "SELECT COUNT(*) as totalRequirements FROM maintenance_criteria_setup";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            $totalRequirements = (int)$result1['totalRequirements'];
            
            if ($totalRequirements === 0) {
                return "0/0";
            }
            
            $query2 = "SELECT COUNT(*) as barangayCount FROM barangay_assessment_files WHERE barangay_id = :barangay_id";
            $stmt2 = $this->pdo->prepare($query2);
            $stmt2->bindParam(':barangay_id', $barangay_id, PDO::PARAM_INT);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $barangayCount = (int)$result2['barangayCount'];
            
            return "$barangayCount/$totalRequirements";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getApprovedCount($barangay_id) {
        try {
            $query1 = "SELECT COUNT(*) as totalRequirements FROM maintenance_criteria_setup";
            $stmt1 = $this->pdo->prepare($query1);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            $totalRequirements = (int)$result1['totalRequirements'];
            
            if ($totalRequirements === 0) {
                return "0/0";
            }
            
            $query2 = "SELECT COUNT(*) as barangayCount FROM barangay_assessment_files WHERE barangay_id = :barangay_id AND status = 'approved'";
            $stmt2 = $this->pdo->prepare($query2);
            $stmt2->bindParam(':barangay_id', $barangay_id, PDO::PARAM_INT);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $barangayCount = (int)$result2['barangayCount'];
            
            return "$barangayCount/$totalRequirements";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
