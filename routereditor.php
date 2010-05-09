<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/routereditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
    
    if ($_GET['section'] == "new") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('chipsets', Helper::getChipsets());
			
			$smarty->display("header.tpl.php");
			$smarty->display("router_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (UserManagement::checkPermission(4)) {
			$insert_result = RouterEditor::insertNewRouter();

			header('Location: ./router_config.php?router_id='.$insert_result['router_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

?>