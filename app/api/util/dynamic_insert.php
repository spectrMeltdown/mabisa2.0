<?php
function dynamicInsertStmt(string $tableName, array $values): string
{
  $sql = 'insert into permissions(';
  foreach ($values as $value) {
    writeLog($value);
    $sql .= ' ' . $value . ',';
  }
  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ') values(';
  foreach ($values as $value) {
    $sql .= ' :' . $value . ',';
  }
  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ');';

  // remove all newlines (if any)
  return str_replace('\n', '', $sql);
}
