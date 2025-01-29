<?php

function getRoleName(\PDO $pdo, int $roleID): string
{
  $sql = "SELECT * FROM roles WHERE id=:role_id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':role_id' => $roleID]);

  $role = $stmt->fetch(PDO::FETCH_ASSOC);
  return (isset($role) ? $role['name'] : '--');
}
