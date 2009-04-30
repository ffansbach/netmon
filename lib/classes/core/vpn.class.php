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

class vpn {
  function __construct(&$smarty) {
    if ($_GET['section'] == "new") {
      $node = Helper::getNodeDataByNodeId($_GET['node_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('data', Helper::getNodeDataByNodeId($_GET['node_id']));
      $smarty->assign('expiration', $GLOBALS['expiration']);
      $smarty->assign('get_content', "sslcertificate_new");
    }
    if ($_GET['section'] == "generate") {
      $node = Helper::getNodeDataByNodeId($_GET['node_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $keys = $this->generateKeys($_GET['node_id'], $_POST['organizationalunitname'], $_POST['commonname'], $_POST['emailaddress'], $_POST['privkeypass'], $_POST['privkeypass_chk'], $_POST['numberofdays']);
      if ($keys['return']) {
		$this->saveKeysToDB($_GET['node_id'], $keys['vpn_client_cert'], $keys['vpn_client_key']);
		$this->writeCCD($_GET['node_id']);
		$this->downloadKeyBundle($_GET['node_id']);
		$smarty->assign('message', message::getMessage());
      } else {
		$smarty->assign('message', message::getMessage());
        $smarty->assign('get_content', "sslcertificate_new");
      }
    }
    if ($_GET['section'] == "download") {
      $node = Helper::getNodeDataByNodeId($_GET['node_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $this->downloadKeyBundle($_GET['node_id']);
      $smarty->assign('message', message::getMessage());
    }
    if ($_GET['section'] == "info") {
      $node = Helper::getNodeDataByNodeId($_GET['node_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
    	if (!empty($node['vpn_server_ca']) AND !empty($node['vpn_client_cert']) AND !empty($node['vpn_client_key'])) {
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
    	  	$smarty->assign('vpn_config', $this->getVpnConfig($_GET['node_id']));
      		$smarty->assign('certificate_data', $this->getCertificateInfo($_GET['node_id']));
       		$smarty->assign('get_content', "sslcertificate_info");
	    } else {
	        $message[] = array("Es sind nicht genügen Informationen vorhanden um die Keys bereit zu stellen.", 2);
    	  	$message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
      		message::setMessage($message);
			$smarty->assign('message', message::getMessage());
    	}
    }

    if ($_GET['section'] == "regenerate_ccd_subnet") {
      $smarty->assign('subnets', Helper::getSubnetsByUserId($_SESSION['user_id']));
      $smarty->assign('get_content', "regenerate_ccd_subnet");

    } 
    if ($_GET['section'] == "insert_regenerate_ccd_subnet") {
      vpn::regenerateCCD($_POST['subnet_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('get_content', "desktop");
    }

    if ($_GET['section'] == "insert_regenerate_ccd") {
      $this->writeCCD($_GET['node_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('get_content', "desktop");
    }


  }

public function generateKeys($node_id, $organizationalunitname, $commonname, $emailaddress, $privkeypass, $privkeypass_chk, $expiration) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass
			       FROM nodes
			       LEFT JOIN subnets on (subnets.id=nodes.subnet_id)
			       WHERE nodes.id='$node_id'");
    while($row = mysql_fetch_assoc($result)) {
      $vpn = $row;
    }
    unset($db);

    if (empty($vpn['vpn_server_ca']) OR empty($vpn['vpn_server_cert']) OR empty($vpn['vpn_server_key'])) {
      $message[] = array("Die Zertifikate konnten nicht generiert werden.", 2);
      $message[] = array("Der Subnetverwalter muss erst ein Masterzertifikat für das Subnetz erstellen.", 2);
      message::setMessage($message);
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
   			message::setMessage($message);
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
   		message::setMessage($message);
		return false;
	}
}

  public function saveKeysToDB($node_id, $vpn_client_cert, $vpn_client_key) {

    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE nodes SET
vpn_client_cert = '$vpn_client_cert',
vpn_client_key = '$vpn_client_key'
WHERE id = '$node_id'
");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    if ($ergebniss>0) {
      $message[] = array("Die Keys wurden in der Datenbank gespeichert und werden Ihnen jetzt zum Download angeboten.", 1);
      message::setMessage($message);
      return true;
    } else {
      $message[] = array("Die Keys konnten nicht in der Datenbank gespeichert werden.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function downloadKeyBundle($node_id) {
    $keys = Helper::getNodeDataByNodeId($node_id);
    
    if (!empty($keys['vpn_server_ca']) AND !empty($keys['vpn_client_cert']) AND !empty($keys['vpn_client_key'])) {
		//Get Config Datei
		$config = $this->getVpnConfig($node_id);

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
      $zipfile= new zip_file("VpnKeys_".$GLOBALS['net_prefix'].".".$keys['subnet_ip'].".".$keys['node_ip'].".zip");

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

      $zipfile->add_files(array("*"));

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
      message::setMessage($message);
      return false;
    }
  }

  public function getCertificateInfo($node_id) {
    $db = new mysqlClass;
    $keys = Helper::getNodeDataByNodeId($node_id);
    if (!empty($keys['vpn_server_ca']) AND !empty($keys['vpn_client_cert']) AND !empty($keys['vpn_client_key'])) {
      $node_info = openssl_x509_parse($keys['vpn_client_cert']);
      $subnet_info = openssl_x509_parse($keys['vpn_server_ca']);

      $node_info['validFrom_time_t'] = date("d.m.Y H:m:s", $node_info['validFrom_time_t']);
      $node_info['validTo_time_t'] = date("d.m.Y H:m:s", $node_info['validTo_time_t']);

      $subnet_info['validFrom_time_t'] = date("d.m.Y H:m:s", $subnet_info['validFrom_time_t']);
      $subnet_info['validTo_time_t'] = date("d.m.Y H:m:s", $subnet_info['validTo_time_t']);

      $return = array('node'=>$node_info, 'subnet'=>$subnet_info);

      return $return;
    } else return false;
  }

  public function writeCCD($node_id) {
    $node_data = Helper::getNodeDataByNodeId($node_id);
    
    $cert_info = $this->getCertificateInfo($node_id);
    $CN = $cert_info['node']['subject']['CN'];
    if (!empty($CN)) {
      $ccd = "./ccd/";
      $handle = fopen($ccd."$CN", "w+");
      fwrite($handle, "ifconfig-push $GLOBALS[net_prefix].$node_data[subnet_ip].$node_data[node_ip] 255.255.255.0");
      fclose($handle);

      $message[] = array("CCD wurde für den Node mit der ID $node_id erstellt.", 1);
    } else {
      $message[] = array("CCD konnte für den Node mit der ID $node_id  nicht erstellt, da der CN leer ist.", 2);
      $message[] = array("Sie müssen erst ein VPN-Zertifikat anlegen!", 2);
    }
    message::setMessage($message);
    return true;
  }

  public function regenerateCCD($subnet_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id
			       FROM nodes
			       WHERE nodes.subnet_id='$subnet_id'");
    while($row = mysql_fetch_assoc($result)) {
      $node_ids[] = $row['id'];
    }
    unset($db);

    foreach ($node_ids as $node_id) {
      $db = new mysqlClass;
      $result = $db->mysqlQuery("SELECT id
				 FROM nodes
			         WHERE id='$node_id' AND vpn_client_cert !='' AND vpn_client_key!='';");
      while($row = mysql_fetch_assoc($result)) {
	$node_ids[] = $row['id'];
      }
      unset($db);
    }

    foreach ($node_ids as $node_id) {
      vpn::writeCCD($node_id);
    }
    
    return true;
  }
  
  public function getVpnConfig($node_id) {
  	$data = Helper::getNodeDataByNodeId($node_id);
  	
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
option cert /etc/client/client.crt
option key /etc/client/client.key

option comp_lzo 1
option verb 3";
  	
  	return $config;
  }

}
?>