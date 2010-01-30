<?php

require_once('lib/classes/extern/jsonRPCClient.php');

$ip = "10.18.1.1";
$GLOBALS['netmon_url'] = "http://localhost/netmon_trunk/";

$api_router_config = new jsonRPCClient($GLOBALS['netmon_url']."api_router_config.php");

try {
	$ip_id = $api_router_config->getIpIdByIp($ip);
	$ip_data = $api_router_config->getIpDataByIpId($ip_id);
	$subnet_data = $api_router_config->getSubnetById($ip_data['subnet_id']);
	$user_data = $api_router_config->getPlublicUserInfoByID($ip_data['user_id']);
	$community_info = $api_router_config->getCommunityInfo();

} catch (Exception $e) {
	echo nl2br($e->getMessage());
}

echo "<pre>";
	print_r($ip_data);
	print_r($subnet_data);
	print_r($user_data);
	print_r($community_info);
echo "</pre>";

?>