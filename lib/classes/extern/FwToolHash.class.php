<?php

//http://www.net-developers.de/blog/2010/01/13/eindeutige-und-zufallige-hashes-mit-php-generieren-oop-klasse/

class FW_Tool_Hash
{
  private $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
  private $len;
 
  public function __construct($length)
  {
    $this->setLength($length);
  }
 
  public function setLength($length)
  {
    $this->len = (int)$length;
  }
 
  public function getLength()
  {
    return $this->len;
  }
 
  public function getChars()
  {
    return $this->chars;
  }
 
  public function setChars($chars)
  {
    $this->chars = (string)$chars;
  }
 
  public function getHash()
  {
    $hash = "";
    for($i = 0; $i < $this->len; $i++)
    {
      $hash .= $this->chars{mt_rand(0, strlen($this->chars)-1)};
    }
 
    return $hash;
  }
}

?>