<?php
/**
 * delivers a json containing the community-file
 *
 * this reads the provided community-json, sets the current "nodecount"
 * and updates the "lastchange" field if needed.
 */
try {

	require_once("../runtime.php");
	require_once(ROOT_DIR.'/lib/core/db.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');

	$configSavedCommunityJson = ConfigLine::configByName('community_json');

	if(empty($configSavedCommunityJson) || empty(json_decode($configSavedCommunityJson)))
	{
		// no json in config - exit
		return;
	}

	try
	{
		$query = "SELECT COUNT(*) AS count FROM routers";

		$stmt = DB::getInstance()->prepare($query);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		echo $e->getTraceAsString();
	}

	$community = json_decode($configSavedCommunityJson);
	$nodeCount = (int)$result[0]['count'];
	$community->state->nodes = $nodeCount;

	$lastNodeCount = ConfigLine::configByName('last_node_count');

	$now = date('c');

	if($nodeCount != $lastNodeCount)
	{
		// nodecount has changed 
		// update "lastchange-field"
		$community->state->lastchange = $now;
		Config::writeConfigLine('last_node_count', $nodeCount);
		Config::writeConfigLine('last_node_count_time', $now);
	}
	else
	{
		$lastTime = ConfigLine::configByName('last_node_count_time');

		if(!$lastTime)
		{
			// there has never been a setting stored - store and use current datettime
			Config::writeConfigLine('last_node_count_time', $now);
			$lastTime = $now;
		}

		// write to json
		$community->state->lastchange = $lastTime;
	}

	try
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60*15)));
		header('Content-type: application/json');
		echo json_encode($community, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}
	catch (Exception $e)
	{
		echo json_encode('Unable to create communityfile: ',  $e->getMessage(), "\n");
	}

}
catch (Exception $e)
{
	echo json_encode('Caught exception: ',  $e->getMessage(), "\n");
}
