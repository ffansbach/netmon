<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = true;

//WEBSERVER
$GLOBALS['subfolder'] = dirname($_SERVER['PHP_SELF']);
$GLOBALS['domain'] = "netmon.freifunk-ol.de";
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];
$GLOBALS['url_to_netmon'] = "netmon.freifunk-ol.de";

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "freifunksql5";
$GLOBALS['mysql_user'] = "freifunksql5";
$GLOBALS['mysql_password'] = "bla";

//JABBER
$GLOBALS['jabber_server'] = "jabber.nord-west.net";
$GLOBALS['jabber_username'] = "netmon";
$GLOBALS['jabber_password'] = "bla";

//MAIL
$GLOBALS['mail_sending_type'] = "php_mail";
$GLOBALS['mail_sender_adress'] = "netmon@freifunk-ol.de";
$GLOBALS['mail_sender_name'] = "Freifunk Oldenburg";
$GLOBALS['mail_smtp_server'] = "";
$GLOBALS['mail_smtp_username'] = "";
$GLOBALS['mail_smtp_password'] = "";
$GLOBALS['mail_smtp_login_auth'] = "";
$GLOBALS['mail_smtp_ssl'] = "";

//NETWORK
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['community_name'] = "Freifunk Oldenburg";
$GLOBALS['community_website'] = "http://freifunk-ol.de";
$GLOBALS['networkPolicy'] = "http://wiki.freifunk-ol.de/index.php?title=Nutzungsvereinbarung";

//VPNKEYS
$GLOBALS['expiration'] = 3650;

//PROJEKT
$GLOBALS['portal_history_hours'] = 5;
$GLOBALS['days_to_keep_portal_history'] = 1;
$GLOBALS['days_to_keep_mysql_crawl_data'] = 14;
$GLOBALS['mysql_querry_log_time'] = 5;

//Image Generator
$GLOBALS['imggen_supported_chipsets'][] = "Fonera";
$GLOBALS['imggen_supported_chipsets'][] = "DIR300";

//Google Maps Api Key (Get one at http://code.google.com/apis/maps/signup.html)
$GLOBALS['google_maps_api_key'] = 'ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ';

//Crawler
$GLOBALS['crawler_ping_timeout'] = 5;
$GLOBALS['crawler_curl_timeout'] = 5;
$GLOBALS['crawl_cycle'] = 10;

?>