<?php

function dynamicUpdateStmt(string $tableName, array $cols = null, array $vals, string $where): string
{
  // intial sql statement and table name
  $sql = 'update ' . $tableName . ' set';

  // construct set substr. If cols was set, use that. Use keys of setVal otherwise
  if ($cols != null) {
    foreach ($cols as $col) {
      foreach ($vals as $val) {
        writeLog($col);
        writeLog($val);
        $sql .= ' ' . $col . '='  . $val . ',';
      }
    }
  } else {
    writeLog('cols was null');
    foreach ($vals as $k => $v) {
      writeLog($k);
      writeLog($v);
      $sql .= ' ' . $k . '='  . $v . ',';
    }
  }

  // remove trailing comma
  $sql = rtrim($sql, ',');

  // add where condition
  $sql .= ' where' . $where . ';';

  // remove all newlines (if any) and return 
  return str_replace('\n', '', $sql);
}
