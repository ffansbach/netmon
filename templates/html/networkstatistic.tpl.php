<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>


<script type="text/javascript">
	document.body.id='tab1';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./networkstatistic.php">Netzwerkstatistik</a></li>
	<li class="tab2"><a href="./networkhistory.php">Historie</a></li>
</ul>

{if !empty($last_ended_crawl_cycle)}
	<h2>Crawl Status</h2>
	<h3>Aktueller Crawl</h3>
	<p>
		<b>Beginn:</b> {$actual_crawl_cycle.crawl_date|date_format:"%e.%m.%Y %H:%M"}<br>
		<b>Vorraussichtliches Ende:</b> {$actual_crawl_cycle.crawl_date_end|date_format:"%e.%m.%Y %H:%M"} (noch {$actual_crawl_cycle.crawl_date_end_minutes} Minuten)
	</p>
	<h3>Letzter Crawl</h3>
	<p>
		<b>Beginn:</b> {$last_ended_crawl_cycle.crawl_date}<br>
		<b>Ende:</b> {$last_ended_crawl_cycle.crawl_date_end|date_format:"%e.%m.%Y %H:%M"}
	</p>
	
	<h2>Router Status</h2>
	<table style="text-align: center; vertical-align: baseline; font-size: 2em; font-weight: bold;">
		<tr>
			<td style="width: 33%; color: #007B0F;" ><img src="/templates/img/status_up_big.png" title="up - node is reachable" alt="up"/> {$router_status_online}</td>
			<td class="node_status_down nodes" style="width: 33%; color: #CB0000;" ><img src="/templates/img/status_down_big.png" title="down - node is not visible via OLSR" alt="down"/> {$router_status_offline}</td>
			<td class="node_status_pending nodes" style="width: 33%; color: #F8C901;" ><img src="/templates/img/status_pending_big.png" title="pending - node has not yet been seen since registration" alt="pending"/> {$router_status_unknown}</td>
		</tr>
	</table>
	
	<h2>Router nach Chipset</h2>
	<p>
	{foreach item=router_chipset from=$router_chipsets}
		<b>{$router_chipset.chipset_name}:</b> {$router_chipset.count}<br>
	{/foreach}
	</p>

	<h2>Router nach Batman adv. version</h2>
	<p>
	{foreach item=batman_advanced_version_count from=$batman_advanced_versions_count}
		<b>{$batman_advanced_version_count.batman_advanced_version}:</b> {$batman_advanced_version_count.count}<br>
	{/foreach}
	</p>

	<h2>Router nach Kernel Version</h2>
	<p>
	{foreach item=kernel_version_count from=$kernel_versions_count}
		<b>{$kernel_version_count.kernel_version}:</b> {$kernel_version_count.count}<br>
	{/foreach}
	</p>

	<h2>Router nach Firmware Version</h2>
	<p>
	{foreach item=firmware_version_count from=$firmware_versions_count}
		<b>{$firmware_version_count.firmware_version}:</b> {$firmware_version_count.count}<br>
	{/foreach}
	</p>
	
	<h2>Router status History</h2>
	{literal}
		<script>
			$(document).ready(function() {
	{/literal}
			$("#tabs_router_status_history").tabs();
	{literal}
			});
		</script>
	{/literal}
	
	<div id="tabs_router_status_history" style="width: 520px">
		<ul>
			<li><a href="#fragment-1_router_history"><span>1 Tag</span></a></li>
			<li><a href="#fragment-2_router_history"><span>7 Tage</span></a></li>
	   		<li><a href="#fragment-3_router_history"><span>1 Monat</span></a></li>
		</ul>
		<div id="fragment-1_router_history">
			<img src="./tmp/netmon_history_router_status_1_day.png">
		</div>
		<div id="fragment-2_router_history">
			<img src="./tmp/netmon_history_router_status_7_days.png">
		</div>
		<div id="fragment-3_router_history">
			<img src="./tmp/netmon_history_router_status_1_month.png">
		</div>
	</div>

	<h2>Historie der Verbundenen Clients</h2>
	{literal}
		<script>
			$(document).ready(function() {
	{/literal}
			$("#tabs_client_count_history").tabs();
	{literal}
			});
		</script>
	{/literal}
	
	<div id="tabs_client_count_history" style="width: 520px">
		<ul>
			<li><a href="#fragment-1_client_count_history"><span>1 Tag</span></a></li>
			<li><a href="#fragment-2_client_count_history"><span>7 Tage</span></a></li>
	   		<li><a href="#fragment-3_client_count_history"><span>1 Monat</span></a></li>
		</ul>
		<div id="fragment-1_client_count_history">
			<img src="./tmp/netmon_history_client_count_1_day.png">
		</div>
		<div id="fragment-2_client_count_history">
			<img src="./tmp/netmon_history_client_count_7_days.png">
		</div>
		<div id="fragment-3_client_count_history">
			<img src="./tmp/netmon_history_client_count_1_month.png">
		</div>
	</div>
{else}
	<p>Es wurde noch kein Crawlzyklus vollständig beendet, sodass keine Daten generiert werden können</p>
{/if}