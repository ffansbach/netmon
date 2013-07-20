<?php
/** Include Classes **/
require_once('runtime.php');
require_once('lib/classes/core/config.class.php');
require_once('lib/classes/core/ip.class.php');

$ip = Ip_old::getIpById($_GET['ip_id']);

if ($ip['ipv']=='6') {
	$ipv6_address = explode("/", $ip['ip']);
	$address="[$ipv6_address[0]%".Config::getConfigValueByName("network_connection_ipv6_interface");
} elseif ($ip['ipv']=='4') {
	$address = $ip['ip'];
}

$return = array();
$command = "wget_return=`busybox wget -q -O - http://$address/node.data & sleep 10; kill $!`
echo \$wget_return";

exec($command, $return);
$return_string = "";
foreach($return as $string) {
	$return_string .= $string;
}

if(!empty($string)) {
	echo $string;
} else {
	echo $command;
}

?>