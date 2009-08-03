<?php

//WEBSERVER
$GLOBALS['subfolder'] = "netmon";
$GLOBALS['domain'] = "freifunk-ol.de";
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];
$GLOBALS['url_to_netmon'] = $GLOBALS['domain']."/".$GLOBALS['subfolder'];

//MYSQL
$GLOBALS['mysql_host'] = "localhost";
$GLOBALS['mysql_db'] = "freifunksql5";
$GLOBALS['mysql_user'] = "freifunksql5";
$GLOBALS['mysql_password'] = "blabla";
$GLOBALS['mysql_enc'] = "utf8";

//JABBER
$GLOBALS['jabber_server'] = "unixboard.de";
$GLOBALS['jabber_username'] = "floh1111";
$GLOBALS['jabber_password'] = "blabla";

//MAIL
$GLOBALS['mail_sender'] = "Freifunk Oldenburg Netmon <netmon@freifunk-ol.de>";

//FREIFUNKNETZ
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['geographical_center_of_net_latitute'] = "8.213753";
$GLOBALS['geographical_center_of_net_longitude'] = "53.139926";
$GLOBALS['geographical_radius_of_net'] = "10";
$GLOBALS['city_name'] = "Oldenburg";

//PROJECTDATA
$GLOBALS['project_homepage'] = "http://www.freifunk-ol.de/";
$GLOBALS['project_name'] = "Freifunk Oldenburg";
$GLOBALS['project_essid'] = "oldenburg.freifunk.net";

//Logsystem
//Logge bei Querrys die länge brauchen als X sekunden
$GLOBALS['mysql_querry_log_time'] = 5;

//OpenVPN Config
$GLOBALS['countryName'] = "DE";
$GLOBALS['stateOrProvinceName'] = "NDS";
$GLOBALS['localityName'] = "Oldenburg";
$GLOBALS['organizationName'] = "Freifunk Oldenburg";
$GLOBALS['expiration'] = 3650;
$GLOBALS['ccd_dir'] = $GLOBALS['monitor_root']."/ccd";

//Crawl
//Zeit zwischen einem Crawl bis zum nächsten in Minuten
$GLOBALS['timeBetweenCrawls'] = 10;

?>