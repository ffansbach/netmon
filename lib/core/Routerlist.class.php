<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');

	class Routerlist extends ObjectList {
		private $routerlist = array();
		
		/**
		 * Initialize the routerlist with routers
		 * @param $user_id	possible values:
		 *							int: >0, initialize the routerlist with the routers of the user
		 *							boolean: false, initialize the routerlist with routers of all users
		 * @param $offset	possible values:
		 *							int: >=0, controll the position from where the first router in the list ist fetched from db
		 *							boolean: false, initialize offset with 0
		 * @param $limit	possible values:
		 *							int: >=0, controll the maximum numbers of routers in the list
		 *							int: -1, set limit to maximum
		 * @param $sort_by	possible values:
		 *							string: hostname, 
		 *							boolean: false, sort default by router_id
		 * @param $order	possible values:
		 *							string: asc, desc
		 *							boolean: false, order default asc
		 */
		public function __construct($crawl_cycle_id=false, $user_id=false, $crawl_method=false, $status=false, $hardware_name=false,
									$firmware_version=false, $batman_advanced_version=false, $kernel_version=false,
									$offset=false, $limit=false, $sort_by=false, $order=false) {
			$result = array();
			if($offset!==false)
				$this->setOffset((int)$offset);
			if($limit!==false)
				$this->setLimit((int)$limit);
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
			
			// initialize $total_count with the total number of objects in the list (over all pages)
			try {
				$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
													FROM routers r, users u, chipsets c, crawl_routers s
													WHERE
														(r.user_id = :user_id OR :user_id=0) AND
														(r.crawl_method = :crawl_method OR :crawl_method='') AND
														(s.status = :status OR :status='') AND
														(c.hardware_name = :hardware_name OR :hardware_name='') AND
														(s.firmware_version = :firmware_version OR :firmware_version='') AND
														(s.batman_advanced_version = :batman_advanced_version OR :batman_advanced_version='') AND
														(s.kernel_version = :kernel_version OR :kernel_version='') AND
														r.user_id = u.id AND
														r.chipset_id= c.id AND
														r.id = s.router_id AND
														s.crawl_cycle_id = :crawl_cycle_id");
				$stmt->bindParam(':crawl_cycle_id', ($crawl_cycle_id) ? $crawl_cycle_id : (int)crawling::getLastEndedCrawlCycle()['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindParam(':crawl_method', $crawl_method, PDO::PARAM_STR);
				$stmt->bindParam(':status', $status, PDO::PARAM_STR);
				$stmt->bindParam(':hardware_name', $hardware_name, PDO::PARAM_STR);
				$stmt->bindParam(':firmware_version', $firmware_version, PDO::PARAM_STR);
				$stmt->bindParam(':batman_advanced_version', $batman_advanced_version, PDO::PARAM_STR);
				$stmt->bindParam(':kernel_version', $kernel_version, PDO::PARAM_STR);
				$stmt->execute();
				$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			$this->setTotalCount((int)$total_count['total_count']);
			//if limit -1 then get all ressource records
			if($this->getLimit()==-1)
				$this->setLimit($this->getTotalCount());
			
			
			try {
				$stmt = DB::getInstance()->prepare("SELECT r.id as r_id, r.user_id as r_user_id, r.create_date as r_create_date, r.update_date as r_update_date, r.crawl_method as r_crawl_method, r.hostname as r_hostname, r.allow_router_auto_assign as r_allow_router_auto_assign, r.router_auto_assign_login_string as r_allow_router_auto_assign, r.router_auto_assign_hash as r_router_auto_assign_hash, r.description as r_description, r.location as r_location, r.latitude as r_latitude, r.longitude as r_longitude, r.chipset_id as r_chipset_id,
														   u.id as u_id, u.session_id as u_session_id, u.nickname as u_nickname, u.password as u_password, u.openid as u_openid, u.api_key as u_api_key, u.vorname as u_vorname, u.nachname as u_nachname, u.strasse as u_strasse, u.plz as u_plz, u.ort as u_ort, u.telefon as u_telefon, u.email as u_email, u.jabber as u_jabber, u.icq as u_icq, u.website as u_website, u.about as u_about, u.allow_node_delegation as u_allow_node_delegation, u.notification_method as u_notification_method, u.permission as u_permission, u.create_date as u_create_date, u.update_date as u_update_date, u.activated as u_activated,
														   c.id as c_id, c.create_date as c_create_date, c.update_date as c_update_date, c.name as c_name, c.hardware_name as c_hardware_name,
														   s.id as s_id, s.router_id as s_router_id, s.crawl_cycle_id as s_crawl_cycle_id, s.crawl_date as s_crawl_date, s.status as s_status, s.hostname as s_hostname, s.description as s_description, s.location as s_location, s.latitude as s_latitude, s.longitude as s_longitude, s.luciname as s_luciname, s.luciversion as s_luciversion, s.distname as s_distname, s.distversion as s_distversion, s.chipset as s_chipset, s.cpu as s_cpu, s.memory_total as s_memory_total, s.memory_caching as s_memory_caching, s.memory_buffering as s_memory_buffering, s.memory_free as s_memory_free, s.loadavg as s_loadavg, s.processes as s_processes, s.uptime as s_uptime, s.idletime as s_idletime, s.local_time as s_local_time, s.community_essid as s_community_essid, s.community_nickname as s_community_nickname, s.community_email as s_community_email, s.community_prefix as s_community_prefix, s.batman_advanced_version as s_batman_advanced_version, s.fastd_version as s_fastd_version, s.kernel_version as s_kernel_version, s.configurator_version as s_configurator_version, s.nodewatcher_version as s_nodewatches_version, s.firmware_version as s_firmware_version, s.firmware_revision as s_firmware_revision, s.openwrt_core_revision as s_openwrt_core_revision, s.openwrt_feeds_packages_revision as s_openwrt_feeds_packages_revision, s.client_count as s_client_count
													FROM routers r, users u, chipsets c, crawl_routers s
													WHERE
														(r.user_id = :user_id OR :user_id=0) AND
														(r.crawl_method = :crawl_method OR :crawl_method='') AND
														(s.status = :status OR :status='') AND
														(c.hardware_name = :hardware_name OR :hardware_name='') AND
														(s.firmware_version = :firmware_version OR :firmware_version='') AND
														(s.batman_advanced_version = :batman_advanced_version OR :batman_advanced_version='') AND
														(s.kernel_version = :kernel_version OR :kernel_version='') AND
														r.user_id = u.id AND
														r.chipset_id= c.id AND
														r.id = s.router_id AND
														s.crawl_cycle_id = :crawl_cycle_id
													ORDER BY 
														case :sort_by
															when 'router_id' then r.id
															when 'user_id' then u.id
															when 'crawl_cycle_id' then s.crawl_cycle_id
															else NULL
														end
														".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':crawl_cycle_id', ($crawl_cycle_id) ? $crawl_cycle_id : (int)crawling::getLastEndedCrawlCycle()['id'], PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindParam(':crawl_method', $crawl_method, PDO::PARAM_STR);
				$stmt->bindParam(':status', $status, PDO::PARAM_STR);
				$stmt->bindParam(':hardware_name', $hardware_name, PDO::PARAM_STR);
				$stmt->bindParam(':firmware_version', $firmware_version, PDO::PARAM_STR);
				$stmt->bindParam(':batman_advanced_version', $batman_advanced_version, PDO::PARAM_STR);
				$stmt->bindParam(':kernel_version', $kernel_version, PDO::PARAM_STR);
				$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
				$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
				$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			foreach($result as $router) {
				$new_router = new Router((int)$router['r_id'], (int)$router['r_user_id'], $router['r_hostname'],
										 $router['r_description'], $router['r_location'], $router['r_latitude'],
										 $router['r_longitude'], (int)$router['r_chipset_id'], $router['r_crawl_method'],
										 $router['r_create_date'], $router['r_update_date'], $router['r_router_auto_assign_login_string']);
				$new_router->setUser(new User((int)$router['u_id'], $router['u_session_id'], $router['u_nickname'],
											  $router['u_password'], $router['u_openid'], $router['u_api_key'],
											  $router['u_vorname'], $router['u_nachname'], $router['u_strasse'],
											  (int)$router['u_plz'], $router['u_ort'], $router['u_telefon'], $router['u_email'],
											  $router['u_jabber'], $router['u_website'], $router['u_about'],
											  $router['u_notification_method'], (int)$router['u_permission'],
											  (int)$router['u_activated'], $router['u_create_date'], $router['u_update_date']));
				
				$new_router->setChipset(new Chipset((int)$router['c_id'], $router['c_name'],
													$router['c_hardware_name'], $router['c_create_date'], $router['c_update_date']));
				
				$new_router->setStatusdata(new RouterStatus((int)$router['s_id'], (int)$router['s_crawl_cycle_id'],
															(int)$router['s_router_id'], $router['s_status'],
															$router['s_crawl_date'], $router['s_hostname'],
															(int)$router['s_client_count'], $router['s_chipset'],
															$router['s_cpu'], (int)$router['s_memory_total'],
															(int)$router['s_memory_caching'], (int)$router['s_memory_buffering'],
															(int)$router['s_memory_free'], $router['s_loadavg'],
															$router['s_processes'], $router['s_uptime'], $router['s_idletime'],
															$router['s_local_time'], $router['s_distname'],
															$router['s_distversion'], $router['s_openwrt_core_revision'],
															$router['s_openwrt_feeds_packages_revision'],
															$router['s_firmware_version'], $router['s_firmware_revision'],
															$router['s_kernel_version'], $router['s_configurator_version'],
															$router['s_nodewatches_version'], $router['s_fastd_version'],
															$router['s_batman_advanced_version']));
				$this->routerlist[] = $new_router;
			}
		}
		
		public function delete() {
			foreach($this->getRouterlist() as $item) {
				$item->delete();
			}
		}
		
		public function getRouterlist() {
			return $this->routerlist;
		}
		
		public function setRouterlist($list) {
			if(is_array($list)) {
				$this->routerlist = $list;
			}
		}
		
		public function sort($sort, $order) {
			$tmp = array();
			
			$list = $this->getRouterlist();
			foreach($list as $key=>$item) {
				switch($sort) {
					case 'hostname':		$tmp[$key] = $item->getHostname();
											break;
				}
			}
			
			if($order == 'asc')
				array_multisort($tmp, SORT_ASC, $list);
			elseif($order == 'desc')
				array_multisort($tmp, SORT_DESC, $list);
			
			$new_list = array();
			for($i=0; $i<count($list); $i++) {
				if(!empty($list[$i])) {
					$new_list[] = $list[$i];
				}
			}
			
			$this->setRouterlist($new_list);
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('routerlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->routerlist as $routerlist) {
				$domxmlelement->appendChild($routerlist->getDomXMLElement($domdocument));
			}
			return $domxmlelement;
		}
	}
?>