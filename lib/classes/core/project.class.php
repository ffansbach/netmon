<?php

require_once('./lib/classes/core/subnetcalculator.class.php');

class Project  {
	public function getProjects() {
		try {
			$sql = "SELECT  *
					FROM projects";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$projects[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $projects;
	}

	public function getProjectData($project_id) {
		try {
			$sql = "SELECT *
				FROM projects
				WHERE id='$project_id'";
			$result = DB::getInstance()->query($sql);
			$project = $result->fetch(PDO::FETCH_ASSOC);
			if($project['ipv']=='ipv4') {
				$project['ipv4_netmask_dot'] = SubnetCalculator::getNmask($project['ipv4_netmask']);
				$project['ipv4_bcast'] = SubnetCalculator::getDqBcast($project['ipv4_host'], $project['ipv4_netmask']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $project;
	}
}

?>