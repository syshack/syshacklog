<?php
$host = "127.0.0.1";
$community = "public"; 
$status = snmp2_real_walk($host,$community,"");
print_r($status);
?>
