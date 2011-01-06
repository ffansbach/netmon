<?php

require_once('runtime.php');
require_once('lib/classes/core/project.class.php');

$projectlist = Project::getProjectList();

$smarty->assign('projectlist', $projectlist);

$smarty->display("header.tpl.php");
$smarty->display("projectlist.tpl.php");
$smarty->display("footer.tpl.php");

?>