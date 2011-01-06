<?php
require_once('runtime.php');
require_once('lib/classes/core/login.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/rrdtool.class.php');

if($_GET['section']=="insert_olsr_data") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root

	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		try {
			$sql = "SELECT *
			        FROM  crawl_cycle
	        		ORDER BY crawl_date desc
				LIMIT 1;";
			$result = DB::getInstance()->query($sql);
			$last_crawl_cycle = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		try {
			$sql = "SELECT *
	        		FROM  crawl_olsr
				WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$crawl_olsr[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(empty($crawl_olsr)) {
			try {
				DB::getInstance()->exec("INSERT INTO crawl_olsr (router_id, crawl_cycle_id, olsrd_links, crawl_date)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$_POST[olsrd_links]', NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
}


?>