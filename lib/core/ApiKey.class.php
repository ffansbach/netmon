<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	
	class ApiKey extends Object {
		private $api_key = "";
		private $object_id = 0;
		private $object_type = "";
		private $description = "";
		
		public function __construct($id=false, $api_key=false, $object_id=false, $object_type=false, $description=false,
									$create_date=false, $update_date=false) {
			$this->setId($id);
			$this->setApiKey($api_key);
			$this->setObjectId($object_id);
			$this->setObjectType($object_type);
			$this->setDescription($description);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM api_keys
													WHERE
														(id = :id OR :id=0) AND
														(api_key = :api_key OR :api_key='') AND
														(object_id = :object_id OR :object_id=0) AND
														(object_type = :object_type OR :object_type='') AND
														(description = :description OR :description='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':id', $this->getId(), PDO::PARAM_INT);
				$stmt->bindParam(':api_key', $this->getApiKey(), PDO::PARAM_STR);
				$stmt->bindParam(':object_id', $this->getObjectId(), PDO::PARAM_INT);
				$stmt->bindParam(':object_type', $this->getObjectType(), PDO::PARAM_STR);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setId((int)$result['id']);
				$this->setApiKey($result['api_key']);
				$this->setObjectId($result['object_id']);
				$this->setObjectType($result['object_type']);
				$this->setDescription($result['description']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE api_keys SET
																api_key = ?,
																object_id = ?,
																object_type = ?,
																description = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getApiKey(), $this->getObjectId(), $this->getObjectType(),
										 $this->getDescription(), $this->getId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getApiKey() != "" AND $this->getObjectId()!=0 AND $this->getObjectType()!="") {
				$tmp_api_key = new ApiKey(false, $this->getApiKey());
				if(!$tmp_api_key->fetch()) {
					try {
						$stmt = DB::getInstance()->prepare("INSERT INTO api_keys (api_key, object_id, object_type, description, create_date, update_date)
															VALUES (?, ?, ?, ?, NOW(), NOW())");
						$stmt->execute(array($this->getApiKey(), $this->getObjectId(), $this->getObjectType(),
											 $this->getDescription()));
						return DB::getInstance()->lastInsertId();
					} catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
				}
			}
			return false;
		}
		
		public function delete() {
			if($this->getId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM api_keys WHERE id=?");
					$stmt->execute(array($this->getId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				return false;
			}
		}
		
		public function setApiKey($api_key) {
			if(is_string($api_key)) {
				$this->api_key = $api_key;
				return true;
			}
			return false;
		}
		
		public function setObjectId($object_id) {
			if(is_int($object_id)) {
				$this->object_id = $object_id;
				return true;
			}
			return false;
		}
		
		public function setObjectType($object_type) {
			if(is_string($object_type)) {
				$this->object_type = $object_type;
				return true;
			}
			return false;
		}
		
		public function setDescription($description) {
			if(is_string($description)) {
				$this->description = $description;
				return true;
			}
			return false;
		}
		
		public function getApiKey() {
			return $this->api_key;
		}
		
		public function getObjectId() {
			return $this->object_id;
		}
		
		public function getObjectType() {
			return $this->object_type;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public static function generateApiKey() {
			//generate random string
			return md5(
						uniqid(
								(string)microtime(true)
									+sha1(
										(string)rand(0,10000)  //100% Zufall
										+$thumb_tmp_name
										)
								)
						+md5($orig_name)
						);
		}
	}
?>