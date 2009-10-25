<?php
  class DB {
  
  /*** Declare instance ***/
  private static $instance = NULL;
  
  /**
  *
  * the constructor is set to private so
  * so nobody can create a new instance using new
  *
  */
  private function __construct() {
    /*** maybe set the db name here later ***/
  }
  
  /**
  *
  * Return DB instance or create intitial connection
  *
  * @return object (PDO)
  *
  * @access public
  *
  */
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new PDO("mysql:host=".$GLOBALS['mysql_host'].";dbname=".$GLOBALS['mysql_db'], $GLOBALS['mysql_user'], $GLOBALS['mysql_password']);
      self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$instance-> query('SET NAMES utf8');
    }
    return self::$instance;
  }
  
  /**
  *
  * Like the constructor, we make __clone private
  * so nobody can clone the instance
  *
  */
  private function __clone(){

  }

} /*** end of class ***/

?>