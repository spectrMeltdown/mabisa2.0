<?php
function urlExists($url): bool
{
  if (empty($url)) return false;
  $headers = @get_headers($url);
  return $headers && strpos($headers[0], '200') !== false;
}
