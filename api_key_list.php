<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/ApiKeyList.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if(isset($_GET['object_type']) AND isset($_GET['object_id'])) {
		if($_GET['object_type'] == "router") {
			$router = new Router((int)$_GET['object_id']);
			$router->fetch();
			//Root and owning user can see api keys
			if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $router->getUserId())) {
				$api_key_list = new ApiKeyList((int)$_GET['object_id'], 'router');
				$smarty->assign('api_key_list', $api_key_list->getList());
				
				$smarty->display("header.tpl.html");
				$smarty->display("api_key_list.tpl.html");
				$smarty->display("footer.tpl.html");
			} else {
				Permission::denyAccess(PERM_ROOT, (int)$router->getUserId());
			}
		} elseif($_GET['object_type'] == "user") {
			$user = new User((int)$_GET['object_id']);
			$user->fetch();
			//Root and owning user can see api keys
			if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $user->getUserId())) {
				$api_key_list = new ApiKeyList((int)$_GET['object_id'], 'user');
				$smarty->assign('api_key_list', $api_key_list->getList());
				
				$smarty->display("header.tpl.html");
				$smarty->display("api_key_list.tpl.html");
				$smarty->display("footer.tpl.html");
			} else {
				Permission::denyAccess(PERM_ROOT, (int)$user->getUserId());
			}
		}
	} elseif(Permission::checkPermission(PERM_ROOT)) {

	} else {
		//no permission to access this site
	}
?>