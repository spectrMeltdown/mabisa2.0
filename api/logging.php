<?php
// TODO: remove this in live environment
function writeLog($message, $file = null)
{
  global $disableLogging;
  if (isset($disableLogging)) return;
  if ($file == null) $file = substr(__DIR__, 0, -3) . 'API.log';
  $date = date('Y-m-d H:i:s');
  file_put_contents($file, "[$date]" . var_export($message, true) . PHP_EOL, FILE_APPEND);
}
