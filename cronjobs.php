<?php
/**
* IF Netmon is called by the server/cronjob
*/
if (empty($_SERVER["REQUEST_URI"])) {
	$path = dirname(__FILE__)."/";
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	$GLOBALS['netmon_root_path'] = $path."/";
}

if(!empty($_SERVER['REMOTE_ADDR'])) {
	die("This script can only be run by the server directly.");
}

require_once('runtime.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/rrdtool.class.php');

/**
* Crawl cycles and offline crawls
**/
echo "Beende alten Crawl Cycle und starte neuen\n";
Crawling::organizeCrawlCycles();
//exec("echo \"cronjobs.php \"`date`\" \"`whoami` >> /var/kunden/webs/freifunk/netmon/rrdtool/databases/whoamitest.txt", $return);
/**
* Service Crawls
**/

//require_once($GLOBALS['netmon_root_path'].'lib/classes/crawler/json_service_crawler.php');

/**
* Clean database
**/

//Delete old Crawls
echo "Bereinige Datenbank\n";
Crawling::deleteOldCrawlDataExceptLastOnlineCrawl(($GLOBALS['hours_to_keep_mysql_crawl_data']*60*60));
Crawling::deleteOldHistoryData(($GLOBALS['hours_to_keep_history_table']*60*60));


//Delete Old not assigned routers
echo "Bereinige Tabelle der neuen Router\n";
DB::getInstance()->exec("DELETE FROM routers_not_assigned WHERE TO_DAYS(update_date) < TO_DAYS(NOW())-2");

/**
* Remove old generated images
**/
echo "Bereinige Image generator\n";
$files = scandir($GLOBALS['netmon_root_path'].'scripts/imagemaker/tmp/');
foreach($files as $file) {
	if ($file!=".." AND $file!=".") {
		$exploded_name = explode("_", $file);
		if(!empty($exploded_name[2]) AND is_numeric($exploded_name[2]) AND $exploded_name[2]<(time()-1800)) {
			exec("rm -Rf $GLOBALS[netmon_root_path]/scripts/imgbuild/dest/$file");
		}
	}
}

/**
* Crawl
**/
echo "Crawle...\n";
require_once('integrated_xml_ipv6_crawler.php');

echo "Beende\n";

?>