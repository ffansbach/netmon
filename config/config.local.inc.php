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
$GLOBALS['mail_smtp_server'] = "smtp.server.tld";
$GLOBALS['mail_smtp_username'] = "loginadress";
$GLOBALS['mail_smtp_password'] = "password";
$GLOBALS['mail_smtp_login_auth'] = "login";
$GLOBALS['mail_smtp_ssl'] = "";

//NETWORK
$GLOBALS['net_prefix'] = "yourprefix";
$GLOBALS['community_name'] = "Yourcomunityname";
$GLOBALS['community_website'] = "http://yourcommunitywebsite.tld";
$GLOBALS['networkPolicy'] = "http://www.picopeer.net/";

//VPNKEYS
$GLOBALS['expiration'] = 3650;

//PROJEKT
$GLOBALS['portal_history_hours'] = 5;
$GLOBALS['days_to_keep_portal_history'] = 1;
$GLOBALS['mysql_querry_log_time'] = 5;

//Image Generator
$GLOBALS['imggen_supported_chipsets'][] = "Fonera";
$GLOBALS['imggen_supported_chipsets'][] = "DIR300";

//Google Maps Api Key (Get one at http://code.google.com/apis/maps/signup.html)
$GLOBALS['google_maps_api_key'] = 'ABQIAAAACRLdP-ifG9hOW_8o3tqVjBT5NsVQw1hcITWIyu14Fuv7KbvrKhRmX1uacYtrW5R3jOkcGrF76Cjmdg';

//Crawler
$GLOBALS['crawler_ping_timeout'] = 5;
$GLOBALS['crawler_curl_timeout'] = 5;
$GLOBALS['crawl_cycle'] = 10;

?>