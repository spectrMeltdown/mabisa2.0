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
require_once $pathPrepend . 'api/get_user.php'; // this will provide userData array
// unset after using
unset($customUserID);
global $userData;
if (isset($userData['error'])) {
  setcookie('id', '', time() - 3600, '/');
  require_once 'account_error.php';
  exit;
}

// get the general permissions
function getPermissions($isBarPerms = false): array | string
{
  // auto-skip if super admin
  global $userData;
  // writeLog($userData);
  if (strtolower($userData['role']) == 'super admin') return 'all';
  $sql = '';
  if ($isBarPerms) {
    // obtains all barangays with indicators & permissions that match the user_id
    $sql = 'SELECT urb.barangay_id, urb.indicator_id, p.* from permissions p 
   INNER JOIN user_roles_barangay urb on urb.permission_id = p.id
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
  $permissions = [];
  if ($isBarPerms) {
    $permissions = $query->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $permissions = $query->fetch(PDO::FETCH_ASSOC);
  }
  if ($permissions == false) return [];
  // writeLog('bar perms was: ');
  // writeLog($isBarPerms);
  // writeLog('Permissions is: ');
  // writeLog($permissions);

  if ($isBarPerms) {
    // note: modifying original permissions array
    foreach ($permissions as &$entry) {
      // backup ids
      $barID = $entry['barangay_id'] ?? '';
      $indID = $entry['indicator_id'] ?? '';

      // get the permissions that have their value set to 1 (true)
      $entry = array_keys(array_filter($entry, function ($value) {
        return $value == 1;
      }));

      // add back to array
      $entry['bid'] = $barID;
      $entry['iid'] = $indID;
    }
  } else {
    // get the permissions that have their value set to 1 (true)
    $permissions = array_keys(array_filter($permissions, function ($value) {
      return $value == 1;
    }));
  }
  return $permissions; //return only column names if isBarPerms is false.
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
function userHasPerms(string | array  $perms, string $permType, string $barID = null, string $indicatorID = null): bool
{
  // some parameter checks
  if (!empty($indicatorID) && empty($barID)) throw new Exception('indicatorID provided, but no barID!!');
  if ($permType == 'gen' && (!empty($indicatorID) || !empty($barID))) throw new Exception('barID & indicatorID are for "bar" perms or "any" perms only!');

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
    // writeLog('perm type is ' . $permType);
    return checkPerms($userBarPerms, $perms, $barID, (string)$indicatorID, $barID, (string)$indicatorID);
  } else if ($permType == 'any') {
    // writeLog('perm type is ' . $permType);
    return checkPerms(array_merge($userGenPerms, $userBarPerms), $perms, $barID, (string)$indicatorID);
  }
  // throw if none of the above
  else throw new Exception('Invalid perm passed.');
}

// TODO: apply the barID and indicator id checks. Modify user bar perms to include the ids in each array entry.
function checkPerms($grantedPerms, $permsQuery, string $barID = null, string $indicatorID = null)
{
  if (count(array_filter($grantedPerms, 'is_array')) > 0) {
    writeLog('granted perms has array');
    // if array, loop and check against each item
    if (is_array($permsQuery)) {
      writeLog('permsQuery was array');
      // writeLog('granted perms are: ');
      // writeLog($grantedPerms);
      $result = [];
      foreach ($permsQuery as $perm) {
        // if the a granted perm matches a query perm, then add to result
        $result = array_filter($grantedPerms, function ($grantedP) use ($perm) {
          writeLog('granted in array filter: ');
          writeLog($grantedP);
          writeLog('query in array filter: ');
          writeLog($perm);
          // writeLog('');
          // writeLog(str_contains($genPerm, $perm));
          return str_contains((string)$grantedP, $perm);
        });
        // if they are equal, means that all specified perms are granted
      }
      // writeLog('perms count was: ' . count($permsQuery));
      // 1writeLog($permsQuery);
      // writeLog('result count was: ' . count($result));
      // writeLog($result);
      // writeLog('');
      return count($permsQuery) == count($result);
    }
    // if string, loop only through the permissions array
    else if (is_string($permsQuery)) {
      writeLog('permsQuery was string');
      // writeLog('granted perms are: ');
      // writeLog($grantedPerms);
      if (!empty($barID) && !empty($indicatorID)) {
        writeLog('barID & indicatorID');
        $result = array_filter($grantedPerms, function ($entry) use ($permsQuery, $barID, $indicatorID) {
          // writeLog('granted: ');
          // writeLog($entry);
          // writeLog('permsQuery: ');
          // writeLog($permsQuery);
          // writeLog('');
          // writeLog(str_contains($genPerm, $permsQuery));
          if (is_array($entry)) {
            // get the current entry barID and indicatorID
            $entryBarID = $entry['bid'];
            $entryIndID = $entry['iid'];
            // filter the array in the array 
            return !empty(array_filter($entry, function ($item) use ($permsQuery, $barID, $indicatorID, $entryBarID, $entryIndID) {
              // writeLog('entryBarID');
              // writeLog($entryBarID . ' ' . $barID);
              // writeLog('entryIndID');
              // writeLog($entryIndID . ' ' . $indicatorID);
              return str_contains((string)$item, $permsQuery) && $barID == $entryBarID && $indicatorID == $entryIndID;
            }));
          } else {
            return str_contains((string)$entry, $permsQuery);
          }
        });
        return count($result) > 0;
      } else if (!empty($barID)) {
        writeLog('barID only');
        $result = array_filter($grantedPerms, function ($entry) use ($permsQuery, $barID) {
          // writeLog('granted: ');
          // writeLog($entry);
          // writeLog('permsQuery: ');
          // writeLog($permsQuery);
          // writeLog('');
          // writeLog(str_contains($genPerm, $permsQuery));
          if (is_array($entry)) {
            writeLog('is array');
            // filter the array in the array 
            $entryBarID = $entry['bid'];
            return !empty(array_filter($entry, function ($item) use ($permsQuery, $barID, $entryBarID) {
              writeLog('Bar id is: ' . $barID);
              writeLog('Entry id is: ' . $entryBarID);
              return str_contains((string)$item, $permsQuery) && $barID == $entryBarID;
            }));
          } else {
            writeLog('is string');
            return str_contains((string)$entry, $permsQuery);
          }
        });
        writeLog('result is: ');
        writeLog(count($result) > 0);
        return count($result) > 0;
      } else {
        writeLog('no ids');
        $result = array_filter($grantedPerms, function ($entry) use ($permsQuery) {
          // writeLog('granted: ');
          // writeLog($entry);
          // writeLog('permsQuery: ');
          // writeLog($permsQuery);
          // writeLog('');
          // writeLog(str_contains($genPerm, $permsQuery));
          if (is_array($entry)) {
            writeLog('entry is array');
            return !empty(array_filter($entry, function ($item) use ($permsQuery) {
              // writeLog('entryBarID');
              // writeLog($item);
              return str_contains((string)$item, $permsQuery);
            }));
          } else {
            writeLog('entry is string');
            return str_contains((string)$entry, $permsQuery);
          }
        });
        // if there is a match, return true
        return !empty($result);
      }
    }
    // throw if neither
    else {
      throw new Exception('permsQuery was not a string or array.');
    }
  }
  // IF NOT BAR PERMS
  else {
    // writeLog(gettype($permsQuery));
    // if array, loop and check against each item
    if (is_array($permsQuery)) {
      // writeLog('perms was array');
      // writeLog('granted perms are: ');
      // writeLog($grantedPerms);
      $result = [];
      foreach ($permsQuery as $perm) {
        // if the a granted perm matches a query perm, then add to result
        $result = array_filter($grantedPerms, function ($grantedP) use ($perm) {
          // writeLog('granted: ');
          // writeLog($grantedP);
          // writeLog('query: ');
          // writeLog($perm);
          // writeLog('');
          // writeLog(str_contains($genPerm, $perm));
          return str_contains((string)$grantedP, $perm);
        });
        // if they are equal, means that all specified perms are granted
      }
      writeLog('perms count was: ' . count($permsQuery));
      // writeLog($permsQuery);
      writeLog('result count was: ' . count($result));
      // writeLog($result);
      // writeLog('');
      return count($permsQuery) == count($result);
    }
    // if string, loop only through the permissions array
    else if (is_string($permsQuery)) {
      // writeLog('perms was string');
      // writeLog('granted perms are: ');
      // writeLog($grantedPerms);
      $result = array_filter($grantedPerms, function ($grantedP) use ($permsQuery) {
        // writeLog('granted: ');
        // writeLog($grantedP);
        // writeLog('permsQuery: ');
        // writeLog($permsQuery);
        // writeLog('');
        // writeLog(str_contains($genPerm, $permsQuery));
        return str_contains((string)$grantedP, $permsQuery);
      });
      // if there is a match, return true
      return !empty($result);
    }
    // throw if neither
    else {
      throw new Exception('permsQuery was not a string or array.');
    }
  }
  throw new Exception('no processing happened!');
}
