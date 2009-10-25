<?php
class install {
	public function insertDB() {
		try {
			$sql = file_get_contents('./netmon.sql');
			DB::getInstance()->exec($sql);
		}
		catch(PDOException $e) {
			$exception = $e->getMessage();
		}
	}

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
/*		$mysql_config[$mysql_begin+1] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['host'].'";';
		$mysql_config[$mysql_begin+2] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['database'].'";';
		$mysql_config[$mysql_begin+3] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['user'].'";';
		$mysql_config[$mysql_begin+4] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['password'].'";';

		for($i=$mysql_begin+1; $i<=$mysql_begin+4; $i++) {
			$file[$i] = $mysql_config[$i];
		}*/

		return $file;
	}

}
?>