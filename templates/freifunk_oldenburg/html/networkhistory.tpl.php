<h1>History der letzten {$history_hours} Stunden</h1>

<form action="./networkhistory.php" method="POST" enctype="multipart/form-data">
<p>Historie der letzten <input name="history_hours" type="text" size="1" value="{$history_hours}"> Stunden anzeigen <input type="submit" value="aktualisieren"></p>
</form>

{if !empty($history)}
	<ul>
		{foreach key=count item=hist from=$history}
			<li>
				{if $hist.object == 'router'}
					<b>{$hist.create_date|date_format:"%e.%m.%Y %H:%M"}:</b> 
					<a href="./router_status.php?router_id={$hist.additional_data.router_id}">{$hist.additional_data.hostname}</a> (<a href="./user.php?user_id={$hist.additional_data.user_id}">{$hist.additional_data.nickname}</a>)
					{if $hist.data.action == 'status' AND $hist.data.to == 'online'}
						geht <span style="color: #007B0F;">online</span>
					{/if}
					{if $hist.data.action == 'status' AND $hist.data.to == 'offline'}
						geht <span style="color: #CB0000;">offline</span>
					{/if}
					{if $hist.data.action == 'reboot'}
						wurde <span style="color: #000f9c;">Rebootet</span>
					{/if}
					{if $hist.data.action == 'new'}
						wurde Netmon hinzugefügt
					{/if}
					{if $hist.data.action == 'batman_advanced_version'}
						änderte Batman adv. Version von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
					{if $hist.data.action == 'firmware_version'}
						änderte Firmware Version von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
					{if $hist.data.action == 'nodewatcher_version'}
						änderte Nodewatcher Version von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
					{if $hist.data.action == 'hostname'}
						änderte Hostname von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
					{if $hist.data.action == 'chipset'}
						änderte Chipset von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
					{if $hist.data.action == 'chipset'}
						änderte Chipset von {$hist.data.from} zu {$hist.data.to}</span>
					{/if}
				{/if}
				{if $hist.object == 'not_assigned_router'}
					<b>{$hist.create_date|date_format:"%e.%m.%Y %H:%M"}:</b> 
					{$hist.data.router_auto_assign_login_string} 
					{if $hist.data.action == 'new'}
						 erscheint in der <a href="./routers_trying_to_assign.php">Liste der neuen, nicht zugewiesenen Router</a>
					{/if}
				{/if}
				{if $hist.object == 'ip'}
					<b>{$hist.create_date|date_format:"%e.%m.%Y %H:%M"}:</b> 
					{if $hist.data.ipv == 4}IPv4{else if $hist.data.ipv == 6}IPv6{/if} Adresse {$hist.data.ip}
					{if $hist.data.action == 'new'}
						 hinzugefügt.
					{/if}
				{/if}
			</li>
		{/foreach}
	</ul>
{else}
<p>In den letzten {$history_hours} Stunden ist nichts passiert.</p>
{/if}