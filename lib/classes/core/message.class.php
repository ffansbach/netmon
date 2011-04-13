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
 * This file contains the class for system messages.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once($path.'lib/classes/core/config.class.php');
require_once($path.'lib/classes/extern/Zend/Service/Twitter.php');

class Message {
	/*This are the internal status messages of netmon*/
	public function getMessage() {
		$messages = $_SESSION['system_messages'];
		unset($_SESSION['system_messages']);
		return $messages;
	}
	
	public function getMessageWithoutDelete() {
		$messages = $_SESSION['system_messages'];
		return $messages;
	}
	
	public function setMessage($message) {
		foreach ($message as $value) {
			$_SESSION['system_messages'][] = $value;
		}
	}

	public function postTwitterMessage($statusMessage) {
		//Send Message to twitter
		$config_line = Config::getConfigLineByName('twitter_token');
		if(!empty($GLOBALS['twitter_username']) AND !empty($config_line)) {
			$config = array(
				'callbackUrl' => 'http://example.com/callback.php',
				'siteUrl' => 'http://twitter.com/oauth',
				'consumerKey' => $GLOBALS['twitter_consumer_key'],
				'consumerSecret' => $GLOBALS['twitter_consumer_secret']
			);
			
			$token = unserialize($config_line['value']);
			$client = $token->getHttpClient($config);
			$client->setUri('http://twitter.com/statuses/update.json');
			$client->setMethod(Zend_Http_Client::POST);
			$client->setParameterPost('status', $statusMessage);
			$response = $client->request();
		}
	}
}

?>