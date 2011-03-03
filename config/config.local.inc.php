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
$GLOBALS['mail_sending_type'] = "your_mail_sending_type"; //Can be "smtp" or "php_mail"
$GLOBALS['mail_sender_adress'] = "your_sender_email";
$GLOBALS['mail_sender_name'] = "name_of_your_community";
$GLOBALS['mail_smtp_server'] = "your_smtp_server";
$GLOBALS['mail_smtp_username'] = "your_smtp_username";
$GLOBALS['mail_smtp_password'] = "your_smtp_password";
$GLOBALS['mail_smtp_login_auth'] = "";
$GLOBALS['mail_smtp_ssl'] = "";

//NETWORK
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['community_name'] = "name_of_your_community";
$GLOBALS['community_website'] = "http://url_to_your_community_website";
$GLOBALS['enable_network_policy'] = true;
$GLOBALS['networkPolicy'] = "http://url_to_your_network_policy";

//VPNKEYS
$GLOBALS['expiration'] = 3650;

//PROJEKT
$GLOBALS['days_to_keep_mysql_crawl_data'] = 30;

//GOOGLEMAPSAPIKEY
$GLOBALS['google_maps_api_key'] = "your_google_maps_api_key";

//CRAWLER
$GLOBALS['crawl_cycle'] = 10;
$GLOBALS['crawler_ping_timeout'] = 5;
$GLOBALS['crawler_curl_timeout'] = 5;

?>
