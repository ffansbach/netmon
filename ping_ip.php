<?php
/** Include Classes **/
require_once('runtime.php');

if ($_GET['ipv']=='6') {
	$ipv6_address = explode("/", $_GET['ip']);
	$command = "ping6 -c 4 -I $GLOBALS[netmon_ipv6_interface] $ipv6_address[0]";
} elseif ($_GET['ipv']=='4') {
	$command = "ping -c 4 $_GET[ip]";
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