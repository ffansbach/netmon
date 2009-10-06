<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/vpn.class.php');
  require_once('./lib/classes/core/helper.class.php');
  
  $vpn = new vpn;

    if ($_GET['section'] == "new") {
      $ip = Helper::getIpDataByIpId($_GET['ip_id']);
      usermanagement::isOwner($smarty, $ip['user_id']);
      $smarty->assign('message', message::getMessage());
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('data', Helper::getIpDataByIpId($_GET['ip_id']));
      $smarty->assign('expiration', $GLOBALS['expiration']);
      $smarty->assign('get_content', "sslcertificate_new");
     	$smarty->display("header.tpl.php");
		$smarty->display("sslcertificate_new.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "generate") {
      $ip = Helper::getIpDataByIpId($_GET['ip_id']);
      usermanagement::isOwner($smarty, $ip['user_id']);
      $keys = $vpn->generateKeys($_GET['ip_id'], $_POST['organizationalunitname'], $_POST['commonname'], $_POST['emailaddress'], $_POST['privkeypass'], $_POST['privkeypass_chk'], $_POST['numberofdays']);
      if ($keys['return']) {
		$vpn->saveKeysToDB($_GET['ip_id'], $keys['vpn_client_cert'], $keys['vpn_client_key']);
		$vpn->writeCCD($_GET['ip_id']);
		$vpn->downloadKeyBundle($_GET['ip_id']);
		$smarty->assign('message', message::getMessage());
      } else {
	$smarty->assign('message', message::getMessage());
        $smarty->assign('get_content', "sslcertificate_new");
      }
    }
    if ($_GET['section'] == "download") {
      $ip = Helper::getIpDataByIpId($_GET['ip_id']);
      usermanagement::isOwner($smarty, $ip['user_id']);
      $vpn->downloadKeyBundle($_GET['ip_id']);
      $smarty->assign('message', message::getMessage());
    }
    if ($_GET['section'] == "info") {
      $ip = Helper::getIpDataByIpId($_GET['ip_id']);
      usermanagement::isOwner($smarty, $ip['user_id']);
    	if (!empty($ip['vpn_server_ca']) AND !empty($ip['vpn_client_cert']) AND !empty($ip['vpn_client_key'])) {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
    	  	$smarty->assign('vpn_config', $vpn->getVpnConfig($_GET['ip_id']));
      		$smarty->assign('certificate_data', $vpn->getCertificateInfo($_GET['ip_id']));
       		$smarty->display("header.tpl.php");
		$smarty->display("sslcertificate_info.tpl.php");
		$smarty->display("footer.tpl.php");
	    } else {
	        $message[] = array("Es sind nicht genügen Informationen vorhanden um die Keys bereit zu stellen.", 2);
    	  	$message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
      		message::setMessage($message);
		header('Location: ./ip.php?id='.$_GET['ip_id']);

    	}
    }

    if ($_GET['section'] == "regenerate_ccd_subnet") {
      $smarty->assign('subnets', Helper::getSubnetsByUserId($_SESSION['user_id']));
      $smarty->display("header.tpl.php");
      $smarty->display("regenerate_ccd_subnet.tpl.php");
      $smarty->display("footer.tpl.php");
    } 
    if ($_GET['section'] == "insert_regenerate_ccd_subnet") {
      vpn::regenerateCCD($_POST['subnet_id']);
      header('Location: subnet.php?id='.$_POST['subnet_id']);
    }

    if ($_GET['section'] == "insert_regenerate_ccd") {
      $vpn->writeCCD($_GET['ip_id']);
      header('Location: ip.php?id='.$_GET['ip_id']);
    }
    if ($_GET['section'] == "insert_delete_ccd") {
      $vpn->deleteCCD($_GET['ip_id']);
      header('Location: ip.php?id='.$_GET['ip_id']);
    }
?>