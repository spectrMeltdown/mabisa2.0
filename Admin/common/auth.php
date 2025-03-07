<?php
// redirect if logged out, except on login page
if (empty($_COOKIE['id']) && !isset($isLoginPage)) {
  header('location:/mabisa/Admin/logged_out.php');
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
  header('Location:/mabisa/Admin/account_error.php');
  exit;
}

// get the general permissions
function getPermissions($isBarPerms = false): array | string
{
  // auto-skip if super admin
  global $userData;
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
/** @param perms can be a single perm or a array of perms. If array is used, the user must contain all those permissions to be true.
 *  @param permType must be 'gen' for global/general permissions,'bar' for barangay permissions 'any' for either of them.
 * @return bool return true if has all perms specified. False if otherwise.
 */
function userHasPerms(string $perms, string $permType, string|null $barID = null, string|null $indicatorID = null): bool
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

  // check if global/general perms
  if ($permType == 'gen') return checkPerms($userGenPerms, $perms);

  // check if barangay perms
  else if ($permType == 'bar') return checkPerms($userBarPerms, $perms, $barID, (string)$indicatorID);
  else if ($permType == 'any') return checkPerms(array_merge($userGenPerms, $userBarPerms), $perms, $barID, (string)$indicatorID);

  // throw if none of the above
  else throw new Exception('Invalid perm passed.');
}

function checkPerms(array $grantedPerms, string $permsQuery, string|null $barID = null, string|null $indicatorID = null)
{
  // if bar perms
  if (count(array_filter($grantedPerms, 'is_array')) > 0) {
    if (!empty($barID) && !empty($indicatorID)) {
      $result = array_filter($grantedPerms, function ($entry) use ($permsQuery, $barID, $indicatorID) {
        if (is_array($entry)) {
          // get the current entry barID and indicatorID
          $entryBarID = $entry['bid'];
          $entryIndID = $entry['iid'];
          // filter the array in the array 
          return !empty(array_filter($entry, function ($item) use ($permsQuery, $barID, $indicatorID, $entryBarID, $entryIndID) {
            return str_contains((string)$item, $permsQuery) && $barID == $entryBarID && $indicatorID == $entryIndID;
          }));
        } else {
          return str_contains((string)$entry, $permsQuery);
        }
      });
      return count($result) > 0;
    } else if (!empty($barID)) {
      $result = array_filter($grantedPerms, function ($entry) use ($permsQuery, $barID) {
        if (is_array($entry)) {
          // filter the array in the array 
          $entryBarID = $entry['bid'];
          return !empty(array_filter($entry, function ($item) use ($permsQuery, $barID, $entryBarID) {
            return str_contains((string)$item, $permsQuery) && $barID == $entryBarID;
          }));
        } else {
          return str_contains((string)$entry, $permsQuery);
        }
      });
      return count($result) > 0;
    } else {
      $result = array_filter($grantedPerms, function ($entry) use ($permsQuery) {
        if (is_array($entry)) {
          return !empty(array_filter($entry, function ($item) use ($permsQuery) {
            return str_contains((string)$item, $permsQuery);
          }));
        } else {
          return str_contains((string)$entry, $permsQuery);
        }
      });
      return !empty($result);
    }
  }
  // if global/gen perms
  else {
    $result = array_filter($grantedPerms, function ($grantedP) use ($permsQuery) {
      return str_contains((string)$grantedP, $permsQuery);
    });
    return !empty($result);
  }
  throw new Exception('no processing happened!');
}
