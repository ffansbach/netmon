<?php
try {
	require_once("../runtime.php");
	require_once(ROOT_DIR.'/config/config.local.inc.php');
	require_once(ROOT_DIR.'/lib/core/db.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');

	require_once(ROOT_DIR.'/lib/extern/node_list.php');
	require_once(ROOT_DIR.'/lib/extern/node.php');

	try
	{
		$query = "SELECT r.id,
				r.hostname,
				r.latitude,
				r.longitude,
				u.id as u_id,
				u.nickname,
				s.status,
				s.client_count,
				UNIX_TIMESTAMP(r.update_date) as last_update,
				UNIX_TIMESTAMP(max(cr.crawl_date)) as last_seen
				FROM
					routers AS r
					JOIN crawl_routers AS s ON r.id = s.router_id
					JOIN users AS u ON r.user_id = u.id
					LEFT JOIN crawl_routers AS cr ON cr.router_id = r.id AND cr.status = 'online'
				WHERE
					s.crawl_cycle_id = :crawl_cycle_id
				GROUP BY r.id";
		$stmt = DB::getInstance()->prepare($query);

		$lastCycleID = (int)crawling::getLastEndedCrawlCycle()['id'];
		$stmt->bindParam(':crawl_cycle_id', $lastCycleID, PDO::PARAM_INT);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		echo $e->getTraceAsString();
	}

	// no that we have something - compule the data
	$nodeList = new nodeList();

	$nodeList->setCommunityName('FreifunkEmskirchen');
	$nodeList->setWebsite('http://www.freifunk-emskirchen.de');
	$nodeList->setCommunityFile('https://raw.githubusercontent.com/ffansbach/community-files/master/emskirchen.json');

	foreach($result as $resultNode)
	{
		$node = new node($resultNode['id'], $resultNode['hostname']);

		$last_seen = $resultNode['last_update'];

		if(!empty($resultNode['last_seen']) && ($resultNode['last_seen'] > $resultNode['last_update']))
		{
			$last_seen = $resultNode['last_seen'];
		}

		$node->setType('AccessPoint');
		$node->setHref('https://netmon.freifunk-emskirchen.de/router.php?router_id='.$resultNode['id']);

		$node->setStatus(
			($resultNode['status'] == 'online'),
			$resultNode['client_count'],
			$last_seen
		);

		$node->setGeo(
			$resultNode['latitude'],
			$resultNode['longitude']
		);

		$node->setUserId($resultNode['u_id']);
		$nodeList->addPerson(
			$resultNode['u_id'],
			$resultNode['nickname'],
			'https://netmon.freifunk-emskirchen.de/user.php?user_id='.$resultNode['u_id']
		);

		$nodeList->addNode($node->getNode());
	}

	try
	{

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60*15)));
		header('Content-type: application/json');
		echo json_encode($nodeList->getList(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

	}
	catch (Exception $e)
	{
		echo 'Unable to create nodelist: ',  $e->getMessage(), "\n";
	}

}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}