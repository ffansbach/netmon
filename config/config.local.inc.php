<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = true;

//WEBSERVER
$GLOBALS['url_to_netmon'] = "http://netmon.freifunk-ol.de";

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "freifunksql5";
$GLOBALS['mysql_user'] = "freifunksql5";
$GLOBALS['mysql_password'] = "5fac80ba3895";

//JABBER
$GLOBALS['jabber_server'] = "jabber.nord-west.net";
$GLOBALS['jabber_username'] = "netmon";
$GLOBALS['jabber_password'] = "8832e25ac256";

//TWITTER
$GLOBALS['twitter_consumer_key'] = "UwDrOCWxNv6i29ulmTZgmA";
$GLOBALS['twitter_consumer_secret'] = "CD1AFt7pbc50HqJvplirI4YeTxrZMzoCJn3Kpgt0eBg";
$GLOBALS['twitter_username'] = "ff_ol";

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
$GLOBALS['enable_network_policy'] = true;
$GLOBALS['networkPolicy'] = "http://wiki.freifunk-ol.de/index.php?title=Nutzungsvereinbarung";

//VPNKEYS
$GLOBALS['expiration'] = 3650;

//PROJEKT
//$GLOBALS['days_to_keep_mysql_crawl_data'] = 1;
$GLOBALS['hours_to_keep_mysql_crawl_data'] = 5;
$GLOBALS['hours_to_keep_history_table'] = 48;

//GOOGLEMAPSAPIKEY
$GLOBALS['google_maps_api_key'] = 'ABQIAAAACRLdP-ifG9hOW_8o3tqVjBTYNVZxCa8VDwolhiZjPiQmiWfAkxS6Oz2etgffSmqk2L962Xvxv9msQw';

//CRAWLER
$GLOBALS['crawl_cycle'] = 10;
$GLOBALS['crawler_ping_timeout'] = 5;
$GLOBALS['crawler_curl_timeout'] = 5;

//TEMPLATE
$GLOBALS['template'] = 'freifunk_oldenburg';

//NETWORK_CONNECTION
$GLOBALS['netmon_is_connected_to_network_by_ipv6'] = true;
$GLOBALS['netmon_ipv6_interface'] = "batvpn";
$GLOBALS['netmon_is_connected_to_network_by_ipv4'] = true;

?>
