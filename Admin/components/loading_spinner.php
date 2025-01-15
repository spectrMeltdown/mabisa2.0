<?php
function createSpinner(float $size)
{
  echo '
    <img src="../img/loading.svg" width="' . (string)$size . '" height="' . (string)$size . '"/>
  ';
}
