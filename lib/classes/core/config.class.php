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

	public function getConfigValueByName($name) {
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
		
		return $config['value'];
	}

	public function writeConfigLine($name, $value) {
		// just ever insert, and on error update
		//If config line does not exists, create
		try {
			DB::getInstance()->exec("INSERT INTO config (name, value, create_date)
						 VALUES ('$name', '$value', NOW());");
			$config_line['id'] = DB::getInstance()->lastInsertId();
		}
		catch(PDOException $e) {
			//If config line exists, update
			try {
				$result = DB::getInstance()->exec("UPDATE config SET
									  value='$value'
								   WHERE name = '$name'");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

		return $config_line['id'];
	}
}

?>
