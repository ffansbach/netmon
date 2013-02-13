<?php

require_once("lib/classes/core/user.class.php");

class Imagemaker {
	public function getImages() {
		try {
			$sql = "SELECT imagemaker_images.id as image_id, imagemaker_images.title, imagemaker_images.create_date,
				       users.id as user_id, users.nickname 
					FROM imagemaker_images
					LEFT JOIN users ON (users.id=imagemaker_images.user_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$images[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $images;
	}

	public function getImageByImageId($image_id) {
		try {
			$sql = "SELECT imagemaker_images.id as image_id, imagemaker_images.title, imagemaker_images.description,
					users.id as user_id, users.nickname 
					FROM imagemaker_images
					LEFT JOIN users ON (users.id=imagemaker_images.user_id)
					WHERE imagemaker_images.id=$image_id";
			$result = DB::getInstance()->query($sql);
			$image = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $image;
	}

	public function getImageConfigs() {
		try {
			$sql = "SELECT imagemaker_configs.id as config_id, imagemaker_configs.title, imagemaker_configs.description,
					users.nickname 
					FROM imagemaker_configs
					LEFT JOIN users ON (users.id=imagemaker_configs.user_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$configs[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $configs;
	}

	public function getImageConfigsByImageId($image_id) {
		try {
			$sql = "SELECT imagemaker_configs.id as config_id, imagemaker_configs.title, imagemaker_configs.description,
					users.nickname 
					FROM imagemaker_configs
					LEFT JOIN users ON (users.id=imagemaker_configs.user_id)
					WHERE imagemaker_configs.image_id=$image_id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$configs[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $configs;
	}

	public function getImageConfigByConfigId($config_id) {
		try {
			$sql = "SELECT imagemaker_configs.id as config_id, imagemaker_configs.title, imagemaker_configs.description,
					users.nickname 
					FROM imagemaker_configs
					LEFT JOIN users ON (users.id=imagemaker_configs.user_id)
					WHERE imagemaker_configs.id=$config_id";
			$result = DB::getInstance()->query($sql);
			$image = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $image;
	}

	public function deleteImage($image_id) {
		$image_data = Imagemaker::getImageByImageId($image_id);
		$user = User::getUserByID($_SESSION['user_id']);

		//If is owning user or if root
		if(UserManagement::isThisUserOwner($image_data['user_id'], $session['user_id']) OR $user['permission']==120) {
			//Get and Delte all corespondig configurations
			$image_configs = Imagemaker::getImageConfigsByImageId($image_id);
			foreach($image_configs as $image_config) {
				Imagemaker::deleteImageConfig($image_config['config_id']);
			}
			
			//Delete Image from Harddrive
			$command = "rm -R ".$_SERVER["DOCUMENT_ROOT"].dirname($_SERVER['PHP_SELF'])."scripts/imagemaker/images/$image_id";
			exec($command);

			//Delete Image from DB
			DB::getInstance()->exec("DELETE FROM imagemaker_images WHERE id='$image_id';");

			$message[] = array("Das Image $image_data[title] wurde entfernt", 1);
			Message::setMessage($message);
		} else {
			$message[] = array("Du darfst dieses Image nicht löschen", 1);
			Message::setMessage($message);
		}
	}

	public function deleteImageConfig($config_id) {
		//Delete Configuration from Harddrive
		$filename = "scripts/imagemaker/configurations/$config_id";
		if(is_file($filename)) {
			unlink($filename);
		}
		//Delete Configuration from DB
		DB::getInstance()->exec("DELETE FROM imagemaker_configs WHERE id='$config_id';");
	}


}

?>