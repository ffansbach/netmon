<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = false;

//WEBSERVER
$GLOBALS['subfolder'] = dirname($_SERVER['PHP_SELF']);
$GLOBALS['domain'] = $_SERVER["HTTP_HOST"];
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];
$GLOBALS['url_to_netmon'] = $GLOBALS['domain'].$GLOBALS['subfolder'];

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "netmon_trunk";
$GLOBALS['mysql_user'] = "username";
$GLOBALS['mysql_password'] = "password";

//JABBER
$GLOBALS['jabber_server'] = "server.tld";
$GLOBALS['jabber_username'] = "username";
$GLOBALS['jabber_password'] = "password";

//MAIL
$GLOBALS['mail_sending_type'] = "smtp";
$GLOBALS['mail_sender_adress'] = "emailadress";
$GLOBALS['mail_sender_name'] = "Freifunk Cityname";
$GLOBALS['mail_smtp_server'] = "smtp.server.tpd";
$GLOBALS['mail_smtp_username'] = "loginadress";
$GLOBALS['mail_smtp_password'] = "password";
$GLOBALS['mail_smtp_login_auth'] = "login";
$GLOBALS['mail_smtp_ssl'] = "";

//NETWORK
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['community_name'] = "Yourcomunityname";
$GLOBALS['community_website'] = "http://yourcommunitywebsite.tld";
$GLOBALS['networkPolicy'] = "http://www.picopeer.net/";

//VPN-Keys
$GLOBALS['expiration'] = 3650;

//PROJEKT
$GLOBALS['portal_history_hours'] = 5;
$GLOBALS['days_to_keep_portal_history'] = 1;
$GLOBALS['mysql_querry_log_time'] = 5;

//Image Generator
$GLOBALS['imggen_supported_chipsets'][] = "Atheros AR2317";

?>