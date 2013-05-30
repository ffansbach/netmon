<?php
	require_once('../../lib/classes/core/Networkinterface.class.php');

	class Networkinterfacelist {
		private $networkinterfacelist = array();
		
		public function __construct($router_id=false) {
			$result = array();
			if($router_id!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT interfaces.id as networkinterface_id
														FROM interfaces
														WHERE interfaces.router_id=?");
					$stmt->execute(array($router_id));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT interfaces.id as networkinterface_id
														FROM interfaces");
					$stmt->execute(array());
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			foreach($result as $networkinterface) {
				$this->networkinterfacelist[] = new Networkinterface((int)$networkinterface['networkinterface_id']);
			}
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('networkinterfacelist');
			foreach($this->networkinterfacelist as $networkinterface) {
				$domxmlelement->appendChild($networkinterface->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>