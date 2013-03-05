<?php

require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/clients.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/interfaces.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/batmanadvanced.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/user.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/extern/xmpphp/XMPP.php');

/**
 * This class is used as a container for static methods that deal operations
 * with router objects. Mainly fetching data from the database.
 *
 * @package	Netmon
 */
class Router {
	public function getRouterInfo($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.chipset_id, notify, notification_wait, notified,
								    users.nickname, chipsets.name as chipset_name
							    FROM routers
							    LEFT JOIN users ON (users.id=routers.user_id)
							    LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
							    WHERE routers.id=?");
			$stmt->execute(array($router_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getRouterByHostname($hostname) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
								    users.nickname, chipsets.name as chipset_name
							    FROM routers
							    LEFT JOIN users ON (users.id=routers.user_id)
							    LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
							    WHERE routers.hostname=?");
			$stmt->execute(array($hostname));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getRouterByAutoAssignLoginString($auto_assign_login_string) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.trying_to_assign_notified, routers.trying_to_assign_last_notification_time,
								    users.nickname, chipsets.name as chipset_name
							    FROM routers
							    LEFT JOIN users ON (users.id=routers.user_id)
							    LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
							    WHERE routers.router_auto_assign_login_string=?");
			$stmt->execute(array($auto_assign_login_string));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getRouterByAutoAssignHash($auto_assign_hash) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
								    users.nickname, chipsets.name as chipset_name
							    FROM routers
							    LEFT JOIN users ON (users.id=routers.user_id)
							    LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
							    WHERE routers.router_auto_assign_hash=?");
			$stmt->execute(array($auto_assign_hash));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_routers WHERE router_id=? AND crawl_cycle_id=?");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getCrawlRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_routers WHERE crawl_cycle_id=? AND status=?");
			$stmt->execute(array($crawl_cycle_id, $status));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getCrawlRoutersByCrawlCycleId($crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_routers WHERE crawl_cycle_id=?");
			$stmt->execute(array($crawl_cycle_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getLastOnlineCrawlByRouterId($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM crawl_routers
							    WHERE router_id=? AND status='online' ORDER BY id desc
							    LIMIT 1");
			$stmt->execute(array($router_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getRouters() {
		$rows = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM routers");
			$stmt->execute(array());
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
	
	public function getRouterList($where, $operator, $value) {
		$operator = urldecode($operator);
		//check if the given search value are valid to prevent from sql injection.
		//In this case this pdo prepared statements cant to this
		if(!empty($where) AND !empty($operator) AND !empty($value)
		   AND (($where == "crawl_routers.status"
		    OR $where == "crawl_routers.batman_advanced_version"
		    OR $where == "crawl_routers.kernel_version"
		    OR $where == "crawl_routers.nodewatcher_version"
		    OR $where == "crawl_routers.firmware_version")
		   AND ($operator == "="
		    OR $where == "!="
		    OR $where == "<"
		    OR $where == ">"
		    OR $where == "<="
		    OR $where == ">=")
		   AND (preg_match('/^[a-zA-Z0-9\.\_\-]+$/', $value)))) {		
			$sql_append = "WHERE ".$where.$operator."'$value'";
		} else {
			$sql_append = "";
		}

		$routers = array();
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.*,
					chipsets.id as chipset_id, chipsets.name as chipset_name, chipsets.*,
					users.id as user_id, users.*,
					crawl_routers.status, crawl_routers.nodewatcher_version

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)
					LEFT JOIN crawl_routers on (crawl_routers.router_id=routers.id AND crawl_routers.crawl_cycle_id=?)
					$sql_append");
			$stmt->execute(array($last_endet_crawl_cycle['id']));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		foreach($rows as $row) {
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
		return $routers;
	}

	public function getRouterListByUserId($user_id) {
		$routers = array();
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.*,
					chipsets.id as chipset_id, chipsets.name as chipset_name, chipsets.*,
					users.id as user_id, users.*,
					crawl_routers.status, crawl_routers.nodewatcher_version

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)
					LEFT JOIN crawl_routers on (crawl_routers.router_id=routers.id AND crawl_routers.crawl_cycle_id=?)
					WHERE routers.user_id=?");
			$stmt->execute(array($last_endet_crawl_cycle['id'], $user_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		foreach($rows as $row) {
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
		return $routers;
	}

	public function getRoutersForCrawl($from, $to) {
		$rows=array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM routers WHERE crawl_method='crawler' ORDER BY id ASC LIMIT :from, :to");
			$stmt->bindValue(':from', (int)$from, PDO::PARAM_INT);
			$stmt->bindValue(':to', (int)$to, PDO::PARAM_INT);
			$stmt->execute();			
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		foreach($rows as $key => $row) {
			$rows[$key]['interfaces'] = Interfaces::getInterfacesByRouterId($row['id']);
		}
		return $rows;
	}

	public function getCrawlRouterHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $limit) {
		$rows=array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_routers
							    WHERE router_id=:router_id AND crawl_cycle_id!=:crawl_cycle_id
							    ORDER BY id desc
							    LIMIT :limit");
			$stmt->bindValue(':router_id', $router_id, PDO::PARAM_INT);
			$stmt->bindValue(':crawl_cycle_id', $actual_crawl_cycle_id, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$stmt->execute();
			
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function countRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as count
							    FROM crawl_routers
							    WHERE crawl_cycle_id=? AND status=?");
			$stmt->execute(array($crawl_cycle_id, $status));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows['count'];
	}

	public function countRouters() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as count FROM routers");
			$stmt->execute(array());
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows['count'];
	}

	public function countRoutersByChipsetId($chipset_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as count FROM routers WHERE chipset_id=?");
			$stmt->execute(array($chipset_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows['count'];
	}

	public function getRouterByMacAndCrawlCycleId($mac_addr, $crawl_cycle_id) {
		$rows = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT  crawl_interfaces.router_id, crawl_interfaces.mac_addr,
								    routers.hostname, routers.latitude, routers.longitude
							    FROM crawl_interfaces
							    LEFT JOIN routers on (routers.id=crawl_interfaces.router_id)
							    WHERE crawl_interfaces.crawl_cycle_id=? AND crawl_interfaces.mac_addr=?");
			$stmt->execute(array($crawl_cycle_id, $mac_addr));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getRouterReliability($router_id, $crawl_cycles) {
		$status = array();
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

					$stmt = DB::getInstance()->prepare("UPDATE routers SET notified = 1, last_notification = NOW() WHERE id = ?");
					$stmt->execute(array($router_id));
				}
			}
		} elseif($crawl_data['status']=='online' AND $router_data['notify']==1 AND $router_data['notified']==1) {
					$stmt = DB::getInstance()->prepare("UPDATE routers SET notified = 0 WHERE id = ?");
					$stmt->execute(array($router_id));
		}
	}
	
	public function routerNotifyOverJabberIfDown($router_data, $user_data, $router_crawl_history) {
		$message = "Hallo $user_data[nickname],\n";
		$message .= "dein Router $router_data[hostname] ist seit dem ".date("d.m H:i", strtotime($router_crawl_history[$router_data['notification_wait']-1]['crawl_date']))." uhr offline.\n";
		$message .= "Siehe $GLOBALS[url_to_netmon]/router_status.php?router_id=$router_data[router_id]\n\n";
		$message .= "Bitte stelle den Router zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Router.\n\n";
		$message .= "Mit freundlichen Gruessen\n";
		$message .= "$GLOBALS[community_name]";

		$conn = new XMPPHP_XMPP($GLOBALS['jabber_server'], 5222, $GLOBALS['jabber_username'], $GLOBALS['jabber_password'], 'xmpphp', $server=null, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		try {
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$conn->message($user_data['jabber'], $message);
			$conn->disconnect();
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		}
	}

	public function routerNotifyOverEmailIfDown($router_data, $user_data, $router_crawl_history) {
		$message = "Hallo $user_data[nickname],\n";
		$message .= "dein Router $router_data[hostname] ist seit dem ".date("d.m H:i", strtotime($router_crawl_history[$router_data['notification_wait']-1]['crawl_date']))." uhr offline.\n";
		$message .= "Siehe $GLOBALS[url_to_netmon]/router_status.php?router_id=$router_data[router_id]\n\n";
		$message .= "Bitte stelle den Router zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Router.\n\n";
		$message .= "Mit freundlichen Gruessen\n";
		$message .= "$GLOBALS[community_name]";

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
		$mail->setSubject("Router offline $GLOBALS[community_name]");
		$mail->setBodyText($text);
		$mail->send($transport);*/
	}

	//TODO
	public function notifyAboutRouterTryingToAssign($router_data, $user_data) {
		$text = "Hallo $user_data[nickname],\n\n";
		$text .= "ein Router versucht sich vergeblich mit der Kennung deines Routers $router_data[hostname] mit Netmon zu verbinden.\n";
		$text .= "Eventuell hast du deinen Router vor kurzem neu installiert und vergessen den Anmeldehash deines Routers in Netmon zu resetten.\n\n";
		$text .= "Wenn dem so ist, kannst du den Hash unter folgender URL resetten:\n";
		$text .= "$GLOBALS[url_to_netmon]/routereditor.php?section=edit&router_id=$router_data[router_id]\n\n";
		$text .= "Wenn du meinst, dass dies ein Fehler ist, dann setze dich bitte mit dem Freifunk Team unter fragen@freifunk-ol.de in Verbindung.\n\n";
		$text .= "Mit freundlichen Gruessen\n";
		$text .= "$GLOBALS[community_name]";

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
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_routers
							    WHERE router_id=? AND status='online' AND crawl_cycle_id<?
							    ORDER BY crawl_cycle_id DESC
							    LIMIT 1");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getBiggerOnlineCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_routers
							    WHERE router_id=? AND status='online' AND crawl_cycle_id>?
							    ORDER BY crawl_cycle_id DESC
							    LIMIT 1");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getRouterByIpId($ip_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date,
								    routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string,
								    routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
								    routers.chipset_id, routers.notify, routers.notification_wait, routers.notified, routers.last_notification
							    FROM routers, interfaces, interface_ips
							    WHERE interface_ips.ip_id=? AND interface_ips.interface_id=interfaces.id AND routers.id=interfaces.router_id");
			$stmt->execute(array($ip_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getRouterByInterfaceId($interface_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date,
								    routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string,
								    routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
								    routers.chipset_id, routers.notify, routers.notification_wait, routers.notified, routers.last_notification
							    FROM routers, interfaces
							    WHERE interfaces.id=? AND routers.id=interfaces.router_id");
			$stmt->execute(array($interface_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function areAddsAllowed($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM router_adds WHERE router_id=? LIMIT 1");
			$stmt->execute(array($router_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		if(count($rows) AND $rows['adds_allowd']=='1') return true;
		else return false;
	}

	public function getAddData($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM router_adds WHERE router_id=? LIMIT 1");
			$stmt->execute(array($router_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		$rows['add_small_exists'] = file_exists("./data/adds/".$router_id."_add_small.jpg");
		$rows['add_big_exists'] = file_exists("./data/adds/".$router_id."_add_big.jpg");

		return $data;
	}
}
