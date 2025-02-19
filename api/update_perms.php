<?php

function updatePerms(\PDO $pdo, int|string|null $permID, array $newPerms, array $allPerms, bool $createPermIfNone = false): string|null
{
  // initialize statement
  $sql = 'update permissions set';
  writeLog('New perms is set to: ');
  writeLog($newPerms);

  $execute = [];
  if ($createPermIfNone && $permID == null) {
    $sql = 'insert into permissions (';
    // construct the statement
    foreach ($newPerms as $perm) {
      // add an equals between name of column and new value
      $sql .= ' ' . $perm . ' ,';
    }
    // remove trailing comma
    $sql = rtrim($sql, ',');
    // add continuation
    $sql .= ') values (';

    // add perms & construct execute
    foreach ($newPerms as $perm) {
      $colonPerm = ':' . $perm;
      // set all perms that are in newPerms to true, leave the rest false
      $sql .= ' ' . $colonPerm . ',';
      // add to execute
      $execute[$colonPerm] = 'true';
    }

    // remove trailing comma
    $sql = rtrim($sql, ',');
    // add continuation
    $sql .= ');';
  } else if (!empty($permID)) {
    // throw new Exception('PermID was null!!');
    return null;
  } else {
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
    $execute = [':perms_id' => $permID];
  }

  // remove all newlines (if any)
  str_replace('\n', '', $sql);

  writeLog('updatePerms');
  writeLog($sql);

  $stmt = $pdo->prepare($sql);
  $stmt->execute($execute);
  $lastID = $pdo->lastInsertId();
  if ($lastID != $permID) {
    return $lastID;
  }
  return null;
}
