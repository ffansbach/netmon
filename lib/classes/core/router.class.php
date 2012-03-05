<?php

require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/clients.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/interfaces.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/batmanadvanced.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/user.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/extern/xmpphp/XMPP.php');

class Router {
	public function getRouterInfo($router_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.chipset_id, notify, notification_wait, notified,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.id='$router_id'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByHostname($hostname) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.hostname='$hostname'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByAutoAssignLoginString($auto_assign_login_string) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.trying_to_assign_notified, routers.trying_to_assign_last_notification_time,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.router_auto_assign_login_string='$auto_assign_login_string'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByAutoAssignHash($auto_assign_hash) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.router_auto_assign_hash='$auto_assign_hash'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $crawl_data;
	}

	public function getCrawlRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE crawl_cycle_id='$crawl_cycle_id' AND status='$status'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getMapCrawlRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$sql = "SELECT  crawl_routers.router_id, crawl_routers.crawl_cycle_id, crawl_routers.crawl_date, crawl_routers.status, crawl_routers.ping, crawl_routers.hostname, crawl_routers.description, crawl_routers.location, crawl_routers.latitude, crawl_routers.longitude, crawl_routers.luciname, crawl_routers.luciversion, crawl_routers.distname, crawl_routers.distversion, crawl_routers.chipset, crawl_routers.cpu, crawl_routers.memory_total, crawl_routers.memory_caching, crawl_routers.memory_buffering, crawl_routers.memory_free, crawl_routers.loadavg, crawl_routers.processes, crawl_routers.uptime, crawl_routers.idletime, crawl_routers.local_time, crawl_routers.community_essid, crawl_routers.community_nickname, crawl_routers.community_email, crawl_routers.community_prefix, crawl_routers.batman_advanced_version, crawl_routers.kernel_version, crawl_routers.nodewatcher_version, crawl_routers.firmware_version, crawl_routers.firmware_revision, crawl_routers.openwrt_core_revision, crawl_routers.openwrt_feeds_packages_
revision,
					crawl_clients_count.client_count
					FROM crawl_routers, crawl_clients_count
					WHERE crawl_routers.crawl_cycle_id='$crawl_cycle_id' AND crawl_routers.status='$status' AND crawl_clients_count.crawl_cycle_id='$crawl_cycle_id' AND crawl_clients_count.router_id = crawl_routers.router_id
					ORDER BY crawl_clients_count.client_count ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getCrawlRoutersByCrawlCycleId($crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE crawl_cycle_id='$crawl_cycle_id'
					ORDER BY status ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getLastOnlineCrawlByRouterId($router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND status='online' ORDER BY id desc
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $crawl_data;
	}



	public function getRouters() {
		try {
			$sql = "SELECT  *
					FROM routers";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRouterList($where, $operator, $value) {
		if(!empty($where) AND !empty($value)) {
			$sql_append = "WHERE ".$where.urldecode($operator)."'$value'";
		} else {
			$sql_append = "";
		}

		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.description, routers.location, routers.latitude, routers.longitude,
					chipsets.name as chipset_name, chipsets.hardware_name,
					users.nickname,
					crawl_routers.status, crawl_routers.nodewatcher_version

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)
					LEFT JOIN crawl_routers on (crawl_routers.router_id=routers.id AND crawl_routers.crawl_cycle_id='$last_endet_crawl_cycle[id]')
					$sql_append";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$text=$row['location'];
				$shorttext=$text;
				$length = 20;
				if(strlen($text)>$length){
					$shorttext = substr($text, 0, $length-1);
					$var= explode(" ",substr($text, $length, strlen($text)));
					$shorttext.= $var[0];
				} 
				$row['short_location'] = $shorttext;

				$text=$row['chipset_name'];
				$shorttext=$text;
				$length = 16;
				if(strlen($text)>$length){
					$shorttext = substr($text, 0, $length-1);
					$var= explode(" ",substr($text, $length, strlen($text)));
					$shorttext.= $var[0];
				} 
				$row['short_chipset_name'] = $shorttext;

				$row['actual_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
				$row['router_reliability'] = Router::getRouterReliability($row['router_id'], 500);
				$row['client_count'] = Clients::getClientsCountByRouterAndCrawlCycle($row['router_id'], $last_endet_crawl_cycle['id']);
				$row['originators_count'] = count(BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']));
				$row['interfaces'] = Interfaces::getInterfacesCrawlByCrawlCycle($last_endet_crawl_cycle['id'], $row['router_id']);
				$row['traffic'] = 0;
				foreach($row['interfaces'] as $interface) {
					$row['traffic'] = $row['traffic'] + $interface['traffic_rx_avg'] + $interface['traffic_tx_avg'];
				}
				 
				$row['traffic'] = round($row['traffic']/1024,2);

				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRouterListByUserId($user_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.description, routers.location, routers.latitude, routers.longitude,
					chipsets.name as chipset_name,
					users.nickname

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)
					WHERE routers.user_id='$user_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$text=$row['location'];
				$shorttext=$text;
				$length = 20;
				if(strlen($text)>$length){
					$shorttext = substr($text, 0, $length-1);
					$var= explode(" ",substr($text, $length, strlen($text)));
					$shorttext.= $var[0];
				} 
				$row['short_location'] = $shorttext;

				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$row['actual_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
				$row['router_reliability'] = Router::getRouterReliability($row['router_id'], 500);
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRoutersForCrawl() {
		try {
			$sql = "SELECT  *
					FROM routers
				WHERE crawl_method='crawler'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$row['interfaces'] = Interfaces::getInterfacesByRouterId($row['id']);
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getCrawlRouterHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $limit) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND crawl_cycle_id!='$actual_crawl_cycle_id'
					ORDER BY id desc
					LIMIT $limit";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function countRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
					WHERE crawl_cycle_id='$crawl_cycle_id' AND status='$status'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRouters() {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRoutersByChipsetId($chipset_id) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers
					WHERE chipset_id='$chipset_id'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRoutersByTime($timestamp) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers
					WHERE create_date<=FROM_UNIXTIME($timestamp)";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function getRouterByMacAndCrawlCycleId($mac_addr, $crawl_cycle_id) {
		$interfaces = array();
		try {
			$sql = "SELECT  crawl_interfaces.router_id, crawl_interfaces.mac_addr,
					routers.hostname, routers.latitude, routers.longitude
					FROM crawl_interfaces
					LEFT JOIN routers on (routers.id=crawl_interfaces.router_id)
				WHERE crawl_interfaces.crawl_cycle_id='$crawl_cycle_id' AND crawl_interfaces.mac_addr='$mac_addr'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByIPv4Addr($ipv4_addr) {
		$exploded_addr = explode(".", $ipv4_addr);
		if(count($exploded_addr)>3) {
			$ipv4_addr = $exploded_addr[2].".".$exploded_addr[3];
		}
		$interfaces = array();
		try {
			$sql = "SELECT  interfaces.router_id, interfaces.ipv4_addr,
					routers.hostname, routers.latitude, routers.longitude
					FROM interfaces
					LEFT JOIN routers on (routers.id=interfaces.router_id)
				WHERE interfaces.ipv4_addr='$ipv4_addr'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterReliability($router_id, $crawl_cycles) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$crawl_history = Router::getCrawlRouterHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle['id'], $crawl_cycles);
		if(!empty($crawl_history)) {
			$count = count($crawl_history);
			$status['online_absolut']=0;
			$status['offline_absolut']=0;
			foreach($crawl_history as $crawl) {
				if($crawl['status']=="online")
					$status['online_absolut']++;
				else
					$status['offline_absolut']++;
			}
			
			$status['online_percent']=$status['online_absolut']/$count*100;
			$status['offline_percent']=$status['offline_absolut']/$count*100;
		}
		return($status);
	}

	public function routerOfflineNotification($router_id, $crawl_data) {
		$router_data = Router::getRouterInfo($router_id);
		if($crawl_data['status']=='offline' AND $router_data['notify']==1 AND $router_data['notified']!=1) {
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();


			$user_data = User::getUserByID($router_data['user_id']);
			$router_crawl_history = Router::getCrawlRouterHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle['id'], $router_data['notification_wait']);
			if (!empty($router_crawl_history) AND (count($router_crawl_history)>=$router_data['notification_wait'])) {
				//Wenn der Serivice in der notification_wait einmal online gecrawlt wurde, beende.
				$online = false;
				foreach($router_crawl_history as $hist) {
					if ($hist['status']=="online") {
						$online = true;
						break;
					}
				}
					
				//Wenn offline 
				if (!$online) {
					if($user_data['notification_method']=="email") {
						Router::routerNotifyOverEmailIfDown($router_data, $user_data, $router_crawl_history);
					} elseif ($user_data['notification_method']=="jabber") {
						Router::routerNotifyOverJabberIfDown($router_data, $user_data, $router_crawl_history);
					}
					DB::getInstance()->exec("UPDATE routers SET
									notified = 1,
									last_notification = NOW()
								 WHERE id = '$router_id'");
				}
			}
		} elseif($crawl_data['status']=='online' AND $router_data['notify']==1 AND $router_data['notified']==1) {
			DB::getInstance()->exec("UPDATE routers SET
							notified = 0
						 WHERE id = '$router_id'");
		}
	}

	public function routerNotifyOverJabberIfDown($router_data, $user_data, $router_crawl_history) {
		$conn = new XMPPHP_XMPP($GLOBALS['jabber_server'], 5222, $GLOBALS['jabber_username'], $GLOBALS['jabber_password'], 'xmpphp', $server=null, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		
		try {
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$message = "Hallo $user_data[nickname],

dein Router $router_data[hostname] ist seit dem ".date("d.m H:i", strtotime($router_crawl_history[$router_data['notification_wait']-1]['crawl_date']))." uhr offline.
Siehe $GLOBALS[url_to_netmon]/router_status.php?router_id=$router_data[router_id]

Bitte stelle den Router zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Router.

Mit freundlichen Gruessen
$GLOBALS[community_name]";
			$conn->message($user_data['jabber'], $message);
			$conn->disconnect();
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		}
	}

	public function routerNotifyOverEmailIfDown($router_data, $user_data, $router_crawl_history) {
		$text = "Hallo $user_data[nickname],

dein Router $router_data[hostname] ist seit dem ".date("d.m H:i", strtotime($router_crawl_history[$router_data['notification_wait']-1]['crawl_date']))." uhr offline.
Siehe $GLOBALS[url_to_netmon]/router_status.php?router_id=$router_data[router_id]

Bitte stelle den Router zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Router.

Mit freundlichen Gruessen
$GLOBALS[community_name]";

		if ($GLOBALS['mail_sending_type']=='smtp') {
			$config = array('username' => $GLOBALS['mail_smtp_username'],
					'password' => $GLOBALS['mail_smtp_password']);
			
			if(!empty($GLOBALS['mail_smtp_ssl']))
				$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
			if(!empty($GLOBALS['mail_smtp_login_auth']))
				$config['auth'] = $GLOBALS['mail_smtp_login_auth'];
			
			$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
		}
		
		$mail = new Zend_Mail();
		$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
		$mail->addTo($user_data['email']);
		$mail->setSubject("Router offline $GLOBALS[community_name]");
		$mail->setBodyText($text);
		$mail->send($transport);
	}

	public function notifyAboutRouterTryingToAssign($router_data, $user_data) {
		$text = "Hallo $user_data[nickname],

ein Router versucht sich vergeblich mit der Kennung deines Routers $router_data[hostname] mit Netmon zu verbinden.
Eventuell hast du deinen Router vor kurzem neu installiert und vergessen den Anmeldehash deines Routers in Netmon zu resetten.
Wenn dem so ist, kannst du den Hash unter folgender URL resetten:
$GLOBALS[url_to_netmon]/routereditor.php?section=edit&router_id=$router_data[router_id]

Wenn du meinst, dass dies ein Fehler ist, dann setze dich bitte mit dem Freifunk Team unter fragen@freifunk-ol.de in Verbindung.

Mit freundlichen Gruessen
$GLOBALS[community_name]";

		if ($GLOBALS['mail_sending_type']=='smtp') {
			$config = array('username' => $GLOBALS['mail_smtp_username'],
					'password' => $GLOBALS['mail_smtp_password']);
			
			if(!empty($GLOBALS['mail_smtp_ssl']))
				$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
			if(!empty($GLOBALS['mail_smtp_login_auth']))
				$config['auth'] = $GLOBALS['mail_smtp_login_auth'];
			
			$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
		}
		
/*		$mail = new Zend_Mail();
		$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
		$mail->addTo($user_data['email']);
		$mail->setSubject("Router versucht vergeblich sich mit Netmon zu verbinden");
		$mail->setBodyText($text);
		$mail->send($transport);*/
	}


	public function getSmallerOnlineCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND status='online' AND crawl_cycle_id<'$crawl_cycle_id'
					ORDER BY crawl_cycle_id DESC
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $crawl_data;
	}

	public function getBiggerOnlineCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND status='online' AND crawl_cycle_id>'$crawl_cycle_id'
					ORDER BY crawl_cycle_id DESC
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $crawl_data;
	}

	public function getRouterByIpId($ip_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.chipset_id, routers.notify, routers.notification_wait, routers.notified, routers.last_notification
					FROM routers, interfaces, interface_ips
				WHERE interface_ips.ip_id='$ip_id' AND interface_ips.interface_id=interfaces.id AND routers.id=interfaces.router_id";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByInterfaceId($interface_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.chipset_id, routers.notify, routers.notification_wait, routers.notified, routers.last_notification
					FROM routers, interfaces
				WHERE interfaces.id='$interface_id' AND routers.id=interfaces.router_id";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}
}