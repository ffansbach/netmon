<h1>Netmon-Schnellübersicht</h1>

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
{/if}

{if empty($rss_exception)}
	<h1><a href="{$feed_channels.ID}" target="_blank">{$feed_channels.TITLE} (letztes Update: {$feed_channels.UPDATED|date_format:"%e.%m.%Y"})</a></h1>
	{foreach item=feed_item from=$feed_items}
		<h2><a href="{$feed_item.ID}" target="_blank">{$feed_item.TITLE}</a></h2>
		<p>{$feed_item.SUMMARY}</p>
		<p>{$feed_item.AUTHOR.NAME} am {$feed_item.UPDATED|date_format:"%e.%m.%Y %H:%M"}</p> 
	{/foreach}
{else}
	<h1>Freifunk Oldenburg Blog</h1>
	<p>{$rss_exception}</p>
{/if}

{if empty($trac_rss_exception)}
	<h1><a href="{$trac_feed_channels.LINK}" target="_blank">Freifunk Oldenburg Trac Timeline</a></h1>
	<p>
		{foreach item=trac_feed_item from=$trac_feed_items}
			{$trac_feed_item.PUBDATE|date_format:"%e.%m.%Y %H:%M"}: <a href="{$trac_feed_item.LINK}" target="_blank">{$trac_feed_item.TITLE}</a><br>
		{/foreach}
	</p>
{else}
	<h1>Freifunk Oldenburg Trac Timeline</h1>
	<p>{$trac_rss_exception}</p>
{/if}