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
  // writeLog('bar perms was: ');
  // writeLog($isBarPerms);
  // writeLog('Permissions is: ');
  // writeLog($permissions);

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
// TODO: update to accept barID, versionID, and indicatorID and check against that. For bar permissions
/** @param perms can be a single perm or a array of perms. If array is used, the user must contain all those permissions to be true.
 *  @param permType must be 'gen' for global/general permissions,'bar' for barangay permissions 'any' for either of them.
 * @return bool return true if has all perms specified. False if otherwise.
 */
function userHasPerms(string | array  $perms, string $permType): bool
{
  /** @var array|string */
  global $userGenPerms;
  /** @var array|string */
  global $userBarPerms;

  // check if super admin 
  if ($userGenPerms == 'all' && ($permType == 'gen' || $permType == 'any')) return true;
  if ($userBarPerms == 'all' && ($permType == 'bar' || $permType == 'any')) return true;
  // if ($userBarPerms == 'all' && $permType == 'any') return true;

  // check if global/general perms
  if ($permType == 'gen') {
    writeLog('perm type is ' . $permType);
    return checkPerms($userGenPerms, $perms);
  }
  // check if barangay perms
  else if ($permType == 'bar') {
    writeLog('perm type is ' . $permType);
    return checkPerms($userBarPerms, $perms);
  } else if ($permType == 'any') {
    writeLog('perm type is ' . $permType);
    return checkPerms(array_merge($userGenPerms, $userBarPerms), $perms);
  }
  // throw if none of the above
  else throw new Exception('Invalid perm passed.');
}

function checkPerms($grantedPerms, $permsQuery)
{
  // writeLog(gettype($permsQuery));
  // if array, loop and check against each item
  if (is_array($permsQuery)) {
    writeLog('perms was array');
    writeLog('granted perms are: ');
    writeLog($grantedPerms);
    $result = [];
    foreach ($permsQuery as $perm) {
      // if the a granted perm matches a query perm, then add to result
      $result = array_filter($grantedPerms, function ($grantedP) use ($perm) {
        writeLog('granted: ');
        writeLog($grantedP);
        writeLog('query: ');
        writeLog($perm);
        writeLog('');
        // writeLog(str_contains($genPerm, $perm));
        return str_contains($grantedP, $perm);
      });
      // if they are equal, means that all specified perms are granted
    }
    // writeLog('perms count was: ' . count($permsQuery));
    // writeLog($permsQuery);
    // writeLog('result count was: ' . count($result));
    // writeLog($result);
    // writeLog('');
    return count($permsQuery) == count($result);
  }
  // if string, loop only through the permissions array
  else if (is_string($permsQuery)) {
    writeLog('perms was string');
    writeLog('granted perms are: ');
    writeLog($grantedPerms);
    $result = array_filter($grantedPerms, function ($grantedP) use ($permsQuery) {
      writeLog('granted: ');
      writeLog($grantedP);
      writeLog('permsQuery: ');
      writeLog($permsQuery);
      writeLog('');
      // writeLog(str_contains($genPerm, $permsQuery));
      return str_contains($grantedP, $permsQuery);
    });
    // if there is a match, return true
    return !empty($result);
  }
  // throw if neither
  else {
    throw new Exception('permsQuery was not a string or array.');
  }
}
