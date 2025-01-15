<?php
require_once $pathPrepend . 'api/logging.php';

enum UserRole: int
{
  case Admin = 0;
  case Auditor = 1;
  case Secretary = 2;

  public function toString(): string
  {
    return match ($this) {
      self::Admin => 'Admin',
      self::Auditor => 'Auditor',
      self::Secretary => 'Secretary',
    };
  }
}

function getRole(int $data): ?UserRole
{
  foreach (UserRole::cases() as $role) {
    if ($data === $role->value) {
      return $role;
    }
  }
  return null;
}

function getAccessLevel(string $paramRole): int
{
  foreach (UserRole::cases() as $role) {
    writeLog($role->name);
    if ($paramRole === $role->name) {
      return $role->value;
    }
  }
  return null;
}
