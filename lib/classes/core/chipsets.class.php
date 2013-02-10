<?php

class Chipsets {
	public function newChipset($user_id, $chipset) {
		$chipset = trim($chipset);
		$chipset_check = Chipsets::getChipsetByName($chipset);
		if(empty($chipset_check)) {
			try {
				DB::getInstance()->exec("INSERT INTO chipsets (user_id, create_date, name)
							VALUES ('$user_id', NOW(), '$chipset');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			$chipset_id = DB::getInstance()->lastInsertId();
			return array("result"=>true, "id"=>$chipset_id);
		} else {
			return array("result"=>false);
		}
	}

	public function editChipset($chipset_id, $hardware_name) {
		$hardware_name = trim($hardware_name);
		$result = DB::getInstance()->exec("UPDATE chipsets SET
							hardware_name = '$hardware_name'
						WHERE id = '$chipset_id'");
		if ($result>0) {
			$message[] = array("Der Name der Hardware wurde geändert.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Fehler!", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function deleteChipset($chipset_id) {
		$chipset_data = Chipsets::getChipsetById($chipset_id);
		//Delete the router itself
		DB::getInstance()->exec("DELETE FROM chipsets WHERE id='$chipset_id';");

		$message[] = array("Chipset $chipset_data[name] wurde gelöscht.", 1);
		Message::setMessage($message);
		return true;
	}

	public function getChipsetById($chipset_id) {
		try {
			$sql = "SELECT  *
					FROM chipsets
				WHERE id='$chipset_id'";
			$result = DB::getInstance()->query($sql);
			$chipset = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $chipset;
	}

	public function getChipsets() {
		try {
			$sql = "SELECT  *
					FROM chipsets
					ORDER BY name asc";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$chipsets[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $chipsets;
	}

	public function getChipsetsWithoutName() {
		try {
			$sql = "SELECT  *
					FROM chipsets
					WHERE hardware_name=''
					ORDER BY name asc";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$chipsets[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $chipsets;
	}

	public function getChipsetsWithName() {
		try {
			$sql = "SELECT  *
					FROM chipsets
					WHERE hardware_name!=''
					ORDER BY name asc";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$chipsets[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $chipsets;
	}

	public function getChipsetByName($chipset) {
		$chipset = trim($chipset);
		try {
			$sql = "SELECT  *
					FROM chipsets
					WHERE name='$chipset'";
			$result = DB::getInstance()->query($sql);
			$chipset = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $chipset;
	}
}

?>