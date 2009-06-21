<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  require_once('./lib/classes/core/serviceeditor.class.php');
  
  $serviceeditor = new serviceeditor;

    if ($_GET['section'] == "new") {
	$smarty->assign('message', message::getMessage());
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('node_data', Helper::getNodeInfo($_GET['node_id']));	
	$smarty->display("header.tpl.php");
	$smarty->display("add_service.tpl.php");
	$smarty->display("footer.tpl.php");
    } 
    if ($_GET['section'] == "insert_service") {
      if ($_POST['ips'] > 0) {
	$node_info = Helper::getNodeInfo($_GET['node_id']);
	$range = editingHelper::getFreeIpZone($node_info['subnet_id'], $_POST['ips'], 0);
      } else {
	$range['start'] = "NULL";
	$range['end'] = "NULL";
      }
	$add_result = editingHelper::addNodeTyp($_GET['node_id'], $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $range['start'], $range['end'], $_POST['radius'], $_POST['visible']);
	header('Location: ./service.php?service_id='.$add_result['service_id']);
    }
    if ($_GET['section'] == "edit") {
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('servicedata', Helper::getServiceDataByServiceId($_GET['service_id']));
	$smarty->assign('message', message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("service_edit.tpl.php");
	$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
	$edit_result = $serviceeditor->insertEditService($_GET['service_id'], $_POST['typ'], $_POST['crawler'], $_POST['title'], $_POST['description'], $_POST['radius'], $_POST['visible']);
	header('Location: ./service.php?service_id='.$edit_result['service_id']);
    }
    if ($_GET['section'] == "delete") {
	$node = Helper::getNodeIdByServiceId($_GET['service_id']);
	$serviceeditor->deleteService($_GET['service_id']);
	
	header('Location: ./node.php?id='.$node['id']);
    }
?>