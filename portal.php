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

require_once('runtime.php');
require_once("./lib/classes/extern/FeedParser.class.php");

$smarty->assign('message', Message::getMessage());
/*
try {
	$Parser = new FeedParser();
	$Parser->parse('http://blog.freifunk-ol.de/feed/atom/');
}
catch(Exception $e) {
	$smarty->assign('rss_exception', $e->getMessage());
}

$smarty->assign('feed_channels', $Parser->getChannels());
$smarty->assign('feed_items', $Parser->getItems());
*/
/*
try {
	$TracParser = new FeedParser();
	$TracParser->parse('https://trac.freifunk-ol.de/timeline?ticket=on&changeset=on&milestone=on&wiki=on&max=10&daysback=90&format=rss');
}
catch(Exception $e) {
	$smarty->assign('trac_rss_exception', $e->getMessage());
}

$smarty->assign('trac_feed_channels', $TracParser->getChannels());
$smarty->assign('trac_feed_items', $TracParser->getItems());
*/


$smarty->display("header.tpl.php");
$smarty->display("portal.tpl.php");
$smarty->display("footer.tpl.php");

?>