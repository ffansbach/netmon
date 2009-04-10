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
      $node = Helper::getServiceDataByServiceId($_GET['service_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('data', Helper::getServiceDataByServiceId($_GET['service_id']));
      $smarty->assign('expiration', $GLOBALS['expiration']);
      $smarty->assign('get_content', "sslcertificate_new");
    }
    if ($_GET['section'] == "generate") {
      $node = Helper::getServiceDataByServiceId($_GET['service_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $keys = $this->generateKeys($_GET['service_id'], $_POST['organizationalunitname'], $_POST['commonname'], $_POST['emailaddress'], $_POST['privkeypass'], $_POST['numberofdays']);
      if ($keys['return']) {
	$this->saveKeysToDB($_GET['service_id'], $keys['vpn_client_cert'], $keys['vpn_client_key']);
	$this->writeCCD($_GET['service_id']);
	$this->downloadKeyBundle($_GET['service_id']);
	$smarty->assign('message', message::getMessage());
      } else {
	$smarty->assign('message', message::getMessage());
      }
    }
    if ($_GET['section'] == "download") {
      $node = Helper::getServiceDataByServiceId($_GET['service_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $this->downloadKeyBundle($_GET['service_id']);
      $smarty->assign('message', message::getMessage());
    }
    if ($_GET['section'] == "info") {
      $node = Helper::getServiceDataByServiceId($_GET['service_id']);
      usermanagement::isOwner($smarty, $node['user_id']);
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('certificate_data', $this->getCertificateInfo($_GET['service_id']));
      $smarty->assign('get_content', "sslcertificate_info");
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
      $this->writeCCD($_GET['service_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('get_content', "desktop");
    }


  }

  public function generateKeys($service_id, $organizationalunitname, $commonname, $emailaddress, $privkeypass, $expiration) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass
			       FROM services
			       LEFT JOIN nodes on (nodes.id=services.node_id)
			       LEFT JOIN subnets on (subnets.id=nodes.subnet_id)
			       WHERE services.id='$service_id'");
    while($row = mysql_fetch_assoc($result)) {
      $vpn = $row;
    }
    unset($db);

    if (empty($vpn['vpn_server_ca']) OR empty($vpn['vpn_server_cert']) OR empty($vpn['vpn_server_key'])) {
      $message[] = array("Die Zertifikate konnten nicht generiert werden.", 2);
      $message[] = array("Der Subnetverwalter muss erst ein Masterzertifikat für das Subnetz erstellen.", 2);
      message::setMessage($message);
      return false;
    } else {
      //SSL-Errors lehren
      while ($err = openssl_error_string());
      //Keydaten
      $dn = array("countryName" => $GLOBALS['countryName'], "stateOrProvinceName" => $GLOBALS['stateOrProvinceName'], "localityName" => $GLOBALS['localityName'], "organizationName" => $GLOBALS['organizationName'], "organizationalUnitName" => $organizationalunitname, "commonName" => $commonname, "emailAddress" => $emailaddress);

      //Passphrase auf null setzen wenn kein Paswort übergeben wird.
      if (empty($privkeypass) OR $privkeypass=="" OR !isset($privkeypass)) {
	$privkeypass == null;
      }

      $req_key = openssl_pkey_new();
      if(openssl_pkey_export ($req_key, $out_key)) {
	$req_csr  = openssl_csr_new ($dn, $req_key);
	$req_cert = openssl_csr_sign($req_csr, $vpn['vpn_server_cert'], $vpn['vpn_server_key'], $expiration);
	if(openssl_x509_export ($req_cert, $out_cert)) {
	  //  echo "$out_key\n";
	  // echo "$out_cert\n";
	} else    echo "Failed Cert\n";
      } else echo "FailedKey\n";

      return array("return" => true, "vpn_client_cert" => $out_cert, "vpn_client_key" => $out_key);
    }
  }

  public function saveKeysToDB($service_id, $vpn_client_cert, $vpn_client_key) {

    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE services SET
vpn_client_cert = '$vpn_client_cert',
vpn_client_key = '$vpn_client_key'
WHERE id = '$service_id'
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

  public function downloadKeyBundle($service_id) {
    $keys = Helper::getServiceDataByServiceId($service_id);
    if (!empty($keys['vpn_server_ca']) AND !empty($keys['vpn_client_cert']) AND !empty($keys['vpn_client_key'])) {
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
 } else {
      $message[] = array("Es sind nicht genügen Informationen vorhanden um die Keys bereit zu stellen.", 2);
      $message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function getCertificateInfo($service_id) {
    $db = new mysqlClass;
    $keys = Helper::getServiceDataByServiceId($service_id);
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

  public function writeCCD($service_id) {
    $service_data = Helper::getServiceDataByServiceId($service_id);
    
    $cert_info = $this->getCertificateInfo($service_id);
    $CN = $cert_info['node']['subject']['CN'];
    if (!empty($CN)) {
      $ccd = "./ccd/";
      $handle = fopen($ccd."$CN", "w+");
      fwrite($handle, "ifconfig-push $GLOBALS[net_prefix].$service_data[subnet_ip].$service_data[node_ip] 255.255.255.0");
      fclose($handle);

      $message[] = array("CCD wurde für den Service mit der ID $service_id erstellt.", 1);
    } else {
      $message[] = array("CCD konnte für den Service mit der ID $service_id  nicht erstellt, da der CN leer ist.", 2);
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
				 FROM services
			         WHERE node_id='$node_id' AND vpn_client_cert !='' AND vpn_client_key!='';");
      while($row = mysql_fetch_assoc($result)) {
	$service_ids[] = $row['id'];
      }
      unset($db);
    }

    foreach ($service_ids as $service_id) {
      vpn::writeCCD($service_id);
    }
    
    return true;
  }

}
?>