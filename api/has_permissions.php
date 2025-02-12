<?php
// NOTE: Use this for api files (non-display) only. head.php already gets all permissions for checking

/**
 * @return bool - returns false if any permission in permissions is not granted
 * @param pdo - database connection 
 * @param id - user id of account to check against
 * @param permissions - array of permissions to check against
 * @param isBarPerm - whether or not it is barangay scope permissions
 * @param sampleOnly - to check only the first permission row. Applicable only if isBarPerm is true
 */
function hasPermissions(\PDO $pdo, $id, array $permissions, bool $isBarPerm = false, bool $sampleOnly = false): bool
{
  // TODO: maybe try doing dynamic queries next time, where you can get column names using wildcards and the like
  //   SET @sql = NULL;
  // SELECT GROUP_CONCAT(COLUMN_NAME) INTO @sql
  // FROM INFORMATION_SCHEMA.COLUMNS
  // WHERE TABLE_SCHEMA = 'your_database' AND TABLE_NAME = 'your_table' AND COLUMN_NAME LIKE 'user%';
  // SET @sql = CONCAT('SELECT ', @sql, ' FROM your_table;');
  // PREPARE stmt FROM @sql;
  // EXECUTE stmt;
  // DEALLOCATE PREPARE stmt;
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
    $sql .= 'INNER JOIN user_roles_barangay urb ON urb.permission_id = p.id WHERE urb.user_id = :id';
  } else {
    $sql .= 'INNER JOIN user_roles ur ON ur.permissions_id = p.id WHERE ur.user_id = :id';
  }

  // prepare and execute
  writeLog($sql);

  $query = $pdo->prepare($sql);
  $query->execute([':id' => $id]);
  $permissions = $query->fetchAll(PDO::FETCH_ASSOC);

  if ($isBarPerm && $sampleOnly) {
    $permissions = $permissions[0]; // get only one for sampling 
  }
  writeLog('perms');
  writeLog($permissions);

  // select permissions that are granted (true or 1) 
  // note that $permissions is an array of arrays
  // using array_values to convert associative array to indexed arrayed
  $grantedPerms = array_filter(array_values($permissions), function ($value) {
    writeLog('current value in array_filter');
    writeLog($value);
    return $value == 1;
  });
  writeLog('granted perms');
  writeLog($grantedPerms);

  // return false if permission is not in granted perms 
  $hasPermissions = false;
  foreach ($grantedPerms as $perm) {
    writeLog('current perms in loop');
    writeLog($perm);
    if (in_array($perm, $grantedPerms)) {
      $hasPermissions = true;
    } else {
      $hasPermissions = false;
    }
  }
  return $hasPermissions;
}
