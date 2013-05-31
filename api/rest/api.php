<?php
	require_once("../../config/config.local.inc.php");
	require_once("../../lib/classes/core/db.class.php");
	require_once("../../lib/classes/core/Iplist.class.php");
	require_once("../../lib/classes/core/Ip.class.php");
	require_once("../../lib/classes/core/Networkinterfacelist.class.php");
	require_once("../../lib/classes/core/Networkinterface.class.php");
	require_once("../../lib/classes/core/Routerlist.class.php");
	require_once("../../lib/classes/core/Router.class.php");
	require_once("../../lib/classes/core/Eventlist.class.php");
	require_once("../../lib/classes/core/Event.class.php");
	require_once("../../lib/classes/extern/rest/rest.inc.php");
	
	class API extends Rest {
		private $domxml = null;
		private $domxmlresponse = null;
		
		private $authentication = true;
		private $api_key = "";
		private $method = "";
		private $error_code = 0;
		private $error_message = "";
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->domxml = new DOMDocument('1.0', 'utf-8');
			$this->domxmlresponse = $this->domxml->createElement("netmon_response");
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
		
		private function iplist() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$iplist = new Iplist($this->_request['id']);
				$domxmldata = $iplist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET") {
				$iplist = new Iplist();
				$domxmldata = $iplist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The iplist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function ip() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$ip = new Ip((int)$this->_request['id']);
				if($ip->getIpId() == 0) {
					$this->error_code = 1;
					$this->error_message = "IP not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $ip->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/iplist/');
			}
		}
		
		private function networkinterfacelist() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$networkinterfacelist = new Networkinterfacelist($this->_request['id']);
				$domxmldata = $networkinterfacelist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET") {
				$networkinterfacelist = new Networkinterfacelist();
				$domxmldata = $networkinterfacelist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The networkinterfacelist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function networkinterface() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$networkinterface = new Networkinterface((int)$this->_request['id']);
				if($networkinterface->getInterfaceId() == 0) {
					$this->error_code = 1;
					$this->error_message = "Networkinterface not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $networkinterface->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/networkinterfaclist/');
			}
		}
		
		private function routerlist() {
			if($this->get_request_method() == "GET" && isset($this->_request['user_id'])) {
				$routerlist = new Routerlist($this->_request['user_id']);
				$domxmldata = $routerlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET") {
				$routerlist = new Routerlist();
				$domxmldata = $routerlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The routerlist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function router() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$router = new Router((int)$this->_request['id']);
				if($router->getRouterId() == 0) {
					$this->error_code = 1;
					$this->error_message = "Router not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $router->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/routerlist/');
			}
		}
		
		private function eventlist() {
			if($this->get_request_method() == "GET" && isset($this->_request['router_id']) && isset($this->_request['action'])) {
				$eventlist = new Eventlist('router', $this->_request['router_id'], $this->_request['action']);
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET" && isset($this->_request['router_id'])) {
				$eventlist = new Eventlist('router', $this->_request['router_id']);
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET" && isset($this->_request['action'])) {
				$eventlist = new Eventlist(false, false, $this->_request['action']);
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET") {
				$eventlist = new Eventlist();
				$domxmldata = $eventlist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The eventlist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function event() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$event = new Event((int)$this->_request['id']);
				if($event->getEventId() == 0) {
					$this->error_code = 1;
					$this->error_message = "Event not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $event->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/events/');
			} elseif ($this->get_request_method() == "POST" OR 
			         ($this->get_request_method() == "GET" && !isset($this->_request['id']) && count($_GET)>1)) {
			    if($this->isApiKeyValid($this->api_key)) {
					$data = (!empty($_POST)) ? $_POST : $_GET;
					
					$event = new Event(false, $data['object'], $data['object_id'], $data['action'], $data['data']);
					$event_id = $event->store();
					if($event_id!=false) {
						header('Location: http://netmon.freifunk-ol.de/api/rest/event/'.$event_id);
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
		
		private function events() {
			$this->response($this->finishxml(), 200);
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
			
				return $this->domxml->saveXML();
		}
	}
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>