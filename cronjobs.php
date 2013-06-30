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
require_once('lib/classes/core/Eventlist.class.php');

/**
* Crawl cycles and offline crawls
**/
echo "Beende alten Crawl Cycle und starte neuen\n";
Crawling::organizeCrawlCycles();
//exec("echo \"cronjobs.php \"`date`\" \"`whoami` >> /var/kunden/webs/freifunk/netmon/rrdtool/databases/whoamitest.txt", $return);

/**
* Clean database
**/

//Delete old Crawls
echo "Bereinige Datenbank\n";
Crawling::deleteOldCrawlDataExceptLastOnlineCrawl(($GLOBALS['hours_to_keep_mysql_crawl_data']*60*60));

/**
 * Delete old Events
 */
//get number of total events in db
$total_count = new Eventlist();
$total_count->init(false, false, false, 0, 0);
$total_count = $total_count->getTotalCount();
//fetch the 50 oldest events from db and check if they need to be deleted.
//Then fetch the next 50 oldest events until you get to an event that is not old enough to delete it
//or if you looped through all events.
for($offset=0; $offset<$total_count; $offset+=50) {
	$eventlist = new Eventlist();
	$eventlist->init(false, false, false, $offset, 50, 'create_date', 'asc');
	foreach($eventlist->getEventlist() as $event) {
		if($event->getCreateDate() < time()-60*60*$GLOBALS['hours_to_keep_history_table']) {
			$event->delete();
		} else {
			$offset=$total_count;
			break;
		}
	}
}

//Delete Old not assigned routers
echo "Bereinige Tabelle der neuen Router\n";
DB::getInstance()->exec("DELETE FROM routers_not_assigned WHERE TO_DAYS(update_date) < TO_DAYS(NOW())-2");

/**
* Crawl
**/
echo "Crawle Router...\n";
for ($i=0; $i<=Router::countRouters(); $i+=10) {
        //start an independet crawl process for each 10 routers to crawl routers simultaniously
        $return = array();
        $cmd = "php ".ROOT_DIR."/integrated_xml_ipv6_crawler.php -f".$i." -t10  &> /dev/null & echo $!";
        echo "Running: $cmd\n";
        exec($cmd, $return);
        echo "The initialized crawl process has the pid $return[0]\n";
}

/**
* Service Crawls
**/
/*echo "Crawle Services...\n";
require_once($GLOBALS['netmon_root_path'].'lib/classes/crawler/json_service_crawler.php');*/

?>