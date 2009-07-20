<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/nodeeditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  
  $nodeeditor = new nodeeditor;
  
    if ($_GET['section'] == "new") {
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('existing_subnets', editingHelper::getExistingSubnetsWithID());
	$smarty->assign('message', message::getMessage());

	$smarty->display("header.tpl.php");
	$smarty->display("node_new.tpl.php");
	$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert") {
      $insert_result = $nodeeditor->insertNewNode($_POST['subnet_id'], $_POST['ips']);
		if ($insert_result['result']) {
			header('Location: ./node.php?id='.$insert_result['node_id']);
		} else {
			header('Location: ./nodeeditor.php?section=new');
		}
    }
    if ($_GET['section'] == "edit") {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$node_data = Helper::getNodeDataByNodeId($_GET['id']);
		$smarty->assign('node_data', $node_data);

		$smarty->display("header.tpl.php");
		$smarty->display("node_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "delete") {
		$nodeeditor->deleteNode($_GET['id']);
		header('Location: ./user.php?id='.$_SESSION['user_id']);
    }
?>