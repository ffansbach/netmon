<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = false;

//WEBSERVER
$GLOBALS['url_to_netmon'] = "http://url_to_your_netmon_installation";

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "your_mysql_db";
$GLOBALS['mysql_user'] = "your_mysql_user";
$GLOBALS['mysql_password'] = "your_mysql_password";

//JABBER
$GLOBALS['jabber_server'] = "your_jabber_server";
$GLOBALS['jabber_username'] = "your_jabber_username";
$GLOBALS['jabber_password'] = "your_jabber_pasword";

//TWITTER
$GLOBALS['twitter_consumer_key'] = "your_twitter_consumer_key";
$GLOBALS['twitter_consumer_secret'] = "your_twitter_consumer_secret";
$GLOBALS['twitter_username'] = "your_twitter_username";

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
$GLOBALS['days_to_keep_mysql_crawl_data'] = 1;

//GOOGLEMAPSAPIKEY
$GLOBALS['google_maps_api_key'] = 'ABQIAAAACRLdP-ifG9hOW_8o3tqVjBTYNVZxCa8VDwolhiZjPiQmiWfAkxS6Oz2etgffSmqk2L962Xvxv9msQw';

//CRAWLER
$GLOBALS['crawl_cycle'] = 10;
$GLOBALS['crawler_ping_timeout'] = 5;
$GLOBALS['crawler_curl_timeout'] = 5;

//TEMPLATE
$GLOBALS['template'] = 'freifunk_oldenburg';

?>