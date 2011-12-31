<?php
    $size = pow(2, 16);
     
    $startTime = microtime(true);
    $array = array();
    for ($key = 0, $maxKey = ($size - 1) * $size; $key <= $maxKey; $key += $size) {
        $array[$key] = 0;
    }
    $endTime = microtime(true);
?>
