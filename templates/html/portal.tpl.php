<h1>Netmon 0.2 TESTING, Codename "shake-up"</h1>

<h2>Netmon-Schnellübersicht</h2>

{if $ip_status}
<img src="./tmp/ip_status.png">
{/if}
{if $vpn_status}
<img src="./tmp/vpn_status.png">
{/if}
{if $service_status}
<img src="./tmp/service_status.png">
{/if}

{if !$ip_status AND !$vpn_status AND !$service_status}
<p>Schnellübersichtdiagramme können nicht generiert werden, da keine Daten vorhanden sind.</p>
{/if}

<h2>History der letzten {$portal_history_hours} Stunden</h2>

{if !empty($history)}
{foreach key=count item=hist from=$history}
	{$hist.create_date}: 
	{if $hist.type == 'user'}Der Benutzer <a href="./user.php?id={$hist.object_id}">{$hist.object_name_1}</a> hat sich registriert
	{elseif $hist.type == 'subnet'} Das Subnets <a href="./subnet.php?id={$hist.object_id}">{$hist.object_name_1}</a> wurde angelgt
	{elseif $hist.type == 'ip'} Die Ip <a href="./ip.php?id={$hist.object_id}">{$net_prefix}.{$hist.object_name_1}.{$hist.object_name_2}</a> wurde angelegt
	{elseif $hist.type == 'service'} Ein <a href="./service.php?id={$hist.service_id}">Service</a> wurde auf der IP <a href="./ip.php?id={$hist.ip_id}">{$net_prefix}.{$hist.ip}</a> angelegt
	{elseif $hist.data.action == 'status'}
		{if $hist.data.action == 'status' AND $hist.data.from=='offline'}
			{$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) geht online.
		{elseif $hist.data.action == 'status' AND $hist.data.from=='online'}
			{$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) geht offline.
		{/if}
	{elseif $hist.data.action == 'distversion'}
		{$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) Distversion geändert ({$hist.data.from} -> {$hist.data.to}).
	{/if}
<br>
{/foreach}
{else}
<p>In den letzten {$portal_history_hours} Stunden ist nichts passiert.</p>
{/if}

<h1><a href="{$feed_channels.ID}" target="_blank">{$feed_channels.TITLE} (letztes Update: {$feed_channels.UPDATED|date_format:"%e.%m.%Y"})</a></h1>
{foreach item=feed_item from=$feed_items}
	<h2><a href="{$feed_item.ID}" target="_blank">{$feed_item.TITLE}</a></h2>
	<p>{$feed_item.SUMMARY}</p>
	<p>{$feed_item.AUTHOR.NAME} am {$feed_item.UPDATED|date_format:"%e.%m.%Y %H:%M"}</p> 
{/foreach}

<h1><a href="{$trac_feed_channels.LINK}" target="_blank">Freifunk Oldenburg Trac Timeline</a></h1>
<p>
	{foreach item=trac_feed_item from=$trac_feed_items}
		{$trac_feed_item.PUBDATE|date_format:"%e.%m.%Y %H:%M"}: <a href="{$trac_feed_item.LINK}" target="_blank">{$trac_feed_item.TITLE}</a><br>
	{/foreach}
</p>