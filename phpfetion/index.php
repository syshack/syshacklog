<?php
require 'lib/PHPFetion.php';
echo "hello";
$fetion = new PHPFetion('fetionNum', 'fetionPWD');
$fetion->send('MSG_To', 'MSG_Content');
