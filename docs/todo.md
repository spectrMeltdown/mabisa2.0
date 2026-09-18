# Important

## Misc

- fix logout btn and profile pic not working in Admin sub folders (criteria, version, area, etc folders)

## Settings page

- add change password btn in settings, current password must be checked before doing so
- add forgot password in login page (needs email service)

## Users Page

- roles integration (per button)
- edit user
- delete user
  - get permissions id from barangay and general permissions
  - delete those first before deleting the user
- remove mobile number (sms not used by client)

## All Criteria Pages

- roles integration (per button)

## All non-display files

- add auth & permissions check

## Dashboard

- Audit logs
- display annoucements in navbar (just use polling method for real-time updates)
- add annoucements
- edit annoucements
- delete annoucements

## Database

- remove user_roles_barangay_id column from user_roles (currently using user_id directly for queries)

# Deployment to server

- change links that used the `__DIR__, $_SERVER['HTTP_HOST'], $_SERVER['DOCUMENT_ROOT']`. Adjust to the VPS's or domain's structure
- remove all logging including:
  - console.log
  - writeLog (also all require/includes for this)
  - echo (that were used for logging)
  - comments
  - JSON_PRETTY_PRINT (adds unnecessary newlines and spacing)
- minify JS & PHP files

# Code cleanup

- instead of redirecting to no_permissions.php, replace the current content with the one from no permissions. (e.g. display no permissions on users page if not allowed. URL still says users.php)
- modify database tables:
  - rename user_roles_barangay -> user_barangay_permissions
  - rename user_roles -> user_global_permissions
  - split permissions table to individual tables, set id to be a foreign key user_id.:
    - permissions_criteria
    - permissions_users
    - permissions_reports
    - permissions_maps
    - permissions_assessment
      - add the foreign key "user_barangay_permissions_id", make it ON DELETE CASCADE
- note that user access per buttons on the page are insecure, because although the buttons are not displayed, the javascript code for them still exist (POTENTIAL SECURITY ISSUE)
