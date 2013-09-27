<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	
	class Iplist Extends ObjectList {
		private $iplist = array();
		
		public function __construct($interface_id=false, $network_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
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
													FROM ips
													WHERE
														(interface_id = :interface_id OR :interface_id=0) AND
														(network_id = :network_id OR :network_id=0)");
				$stmt->bindParam(':interface_id', $interface_id, PDO::PARAM_INT);
				$stmt->bindParam(':network_id', $network_id, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT id as ip_id, ips.*
													FROM ips
													WHERE
														(interface_id = :interface_id OR :interface_id=0) AND
														(network_id = :network_id OR :network_id=0)
													ORDER BY
														case :sort_by
															when 'ip_id' then ips.id
															when 'interface_id' then ips.interface_id
															when 'network_id' then ips.network_id
															else NULL
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':interface_id', $interface_id, PDO::PARAM_INT);
				$stmt->bindParam(':network_id', $network_id, PDO::PARAM_INT);
				$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
				$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
				$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			foreach($result as $ip) {
				$ip = new Ip((int)$ip['ip_id'], (int)$ip['interface_id'], (int)$ip['network_id'],
							 $ip['ip'], $ip['create_date'], $ip['update_date']);
				$this->iplist[] = $ip;
			}
		}
		
		public function add($item) {
			if($item instanceof Iplist) {
				$this->setIplist(array_merge($this->getIplist(), $item->getIplist()));
			} elseif($item instanceof Ip) {
				$this->iplist[] = $item;
			}
		}
		
		public function delete() {
			foreach($this->getIplist() as $ip) {
				$ip->delete();
			}
		}
		
		public function deleteDuplicates() {
			for($i=0; $i<count($this->iplist); $i++) {
				$ip2hold = $this->iplist[$i];
				for($ii=0; $ii<count($this->iplist); $ii++) {
					$ip2test = $this->iplist[$ii];
					if($ip2hold->getIpId()!=$ip2test->getIpId() AND $ip2hold->getIp()==$ip2test->getIp()) {
						$ip2test->delete();
						array_splice($this->iplist, $ii, 1);
					}
				}
			}
		}
		
		public function setIplist($iplist) {
			if(is_array($iplist)) {
				$this->iplist = $iplist;
				return true;
			}
			return false;
		}
		
		public function getIplist() {
			return $this->iplist;
		}
		
		public function getNumberOfElements() {
			return count($this->iplist);
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('iplist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->iplist as $ip) {
				$domxmlelement->appendChild($ip->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>