<?php
	require_once('runtime.php');
	require_once('lib/core/helper.class.php');
	require_once('lib/core/register.class.php');
	require_once('lib/core/user_old.class.php');
	
	$register = new register;
	
	if (empty($_POST['email'])) {
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("resend_activation_mail.tpl.html");
		$smarty->display("footer.tpl.html");
	} else {
		$user = User_old::getUserByEmail($_POST['email']);
		if ($user) {
			if ($user['activated']!="0") {
				if(empty($user['openid'])) {
					$new_password = Helper::randomPassword(8);
					$register->setNewPassword($new_password, $user['id']);
				}
				$register->sendRegistrationEmail($user['email'], $user['nickname'], $new_password, $user['activated'], strtotime($user['create_date']), $user['openid']);
				header('Location: ./login.php');
			} else {
				$message[] = array("Der Benutzer mit der Emailadresse $_POST[email] wurde bereits freigeschaltet!", 2);
				Message::setMessage($message);
				header('Location: ./login.php');
			}
		} else {
			$message[] = array("Der Benutzer mit der Emailadresse $_POST[email] existiert nicht!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>