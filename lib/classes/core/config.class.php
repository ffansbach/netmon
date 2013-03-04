<?php

class Config {
	/**
	* Fetches a complete configuration line from the database by name
	* @author  Clemens John <clemens-john@gmx.de>
	* @return array() configuration line
	*/
	public function getConfigLineByName($name) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM config WHERE name=?");
			$stmt->execute(array($name));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $rows;
	}

	/**
	* Fetches a configuration value line from the database by name
	* @author  Clemens John <clemens-john@gmx.de>
	* @return string configuration value
	*/
	public function getConfigValueByName($name) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT value FROM config WHERE name=?");
			$stmt->execute(array($name));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $rows['value'];
	}
	
	/**
	* Writes a configuration line.
	* If the configuration variable (name) does not exists, then create a new one. Otherwise update.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $name string name of the configuration value
	* @param $value string value of the configuration
	* @return int id of the line created or edited
	*/
	public function writeConfigLine($name, $value) {
		//If config line does not exists, create otherwise update
		$config_line = Config::getConfigLineByName($name);
		if(empty($config_line)) {
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO config (name, value, create_date) VALUES (?, ?, NOW())");
				$stmt->execute(array($name, $value));
				return DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		} else {
			try {
				$stmt = DB::getInstance()->prepare("UPDATE config SET value = ? WHERE name = ?");
				$stmt->execute(array($value, $name));
				return $config_line['id'];
			} catch(PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}
	}
}

?>
