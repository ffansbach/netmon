<?php
	require_once(ROOT_DIR.'/lib/core/ObjectStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/Eventlist.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/Chipset.class.php');
	require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
	
	class RouterStatus extends ObjectStatus {
		private $router_id = 0;
		private $status = "";
		private $hostname = "";
		private $client_count = 0;
		private $chipset = "";
		private $cpu = "";
		private $memory_total = 0;
		private $memory_caching = 0;
		private $memory_buffering = 0;
		private $memory_free = 0;
		private $loadavg = "";
		private $processes = "";
		private $uptime = "";
		private $idletime = "";
		private $localtime = "";
		private $distname = "";
		private $distversion = "";
		private $openwrt_core_revision = "";
		private $openwrt_feeds_packages_revision = "";
		private $firmware_version = "";
		private $firmware_revision = "";
		private $kernel_version = "";
		private $configurator_version = "";
		private $nodewatcher_version = "";
		private $fastd_version = "";
		private $batman_advanced_version = "";
		public $available_statusses = array("online", "offline", "unknown");
		
		public function __construct($status_id=false, $crawl_cycle_id=false, $router_id=false,
									$status = false, $create_date=false, $hostname = false, $client_count = false, $chipset = false,
									$cpu = false, $memory_total = false, $memory_caching = false, $memory_buffering = false,
									$memory_free = false, $loadavg = false, $processes = false, $uptime = false,
									$idletime = false, $localtime=false, $distname = false, $distversion = false, $openwrt_core_revision = false, 
									$openwrt_feeds_packages_revision = false, $firmware_version = false,
									$firmware_revision = false, $kernel_version = false, $configurator_version = false, 
									$nodewatcher_version = false, $fastd_version = false, $batman_advanced_version=false) {
			$this->setStatusId($status_id);
			$this->setCrawlCycleId($crawl_cycle_id);
			$this->setRouterId($router_id);
			$this->setStatus($status);
			$this->setCreateDate($create_date);
			$this->setHostname($hostname);
			$this->setClientCount($client_count);
			$this->setChipset($chipset);
			$this->setCpu($cpu);
			$this->setMemoryTotal($memory_total);
			$this->setMemoryBuffering($memory_buffering);
			$this->setMemoryCaching($memory_caching);
			$this->setMemoryFree($memory_free);
			$this->setLoadavg($loadavg);
			$this->setProcesses($processes);
			$this->setUptime($uptime);
			$this->setIdletime($idletime);
			$this->setLocaltime($localtime);
			$this->setDistname($distname);
			$this->setDistversion($distversion);
			$this->setOpenwrtCoreRevision($openwrt_core_revision);
			$this->setOpenwrtFeedsPackagesRevision($openwrt_feeds_packages_revision);
			$this->setFirmwareVersion($firmware_version);
			$this->setFirmwareRevision($firmware_revision);
			$this->setKernelVersion($kernel_version);
			$this->setConfiguratorVersion($configurator_version);
			$this->setNodewatcherVersion($nodewatcher_version);
			$this->setFastdVersion($fastd_version);
			$this->setBatmanAdvancedVersion($batman_advanced_version);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM crawl_routers
													WHERE
														(id = :status_id OR :status_id=0) AND
														(crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0) AND
														(router_id = :router_id OR :router_id=0) AND
														(crawl_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(hostname = :hostname OR :hostname='')");
				$stmt->bindParam(':status_id', $this->getStatusId(), PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', $this->getCrawlCycleId(), PDO::PARAM_INT);
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':hostname', $this->getHostname(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setStatusId((int)$result['id']);
				$this->setCrawlCycleId((int)$result['crawl_cycle_id']);
				$this->setRouterId((int)$result['router_id']);
				$this->setStatus($result['status']);
				$this->setCreateDate($result['crawl_date']);
				$this->setHostname($result['hostname']);
				$this->setClientCount((int)$result['client_count']);
				$this->setChipset($result['chipset']);
				$this->setCpu($result['cpu']);
				$this->setMemoryTotal($result['memory_total']);
				$this->setMemoryBuffering($result['memory_buffering']);
				$this->setMemoryCaching($result['memory_caching']);
				$this->setMemoryFree($result['memory_free']);
				$this->setLoadavg($result['loadavg']);
				$this->setProcesses($result['processes']);
				$this->setUptime($result['uptime']);
				$this->setIdletime($result['idletime']);
				$this->setLocaltime($result['local_time']);
				$this->setDistname($result['distname']);
				$this->setDistversion($result['distversion']);
				$this->setOpenwrtCoreRevision($result['openwrt_core_revision']);
				$this->setOpenwrtFeedsPackagesRevision($result['openwrt_feeds_packages_revision']);
				$this->setFirmwareVersion($result['firmware_version']);
				$this->setFirmwareRevision($result['firmware_revision']);
				$this->setKernelVersion($result['kernel_version']);
				$this->setConfiguratorVersion($result['configurator_version']);
				$this->setNodewatcherVersion($result['nodewatcher_version']);
				$this->setFastdVersion($result['fastd_version']);
				$this->setBatmanAdvancedVersion($result['batman_advanced_version']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getStatusId() != 0 AND $this->getCrawlCycleId() != 0 AND $this->getRouterId() != 0 AND $this->getStatus() != "") {
				echo "UPDATE NOT IMPLEMENTED NOW (and will possibly never be implemented because it is not needed...";
			} elseif($this->getCrawlCycleId() != 0 AND $this->getRouterId() != 0 AND $this->getStatus() != "") {
				//check if there already exists an crawl item in this crawl cycle
				$tmp = new RouterStatus(false, $this->getCrawlCycleId(), $this->getRouterId());
				if(!$tmp->fetch()) {
					try {
						$stmt = DB::getInstance()->prepare("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status,
																					   hostname, distname, distversion, chipset,
																					   cpu, memory_total, memory_caching, memory_buffering,
																					   memory_free, loadavg, processes, uptime, idletime,
																					   local_time, batman_advanced_version, fastd_version,
																					   kernel_version, configurator_version,
																					   nodewatcher_version, firmware_version,
																					   firmware_revision, openwrt_core_revision,
																					   openwrt_feeds_packages_revision, client_count)
															VALUES (?, ?, NOW(), ?,
																	?, ?, ?, ?,
																	?, ?, ?, ?,
																	?, ?, ?, ?, ?,
																	?, ?, ?,
																	?, ?,
																	?, ?,
																	?, ?,
																	?, ?)");
						$stmt->execute(array($this->getRouterId(), $this->getCrawlCycleId(), $this->getStatus(),
											$this->getHostname(), $this->getDistname(), $this->getDistversion(), $this->getChipset(),
											$this->getCpu(), $this->getMemoryTotal(), $this->getMemoryCaching(), $this->getMemoryBuffering(),
											$this->getMemoryFree(), $this->getLoadavg(), $this->getProcesses(), $this->getUptime(), $this->getIdletime(),
											$this->getLocaltime(), $this->getBatmanAdvancedVersion(), $this->getFastdVersion(),
											$this->getKernelVersion(), $this->getConfiguratorVersion(),
											$this->getNodewatcherVersion(), $this->getFirmwareVersion(),
											$this->getFirmwareRevision(), $this->getOpenwrtCoreRevision(),
											$this->getOpenwrtFeedsPackagesRevision(), $this->getClientCount()));
					} catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
						
					//Update RRD-Files for graphs
					RrdTool::updateRouterMemoryHistory($this->getRouterId(), $this->getMemoryFree(), $this->getMemoryCaching(), $this->getMemoryBuffering());
					$processes = explode("/", $this->getProcesses());
					$processes[0] = (isset($processes[0])) ? $processes[0] : 0;
					$processes[1] = (isset($processes[1])) ? $processes[1] : 1;
					RrdTool::updateRouterProcessHistory($this->getRouterId(), $processes[0], $processes[1]);
					
					//create events
					$eventlist = new Eventlist();
					$router = new Router($this->getRouterId());
					$router->fetch();
					
					$last_router_status = new RouterStatus(false, (int)Crawling::getLastEndedCrawlCycle()['id'], $this->getRouterId());
					if($last_router_status->fetch()) {
						if ($this->getStatus() AND $last_router_status->getStatus() AND $this->getStatus()!=$last_router_status->getStatus()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "status", array('from'=>$last_router_status->getStatus(), 'to'=>$this->getStatus(), 'hostname'=>$router->getHostname())));
						}
						if($this->getUptime() AND $last_router_status->getUptime() AND $this->getUptime()<$last_router_status->getUptime()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "reboot", array('hostname'=>$router->getHostname())));
						}
						if ($this->getHostname() AND $last_router_status->getHostname() AND $this->getHostname()!=$last_router_status->getHostname()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "hostname", array('from'=>$last_router_status->getHostname(), 'to'=>$this->getHostname(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getChipset() AND $last_router_status->getChipset() AND $this->getChipset()!=$last_router_status->getChipset()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "chipset", array('from'=>$last_router_status->getChipset(), 'to'=>$this->getChipset(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getNodewatcherVersion() AND $last_router_status->getNodewatcherVersion() AND $this->getNodewatcherVersion()!=$last_router_status->getNodewatcherVersion()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "nodewatcher_version", array('from'=>$last_router_status->getNodewatcherVersion(), 'to'=>$this->getNodewatcherVersion(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getFirmwareVersion() AND $last_router_status->getFirmwareVersion() AND $this->getFirmwareVersion()!=$last_router_status->getFirmwareVersion()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "firmware_version", array('from'=>$last_router_status->getFirmwareVersion(), 'to'=>$this->getFirmwareVersion(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getBatmanAdvancedVersion() AND $last_router_status->getBatmanAdvancedVersion() AND $this->getBatmanAdvancedVersion()!=$last_router_status->getBatmanAdvancedVersion()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "batman_advanced_version", array('from'=>$last_router_status->getBatmanAdvancedVersion(), 'to'=>$this->getBatmanAdvancedVersion(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getDistversion() AND $last_router_status->getDistversion() AND $this->getDistversion()!=$last_router_status->getDistversion()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "distversion", array('from'=>$last_router_status->getDistversion(), 'to'=>$this->getDistversion(), 'hostname'=>$router->getHostname())));
						}
						if ($this->getDistname() AND $last_router_status->getDistname() AND $this->getDistname()!=$last_router_status->getDistname()) {
							$eventlist->add(new Event(false, false, "router", $this->getRouterId(), "distname", array('from'=>$last_router_status->getDistname(), 'to'=>$this->getDistname(), 'hostname'=>$router->getHostname())));
						}
					}
					$eventlist->store();
					
					//Check if netmon already knows the given chipset and if
					//the router assigned to this status has the right chipset set
					if($this->getStatus() == "online" AND $router->getChipset()->getName() != $this->getChipset()) {
						$chipset = new Chipset(false, $this->getChipset());
						if(!$chipset->fetch()) {
							$chipset->store();
						}
						$router->setChipsetId($chipset->getChipsetId());
						$router->store();
					}
					
					//Return the ID of the inserted RouterStatus
					return DB::getInstance()->lastInsertId();
				}
			}
			return false;
		}
		
		public function delete() {
			if($this->getStatusId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM crawl_routers WHERE id=?");
					$stmt->execute(array($this->getStatusId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setStatus($status) {
			if(in_array($status, $this->available_statusses))
				$this->status = $status;
		}
		
		public function setHostname($hostname) {
			if($hostname!=false)
				$this->hostname = trim($hostname);
		}
		
		public function setClientCount($client_count) {
			if(is_int($client_count))
				$this->client_count = $client_count;
		}
		
		public function setChipset($chipset) {
			if($chipset!=false)
				$this->chipset = trim($chipset);
		}
		
		public function setCpu($cpu) {
			if($cpu!=false)
				$this->cpu = trim($cpu);
		}
		
		public function setMemoryTotal($memory_total) {
			if(is_int($memory_total))
				$this->memory_total = $memory_total;
		}
		
		public function setMemoryBuffering($memory_buffering) {
			if(is_int($memory_buffering))
				$this->memory_buffering = $memory_buffering;
		}
		
		public function setMemoryCaching($memory_caching) {
			if(is_int($memory_caching))
				$this->memory_caching = $memory_caching;
		}
		
		public function setMemoryFree($memory_free) {
			if(is_int($memory_free))
				$this->memory_free = $memory_free;
		}
		
		public function setLoadavg($loadavg) {
			if($loadavg!=false)
				$this->loadavg = trim($loadavg);
		}
		
		public function setProcesses($processes) {
			if($processes!=false)
				$this->processes = trim($processes);
		}
		
		public function setUptime($uptime) {
			if($uptime!=false)
				$this->uptime = trim($uptime);
		}
		
		public function setIdletime($idletime) {
			if($idletime!=false)
				$this->idletime = trim($idletime);
		}
		
		public function setLocaltime($localtime) {
			if($localtime!=false)
				$this->localtime = trim($localtime);
		}
		
		public function setDistname($distname) {
			if(is_string($distname))
				$this->distname = trim($distname);
		}
		
		public function setDistversion($distversion) {
			if($distversion!=false)
				$this->distversion = trim($distversion);
		}
		
		public function setOpenwrtCoreRevision($openwrt_core_revision) {
			if($openwrt_core_revision!=false)
				$this->openwrt_core_revision = trim($openwrt_core_revision);
		}
		
		public function setOpenwrtFeedsPackagesRevision($openwrt_feeds_packages_revision) {
			if($openwrt_feeds_packages_revision!=false)
				$this->openwrt_feeds_packages_revision = trim($openwrt_feeds_packages_revision);
		}
		
		public function setFirmwareVersion($firmware_version) {
			if($firmware_version!=false)
				$this->firmware_version = trim($firmware_version);
		}
		
		public function setFirmwareRevision($firmware_revision) {
			if($firmware_revision!=false)
				$this->firmware_revision = trim($firmware_revision);
		}
		
		public function setKernelVersion($kernel_version) {
			if($kernel_version!=false)
				$this->kernel_version = trim($kernel_version);
		}
		
		public function setConfiguratorVersion($configurator_version) {
			if($configurator_version!=false)
				$this->configurator_version = trim($configurator_version);
		}
		
		public function setNodewatcherVersion($nodewatcher_version) {
			if($nodewatcher_version!=false)
				$this->nodewatcher_version = trim($nodewatcher_version);
		}
		
		public function setFastdVersion($fastd_version) {
			if($fastd_version!=false)
				$this->fastd_version = trim($fastd_version);
		}
		
		public function setBatmanAdvancedVersion($batman_advanced_version) {
			if($batman_advanced_version!=false)
				$this->batman_advanced_version = trim($batman_advanced_version);
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getStatus() {
			return $this->status;
		}
		
		public function getHostname() {
			return $this->hostname;
		}
		
		public function getClientCount() {
			return $this->client_count;
		}
		
		public function getChipset() {
			return $this->chipset;
		}
		
		public function getCpu() {
			return $this->cpu;
		}
		
		public function getMemoryTotal() {
			return $this->memory_total;
		}
		
		public function getMemoryBuffering() {
			return $this->memory_buffering;
		}
		
		public function getMemoryCaching() {
			return $this->memory_caching;
		}
		
		public function getMemoryFree() {
			return $this->memory_free;
		}
		
		public function getLoadavg() {
			return $this->loadavg;
		}
		
		public function getProcesses() {
			return $this->processes;
		}
		
		public function getUptime() {
			return $this->uptime;
		}
		
		public function getIdletime() {
			return $this->idletime;
		}
		
		public function getLocaltime() {
			return $this->localtime;
		}
		
		public function getDistname() {
			return $this->distname;
		}
		
		public function getDistversion() {
			return $this->distversion;
		}
		
		public function getOpenwrtCoreRevision() {
			return $this->openwrt_core_revision;
		}
		
		public function getOpenwrtFeedsPackagesRevision() {
			return $this->openwrt_feeds_packages_revision;
		}
		
		public function getFirmwareVersion() {
			return $this->firmware_version;
		}
		
		public function getFirmwareRevision() {
			return $this->firmware_revision;
		}
		
		public function getKernelVersion() {
			return $this->kernel_version;
		}
		
		public function getConfiguratorVersion() {
			return $this->configurator_version;
		}
		
		public function getNodewatcherVersion() {
			return $this->nodewatcher_version;
		}
		
		public function getFastdVersion() {
			return $this->fastd_version;
		}
		
		public function getBatmanAdvancedVersion() {
			return $this->batman_advanced_version;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('statusdata');
			$domxmlelement->appendChild($domdocument->createElement("status_id", $this->getStatusId()));
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("crawl_cycle_id", $this->getCrawlCycleId()));
			$domxmlelement->appendChild($domdocument->createElement("status", $this->getStatus()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("hostname", $this->getHostname()));
			$domxmlelement->appendChild($domdocument->createElement("client_count", $this->getClientCount()));
			$domxmlelement->appendChild($domdocument->createElement("chipset", $this->getChipset()));
			$domxmlelement->appendChild($domdocument->createElement("cpu", $this->getCpu()));
			$domxmlelement->appendChild($domdocument->createElement("memory_total", $this->getMemoryTotal()));
			$domxmlelement->appendChild($domdocument->createElement("memory_buffering", $this->getMemoryBuffering()));
			$domxmlelement->appendChild($domdocument->createElement("memory_caching", $this->getMemoryCaching()));
			$domxmlelement->appendChild($domdocument->createElement("memory_free", $this->getMemoryFree()));
			$domxmlelement->appendChild($domdocument->createElement("loadavg", $this->getLoadavg()));
			$domxmlelement->appendChild($domdocument->createElement("processes", $this->getProcesses()));
			$domxmlelement->appendChild($domdocument->createElement("uptime", $this->getUptime()));
			$domxmlelement->appendChild($domdocument->createElement("idletime", $this->getIdletime()));
			$domxmlelement->appendChild($domdocument->createElement("localtime", $this->getLocaltime()));
			$domxmlelement->appendChild($domdocument->createElement("distname", $this->getDistname()));
			$domxmlelement->appendChild($domdocument->createElement("distversion", $this->getDistversion()));
			$domxmlelement->appendChild($domdocument->createElement("openwrt_core_revision", $this->getOpenwrtCoreRevision()));
			$domxmlelement->appendChild($domdocument->createElement("openwrt_feeds_packages_revision", $this->getOpenwrtFeedsPackagesRevision()));
			$domxmlelement->appendChild($domdocument->createElement("firmware_version", $this->getFirmwareVersion()));
			$domxmlelement->appendChild($domdocument->createElement("firmware_revision", $this->getFirmwareRevision()));
			$domxmlelement->appendChild($domdocument->createElement("kernel_version", $this->getKernelVersion()));
			$domxmlelement->appendChild($domdocument->createElement("configurator_version", $this->getConfiguratorVersion()));
			$domxmlelement->appendChild($domdocument->createElement("nodewatcher_version", $this->getNodewatcherVersion()));
			$domxmlelement->appendChild($domdocument->createElement("fastd_version", $this->getFastdVersion()));
			$domxmlelement->appendChild($domdocument->createElement("batman_advanced_version", $this->getBatmanAdvancedVersion()));
			return $domxmlelement;
		}
	
	}
?>