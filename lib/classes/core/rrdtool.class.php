<?php

class RrdTool {

	public function updateRouterMemoryHistory($router_id, $memory_free, $memory_caching, $memory_buffering) {
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/router_".$router_id."_memory.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				exec("rrdtool create $rrd_path --step 600 --start ".time()." DS:memory_free:GAUGE:900:U:U DS:memory_caching:GAUGE:900:U:U DS:memory_buffering:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}

			//Update Database
			exec("rrdtool update $rrd_path ".time().":$memory_free:$memory_caching:$memory_buffering");
	}

	public function updateRouterBatmanAdvOriginatorsCountHistory($router_id, $originators) {
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/router_".$router_id."_originators.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				exec("rrdtool create $rrd_path --step 600 --start ".time()." DS:originators:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}

			//Update Database
			exec("rrdtool update $rrd_path ".time().":$originators");
	}

	public function updateRouterBatmanAdvOriginatorLinkQuality($router_id, $originator, $quality, $timestamp) {
			$originator = str_replace(":","_",$originator);
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/router_".$router_id."_batman_adv_link_quality_$originator.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				exec("rrdtool create $rrd_path --step 600 --start ".(time()-(60*60*24*30))." DS:quality:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}

			//Update Database
			exec("rrdtool update $rrd_path $timestamp:$quality");
	}

	public function updateRouterClientCountHistory($router_id, $clients) {
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/router_".$router_id."_clients.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				$command = "rrdtool create $rrd_path --step 600 --start ".time()." DS:clients:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240";
				exec($command);
			}

			//Update Database
			exec("rrdtool update $rrd_path ".time().":$clients");
	}

	public function updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total) {
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/netmon_history_router_status.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				exec("rrdtool create $rrd_path --step 600 --start ".time()." DS:online:GAUGE:800:U:U DS:offline:GAUGE:800:U:U DS:unknown:GAUGE:800:U:U DS:total:GAUGE:800:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}

			//Update Database
			exec("rrdtool update $rrd_path ".time().":$online:$offline:$unknown:$total");
	}

	public function updateNetmonClientCount($client_count) {
			//Update RRD Graph DB
			$rrd_path = "$GLOBALS[monitor_root]/rrdtool/databases/netmon_hisory_client_count.rrd";
			if(!file_exists($rrd_path)) {
				//Create new RRD-Database
				exec("rrdtool create $rrd_path --step 600 --start ".time()." DS:clients:GAUGE:800:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}

			//Update Database
			exec("rrdtool update $rrd_path ".time().":$client_count");
	}


}

?>