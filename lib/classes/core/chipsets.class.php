<?php

class Chipsets {
	public function newChipset($user_id, $chipset) {
		try {
			DB::getInstance()->exec("INSERT INTO chipsets (user_id, create_date, name)
						 VALUES ('$user_id', NOW(), '$chipset');");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$chipset_id = DB::getInstance()->lastInsertId();

		return array("result"=>true, "id"=>$chipset_id);
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