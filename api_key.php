<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/ApiKeyList.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	
	if($_GET['section'] == "insert_add") {
		//add new api key
		do {
			$api_key = new ApiKey(false, ApiKey::generateApiKey(), (int)$_GET['object_id'], $_GET['object_type'], $_POST['description']);
			$api_key_id = $api_key->store();
		} while(!$api_key_id);
		$message[] = array("Es wurde ein neuer API-Key ".$api_key->getApiKey()." generiert und gespeichert.", 1);
		Message::setMessage($message);
		header('Location: ./api_key_list.php?object_id='.$_GET['object_id'].'&object_type='.$_GET['object_type']);
	} elseif($_GET['section'] == "delete") {
		$api_key = new ApiKey((int)$_GET['api_key_id']);
		$api_key->fetch();
		$message[] = array("Der API-Key ".$api_key->getApiKey()." wurde gelöscht.", 1);
		$api_key->delete();
		Message::setMessage($message);
		header('Location: ./api_key_list.php?object_id='.$_GET['object_id'].'&object_type='.$_GET['object_type']);
	}

?>