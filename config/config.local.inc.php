<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = true;

//WEBSERVER
$GLOBALS['subfolder'] = dirname($_SERVER['PHP_SELF']);
$GLOBALS['domain'] = $_SERVER["HTTP_HOST"];
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];
$GLOBALS['url_to_netmon'] = $GLOBALS['domain'].$GLOBALS['subfolder'];

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "netmon_trunk";
$GLOBALS['mysql_user'] = "root";
$GLOBALS['mysql_password'] = "hs7812kxlsqjog";

//JABBER
$GLOBALS['jabber_server'] = "unixboard.de";
$GLOBALS['jabber_username'] = "floh1111";
$GLOBALS['jabber_password'] = "hs7812kxlsqjog";

//MAIL
$GLOBALS['mail_sending_type'] = "smtp";
$GLOBALS['mail_sender_adress'] = "clemens-john@gmx.de";
$GLOBALS['mail_sender_name'] = "Freifunk Oldenburg Testsystem";
$GLOBALS['mail_smtp_server'] = "mail.gmx.net";
$GLOBALS['mail_smtp_username'] = "clemens-john@gmx.de";
$GLOBALS['mail_smtp_password'] = "hs7812kxlsqjog";
$GLOBALS['mail_smtp_login_auth'] = "login";
$GLOBALS['mail_smtp_ssl'] = "";

//NETWORK
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['city_name'] = "Oldenburg";
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
