<?php

/**
 * This class is used as a container for static methods that deal operations
 * on the routers not assigned table, also called new routers table.
 * Routers that want to interact with netmon need to be assigned to a router object created by the user in the database.
 * To simplify the process of assigning, each router that is not assigned to a router object in netmons database calls
 * netmon regulary and tells netmon that there is a router outside that is not assigned to netmon. He also tells netmon his
 * mac addres that is printed on the back of the router. With this information a user can simply pic his router
 * from the list of new routers and assign it to a new router object in netmons database.
 *
 * @package	Netmon
 */
class RoutersNotAssigned {
	/**
	* @brief get a router by his auto assign login string from the routers not assigned table
	* @param string the autoassign login string of the router you want to fecht
	* @return array() the array containing the router
	*/
	public function getRouterByAutoAssignLoginString($router_auto_assign_login_string) {
		$rows = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM routers_not_assigned
							    WHERE router_auto_assign_login_string=?");
			$stmt->execute(array($router_auto_assign_login_string));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	/**
	* @brief get the list of not assigned routers
	* @return array() list of routers
	*/
	public function getRouters() {
		$rows = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM routers_not_assigned");
			$stmt->execute(array($router_auto_assign_login_string));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}
}

?>