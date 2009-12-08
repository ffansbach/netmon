<?php

//INSTALLATION-LOCK
$GLOBALS['installed'] = true;

//INSTALL-INFO
$GLOBALS['netmon_version'] = "0.2 SVN";
$GLOBALS['netmon_codename'] = "Shake-up";

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

//JABBER
$GLOBALS['jabber_server'] = "jabber.nord-west.net";
$GLOBALS['jabber_username'] = "netmon";
$GLOBALS['jabber_password'] = "blabla";

//MAIL
$GLOBALS['mail_sender'] = "Freifunk Oldenburg Netmon <netmon@freifunk-ol.de>";

//FREIFUNKNETZ
$GLOBALS['net_prefix'] = "10.18";
$GLOBALS['geographical_center_of_net_latitute'] = "53.139926";
$GLOBALS['geographical_center_of_net_longitude'] = "8.213753";
$GLOBALS['geographical_radius_of_net'] = "10000";
$GLOBALS['city_name'] = "Oldenburg";

//PROJECTDATA
$GLOBALS['project_homepage'] = "http://www.freifunk-ol.de/";
$GLOBALS['project_name'] = "Freifunk Oldenburg";
$GLOBALS['project_essid'] = "oldenburg.freifunk.net";
$GLOBALS['project_bssid'] = "02:ca:ff:ee:ba:be";
$GLOBALS['project_kanal'] = "6";

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

//Project
$GLOBALS['networkPolicy'] = "http://wiki.freifunk-ol.de/index.php/Nutzungsvereinbarung";
$GLOBALS['portal_history_hours'] = 5;
$GLOBALS['days_to_keep_portal_history'] = 1;

?>
