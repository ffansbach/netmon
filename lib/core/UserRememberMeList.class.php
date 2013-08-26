<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/UserRememberMe.class.php');

	class UserRememberMeList extends ObjectList {
		private $user_remember_me_list = array();
		
		public function __construct($user_id=false, $sort_by=false, $order=false) {
			$result = array();
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
				
			if($user_id) {
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT user_remember_mes.id as user_remember_me_id
														FROM user_remember_mes
														WHERE user_remember_mes.user_id = :user_id
														ORDER BY
															case :sort_by
																when 'create_date' then user_remember_mes.create_date
																else user_remember_mes.id
															end
														".$this->getOrder());
					$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT user_remember_mes.id as user_remember_me_id
														FROM user_remember_mes
														ORDER BY
															case :sort_by
																when 'create_date' then user_remember_mes.create_date
																else user_remember_mes.id
															end
														".$this->getOrder());
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			foreach($result as $user_remember_me) {
				$user_remember_me = new UserRememberMe((int)$user_remember_me['user_remember_me_id']);
				$user_remember_me->fetch();
				$this->user_remember_me_list[] = $user_remember_me;
			}
		}
		
		public function delete() {
			foreach($this->getUserRememberMeList() as $user_remember_me) {
				$user_remember_me->delete();
			}
		}
		
		public function getUserRememberMeList() {
			return $this->user_remember_me_list;
		}
	}
?>