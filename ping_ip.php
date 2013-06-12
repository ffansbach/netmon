<?php
/** Include Classes **/
require_once('runtime.php');
require_once(ROOT_DIR.'lib/classes/core/ip.class.php');
require_once(ROOT_DIR.'lib/classes/core/config.class.php');

$ip = Ip::getIpById($_GET['ip_id']);

if ($ip['ipv']=='6') {
	$ipv6_address = explode("/", $ip['ip']);
	$command = "ping6 -c 4 -I ".Config::getConfigValueByName("network_connection_ipv6_interface")." $ipv6_address[0]";
} elseif ($ip['ipv']=='4') {
	$command = "ping -c 4 $ip[ip]";
}

$return = array();
echo $command;
exec($command, $return);
foreach($return as $string) {
	$return_string .= $string;
}
echo "<pre>";
	print_r($return);
echo "</pre>";

?>