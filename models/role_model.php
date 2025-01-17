<?php
require_once '../api/logging.php';

enum UserRole: string
{
  case Admin = 'Admin';
  case Auditor = 'Auditor';
  case Secretary = 'Secretary';
}
