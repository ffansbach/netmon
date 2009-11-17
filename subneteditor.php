<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/subneteditor.class.php');
  require_once('./lib/classes/core/subnetcalculator.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');

/*print "<form method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	IP Mask or CIDR: <input type=\"text\" name=\"my_net_info\" size=\"32\" maxlength=\"32\">
</form>";
*/

if(isset($_POST['my_net_info'])) {
$my_net_info = subnetCalculator::checkIfInputIsValidAndCIDR($_POST['my_net_info']);
if(!$my_net_info){
	echo "Invalid Input or Input is not CIDR";
}



	$dq_host = subnetCalculator::getDqHost($my_net_info);
	if (!$dq_host) {
		echo "Not a CIDR Adress";
	}

	$cdr_nmask = subnetCalculator::getCdrNmask($my_net_info);
	if (!$cdr_nmask) {
		echo "Invalid CIDR value. Try an integer 0 - 32.";
	}


	$bin_nmask=subnetCalculator::cdrtobin($cdr_nmask);
	$bin_wmask=subnetCalculator::binnmtowm($bin_nmask);


if (!subnetCalculator::checkIfDqHostIsValid($dq_host)) {
	echo "Invalid IP Address DQ-Host";
}

$bin_host=subnetCalculator::getBinHost($dq_host);
$bin_bcast=subnetCalculator::getBinBcast($dq_host, $cdr_nmask);
$bin_net=subnetCalculator::getBinNet($dq_host, $cdr_nmask);
$bin_first=subnetCalculator::getBinFirstIP($dq_host, $cdr_nmask);
$bin_last=subnetCalculator::getBinLastIP($dq_host, $cdr_nmask);
$host_total=subnetCalculator::getHostsTotal($cdr_nmask);



$subnet_class = subnetCalculator::getSubnetClass($bin_net, $cdr_nmask);
$dotbin_net = $subnet_class['dotbin_net'];
$special = $subnet_class['special'];
$class = $subnet_class['class'];

// Print Results
subnetCalculator::tr('Address:',"<font color=\"blue\">$dq_host</font>",
	'<font color="brown">'.subnetCalculator::dotbin($bin_host,$cdr_nmask).'</font>');
subnetCalculator::tr('Netmask:','<font color="blue">'.subnetCalculator::bintodq($bin_nmask)." = $cdr_nmask</font>",
	'<font color="red">'.subnetCalculator::dotbin($bin_nmask, $cdr_nmask).'</font>');
subnetCalculator::tr('Wildcard:', '<font color="blue">'.subnetCalculator::bintodq($bin_wmask).'</font>',
	'<font color="brown">'.subnetCalculator::dotbin($bin_wmask, $cdr_nmask).'</font>');
subnetCalculator::tr('Network:', '<font color="blue">'.subnetCalculator::bintodq($bin_net).'</font>',
	"<font color=\"brown\">$dotbin_net</font>","<font color=\"Green\">(Class $class)</font>");
subnetCalculator::tr('Broadcast:','<font color="blue">'.subnetCalculator::bintodq($bin_bcast).'</font>',
	'<font color="brown">'.subnetCalculator::dotbin($bin_bcast, $cdr_nmask).'</font>');
subnetCalculator::tr('HostMin:', '<font color="blue">'.subnetCalculator::bintodq($bin_first).'</font>',
	'<font color="brown">'.subnetCalculator::dotbin($bin_first, $cdr_nmask).'</font>');
subnetCalculator::tr('HostMax:', '<font color="blue">'.subnetCalculator::bintodq($bin_last).'</font>',
	'<font color="brown">'.subnetCalculator::dotbin($bin_last, $cdr_nmask).'</font>');
@subnetCalculator::tr('Hosts/Net:', '<font color="blue">'.$host_total.'</font>', "$special");

}




  $subneteditor = new subneteditor;

    if ($_GET['section'] == "new") {
		if (usermanagement::checkPermission(32)) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('existing_subnets', editingHelper::getExistingSubnets());
			$smarty->assign('subnets_with_defined_vpnserver', $subneteditor->getSubnetsWithDefinedVpnserver());
			$smarty->display("header.tpl.php");
			$smarty->display("subnet_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (usermanagement::checkPermission(32)) {
			$checkdata = $subneteditor->checkSubnetData();
			if ($checkdata) {
				$subnet_result = $subneteditor->createNewSubnet($checkdata);
				header('Location: ./subnet.php?id='.$subnet_result['subnet_id']);
			} else {
				header('Location: ./subneteditor.php?section=new');
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "edit") {
		if (usermanagement::checkPermission(32)) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
			$smarty->assign('existing_subnets', editingHelper::getExistingSubnets());
			$smarty->assign('subnets_with_defined_vpnserver', $subneteditor->getSubnetsWithDefinedVpnserver());
			$subnetdata = Helper::getSubnetDataBySubnetID($_GET['id']);
			$smarty->assign('subnet_data', $subnetdata);
//			$smarty->assign('avalailable_subnets', editingHelper::getFreeSubnetsPlusPredefinedSubnet($subnetdata['subnet_ip']));
			$smarty->display("header.tpl.php");
			$smarty->display("subnet_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "update") {
		if (usermanagement::checkPermission(32)) {
			if ($subneteditor->checkSubnetData($_POST['subnet'], $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['vpn_cacrt'])) {
				$subneteditor->updateSubnet($_POST['subnet'],  $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['title'], $_POST['description'], $_POST['longitude'], $_POST['latitude'], $_POST['radius'], $POST['vpn_cacrt']);
				header('Location: ./subnet.php?id='.$_GET['id']);
			} else {
				header('Location: ./subneteditor.php?section=edit&id='.$_GET['id']);
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
	}
    if ($_GET['section'] == "delete") {
		if (usermanagement::checkPermission(32)) {
			if ($_POST['delete'] == "true") {
				$subneteditor->deleteSubnet($_GET['subnet_id']);
				header('Location: ./user.php?id='.$_SESSION['user_id']);
			} else {
				$message[] = array("Sie müssen \"Ja\" anklicken um das Subnets zu löschen.", 2);
				message::setMessage($message);
				header('Location: ./subneteditor.php?section=edit&id='.$_GET['id']);
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
?>