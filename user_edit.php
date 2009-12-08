<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/usermanagement.class.php');
  require_once('./lib/classes/core/user.class.php');
  
  $User = new User;

  if ($_GET['section'] == "edit") {
    $smarty->assign('user', Helper::getUserByID($_GET['id']));

	//Only owner and Root can access this site.
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['id']))
		UserManagement::denyAccess();

    $smarty->assign('is_root', UserManagement::checkPermission(64, $_SESSION['user_id']));

	$permissions = UserManagement::getEditablePermissionsWithNames();
	foreach ($permissions as $key=>$permission) {
		$permission['dual'] = pow(2,$permission['permission']);
		$permissions[$key]['check'] = UserManagement::checkPermission($permission['dual'], $_GET['id']);
		$permissions[$key]['dual'] = $permission['dual'];
	}
    $smarty->assign('permissions', $permissions);

    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("user_edit.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif ($_GET['section'] == "insert_edit") {
    UserManagement::isOwner($smarty, $_SESSION['user_id']);
    $smarty->assign('user', Helper::getUserByID($_GET['id']));
    if ($User->userInsertEdit()) {
      header('Location: user.php?id='.$_SESSION['user_id']);
    } else {
      header('Location: user_edit.php?section=edit&id='.$_SESSION['user_id']);
    }
  } elseif ($_GET['section'] == "delete") {
    UserManagement::isOwner($smarty, $_SESSION['user_id']);
    if ($User->userDelete($_SESSION['user_id'])) {
      header('Location: portal.php');
    } else {
      header('Location: user_edit.php?section=edit&id='.$_SESSION['user_id']);
    }
  }
?>