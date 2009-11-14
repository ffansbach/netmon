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
 * This file contains the class for the logsystem
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class Logsystem {

  public function getTime() {
    return date("d:m:Y H:i:s", time());
  }

  public function mysqlQueryLog($needed_time, $query) {
    $logfile = $GLOBALS['monitor_root']."/log/querylog.log";
    if ($needed_time>$GLOBALS['mysql_querry_log_time']) {
      $time = Logsystem::getTime();
    
      $message = "$time	$needed_time	$_SERVER[REMOTE_ADDR] :: $query \n";

      $datei = fopen($logfile, 'a');
      fwrite($datei, $message);
      fclose($datei);
    }
  }

}

?>