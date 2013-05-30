<?php
	require_once('../../lib/classes/core/ObjectStatus.class.php');
	
	class RouterStatus extends ObjectStatus {
		$router_id = 0;
		$status = "";
		$hostname = "";
		$chipset = "";
		$cpu = "";
		$memory_total = 0;
		$memory_caching = 0;
		$memory_buffering = 0;
		$memory_free = 0;
		$loadavg = "";
		$processes = "";
		$uptime = "";
		$idletime = "";
		$distname = "";
		$distversion = "";
		$openwrt_core_revision = "";
		$openwrt_feeds_packages_revision = "";
		$firmware_version = "";
		$firmware_revision = "";
		$kernel_version = "";
		$configurator_version = "";
		$nodewatcher_version = "";
		$fastd_version = "";
		$batman_advanced_version = "";
		
		$available_statusses = array("online", "offline", "unknown");
		
		public function __construct($status_id=false, $router_id=false) {
			parent::__construct();
			
			$result = array();
			if($router_id!=false) {
				// initialize with data from last endet crawl cycle
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM crawl_routers
															WHERE id = ?");
					$stmt->execute(array($this->getCrawlCycleId()));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($status_id!=false) {
				// initialize with data from a given status
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM crawl_routers
															WHERE id = ?");
					$stmt->execute(array($status_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			$this->setStatusId((int)$result['id']);
			$this->setRouterId((int)$result['router_id']);
			$this->setStatus($result['status']);
			$this->setCreateDate($result['crawl_date']);
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setStatus($status) {
			if(in_array($status, $available_statusses))
				$this->status = $status;
		}
		
		public function getRouterId() {
			return $this->status;
		}
		
		public function getStatus() {
			return $this->status;
		}
	
	}
?>