<?php
	require_once("../../runtime.php");
	require_once(ROOT_DIR.'/config/config.local.inc.php');
	require_once(ROOT_DIR.'/lib/core/db.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/Eventlist.class.php');
	require_once(ROOT_DIR.'/lib/core/Event.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLineList.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZoneList.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecord.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecordList.class.php');
	require_once(ROOT_DIR.'/lib/core/Network.class.php');
	require_once(ROOT_DIR.'/lib/core/Networklist.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/CrawlCycleList.class.php');
	require_once(ROOT_DIR.'/lib/extern/rest/rest.inc.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	class API extends Rest {
		private $domxml = null;
		private $domxmlresponse = null;
		
		private $authentication = true;
		private $api_key = "";
		private $method = "";
		private $error_code = 0;
		private $error_message = "";
		private $default_limit = 50;
		
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->domxml = new DOMDocument('1.0', 'utf-8');
			$this->domxmlresponse = $this->domxml->createElement("netmon_response");
			
			if(!isset($this->_request['offset']))
				$this->_request['offset'] = false;
			if(!isset($this->_request['limit']))
				$this->_request['limit'] = $this->default_limit;
			if(!isset($this->_request['sort_by']))
				$this->_request['sort_by'] = false;
			if(!isset($this->_request['order']))
				$this->_request['order'] = false;
		}
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if(isset($_POST['api_key']))
				$this->api_key = $_POST['api_key'];
			elseif(isset($_GET['api_key']))
				$this->api_key = $_GET['api_key'];
			$this->method = $func;
			
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
		private function router() {
			if ($this->get_request_method() != "GET") {
				$this->error_code = 3;
				$this->error_message = "Wrong method";
				$this->response($this->finishxml(), 405);
				return false;
			}
			
			if (isset($this->_request['router_id'])) {
				$router = new Router((int)$this->_request['router_id']);
			} else if (isset($this->_request['hostname'])) {
				$router = new Router();
				$router->setHostname($this->_request['hostname']);
			} else if (isset($this->_request['mac'])) {
				$router = new Router();
				$router->setMac($this->_request['mac']);
			}
			
			if (!$router) {
				$this->error_code = 2;
				$this->error_message = "Wrong request";
				$this->response($this->finishxml(), 400);
				return false;
			}
			
			if (!$router->fetch()) {
				$this->error_code = 1;
				$this->error_message = "Router not found";
				$this->response($this->finishxml(), 404);
				return false;
			}

			$domxmldata = $router->getDomXMLElement($this->domxml);
			$this->response($this->finishxml($domxmldata), 200);
			return true;
		}
		
		private function routerlist() {
			if($this->get_request_method() == "GET") {
				$this->_request['crawl_cycle_id'] = (isset($this->_request['crawl_cycle_id'])) ? $this->_request['crawl_cycle_id'] : false;
				$this->_request['user_id'] = (isset($this->_request['user_id'])) ? $this->_request['user_id'] : false;
				$this->_request['crawl_method'] = (isset($this->_request['crawl_method'])) ? $this->_request['crawl_method'] : false;
				$this->_request['status'] = (isset($this->_request['status'])) ? $this->_request['status'] : false;
				$this->_request['hardware_name'] = (isset($this->_request['hardware_name'])) ? $this->_request['hardware_name'] : false;
				$this->_request['firmware_version'] = (isset($this->_request['firmware_version'])) ? $this->_request['firmware_version'] : false;
				$this->_request['batman_advanced_version'] = (isset($this->_request['batman_advanced_version'])) ? $this->_request['batman_advanced_version'] : false;
				$this->_request['kernel_version'] = (isset($this->_request['kernel_version'])) ? $this->_request['kernel_version'] : false;
				
				$routerlist = new Routerlist((int)$this->_request['crawl_cycle_id'], (int)$this->_request['user_id'],
											 $this->_request['crawl_method'], $this->_request['status'], $this->_request['hardware_name'],
											 $this->_request['firmware_version'], $this->_request['batman_advanced_version'],
											 $this->_request['kernel_version'],
											 (int)$this->_request['offset'], (int)$this->_request['limit'],
											 $this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $routerlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The routerlist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function networkinterface() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$networkinterface = new Networkinterface((int)$this->_request['id']);
				if($networkinterface->fetch()) {
					$domxmldata = $networkinterface->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 1;
					$this->error_message = "Networkinterface not found";
					$this->response($this->finishxml(), 404);
				}
			}
		}
		
		private function networkinterfacelist() {
			if($this->get_request_method() == "GET") {
				$this->_request['crawl_cycle_id'] = (isset($this->_request['crawl_cycle_id'])) ? $this->_request['crawl_cycle_id'] : false;
				$this->_request['router_id'] = (isset($this->_request['router_id'])) ? $this->_request['router_id'] : false;
				
				$routerlist = new Networkinterfacelist((int)$this->_request['crawl_cycle_id'], (int)$this->_request['router_id'],
													   (int)$this->_request['offset'], (int)$this->_request['limit'],
														$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $routerlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The networkinterfacelist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function ip() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$ip = new Ip((int)$this->_request['id']);
				if($ip->fetch()) {
					$domxmldata = $ip->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 1;
					$this->error_message = "IP not found";
					$this->response($this->finishxml(), 404);
				}
			}
		}
		
		private function iplist() {
			if($this->get_request_method() == "GET") {
				$this->_request['interface_id'] = (isset($this->_request['interface_id'])) ? $this->_request['interface_id'] : false;
				$this->_request['network_id'] = (isset($this->_request['network_id'])) ? $this->_request['network_id'] : false;
				
				$iplist = new Iplist((int)$this->_request['interface_id'], (int)$this->_request['network_id'],
									 (int)$this->_request['offset'], (int)$this->_request['limit'],
									 $this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $iplist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The iplist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function network() {
			if($this->get_request_method() == "GET") {
				$this->_request['network_id'] = (!isset($this->_request['network_id'])) ? false : $this->_request['network_id'];
				$this->_request['user_id'] = (!isset($this->_request['user_id'])) ? false : $this->_request['user_id'];
				$this->_request['ip'] = (!isset($this->_request['ip'])) ? false : $this->_request['ip'];
				$this->_request['netmask'] = (!isset($this->_request['netmask'])) ? false : $this->_request['netmask'];
				$this->_request['ipv'] = (!isset($this->_request['ipv'])) ? false : $this->_request['ipv'];
				
				$network = new Network((int)$this->_request['network_id'], (int)$this->_request['user_id'],
									   $this->_request['ip'], $this->_request['netmask'], $this->_request['ipv']);
				if($network->fetch()) {
					$domxmldata = $network->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 2;
					$this->error_message = "Network not found.";
					$this->response($this->finishxml(), 404);
				}
			} else {
				$this->error_code = 2;
				$this->error_message = "The Network could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function networklist() {
			if($this->get_request_method() == "GET") {
				$this->_request['user_id'] = (!isset($this->_request['user_id'])) ? false : $this->_request['user_id'];
				$this->_request['ipv'] = (!isset($this->_request['ipv'])) ? false : $this->_request['ipv'];
				$networklist = new Networklist((int)$this->_request['user_id'], (int)$this->_request['ipv'], 
																		$this->_request['offset'], $this->_request['limit'],
																		$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $networklist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The Networklist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function dns_zone() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$dns_zone = new DnsZone((int)$this->_request['id']);
				if($dns_zone->fetch()) {
					$domxmldata = $dns_zone->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 1;
					$this->error_message = "DNS Zone not found";
					$this->response($this->finishxml(), 404);
				}
			}
		}
		
		private function dns_zone_list() {
			if($this->get_request_method() == "GET") {
				$this->_request['user_id'] = (!isset($this->_request['user_id'])) ? false : $this->_request['user_id'];
				$dns_zone_list = new DnsZoneList((int)$this->_request['user_id'],
												 $this->_request['offset'], $this->_request['limit'],
												 $this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $dns_zone_list->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The DNS-Zone-List could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function dns_ressource_record() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$dns_ressource_record = new DnsRessourceRecord((int)$this->_request['id']);
				if($dns_ressource_record->fetch()) {
					$domxmldata = $dns_ressource_record->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 1;
					$this->error_message = "DNS Ressource Record not found";
					$this->response($this->finishxml(), 404);
				}
			}
		}
		
		private function dns_ressource_record_list() {
			if($this->get_request_method() == "GET") {
				$this->_request['dns_zone_id'] = (!isset($this->_request['dns_zone_id'])) ? false : $this->_request['dns_zone_id'];
				$this->_request['user_id'] = (!isset($this->_request['user_id'])) ? false : $this->_request['user_id'];
				$dns_ressource_record_list = new DnsRessourceRecordList((int)$this->_request['dns_zone_id'], (int)$this->_request['user_id'], 
																		$this->_request['offset'], $this->_request['limit'],
																		$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $dns_ressource_record_list->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The DNS-Ressource-Record list could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function originator_status_list() {
			if($this->get_request_method() == "GET") {
				$this->_request['router_id'] = (isset($this->_request['router_id'])) ? $this->_request['router_id'] : false;
				$this->_request['crawl_cycle_id'] = (isset($this->_request['crawl_cycle_id'])) ? $this->_request['crawl_cycle_id'] : false;
				
				$originator_status_list = new OriginatorStatusList((int)$this->_request['router_id'], (int)$this->_request['crawl_cycle_id'],
													   (int)$this->_request['offset'], (int)$this->_request['limit'],
														$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $originator_status_list->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The OriginatorStatusList could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function event() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$event = new Event((int)$this->_request['id']);
				if($event->fetch()) {
					$domxmldata = $event->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				} else {
					$this->error_code = 1;
					$this->error_message = "Event not found";
					$this->response($this->finishxml(), 404);
				}
			} elseif ($this->get_request_method() == "POST" OR 
			         ($this->get_request_method() == "GET" && !isset($this->_request['id']) && count($_GET)>1)) {
			    if($this->isApiKeyValid($this->api_key)) {
					$data = (!empty($_POST)) ? $_POST : $_GET;
					$event = new Event(false, false, $this->_request['object'], (int)$this->_request['object_id'],
									   $this->_request['action'], $this->_request['data']);
					$event_id = $event->store();
					if($event_id!=false) {
						header('Location: '.ConfigLine::configByName('url_to_netmon').'/api/rest/event/'.$event_id);
					} else {
						$this->authentication=false;
						$this->error_code = 2;
						$this->error_message = "The Event could not be created. Your request seems to miss some data.";
						$this->response($this->finishxml(), 400);
					}
				} else {
						$this->error_code = 2;
						$this->error_message = "The api_key is not valid.";
						$this->response($this->finishxml(), 401);
				}
			} else {
				$this->response('',406);
			}
		}
		
		private function eventlist() {
			if($this->get_request_method() == "GET") {
				$this->_request['object'] = (isset($this->_request['object'])) ? $this->_request['object'] : false;
				$this->_request['object_id'] = (isset($this->_request['object_id'])) ? $this->_request['object_id'] : false;
				$this->_request['action'] = (isset($this->_request['action'])) ? $this->_request['action'] : false;
				$eventlist = new Eventlist();
				$eventlist->init($this->_request['object'], (int)$this->_request['object_id'], $this->_request['action'],
								(int)$this->_request['offset'], (int)$this->_request['limit'],
								$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The OriginatorStatusList could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function crawl_cycle_list() {
			if($this->get_request_method() == "GET") {
				$eventlist = new CrawlCycleList((int)$this->_request['offset'], (int)$this->_request['limit'],
												$this->_request['sort_by'], $this->_request['order']);
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The CrawlCycleList could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		public function isApiKeyValid($api_key) {
			if(!empty($api_key)) {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE api_key=?");
				$stmt->execute(array($api_key));
				return $stmt->rowCount();
			} else {
				return false;
			}
		}
		
		private function config() {
			$this->_request['api_key'] = (isset($this->_request['api_key'])) ? $this->_request['api_key'] : 'a';
			$user = new User(false, false, false, false, false, $this->_request['api_key']);
			if($user->fetch()) {
				if($user->getPermission()==120) {
					if($this->get_request_method() == "GET" && (isset($this->_request['id']) || isset($this->_request['name']))) {
						$this->_request['id'] = (isset($this->_request['id'])) ? $this->_request['id'] : false;
						$this->_request['name'] = (isset($this->_request['name'])) ? $this->_request['name'] : false;
						$config_line = new ConfigLine((int)$this->_request['id'], $this->_request['name']);
						if($config_line->fetch()) {
							$domxmldata = $config_line->getDomXMLElement($this->domxml);
							$this->response($this->finishxml($domxmldata), 200);
						} else {
							$this->error_code = 1;
							$this->error_message = "Config not found";
							$this->response($this->finishxml(), 404);
						}
					}
					die();
				} else {
					$this->error_code = 2;
					$this->error_message = "Your API-Key has not enough permission.";
				}
			} else {
				$this->error_code = 2;
				$this->error_message = "The api_key is not valid.";
			}
			$this->authentication = 0;
			$this->response($this->finishxml(), 401);
		}
		
		private function configlist() {
			$this->_request['api_key'] = (isset($this->_request['api_key'])) ? $this->_request['api_key'] : 'a';
			$user = new User(false, false, false, false, false, $this->_request['api_key']);
			if($user->fetch()) {
				if($user->getPermission()==120) {

					if($this->get_request_method() == "GET") {
						$config_line_list = new ConfigLineList($this->_request['offset'], $this->_request['limit'],
																$this->_request['sort_by'], $this->_request['order']);
						$domxmldata = $config_line_list->getDomXMLElement($this->domxml);
						$this->response($this->finishxml($domxmldata), 200);
					} else {
						$this->error_code = 2;
						$this->error_message = "The Configlist could not be created, your request seems to be malformed.";
						$this->response($this->finishxml(), 400);
					}
					die();
				} else {
					$this->error_code = 2;
					$this->error_message = "Your API-Key has not enough permission.";
				}
			} else {
				$this->error_code = 2;
				$this->error_message = "The api_key is not valid.";
			}
			$this->authentication = 0;
			$this->response($this->finishxml(), 401);
		}
		
		/*
		 *	Encode array into JSON
		*/
		private function finishxml($domxmldata=false){
				$domxmlrequest = $this->domxml->createElement("request");
				$domxmlrequest->appendChild($this->domxml->createElement("authentication", $this->authentication));
				$domxmlrequest->appendChild($this->domxml->createElement("api_key", $this->api_key));
				$domxmlrequest->appendChild($this->domxml->createElement("method", $this->method));
				$domxmlrequest->appendChild($this->domxml->createElement("error_code", $this->error_code));
				$domxmlrequest->appendChild($this->domxml->createElement("error_message", $this->error_message));
				$this->domxmlresponse->appendChild($domxmlrequest);
				if($domxmldata!=false)
					$this->domxmlresponse->appendChild($domxmldata);
				$this->domxml->appendChild($this->domxmlresponse);
			
				return $this->domxml->saveXML(NULL, LIBXML_NOEMPTYTAG);
		}
	}
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>
