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
 * This file represents the Mysql-Connection-Class
 * Look at http://www.sim4000.de for more information.
 *
 * @author	Christian Blechert
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 * @since 06/2008
 */


class mysqlClass {
 
   private $connection; // Link Ressource
   private $database; // DB Handler
   private $enc; // Encoding
   static $counter = 0; // Query Counter
 
   /**
   * @param String host Hostname des mysql Servers
   * @param String user Benutzername
   * @param String password Passwort zum Benutzer
   * @param String database Zu benutzende Datenbank
   * @param String enc Zeichenkodierung
   */
   function __construct() {
      $host = $GLOBALS['mysql_host'];
      $user = $GLOBALS['mysql_user'];
      $password = $GLOBALS['mysql_password'];
      $database = $GLOBALS['mysql_db'];
      $enc = $GLOBALS['mysql_enc'];
      
      $this->connection = 0;
      $this->database = 0;
      $this->enc = $enc;
      $this->mysqlConnect($host, $user, $password, $database, $enc); // Verbindung herstellen
   }
 
   /**
   * Verbindung bei Vernichten der Klasse trennen
   */
   function __destruct() {
      $this->mysqlDisconnect(); // Trennen
   }
 
   /**
   * Verbindung herstellen
   * @param String host Hostname des mysql Servers
   * @param String user Benutzername
   * @param String password Passwort zum Benutzer
   * @param String database Zu benutzende Datenbank
   * @param String enc Zeichenkodierung
   */
   function mysqlConnect($host, $user, $password, $database, $enc="utf8") {
      if($this->connection!=0) $this->mysqlDisconnect();
      if($this->connection = @mysql_connect($host, $user, $password)) {
         if($this->database = @mysql_select_db($database, $this->connection)) {
            @mysql_query("SET NAMES '".$enc."'");
         }
      }
      if(mysql_errno($this->connection)) die(mysql_error($this->connection));
   }
 
   /**
   * Verbindung trennen
   */
   function mysqlDisconnect() {
      @mysql_close($this->connection);
      if(!mysql_errno()) {
         $this->connection=0;
         $this->database=0;
      } else {
           die(mysql_errno().": ".mysql_error());
      }
   }
 
   /**
   * mySQL Statement absetzen
   * @param String query MySQL Statement
   * @return Resource ResultSet des Statements
   */
   function mysqlQuery($query, $logging=true, $die=true) {
      $time_before = microtime();
      $result = mysql_query($query);
      $time_after = microtime();

      if(mysql_errno($this->connection)) {
         if($die) die(mysql_errno($this->connection).": ".mysql_error($this->connection));
      } else {
         mysqlClass::$counter = mysqlClass::$counter+1;
         if ($logging) {
		     logsystem::mysqlQueryLog($time_after-$time_before, $query);
         }
      }
      return $result;
   }
   
   /**
   * Liefert die Anzahl der betroffenen Zeilen Zurueck
   * @return int
   */
   function mysqlAffectedRows() {
      return @mysql_affected_rows($this->connection);
   }
 
   /**
   * Liefert das result eines Statements in einem 2 Dimensionalen Array
   * @param String query MySQL Statement
   * @return Array[][] result
   */
   function mysqlArray($query) {
      $i=0;
      $result = $this->mysqlQuery($query);
      while($row=mysql_fetch_array($result)) {
         for($j=0; $j<sizeof($row); $j++) {
            $array[$i][@mysql_field_name($result, $j)] = $row[$j];
         }
         $i++;
      }
      $return[0] = mysql_num_rows($result);
      $return[1] = mysql_num_fields($result);
      $return[2] = $array;
      return $return;
   }
 
   /**
   * Verkuerzte Version von mysql_real_escape_string()
   * @param String query Wert zum Escapen
   * @return String der Escaped String
   */
   function sqlij($string) {
      return mysql_real_escape_string($string);
   }
 
   /**
   * Liefert den aktuellen Stand des Query Counter
   * @return int Wert
   */
   function getNumQuerys() {
      return mysqlClass::$counter;
   }
 
   /**
   * Liefert die Zeichenkodierung
   * @return String
   */
   function getEnc() {
      return $this->enc;
   }
   
   /**
    * Gibt falls vorhanden den MySQL Error zuruech
    * @return String
    */
   function getSQLError() {
         return mysql_error($this->connection);
   }
   
   /**
    * Gibt falls vorhanden den Fehler Codes des Errors zurueck
    * @return int
    */
   function getSQLErrno() {
         return mysql_errno($this->connection);
   }
   
   /**
    * Liefert die ID des letzten insert Befehls
    * @return int
    */
   function getInsertID() {
         return mysql_insert_id($this->connection);
   }
 
}
 
?>