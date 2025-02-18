<?php

function updatePerms(\PDO $pdo, int|string $permID, array $newPerms, array $allPerms)
{
  // initialize statement
  $sql = 'update permissions set';
  writeLog('New perms is set to: ');
  writeLog($newPerms);

  // construct the statement
  foreach ($allPerms as $key) {
    // add an equals between name of column and new value
    $sql .= ' ' . $key . ' = ';
    // set all perms that are in newPerms to true, leave the rest false
    $sql .= ' ' . (in_array($key, $newPerms) ? 'true' : 'false') . ',';
  }

  // remove trailing comma
  $sql = rtrim($sql, ',');

  // add id placeholder
  $sql .= ' where id = :perms_id;';

  // remove all newlines (if any)
  str_replace('\n', '', $sql);

  writeLog('updatePerms');
  writeLog($sql);

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':perms_id' => $permID]);
}
