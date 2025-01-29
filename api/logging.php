<?php
function writeLog($message, $file = 'API.log')
{
  $date = date('Y-m-d H:i:s');
  file_put_contents($file, "[$date]" . var_export($message, true) . PHP_EOL, FILE_APPEND);
}
