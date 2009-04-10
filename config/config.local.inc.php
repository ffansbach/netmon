<?php

//WEBSERVER
//Domain oder IP ohne http://www und ohne Slash am Ende
$GLOBALS['domain'] = "freifunk-ol.de";
//Unterordner auf der Domain ohne Slash an Anfang und Ende
$GLOBALS['subfolder'] = "netmon";
$GLOBALS['monitor_root'] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS['subfolder'];

//MYSQL
$GLOBALS['mysql_host'] = "yourdbhost";
$GLOBALS['mysql_db'] = "database";
$GLOBALS['mysql_user'] = "username";
$GLOBALS['mysql_password'] = "password";
$GLOBALS['mysql_enc'] = "utf8";

//FREIFUNKNETZ
//Netzpräfix (Siehe https://wiki.freifunk.net/IP) ohne Punkt am Ende
$GLOBALS['net_prefix'] = "10.18";
//Ungefähre Geographische Position des ganzen Netzes
$GLOBALS['geographical_center_of_net_latitute'] = "8.213753";
$GLOBALS['geographical_center_of_net_longitude'] = "53.139926";
//Ungefährer Geographischer Radius den das Netz hat (in Metern!)
$GLOBALS['geographical_radius_of_net'] = "10000";

//Logsystem
//Logge bei Querrys die länge brauchen als X sekunden
$GLOBALS['mysql_querry_log_time'] = 3;

//OpenVPN Config
$GLOBALS['countryName'] = "DE";
$GLOBALS['stateOrProvinceName'] = "NDS";
$GLOBALS['localityName'] = "Oldenburg";
$GLOBALS['organizationName'] = "Freifunk Oldenburg";
$GLOBALS['expiration'] = 3650;
$GLOBALS['ccd_dir'] = $GLOBALS['monitor_root']."/ccd";

//Crawl
//Zeit zwischen einem Crawl bis zum nächsten in Minuten (muss mit Cronjob übereinstimmen!)
$GLOBALS['timeBetweenCrawls'] = 10;

?>