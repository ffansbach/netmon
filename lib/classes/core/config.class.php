<?php

class Config {
	public function getConfigLineByName($name) {
		try {
			$sql = "SELECT  *
					FROM config
					WHERE name='$name'";
			$result = DB::getInstance()->query($sql);
			$config = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $config;
	}
}

?>