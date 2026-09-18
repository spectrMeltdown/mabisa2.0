<?php
function getPermTableNames($pdo, $strMatch = null, $includeID = false, $includeLastModified = false): array
{
  // get the permissions table details
  $query = $pdo->query("describe permissions;");

  // assign to columsn array 
  $columns = $query->fetchAll(PDO::FETCH_ASSOC);

  // convert to permissions array
  $permissions = [];
  foreach ($columns as $col) {
    $permissions[] = $col['Field'];
  }

  // remove id & last_modified
  if (!$includeID) $permissions = array_filter($permissions, fn($perm) => strtolower($perm) != 'id');
  if (!$includeLastModified) $permissions = array_filter($permissions, fn($perm) => strtolower($perm) != 'last_modified');

  // if specified, filter by a match 
  if ($strMatch != null) {
    // note: id and last_modified are already removed below
    foreach ($permissions as $pk => $pv) {
      if (!str_contains($pv, $strMatch)) {
        unset($permissions[$pk]);
      }
    }
  }
  return $permissions;
}
