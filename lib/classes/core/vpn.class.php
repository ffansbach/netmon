<?php

// +---------------------------------------------------------------------------+
// index.php
// Netmon, Freifunk Netzverwaltung und Monitoring Software
//
// Copyright (c) 2009 Clemens John <clemens-john@gmx.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+/

/**
 * This file contains the class for VPN functionality
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

  require_once('./lib/classes/extern/archive.class.php');

class Vpn {
	public function generateKeys($ip_id, $organizationalunitname, $commonname, $emailaddress, $privkeypass, $privkeypass_chk, $expiration) {
		try {
			$sql = "SELECT subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass
					FROM ips
					LEFT JOIN subnets on (subnets.id=ips.subnet_id)
					WHERE ips.id='$ip_id'";
			$result = DB::getInstance()->query($sql);
			$vpn = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		if (empty($vpn['vpn_server_ca']) OR empty($vpn['vpn_server_cert']) OR empty($vpn['vpn_server_key'])) {
			$message[] = array("Die Zertifikate konnten nicht generiert werden.", 2);
			$message[] = array("Der Subnetverwalter muss erst ein Masterzertifikat für das Subnetz erstellen.", 2);
			Message::setMessage($message);
			return false;
		} elseif (!empty($organizationalunitname) AND !empty($commonname) AND !empty($emailaddress)) {
			//SSL-Errors lehren
			while ($err = openssl_error_string());

			//Keydaten
			$dn = array("countryName" => $GLOBALS['countryName'], "stateOrProvinceName" => $GLOBALS['stateOrProvinceName'], "localityName" => $GLOBALS['localityName'], "organizationName" => $GLOBALS['organizationName'], "organizationalUnitName" => $organizationalunitname, "commonName" => $commonname, "emailAddress" => $emailaddress);
			
			//Passphrase auf null setzen wenn kein Paswort übergeben wird.
			if (empty($privkeypass)) {
				$privkeypass == null;
			} elseif ($privkeypass != $privkeypass_chk) {
				$message[] = array("Die beiden Passwörter stimmen nicht überein.", 2);
				Message::setMessage($message);
				return false;
			}
			
			$req_key = openssl_pkey_new();
			if(openssl_pkey_export ($req_key, $out_key)) {
				$req_csr  = openssl_csr_new ($dn, $req_key);
				$req_cert = openssl_csr_sign($req_csr, $vpn['vpn_server_cert'], $vpn['vpn_server_key'], $expiration);
				if(openssl_x509_export ($req_cert, $out_cert)) {
					//  echo "$out_key\n";
					// echo "$out_cert\n";
				} else echo "Failed Cert\n";
			} else echo "FailedKey\n";

			return array("return" => true, "vpn_client_cert" => $out_cert, "vpn_client_key" => $out_key);
		} else {
			$message[] = array("Die Daten von Ihnen einzugebenen Daten sind unvollständig", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function saveKeysToDB($ip_id, $vpn_client_cert, $vpn_client_key) {
		$result = DB::getInstance()->exec("UPDATE ips
										   SET vpn_client_cert = '$vpn_client_cert',
											   vpn_client_key = '$vpn_client_key'
										   WHERE id = '$ip_id'");
		
		if ($result>0) {
			$message[] = array("Die Keys wurden in der Datenbank gespeichert und werden Ihnen jetzt zum Download angeboten.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Die Keys konnten nicht in der Datenbank gespeichert werden.", 2);
			Message::setMessage($message);
			return false;
		}
	}

  public function downloadKeyBundle($ip_id) {
    $keys = Helper::getIpDataByIpId($ip_id);
    
    if (!empty($keys['vpn_server_ca']) AND !empty($keys['vpn_client_cert']) AND !empty($keys['vpn_client_key'])) {
		//Get Config Datei
		$config = Vpn::getVpnConfig($ip_id);

      $tmpdir = "./tmp/";
      
      $handle = fopen($tmpdir."ca.crt", "w+");
      fwrite($handle, $keys['vpn_server_ca']);
      fclose($handle);

      $handle = fopen($tmpdir."client.key", "w+");
      fwrite($handle, $keys['vpn_client_key']);
      fclose($handle);

      $handle = fopen($tmpdir."client.crt", "w+");
      fwrite($handle, $keys['vpn_client_cert']);
      fclose($handle);
      
      $handle = fopen($tmpdir."openvpn", "w+");
      fwrite($handle, $config);
      fclose($handle);      

      // Objekt erzeugen. Das Argument bezeichnet den Dateinamen
      $zipfile= new zip_file("VpnKeys_".$GLOBALS['net_prefix'].".".$keys['ip'].".zip");

      // Die Optionen
      $zipfile->set_options(array (
        'basedir' => $tmpdir, // Das Basisverzeichnis. Sonst wird der ganze Pfad von / an im Zip gespeichert.
        'followlinks' => 1, // Symlinks sollen berücksichtigt werden
        'inmemory' => 1, // Die Datei nur im Speicher erstellen
        'level' => 6, // Level 1 = schnell, Level 9 = gut
        'recurse' => 1, // In Unterverzeichnisse wechseln
        // Wenn zu grosse dateien verarbeitet werden, kannes zu einem php memory error kommen
        // Man sollte nicht über das halbe memory_limit (php.ini) hinausgehen
        'maxsize' => 12*1024*1024 // Nur Dateien die <= 12 MB gross sind zippen
      ));

      $zipfile->add_files(array("ca.crt", "client.key", "client.crt", "openvpn"));

      // Archiv erstellen
      $zipfile->create_archive();

      // Archiv zum Download anbieten
      $zipfile->download_file();

      unlink($tmpdir."ca.crt");
      unlink($tmpdir."client.key");
      unlink($tmpdir."client.crt");
      unlink($tmpdir."openvpn");
 } else {
      $message[] = array("Es sind nicht genügen Informationen vorhanden um die Keys bereit zu stellen.", 2);
      $message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
      Message::setMessage($message);
      return false;
    }
  }

  public function getCertificateInfo($ip_id) {
    $keys = Helper::getIpDataByIpId($ip_id);
    if (!empty($keys['vpn_server_ca']) AND !empty($keys['vpn_client_cert']) AND !empty($keys['vpn_client_key'])) {
      $ip_info = openssl_x509_parse($keys['vpn_client_cert']);
      $subnet_info = openssl_x509_parse($keys['vpn_server_ca']);

      $ip_info['validFrom_time_t'] = date("d.m.Y H:m:s", $ip_info['validFrom_time_t']);
      $ip_info['validTo_time_t'] = date("d.m.Y H:m:s", $ip_info['validTo_time_t']);

      $subnet_info['validFrom_time_t'] = date("d.m.Y H:m:s", $subnet_info['validFrom_time_t']);
      $subnet_info['validTo_time_t'] = date("d.m.Y H:m:s", $subnet_info['validTo_time_t']);

      $return = array('ip'=>$ip_info, 'subnet'=>$subnet_info);

      return $return;
    } else return false;
  }

  public function writeCCD($ip_id, $ccd_content=false) {
	$ip_data = Helper::getIpDataByIpId($ip_id);
	$subnet_data = Helper::getSubnetDataBySubnetID($ip_data['subnet_id']);
	$netmask = SubnetCalculator::getNmask($subnet_data['netmask']);
    
    $cert_info = Vpn::getCertificateInfo($ip_id);
    $CN = $cert_info['ip']['subject']['CN'];
    if (!empty($CN)) {
      $ccd = "./ccd/";
      $handle = fopen($ccd."$CN", "w+");
		if(!$ccd_content) {
			$ccd_content = "ifconfig-push $GLOBALS[net_prefix].$ip_data[ip] $netmask";
		}

      fwrite($handle, $ccd_content);
      fclose($handle);

      $message[] = array("CCD wurde für die IP $GLOBALS[net_prefix].$ip_data[ip] erstellt.", 1);
    } else {
      $message[] = array("CCD für die IP $GLOBALS[net_prefix].$ip_data[ip] konnte nicht erstellt, da der CN leer ist.", 2);
      $message[] = array("Sie müssen erst ein VPN-Zertifikat erstellen!", 2);
    }
    Message::setMessage($message);
    return true;
  }
  
	public function deleteCCD($ip_id) {
		$ip = Helper::getIpDataByIpId($ip_id);
		$cert_info = Vpn::getCertificateInfo($ip_id);
		$ccd = "./ccd/";
		if (empty($cert_info['ip']['subject']['CN'])) {
			$message[] = array("CCD der IP $GLOBALS[net_prefix].$ip[ip] kann nicht gelöscht werden, da kein CN eingetragen ist.", 0);
			Message::setMessage($message);
			return false;
		} elseif (file_exists($ccd.$cert_info['ip']['subject']['CN'])) {
			if (@unlink($ccd."$ip_id")) {
				$message[] = array("CCD der IP $GLOBALS[net_prefix].$ip[ip] wurde gelöscht.", 1);
				Message::setMessage($message);
				return true;
			}
		} else {
			$message[] = array("CCD der IP $GLOBALS[net_prefix].$ip[ip] kann nicht gelöscht werden, da er nicht existiert.", 0);
			Message::setMessage($message);
			return false;
		}
	}

	public function regenerateCCD($subnet_id) {

		try {
			$sql = "SELECT ips.id
					FROM ips
					WHERE ips.subnet_id='$subnet_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ip_ids[] = $row['id'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach ($ip_ids as $ip_id) {
			try {
				$sql = "SELECT id
						FROM ips
						WHERE id='$ip_id' AND vpn_client_cert !='' AND vpn_client_key!='';";
				$result = DB::getInstance()->query($sql);
				foreach($result as $row) {
					$ip_ids[] = $row['id'];
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
		foreach ($ip_ids as $ip_id) {
			Vpn::writeCCD($ip_id);
		}
		
		return true;
	}
  
  public function getVpnConfig($ip_id) {
  	$data = Helper::getIpDataByIpId($ip_id);
  	
  	$config = "package openvpn

config openvpn client
option enable 1
option client 1

option dev $data[vpn_server_device]
option proto $data[vpn_server_proto]
list remote \"$data[vpn_server] $data[vpn_server_port]\"

option resolv_retry infinite
option nobind 1
option persist_key 1
option persist_tun 1

option ca /etc/config/ca.crt
option cert /etc/config/client.crt
option key /etc/config/client.key

option comp_lzo 1
option verb 3";
  	
  	return $config;
  }

	public function getCCD($ip_id) {
    $cert_info = Vpn::getCertificateInfo($ip_id);
    $CN = $cert_info['ip']['subject']['CN'];

		$ccd = "./ccd/";
		$file = @file_get_contents($ccd.$CN);
		if($file)
			return $file;
		else
			return false;
	}

}
?>