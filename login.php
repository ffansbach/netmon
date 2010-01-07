<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/login.class.php');
  require_once('./lib/classes/core/user.class.php');
  require_once('lib/classes/extern/class.openid.php');

  $Login = new Login;
  $message = new message;

  if ($_GET['section']=="login") {
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif ($_GET['section']=="login_send" AND $Login->user_login($_POST['nickname'], $_POST['password'], $_POST['remember'])) {
	if(isset($_SESSION['redirect_url']))
		header('Location: '.$_SESSION['redirect_url']);
	else
		header('Location: desktop.php');
  }
  elseif ($_GET['section']=="openid_login_send"){
    if($_POST['remember']) {
		$_SESSION['openid_login_remember']=$_POST['openid_url'];
    }
    // EXAMPLE
    if ($_POST['openid_action'] == "login"){ // Get identity from user and redirect browser to OpenID Server
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_POST['openid_url']);
	$openid->SetTrustRoot('http://' . $_SERVER["HTTP_HOST"]);
	$openid->SetRequiredFields(array('email','fullname'));
	$openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
	if ($openid->GetOpenIDServer()){
		$openid->SetApprovedURL('http://'.$_SERVER["HTTP_HOST"].'/login.php?section=openid_login_send');  	// Send Response from OpenID server to this script
		$openid->Redirect(); 	// This will redirect user to OpenID Server
	}else{
		$error = $openid->GetError();
		$messages[] = array("Open-ID Login Fehlgeschlagen", 2);		
		$messages[] = array("ERROR CODE: " . $error['code'], 2);
		$messages[] = array("ERROR DESCRIPTION: " . $error['description'], 2);
		Message::setMessage($messages);
		header('Location: login.php');
	}
    } else if($_GET['openid_mode'] == 'id_res'){ 	// Perform HTTP Request to OpenID server to validate key
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_GET['openid_identity']);
	$openid_validation_result = $openid->ValidateWithServer();
	if ($openid_validation_result == true){ 		// OK HERE KEY IS VALID
		$short_openid = substr($_GET['openid_identity'], 7);
		if (User::checkIfOpenIdHasUser($short_openid)) {
			 login::user_login (false, false, $_SESSION['openid_login_remember'], false, $short_openid);
			 header('Location: desktop.php');
		} else {
			$messages[] = array("Ihre Open-ID ist mit keinem Benutzerkonto verknüpft.", 0);
			$messages[] = array("Wenn Sie möchten, können sie jetzt ein Benutzerkonto erstellen, dass mit Ihrer Open-ID verknüpft ist.", 0);
			$messages[] = array("Wenn Sie bereits ein Benutzerkonto besitzen, können Sie Ihr Konto unter <i>Benutzer ändern</i> mit Ihrer Open-ID verknüpfen.", 0);
			
			Message::setMessage($messages);
			header("Location: register.php?openid=$short_openid");
		}
	}else if($openid->IsError() == true){			// ON THE WAY, WE GOT SOME ERROR
		$error = $openid->GetError();
		$messages[] = array("Open-ID Login Fehlgeschlagen", 2);		
		$messages[] = array("ERROR CODE: " . $error['code'], 2);
		$messages[] = array("ERROR DESCRIPTION: " . $error['description'], 2);
		Message::setMessage($messages);
		header('Location: login.php');
	}else{											// Signature Verification Failed
		$messages[] = array("Open-ID Login Fehlgeschlagen", 2);		
		$messages[] = array("INVALID AUTHORIZATION", 2);
		Message::setMessage($messages);
		header('Location: login.php');
	}
    }else if ($_GET['openid_mode'] == 'cancel'){ // User Canceled your Request
		$messages[] = array("Open-ID Login Fehlgeschlagen", 2);		
		$messages[] = array("USER CANCELED REQUEST", 2);
		Message::setMessage($messages);
		header('Location: login.php');
    }
} elseif($_GET['section']=="logout") {
    $Login->user_logout($message);
    header('Location: portal.php');
  } else {
    $_GET['section'] = "login";
    require('login.php');
  }
?>