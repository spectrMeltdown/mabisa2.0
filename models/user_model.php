<?php

declare(strict_types=1);
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
  // public string $createdAt; // timestamp is preferred when frequently updating a field, in this case, we don't update this.
  public string $role; // enum

  public function __construct(string $id, string $username, string $fullName, ?Barangay $barangay, string $email, int $mobileNum, string $password, bool $policyRead, string $role, /*string $createdAt*/)
  {
    $this->id = $id;
    $this->username = $username;
    $this->fullName = $fullName;
    $this->barangay = $barangay;
    $this->email = $email;
    $this->mobileNum = $mobileNum;
    $this->password = $password;
    $this->policyRead = $policyRead;
    // $this->createdAt = $createdAt;
    $this->role = $role;
  }

  // __toString method to define how to convert the object to a string
  // public function __toString(): string
  // {
  //   return "User [ID: {$this->id}, Username: {$this->username}, Full Name: {$this->fullName}, Email: {$this->email}]";
  // }
}