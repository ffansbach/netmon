<?php

class rssParser {
	
	public function parseRSS($xml) {
		$rssdata['title'] = $xml->channel->title;
	    
    	$cnt = count($xml->channel->item);
    	for($i=0; $i<$cnt; $i++) {
    		$rssdata[$i]['url'] = $xml->channel->item[$i]->link;
    		$rssdata[$i]['title'] = $xml->channel->item[$i]->title;
    		$rssdata[$i]['description'] =  $xml->channel->item[$i]->description;
    	}
		return($rssdata);
	}
	
	public function parseAtom($xml) {
	    echo "<strong>".$xml->author->name."</strong>";
   		$cnt = count($xml->entry);
    	for($i=0; $i<$cnt; $i++) {
			$urlAtt = $xml->entry->link[$i]->attributes();
			$url	= $urlAtt['href'];
			$title 	= $xml->entry->title;
			$desc	= strip_tags($xml->entry->content);
    	}
    	return(array('url' => $url, 'title' => $title, 'desc' => $desc));
	}
	
}