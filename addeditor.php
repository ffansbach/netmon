<?php
	require_once('runtime.php');
	require_once('lib/classes/core/dns.class.php');
	require_once('lib/classes/core/ip.class.php');
	require_once('lib/classes/core/router.class.php');

	if(isset($_GET['section']) AND $_GET['section']=='upload') {
		if(isset($_FILES['add_small']['tmp_name'])){
			$image_data = getimagesize($_FILES['add_small']['tmp_name']);
			if($image_data[0]<=150 AND $image_data[1]<=50 AND $image_data[2] == 2) {
				if(!move_uploaded_file($_FILES['add_small']['tmp_name'], "./data/adds/".$_GET['router_id']."_add_small.jpg")){
					$message[] = array("Fehler beim Verschieben der Datei.", 2);
					Message::setMessage($message);
				}
			} else {
				$message[] = array("Das Bild ist zu groß oder der Dateityp ist falsch!", 2);
				Message::setMessage($message);
			}
		}

		if(isset($_FILES['add_big']['tmp_name'])){
			// && $_FILES['thefile']['type']=="application/msword"
			if(!move_uploaded_file($_FILES['add_big']['tmp_name'], "./data/adds/".$_GET['router_id']."_add_big.jpg")){
				$message[] = array("Fehler beim Verschieben der Datei.", 2);
				Message::setMessage($message);
			}
		}
	}

	if(isset($_GET['section']) AND $_GET['section']=='allow_adds') {
		if($_POST['adds_allowed']==1) {
			$add_data = Router::getAddData($_GET['router_id']);
			if(!empty($add_data)) {
				$result = DB::getInstance()->exec("UPDATE router_adds SET
										adds_allowed = '1'
								   WHERE router_id = '$_GET[router_id]'");
			} else {
				DB::getInstance()->exec("INSERT INTO router_adds (router_id, adds_allowed)
							      VALUES ('$_GET[router_id]', $_POST[adds_allowed]);");
			}
			$message[] = array("Werbung wurde erlaubt.", 1);
			Message::setMessage($message);
		} else {
			$add_data = Router::getAddData($_GET['router_id']);
			if(!empty($add_data)) {
				$result = DB::getInstance()->exec("UPDATE router_adds SET
										adds_allowed = '0'
								   WHERE router_id = '$_GET[router_id]'");
			}
			$message[] = array("Werbung wurde ausgeschaltet.", 1);
			Message::setMessage($message);
		}
	}



	$smarty->assign('message', Message::getMessage());

	$smarty->assign('add_data', Router::getAddData($_GET['router_id']));

	$smarty->assign('add_small_exists', file_exists("./data/adds/".$_GET['router_id']."_add_small.jpg"));
	$smarty->assign('add_big_exists', file_exists("./data/adds/".$_GET['router_id']."_add_big.jpg"));

	$smarty->display("header.tpl.php");
	$smarty->display("addeditor.tpl.php");
	$smarty->display("footer.tpl.php");

?>