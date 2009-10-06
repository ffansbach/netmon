<?php

// +---------------------------------------------------------------------------+
// index.php
// Netmon, Freifunk Netzverwaltung und Monitoring Software
//
// Copyright (c) 2009 Clemens John <clemens-john@gmx.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+/

/**
 * This file contains the class for the userlist site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class userlist {
	function getList() {
		try {
			$sql = "SELECT u.id, u.nickname, jabber, icq, website, email, create_date
					FROM users u
					ORDER BY u.create_date DESC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$row['create_date'] = Helper::makeSmoothIplistTime(strtotime($row['create_date']));
				$userlist[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		foreach ($userlist as $key=>$user){
			try {
				$sql = "SELECT count(*) as ipcount
						FROM ips
						WHERE user_id='$user[id]'";
				$result = DB::getInstance()->query($sql);
				$count = $result->fetch(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			$userlist[$key]['ipcount'] = $count['ipcount'];
		}
		return $userlist;
	}
}

?>