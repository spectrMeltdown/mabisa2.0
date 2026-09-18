<?php
function createSpinner(float $size)
{
  echo '
    <img src="../../../assets/img/loading.svg" width="' . (string)$size . '" height="' . (string)$size . '"/>
  ';
}

// createSpinner(69);