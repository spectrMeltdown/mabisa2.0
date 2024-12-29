<?php
class User
{
  // public string $profilePicLink; // might add profile pics later
  public string $id; // auto-generated from uuid
  public string $username;
  public string $fullName;
  public ?Barangay $barangay; // if secretary, the barangay where they are assigned
  public string $email;
  public int $mobileNum; // should include country code
  public string $password;
  public bool $policyRead; // true if the user already read the policy. False if not.
  public DateTime $createdAt; // timestamp is preferred when frequently updating a field, in this case, we don't update this.
  public UserRole $role; // enum

  public function __construct(string $id, string $username, string $fullName, ?Barangay $barangay, string $email, int $mobileNum, string $password, bool $policyRead, UserRole $role, DateTime $createdAt)
  {
    $this->$id = $id;
    $this->$username = $username;
    $this->$fullName = $fullName;
    $this->$barangay = $barangay;
    $this->$email = $email;
    $this->$mobileNum = $mobileNum;
    $this->$password = $password;
    $this->$policyRead = $policyRead;
    $this->$createdAt = $createdAt;
    $this->$role = $role;
  }
}
