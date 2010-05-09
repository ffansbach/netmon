<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  require_once('./lib/classes/core/interfaceeditor.class.php');
  require_once('./lib/classes/core/interfaces.class.php');
  require_once('./lib/classes/core/project.class.php');

    if ($_GET['section'] == "add") {
		if (UserManagement::checkPermission(32)) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('projects', Project::getProjects());

			$smarty->display("header.tpl.php");
			$smarty->display("interface_add.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert_add") {
		if (UserManagement::checkPermission(32)) {
			Interfaces::addNewInterface();
			header('Location: ./router_config.php?router_id='.$_GET['router_id']);

		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
?>