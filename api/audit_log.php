<?php

class Audit_log
{
    private $pdo;
    private $userId;
    private $username;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        
        if (isset($_COOKIE['id'])) {
            $this->userId = $_COOKIE['id'];
            
            $sql = "SELECT username FROM users WHERE id = :id";
            $query = $this->pdo->prepare($sql);
            $query->execute(['id' => $this->userId]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $this->username = $user['username'];
            } else {
                throw new Exception("User not found.");
            }
        } else {
            throw new Exception("User ID not found in cookies.");
        }
    }

    public function userLog($action)
    {
        try {
            $query = "INSERT INTO audit_log 
                    (user_id, username, action, time_and_date) 
                    VALUES 
                    (:user_id, :username, :action, NOW())";
            $stmt = $this->pdo->prepare($query);

            $stmt->execute([
                ':user_id' => $this->userId,
                ':username' => $this->username,
                ':action' => $action,
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}