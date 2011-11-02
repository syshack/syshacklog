<?php
function get_server_info($host, $community, $objectid) { 
$status = snmpget($host, $community, $objectid); 
$tmp = explode(":", $status); 
if (count($tmp) > 1) { 
$status = trim($tmp[1]); 
} 
return $status; 
} 
$host="127.0.0.1"; 
$community="public"; 
//获取$host服务器的1分钟平均负载 
$load1 = get_server_info($host,$community,".1.3.6.1.4.1.2021.10.1.3.1");
$load5 = get_server_info($host,$community,".1.3.6.1.4.1.2021.10.1.3.2");
$load15 = get_server_info($host,$community,".1.3.6.1.4.1.2021.10.1.3.3");
print "1  minute Load:". $load1."\n";
print "5  minute Load:". $load5."\n";
print "15 minute Load:". $load15."\n";

?>
