<?php

/**
 * This class is used as a container for static methods that deal operations
 * on chipset objects. Chipsets are Strings that clearly defines a board or chipset of a router. Becaus normal
 * users dont know this cryptic names like "Broadcom BCM4712 chip rev 1" the chipset table acts as a mapper
 * to map the cryptic chipset names like "Broadcom BCM4712 chip rev 1" to a user readable Name like "WRT54G".
 *
 * @package	Netmon
 */
class Chipsets {
	public function newChipset($user_id, $chipset) {
		$chipset = trim($chipset);
		$chipset_check = Chipsets::getChipsetByName($chipset);
		if(empty($chipset_check)) {
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO chipsets (user_id, create_date, name)
								    VALUES (?, NOW(), ?)");
				$stmt->execute(array($user_id, $chipset));
				$chipset_id = DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
				return array("result"=>false);
			}
			return array("result"=>true, "id"=>$chipset_id);
		} else {
			return array("result"=>false);
		}
	}

	public function editChipset($chipset_id, $hardware_name) {
		$hardware_name = trim($hardware_name);
		try {
			$stmt = DB::getInstance()->prepare("UPDATE chipsets SET hardware_name = ? WHERE id = ?");
			$stmt->execute(array($hardware_name, $chipset_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
			$message[] = array("Beim Bearbeiten des Chipsatzes ist ein Fehler aufgetreten.", 2);
			Message::setMessage($message);
			return false;
		}
		$message[] = array("Der Name des Chipsatzes wurde zu $hardware_name geändert.", 1);
		Message::setMessage($message);
		return true;
	}

	public function deleteChipset($chipset_id) {
		$chipset_data = Chipsets::getChipsetById($chipset_id);
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM chipsets WHERE id=?");
			$stmt->execute(array($chipset_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
			return false;
		}
		$message[] = array("Chipset $chipset_data[name] wurde gelöscht.", 1);
		Message::setMessage($message);
		return true;
	}

	public function getChipsetById($chipset_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  * FROM chipsets WHERE id=?");
			$stmt->execute(array($chipset_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getChipsets() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM chipsets ORDER BY name asc");
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getChipsetsWithoutName() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM chipsets WHERE hardware_name='' ORDER BY name asc");
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getChipsetsWithName() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM chipsets
							    WHERE hardware_name!=''
							    ORDER BY name asc");
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getChipsetByName($chipset) {
		$chipset = trim($chipset);
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM chipsets WHERE name=?");
			$stmt->execute(array($chipset));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
}

?>