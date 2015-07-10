<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Network.class.php');
	
	class Ip extends Object {
		private $ip_id = 0;
		private $interface_id = 0;
		private $network_id = 0;
		private $ip = "";
		private $statusdata = array();
		private $statusdata_history = array();
		private $network = null;
		
		public function __construct($ip_id=false, $interface_id=false, $network_id=false, $ip=false,
									$create_date=false, $update_date=false) {
				$this->setIpId($ip_id);
				$this->setInterfaceId($interface_id);
				$this->setNetworkId($network_id);
				$this->setIp($ip);
				$this->setCreateDate($create_date);
				$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM ips
													WHERE
														(id = :ip_id OR :ip_id=0) AND
														(interface_id = :interface_id OR :interface_id=0) AND
														(network_id = :network_id OR :network_id=0) AND
														(ip = :ip OR :ip='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':ip_id', $this->getIpId(), PDO::PARAM_INT);
				$stmt->bindParam(':interface_id', $this->getInterfaceId(), PDO::PARAM_INT);
				$stmt->bindParam(':network_id', $this->getNetworkId(), PDO::PARAM_INT);
				$stmt->bindParam(':ip', $this->getIp(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setIpId((int)$result['id']);
				$this->setInterfaceId((int)$result['interface_id']);
				$this->setNetworkId((int)$result['network_id']);
				$this->setIp($result['ip']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			//if address is ipv6 link local, then it is possible to store more than one of these adresses
			//on different interfaces in different networks. So we need to check if the address already exists
			//on the given interface and if not, add
			if($this->getInterfaceId()!=0 AND $this->getIp()!="" AND $this->ipIsInNetwork()) {
				if(strpos($this->getIp(), "fe80")!==false) {
					$ip = new Ip(false, $this->getInterfaceId(), $this->getNetworkId(), $this->getIp());
					if(!$ip->fetch()) {
						return $this->insert();
					}
				} else {
					$ip = new Ip(false, false, $this->getNetworkId(), $this->getIp());
					$result = $ip->fetch();
					
					if($this->getIpId() != 0 AND (($result AND $ip->getIpId()==$this->getIpId()) OR !$result)) {
						return $this->update();
					} elseif($this->getInterfaceId()!=0 AND $this->getIp()!="" AND $this->getNetworkId()!=0 AND $ip->getIpId()==0) {
						return $this->insert();
					}
				}
			}
			
			return false;
		}
		
		private function update() {
			try {
				$stmt = DB::getInstance()->prepare("UPDATE ips SET
															interface_id = ?,
															network_id = ?,
															ip = ?,
															update_date = NOW()
													WHERE id=?");
				$stmt->execute(array($this->getInterfaceId(), $this->getNetworkId(), $this->getIp(), $this->getIpId()));
				return $stmt->rowCount();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
		
		private function insert() {
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO ips (interface_id, network_id, ip, create_date, update_date)
													VALUES (?, ?, ?, NOW(), NOW())");
				$stmt->execute(array($this->getInterfaceId(), $this->getNetworkId(), $this->getIp()));
				return DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
		
		public function delete() {
			if($this->getIpId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM ips WHERE id=?");
					$stmt->execute(array($this->getIpId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setIpId($ip_id) {
			if(is_int($ip_id))
				$this->ip_id = $ip_id;
		}
		
		public function setInterfaceId($interface_id) {
			if(is_int($interface_id))
				$this->interface_id = $interface_id;
		}
		
		public function setNetworkId($network_id) {
			if(is_int($network_id)) {
				$network = new Network($network_id);
				if($network->fetch()) {
					$this->network_id = $network_id;
					$this->network = $network;
					return true;
				}
			}
			return false;
		}
		
		public function setIp($ip) {
			$ip = trim($ip);
			if(is_string($ip) AND $this->getNetwork()!=null AND Ip::isValidIp($ip, $this->getNetwork()->getIpv())) {
				if($this->getNetwork()->getIpv()==6) {
					$this->ip = (string)Ip::ipv6Expand($ip);
					return true;
				} elseif($this->getNetwork()->getIpv()==4) {
					$this->ip = $ip;
					return true;
				}
			}
			
			return false;
		}
		
		public function getIpId() {
			return $this->ip_id;
		}
		
		public function getInterfaceId() {
			return $this->interface_id;
		}
		
		public function getNetworkId() {
			return $this->network_id;
		}
		
		public function getIp() {
			return $this->ip;
		}
		
		/**
		* Usefull for ipv6 addresses
		*/
		public function getIpCompressed() {
			return inet_ntop(inet_pton($this->getIp()));
		}
		
		public function getNetwork() {
			return $this->network;
		}
		
		public function getNetworkinterface() {
			$networkinterface = new Networkinterface($this->getInterfaceId());
			if($networkinterface->fetch())
				return $networkinterface;
			return false;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('ip');
			$domxmlelement->appendChild($domdocument->createElement("ip_id", $this->getIpId()));
			$domxmlelement->appendChild($domdocument->createElement("interface_id", $this->getInterfaceId()));
			$domxmlelement->appendChild($domdocument->createElement("network_id", $this->getNetworkId()));
			$domxmlelement->appendChild($domdocument->createElement("ip", $this->getIp()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			$domxmlelement->appendChild($this->getNetwork()->getDomXMLElement($domdocument));
			return $domxmlelement;
		}
		
		public static function isValidIp($ip, $ipv) {
			if($ipv==4) {
				return (bool)ip2long($ip);
			} elseif($ipv==6) {
				//https://mebsd.com/coding-snipits/php-regex-ipv6-with-preg_match-revisited.html
				$regex = '/^(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|(25[0-5]|(2[0-4]|1\d|[1-9])?\d)(\.(?7)){3})\z/i';
				return (bool)preg_match($regex, $ip);
			}
			
			return false;
		}
		
		/**
		* Expand an IPv6 Address
		*
		* This will take an IPv6 address written in short form and expand it to include all zeros. 
		* GPL Source: http://www.soucy.org/project/inet6/
		*
		* @param  string  $addr A valid IPv6 address
		* @return string  The expanded notation IPv6 address
		*/
		public static function ipv6Expand($addr) {
			/* Check if there are segments missing, insert if necessary */
			if (strpos($addr, '::') !== false) {
				$part = explode('::', $addr);
				$part[0] = explode(':', $part[0]);
				$part[1] = explode(':', $part[1]);
				$missing = array();
				for ($i = 0; $i < (8 - (count($part[0]) + count($part[1]))); $i++)
					array_push($missing, '0000');
				$missing = array_merge($part[0], $missing);
				$part = array_merge($missing, $part[1]);
			} else {
				$part = explode(":", $addr);
			} // if .. else
			
			/* Pad each segment until it has 4 digits */
			foreach ($part as &$p) {
				while (strlen($p) < 4) $p = '0' . $p;
			} // foreach
			unset($p);
			/* Join segments */
			$result = implode(':', $part);
			/* Quick check to make sure the length is as expected */ 
			if (strlen($result) == 39) {
				return $result;
			} else {
				return false;
			} // if .. else
		}
		
		/**
		* Generate an IPv6 mask from prefix notation
		*
		* This will convert a prefix to an IPv6 address mask (used for IPv6 math) 
		*
		* @param  integer $prefix The prefix size, an integer between 1 and 127 (inclusive)
		* @return string  The IPv6 mask address for the prefix size
		*/
		public static function ipv6PrefixToMask($prefix) {
			/* Make sure the prefix is a number between 1 and 127 (inclusive) */
			$prefix = intval($prefix);
			if ($prefix < 0 || $prefix > 128) return false;
			$mask = '0b';
			for ($i = 0; $i < $prefix; $i++) $mask .= '1';
			for ($i = strlen($mask) - 2; $i < 128; $i++) $mask .= '0';
			$mask = gmp_strval(gmp_init($mask), 16);
			$result='';
			for ($i = 0; $i < 8; $i++) {
				$result .= substr($mask, $i * 4, 4);
				if ($i != 7) $result .= ':';
			} // for
			return $result;
		}
		
		public static function ipv6NetworkFromAddr($addr, $prefix) {
			$size = 128 - $prefix;
			$addr = gmp_init('0x' . str_replace(':', '', $addr));
			$mask = gmp_init('0x' . str_replace(':', '', Ip::ipv6PrefixToMask($prefix)));
			$prefix = gmp_and($addr, $mask);
			return gmp_strval($prefix, 16);
		}
		
		//http://stackoverflow.com/questions/7951061/matching-ipv6-address-to-a-cidr-subnet
		public function ipIsInNetwork($network=false) {
			$network = ($network) ? $network : $this->getNetwork();
			
			if($network instanceof Network) {
				$ip = inet_pton($this->getIp());
				$binaryip=Ip::inet_to_bits($ip, $network->getIpv());
				
				$net = $network->getIp();
				$maskbits = $network->getNetmask();
				//list($net,$maskbits)=explode('/',$cidrnet);
				$net=inet_pton($net);
				$binarynet=Ip::inet_to_bits($net, $network->getIpv());
				
				$ip_net_bits=substr($binaryip, 0, $maskbits);
				$net_bits   =substr($binarynet, 0, $maskbits);
				
				if($ip_net_bits!==$net_bits) return false;
				else return true;
			}
			return false;
		}
		
		// converts inet_pton output to string with bits
		//http://stackoverflow.com/questions/7951061/matching-ipv6-address-to-a-cidr-subnet
		public static function inet_to_bits($inet, $ipv) {
			//ATTENTION: the inet_pton() function seems to handle adresses
			//different between PHP version 5.4 and 5.6. Use the following:
			//PHP 5.4: unpack('A4', $inet) //capitalized A16
			//PHP 5.6: unpack('a4', $inet) //lowercased a16
			//The PHP 5.6 change has only been tested with IPv6 (IPv4 is TODO!)
			if(version_compare(phpversion(), '5.6', '<')) {
				$ipv4Format = "A4";
				$ipv6Format = "A16";
			} else {
				$ipv4Format = "a4";
				$ipv6Format = "a16";
			}
			if($ipv==4) $unpacked = unpack($ipv4Format, $inet);
			elseif($ipv==6) $unpacked = unpack($ipv6Format, $inet);
			
			$unpacked = str_split($unpacked[1]);
			$binaryip = '';
			foreach ($unpacked as $char) {
				$binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
			}
			return $binaryip;
		}
	}
?>