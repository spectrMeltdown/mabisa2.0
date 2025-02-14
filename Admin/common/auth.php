<?php

// redirect if logged out, except on login page
if (empty($_COOKIE['id']) && !isset($isLoginPage)) {
  header('location:logged_out.php');
  exit;
}

$pathPrepend = isset($isInFolder) ? '../../' : '../';

// get user data
$customUserID = 'self';
$userData;
require $pathPrepend . 'api/get_user.php'; // this will provide userData array
// unset after using
unset($customUserID);

// get the general permissions
function getPermissions($isBarPerms = false): array | string
{
  // auto-skip if super admin
  global $userData;
  // writeLog($userData);
  if (strtolower($userData['role']) == 'super admin') return 'all';
  $sql = '';
  if ($isBarPerms) {
    $sql = 'SELECT b.brgyid, p.* from permissions p 
   INNER JOIN user_roles_barangay urb on urb.permission_id = p.id
   INNER JOIN refbarangay b on b.brgyid = urb.barangay_id
   WHERE urb.user_id = :id;
   ';
  } else {
    $sql = 'SELECT * from permissions p 
   inner join user_roles ur on ur.permissions_id = p.id
   WHERE ur.user_id = :id;
   ';
  }
  global $pdo;
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $userData['id']]);
  $permissions = $query->fetch(PDO::FETCH_ASSOC);
  if ($permissions == false) return [];
  writeLog('bar perms was: ');
  writeLog($isBarPerms);
  writeLog('Permissions is: ');
  writeLog($permissions);

  // get the permissions that have their value set to 1 (true)
  $permissions = array_keys(array_filter($permissions, function ($value) {
    return $value == 1;
  }));
  return $permissions; //return only column names
}

// the global or general scope permissions
$userGenPerms = getPermissions();
// the barangay scope permissions
$userBarPerms = getPermissions(true);

// a function to check user perms. Used on almost all pages that import this file.
function userHasPerms(string | array  $perms, string $permType): bool
{
  /** @var array|string */
  global $userGenPerms;
  /** @var array|string */
  global $userBarPerms;

  // check if super admin 
  if ($userGenPerms == 'all' && $permType == 'gen') return true;
  if ($userBarPerms == 'all' && $permType == 'bar') return true;

  // check if global/general perms
  if ($permType == 'gen') return checkPerms($userGenPerms, $perms);
  // check if barangay perms
  else if ($permType == 'bar') return checkPerms($userBarPerms, $perms);
  // throw if none of the above
  else throw new Exception('Invalid perm passed.');
}

function checkPerms($grantedPerms, $permsQuery)
{
  // if array, loop and check against each item
  if (is_array($permsQuery)) {
    // writeLog('perms was array');
    foreach ($permsQuery as $perm) {
      $result = array_filter($grantedPerms, function ($genPerm) use ($perm) {
        // writeLog('gen perm matches?');
        // writeLog($genPerm);
        // writeLog($perm);
        // writeLog(str_contains($genPerm, $perm));
        return str_contains($genPerm, $perm);
      });
      // if there is a match, return true
      return !empty($result);
    }
  }
  // if string, loop only through the permissions array
  else if (is_string($permsQuery)) {
    // writeLog('perms was string');
    $result = array_filter($grantedPerms, function ($genPerm) use ($permsQuery) {
      // writeLog('gen perm matches (string)?');
      // writeLog($genPerm);
      // writeLog($permsQuery);
      // writeLog(str_contains($genPerm, $permsQuery));
      return str_contains($genPerm, $permsQuery);
    });
    // if there is a match, return true
    return !empty($result);
  }
  // throw if neither
  else {
    throw new Exception('Perms was not a string or array.');
  }
}
