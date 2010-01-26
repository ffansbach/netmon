<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = false;

//WEBSERVER
//$GLOBALS['subfolder'] = "netmon_trunk";
$GLOBALS['subfolder'] = dirname($_SERVER['PHP_SELF']);
//$GLOBALS['domain'] = "localhost";
$GLOBALS['domain'] = $_SERVER["HTTP_HOST"];
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];
//$GLOBALS['url_to_netmon'] = $GLOBALS['domain']."/".$GLOBALS['subfolder'];
$GLOBALS['url_to_netmon'] = $GLOBALS['domain'];

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "freifunksql5";
$GLOBALS['mysql_user'] = "freifunksql5";
$GLOBALS['mysql_password'] = "blabla";

//JABBER
$GLOBALS['jabber_server'] = "jabber.nord-west.net";
$GLOBALS['jabber_username'] = "netmon";
$GLOBALS['jabber_password'] = "blabla";

//MAIL
$GLOBALS['mail_sender'] = "Freifunk Oldenburg Netmon <netmon@freifunk-ol.de>";

//FREIFUNKNETZ
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['city_name'] = "Oldenburg";

//Logsystem
//Log querries that take longer than 5 sec.
$GLOBALS['mysql_querry_log_time'] = 5;

//VPN-Keys
$GLOBALS['expiration'] = 3650;

//Project
$GLOBALS['networkPolicy'] = "http://www.picopeer.net/";
$GLOBALS['portal_history_hours'] = 5;
$GLOBALS['days_to_keep_portal_history'] = 1;

//Image Generator
$GLOBALS['imggen_supported_chipsets'][] = "Atheros AR2317";

?>
