<?php

require_once('runtime.php');
require_once('lib/classes/core/project.class.php');

$project_data = Project::getProjectData($_GET['project_id']);
echo "<pre>";
print_r($project_data);
echo "</pre>";

$smarty->assign('project_data', $project_data);

$smarty->display("header.tpl.php");
$smarty->display("project.tpl.php");
$smarty->display("footer.tpl.php");

?>