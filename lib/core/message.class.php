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

require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
require_once(ROOT_DIR.'/lib/extern/Zend/Service/Twitter.php');

class Message {
	/*This are the internal status messages of netmon*/
	public function getMessage() {
		if(!isset($_SESSION['system_messages']))
			$_SESSION['system_messages'] = array();
		$messages = $_SESSION['system_messages'];
		unset($_SESSION['system_messages']);
		return $messages;
	}
	
	public function getMessageWithoutDelete() {
		if(!isset($_SESSION['system_messages']))
			$_SESSION['system_messages'] = array();
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
		$config_line = ConfigLine::configByName('twitter_token');
		if(ConfigLine::configByName('twitter_username') AND $config_line) {
			$config = array(
				'callbackUrl' => 'http://example.com/callback.php',
				'siteUrl' => 'http://twitter.com/oauth',
				'consumerKey' => ConfigLine::configByName('twitter_consumer_key'),
				'consumerSecret' => ConfigLine::configByName('twitter_consumer_secret')
			);
			
			$token = unserialize($config_line);
			$client = $token->getHttpClient($config);
			$client->setUri('https://api.twitter.com/1.1/statuses/update.json');
			$client->setMethod(Zend_Http_Client::POST);
			$client->setParameterPost('status', $statusMessage);
			$response = $client->request();
			if($response->getStatus() == 200)
				$message[] = array("Folgendes wurde auf dem Twitteraccount von <a href=\"http://twitter.com/".ConfigLine::configByName('twitter_username')."\">".ConfigLine::configByName('twitter_username')."</a> angekündigt: <i>\"$statusMessage\"</i>", 1);
			else
				$message[] = array("Beim senden der Twitternachricht ist folgender Fehler aufgetreten: ".$response->getStatus(), 0);
			Message::setMessage($message);
		}
	}
}

?>