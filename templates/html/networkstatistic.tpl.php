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

<table style="text-align: center;
  vertical-align: baseline;
  font-size: 2em;
  font-weight: bold;">
<tr>

<td style="width: 33%; color: #007B0F;" ><img src="/templates/img/status_up_big.png" title="up - node is reachable" alt="up"/> {$router_status_history.0.online}</td>

<td class="node_status_down nodes" style="width: 33%; color: #CB0000;" ><img src="/templates/img/status_down_big.png" title="down - node is not visible via OLSR" alt="down"/> {$router_status_history.0.offline}</td>

<td class="node_status_pending nodes" style="width: 33%; color: #F8C901;" ><img src="/templates/img/status_pending_big.png" title="pending - node has not yet been seen since registration" alt="pending"/> {$router_status_history.0.unknown}</td>

</tr>
</table>

<h2>Router by Type</h2>
<p>
{foreach item=router_chipset from=$router_chipsets}
	<b>{$router_chipset.chipset_name}:</b> {$router_chipset.count}<br>
{/foreach}
</p>

<h2>Router status History</h2>
<img src="./tmp/networkstatistic_status.png">

<!--
<h1>History der letzten {$portal_history_hours} Stunden</h1>

{if !empty($history)}
{foreach key=count item=hist from=$history}
	{if $hist.data.action == 'status'}
		{if $hist.data.from=='offline'}
			{$hist.create_date}: {$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) geht online.<br>
		{elseif $hist.data.from=='online'}
			{$hist.create_date}: {$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) geht offline.<br>
		{/if}
	{/if}
	{if $hist.data.action == 'reboot'}
		{$hist.create_date}: {$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) wurde rebootet.<br>
	{/if}


{/foreach}
{else}
<p>In den letzten {$portal_history_hours} Stunden ist nichts passiert.</p>
{/if}-->