<?php

declare(strict_types=1);
require_once 'models/user.php';

class UserService
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Create a new user
  public function createUser(
    string $id,
    string $username,
    string $fullName,
    ?Barangay $barangay,
    string $email,
    int $mobileNum,
    string $password,
    bool $policyRead,
    UserRole $role,
    DateTime $createdAt
  ): string {
    $sql = "INSERT INTO users (id, username, full_name, barangay, email, mobile_num, password, policy_read, role, created_at) 
            VALUES (:id, :username, :fullName, :barangay, :email, :mobileNum, :password, :policyRead, :role, :createdAt)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':fullName', $fullName);
    $stmt->bindValue(':barangay', $barangay ? $barangay->value : null);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mobileNum', $mobileNum);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':policyRead', $policyRead, PDO::PARAM_BOOL);
    $stmt->bindParam(':role', $role->value);
    $stmt->bindParam(':createdAt', $createdAt->format('Y-m-d H:i:s')); // Format DateTime

    $stmt->execute();

    return $this->pdo->lastInsertId(); // Return the new user's ID
  }

  // Fetch a user by ID
  public function getUserById(string $id): ?User
  {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      $barangay = $data['barangay'] ? Barangay::from($data['barangay']) : null;
      $role = UserRole::from($data['role']);
      $createdAt = new DateTime($data['created_at']);
      return new User(
        id: $data['id'],
        username: $data['username'],
        fullName: $data['full_name'],
        barangay: $barangay,
        email: $data['email'],
        mobileNum: (int)$data['mobile_num'],
        password: $data['password'],
        policyRead: (bool)$data['policy_read'],
        role: $role,
        createdAt: $createdAt
      );
    }

    return null;
  }

  // Fetch all users
  public function getAllUsers(): array
  {
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $this->pdo->query($sql);

    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $barangay = $row['barangay'] ? Barangay::from($row['barangay']) : null;
      $role = UserRole::from($row['role']);
      $createdAt = new DateTime($row['created_at']);
      $users[] = new User(
        id: $row['id'],
        username: $row['username'],
        fullName: $row['full_name'],
        barangay: $barangay,
        email: $row['email'],
        mobileNum: (int)$row['mobile_num'],
        password: $row['password'],
        policyRead: (bool)$row['policy_read'],
        role: $role,
        createdAt: $createdAt
      );
    }

    return $users;
  }

  // Update a user
  public function updateUser(
    string $id,
    string $username,
    string $fullName,
    ?Barangay $barangay,
    string $email,
    int $mobileNum,
    string $password,
    bool $policyRead,
    UserRole $role,
    DateTime $createdAt
  ): bool {
    $sql = "UPDATE users SET 
                username = :username, 
                full_name = :fullName, 
                barangay = :barangay, 
                email = :email, 
                mobile_num = :mobileNum, 
                password = :password, 
                policy_read = :policyRead, 
                role = :role, 
                created_at = :createdAt 
            WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':fullName', $fullName);
    $stmt->bindValue(':barangay', $barangay ? $barangay->value : null);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mobileNum', $mobileNum);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':policyRead', $policyRead, PDO::PARAM_BOOL);
    $stmt->bindParam(':role', $role->value);
    $stmt->bindParam(':createdAt', $createdAt->format('Y-m-d H:i:s'));

    return $stmt->execute();
  }

  // Delete a user
  public function deleteUser(string $id): bool
  {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }
}