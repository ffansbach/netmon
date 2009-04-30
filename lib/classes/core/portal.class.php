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
 * This file contains the class for the subnet site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */
 
require_once('./lib/classes/core/rssparser.class.php');

class portal {
  function __construct(&$smarty) {
      $curl_handle=curl_init();
      curl_setopt($curl_handle,CURLOPT_URL, "http://blog.freifunk-ol.de/feed/");
      curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,5);
      curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
      $data = curl_exec($curl_handle);
      curl_close($curl_handle);
      
      // $data;
  	
	  $doc = new SimpleXmlElement($data, LIBXML_NOCDATA);

      $rssParser = new rssParser;
      $smarty->assign('rssdata', $rssParser->parseRSS($doc));

      $smarty->assign('get_content', "portal");
      return true;
  }
}

?>