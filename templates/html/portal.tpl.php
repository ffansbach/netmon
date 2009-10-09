<h1>Netmon 0.2 TESTING, Codename "shake-up"</h1>

<h2>History der letzten {$portal_history_hours} Stunden</h2>

{if !empty($history)}
{foreach key=count item=hist from=$history}
	{$hist.create_date}: 
	{if $hist.type == 'user'}Der Benutzer <a href="./user.php?id={$hist.object_id}">{$hist.object_name_1}</a> hat sich registriert
	{elseif $hist.type == 'subnet'} Das Subnets <a href="./subnet.php?id={$hist.object_id}">{$hist.object_name_1}</a> wurde angelgt
	{elseif $hist.type == 'ip'} Die Ip <a href="./ip.php?id={$hist.object_id}">{$net_prefix}.{$hist.object_name_1}.{$hist.object_name_2}</a> wurde angelegt
	{elseif $hist.type == 'service'} Ein <a href="./service.php?id={$hist.service_id}">Service</a> wurde auf der IP <a href="./ip.php?id={$hist.ip_id}">{$net_prefix}.{$hist.subnet_ip}.{$hist.ip_ip}</a> angelegt
	{elseif $hist.data.action == 'status'}
		{if $hist.data.action == 'status' AND $hist.data.from=='offline'}
			Der Service {$hist.data.service_id} auf der IP {$net_prefix}.{$hist.additional_data.subnet_ip}.{$hist.additional_data.ip_ip} ({$hist.additional_data.nickname}) geht online.
		{elseif $hist.data.action == 'status' AND $hist.data.from=='online'}
			Der Service {$hist.data.service_id} auf der IP {$net_prefix}.{$hist.additional_data.subnet_ip}.{$hist.additional_data.ip_ip} ({$hist.additional_data.nickname}) geht offline.
		{/if}
	{/if}
<br>
{/foreach}
{else}
<p>In den letzten {$portal_history_hours} Stunden ist nichts passiert.</p>
{/if}

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

<!--Bindet einen RSS-Feed ein. Siehe lib/classes/core/portal.clas.php-->
<!--<h2>Neues im <a href="http://blog.freifunk-ol.de/">{$rssdata.title}</a></h2>

{foreach item=rss from=$rssdata}
	<h3><a href="{$rss.url}">{$rss.title}</a></h3>
	<p>{$rss.description}</p>
{/foreach}-->