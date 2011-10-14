<?php
$hosts = array ("127.0.0.1" => "public");
function getStatus($host,$public)
{
$status = @snmp2_real_walk($host,$public,".1.3.6.1.4.1.2021");
$host_status['uptime_1min'] = (float)@substr($status['UCD-SNMP-MIB::laLoad.1'],9);
$host_status['uptime_5min'] = (float)@substr($status['UCD-SNMP-MIB::laLoad.2'],9);
$host_status['uptime_15min'] = (float)@substr($status['UCD-SNMP-MIB::laLoad.3'], 9);
$host_status['user_cpu'] = (int)@substr($status['UCD-SNMP-MIB::ssCpuUser.0'], 9);
$host_status['system_cpu'] = (int)@substr($status['UCD-SNMP-MIB::ssCpuSystem.0'], 9);
$host_status['idle_cpu'] = (int)@substr($status['UCD-SNMP-MIB::ssCpuIdle.0'], 9);
$host_status['total_swap'] = (int)@substr($status['UCD-SNMP-MIB::memTotalSwap.0'], 9);
$host_status['available_swap'] = (int)@substr($status['UCD-SNMP-MIB::memAvailSwap.0'], 9);
$host_status['total_ram'] = (int)@substr($status['UCD-SNMP-MIB::memTotalReal.0'], 9);
$host_status['used_ram'] = $host_status['total_ram'] - (int)@substr($status['UCD-SNMP-MIB::memAvailReal.0'], 9);
$host_status['cached_memory'] = (int)@substr($status['UCD-SNMP-MIB::memCached.0'], 9);
return $host_status;
}
header('Content-Type: text/plain; charset=UTF-8');
echo "<pre>\n";
foreach ($hosts as $host => $public){
$host_status = getStatus($host,$public);
echo "<hr>\n";
echo "主机：".$host;
echo "<hr>\n";
foreach ($host_status as $item => $stat)
	{
		echo "$item:$stat\n";	
	}
					}
echo "</pre>\n";
?>
