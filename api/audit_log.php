<?php

class Audit_log
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function userLog($userId, $username, $action)
    {
        try {
            $query1 = "INSERT INTO audit_log 
                    (user_id, username, action, time_and_date) 
                    VALUES 
                    (:user_id, :username, :action, NOW())";
        $stmt1 = $this->pdo->prepare($query1);

        $stmt1->execute([
            ':user_id' => $userId,
            ':username' => $username,
            ':action' => $action,
           
        ]);
        return true;
        
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
