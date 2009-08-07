<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/nodeeditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  
  $nodeeditor = new nodeeditor;
  
    if ($_GET['section'] == "new") {
		if (usermanagement::checkPermission(4)) {
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
			$smarty->assign('existing_subnets', editingHelper::getExistingSubnetsWithID());
			$smarty->assign('message', message::getMessage());
			
			$smarty->display("header.tpl.php");
			$smarty->display("node_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (usermanagement::checkPermission(4)) {
			$insert_result = $nodeeditor->insertNewNode($_POST['subnet_id'], $_POST['ips']);
			if ($insert_result['result']) {
				header('Location: ./node.php?id='.$insert_result['node_id']);
			} else {
				header('Location: ./nodeeditor.php?section=new');
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen oder editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
	}

    if ($_GET['section'] == "edit") {
		$node_data = Helper::getNodeDataByNodeId($_GET['id']);
		usermanagement::isOwner($smarty, $node_data['user_id']);

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$node_data = Helper::getNodeDataByNodeId($_GET['id']);
		$smarty->assign('node_data', $node_data);

		$smarty->display("header.tpl.php");
		$smarty->display("node_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "delete") {
		$node_data = Helper::getNodeDataByNodeId($_GET['id']);
		usermanagement::isOwner($smarty, $node_data['user_id']);

		$nodeeditor->deleteNode($_GET['id']);
		header('Location: ./user.php?id='.$_SESSION['user_id']);
    }
?>