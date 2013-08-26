<?php
require_once('runtime.php');
require_once(ROOT_DIR.'/lib/core/login.class.php');
require_once(ROOT_DIR.'/lib/core/user.class.php');
require_once(ROOT_DIR.'/lib/core/UserRememberMe.class.php');

if($_SESSION['last_page'] != $_SESSION['current_page'] AND empty($_SESSION['redirect_after_login_url'])) {
	$_SESSION['redirect_after_login_url'] = $_SESSION['last_page'];
}

if ($_GET['section']=="login") {
	$smarty->assign('message', Message::getMessage());
	
	$smarty->display("header.tpl.html");
	$smarty->display("login.tpl.html");
	$smarty->display("footer.tpl.html");
} elseif ($_GET['section']=="login_send") {
	//check if login is successfull and if not, go back to loginpage with errormessage
	if(isset($_POST['nickname']) AND isset($_POST['password'])) {
		$user_data = User_old::getUserByNickname($_POST['nickname']);
		$phpass = new PasswordHash(8, false);
		if(empty($user_data) OR !$phpass->CheckPassword($_POST['password'], $user_data['password'])) {
			$messages[] = array("Passwort oder Benutzername stimmen nicht.", 2);
			Message::setMessage($messages);
			header('Location: login.php');
			die();
		}
	} elseif(isset($_POST['openid_identifier']) OR isset($_GET['openid_mode'])) {
			$status = "";
			if (!empty($_POST['openid_identifier'])) { //login initiation			
				$consumer = new Zend_OpenId_Consumer();
				if (!$consumer->login($_POST['openid_identifier'], "login.php?section=login_send&remember=".$_POST['remember'])) {
					$status = "OpenID Login fehlgeschlagen.";
				}
			} else if (isset($_GET['openid_mode'])) { //login result from openid server
				if ($_GET['openid_mode'] == "id_res") {
					$consumer = new Zend_OpenId_Consumer();
					if ($consumer->verify($_GET, $id)) {
						$user_data = User_old::getUserByOpenID($id);
						if(empty($user_data)) {
							$messages[] = array("Mit dieser Open-ID ist kein gültiger Benutzer verknüpft.", 2);
							Message::setMessage($messages);
							header('Location: login.php');
							die();
						}
					} else {
						$messages[] = array("Diese Identität ist nicht gültig.", 2);
						Message::setMessage($messages);
						header('Location: login.php');
						die();
					}
				} else if ($_GET['openid_mode'] == "cancel") {
					$messages[] = array("Der Loginprozess wurde abgebrochen.", 2);
					$messages[] = array("Bitte versuche es erneut.", 2);
					Message::setMessage($messages);
					header('Location: login.php');
					die();
				}
			} else {
				$messages[] = array("Es ist ein Problem aufgetreten.", 2);
				Message::setMessage($messages);
				header('Location: login.php');
				die();
			}
	} else {
		$messages[] = array("Sie müssen einen Benutzernamen sowie ein Passwort oder eine Open-ID angeben.", 2);
		Message::setMessage($messages);
		header('Location: login.php');
		die();
	}
	
	//at this point the user was logged in successfully

	//store the session-id to the database
	$stmt = DB::getInstance()->prepare("UPDATE users SET session_id = ? WHERE id = ?");
	$stmt->execute(array(session_id(), $user_data['id']));
	//store the 
	$_SESSION['user_id'] = $user_data['id'];
	
	//set remember me coockie if the user requested this
	if($_POST['remember'] OR $_GET['remember']) {
		//http://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice/	
		//generate long random password
		$random_password = Helper::randomPassword(56);
		//hash the random password like a normal password
		$phpass = new PasswordHash(8, false);
		$random_password_hash = $phpass->HashPassword($random_password);
		
		$user_remember_me = new UserRememberMe(false, (int)$user_data['id'], $random_password_hash);
		$user_remember_me->store();
		setcookie("remember_me", $user_data['id'].",".$random_password, time() + 60*60*24*14);
	}
	
	$messages[] = array("Herzlich willkommen ".$user_data['nickname'], 1);
	Message::setMessage($messages);
	
	//redirect the user to the page he visteted previously or to his userpage
	if(!empty($_SESSION['redirect_after_login_url']) AND
			strpos($_SESSION['redirect_after_login_url'], "register")===false AND
			strpos($_SESSION['redirect_after_login_url'], "login")===false) {
		header("Location: $_SESSION[redirect_after_login_url]");
	} else {
		header('Location: user.php?user_id='.$_SESSION['user_id']);
	}
}
elseif($_GET['section']=="logout") {
	Login::user_logout();
	header('Location: index.php');
} else {
	header('Location: login.php?section=login');
}

?>