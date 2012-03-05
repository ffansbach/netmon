<?php
/** Include Classes **/
require_once('runtime.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/ip.class.php');

$ip = Ip::getIpById($_GET['ip_id']);

if ($ip['ipv']=='6') {
	$ipv6_address = explode("/", $ip['ip']);
	$address="[$ipv6_address[0]%$GLOBALS[netmon_ipv6_interface]]";
} elseif ($ip['ipv']=='4') {
	$address = $ip['ip'];
}

$return = array();
$command = "wget_return=`busybox wget -q -O - http://$address/node.data & sleep 5; kill $!`
echo \$wget_return";

//echo $command;

exec($command, $return);
$return_string = "";
foreach($return as $string) {
	$return_string .= $string;
}

echo $string;

?>