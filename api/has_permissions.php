<?php


/**
 * @param pdo - database connection 
 * @param id - user id of account to check against
 * @param permissions - array of permissions to check against
 * @param isBarPerm - whether or not it is barangay scope permissions
 * @return bool - returns false if any permission in permissions is not granted
 */
function hasPermissions(\PDO $pdo, $id, array $permissions, bool $isBarPerm = false): bool
{
  // construct single string from all permissions
  $permissionString = '';
  foreach ($permissions as $permission) {
    $permissionString .= $permission . ', ';
  }
  // remove trailing comma and slash
  $permissionString = substr($permissionString, 0, -2);

  // construct sql statement
  $sql = 'SELECT ' . $permissionString . ' FROM permissions p ';
  if ($isBarPerm) {
    $sql .= 'INNER JOIN user_roles_barangay urb ON urb.permissions_id = p.id WHERE urb.user_id = :id';
  } else {
    $sql .= 'INNER JOIN user_roles ur ON ur.permissions_id = p.id WHERE ur.user_id = :id';
  }

  // prepare and execute
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $id]);
  $permissions = $query->fetchAll(PDO::FETCH_ASSOC);

  // select permissions that are granted (true or 1)
  $grantedPerms = array_filter($permissions, function ($value) {
    return $value == 1;
  });

  //
  $hasPermissions = false;
  foreach ($permissions as $permission) {
    if (in_array($permission, $grantedPerms)) {
      $hasPermissions = true;
    }
  }
  return $hasPermissions;
}
