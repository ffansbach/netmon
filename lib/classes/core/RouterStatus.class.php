<?php
	require_once('../../lib/classes/core/ObjectStatus.class.php');
	
	class RouterStatus extends ObjectStatus {
		private $router_id = 0;
		private $status = "";
		private $hostname = "";
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
		
		public function __construct($status_id=false, $router_id=false) {
			parent::__construct();
			$result = array();
			if($router_id!=false) {
				// initialize with data from last endet crawl cycle
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM crawl_routers
															WHERE crawl_cycle_id = ?");
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
			$this->setHostname($result['hostname']);
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
			$this->hostname = $hostname;
		}
		
		public function setChipset($chipset) {
			$this->chipset = $chipset;
		}
		
		public function setCpu($cpu) {
			$this->cpu = $cpu;
		}
		
		public function setMemoryTotal($memory_total) {
			$this->memory_total = $memory_total;
		}
		
		public function setMemoryBuffering($memory_buffering) {
			$this->memory_buffering = $memory_buffering;
		}
		
		public function setMemoryCaching($memory_caching) {
			$this->memory_caching = $memory_caching;
		}
		
		public function setMemoryFree($memory_free) {
			$this->memory_free = $memory_free;
		}
		
		public function setLoadavg($loadavg) {
			$this->loadavg = $loadavg;
		}
		
		public function setProcesses($processes) {
			$this->processes = $processes;
		}
		
		public function setUptime($uptime) {
			$this->uptime = $uptime;
		}
		
		public function setIdletime($idletime) {
			$this->idletime = $idletime;
		}
		
		public function setDistname($distname) {
			$this->distname = $distname;
		}
		
		public function setDistversion($distversion) {
			$this->distversion = $distversion;
		}
		
		public function setOpenwrtCoreRevision($openwrt_core_revision) {
			$this->openwrt_core_revision = $openwrt_core_revision;
		}
		
		public function setOpenwrtFeedsPackagesRevision($openwrt_feeds_packages_revision) {
			$this->openwrt_feeds_packages_revision = $openwrt_feeds_packages_revision;
		}
		
		public function setFirmwareVersion($firmware_version) {
			$this->firmware_version = $firmware_version;
		}
		
		public function setFirmwareRevision($firmware_revision) {
			$this->firmware_revision = $firmware_revision;
		}
		
		public function setKernelVersion($kernel_version) {
			$this->kernel_version = $kernel_version;
		}
		
		public function setConfiguratorVersion($configurator_version) {
			$this->configurator_version = $configurator_version;
		}
		
		public function setNodewatcherVersion($nodewatcher_version) {
			$this->nodewatcher_version = $nodewatcher_version;
		}
		
		public function setFastdVersion($fastd_version) {
			$this->fastd_version = $fastd_version;
		}
		
		public function setBatmanAdvancedVersion($batman_advanced_version) {
			$this->batman_advanced_version = $batman_advanced_version;
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