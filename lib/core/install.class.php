<?php
class Install {
	public function getFileLineByLine($path) {
		$file = file($path);
		foreach($file as $key=>$value) {
			$file[$key] = trim($value);
		}
		return $file;
	}

	public function writeEmptyFileLineByLine($path, $data) {
		$f = fopen($path, "w");
		foreach($data as $value) {
			fwrite($f,$value."\n");
		}
		fclose($f);
	}

	public function checkIfDbIsEmpty() {
		try {
			$sql = "SHOW TABLES;";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$tables[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		if(empty($tables))
			return true;
		else
			return false;
	}

	public function changeConfigSection($section, $file, $configs) {
		$mysql_begin = (array_search($section, $file)+1);
		foreach($configs as $key=>$value) {
			$file[$mysql_begin+$key] = $value;
		}

		return $file;
	}

}
?>