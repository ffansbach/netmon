<script type="text/javascript">
	document.body.id='tab2';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./networkstatistic.php">Netzwerkstatistik</a></li>
	<li class="tab2"><a href="./networkhistory.php">Historie</a></li>
</ul>


<h1>History der letzten {$history_hours} Stunden</h1>

<form action="./networkhistory.php" method="POST" enctype="multipart/form-data">
<p>Historie der letzten <input name="history_hours" type="text" size="1" value="5"> Stunden anzeigen <input type="submit" value="aktualisieren"></p>
</form>

{if !empty($history)}
	<ul>
		{foreach key=count item=hist from=$history}
			<li>
				{if $hist.object == 'router'}
					<b>{$hist.create_date|date_format:"%e.%m.%Y %H:%M"}:</b> 
					{if $hist.data.action == 'status' AND $hist.data.to == 'online'}
						{$hist.additional_data.hostname} ({$hist.additional_data.nickname}) geht <span style="color: #007B0F;">online</span>
					{/if}
					{if $hist.data.action == 'status' AND $hist.data.to == 'offline'}
						{$hist.additional_data.hostname} ({$hist.additional_data.nickname}) geht <span style="color: #CB0000;">offline</span>
					{/if}
					{if $hist.data.action == 'reboot'}
						{$hist.additional_data.hostname} ({$hist.additional_data.nickname}) wurde <span style="color: #000f9c;">Rebootet</span>
					{/if}
				{/if}
<!--	{if $hist.object == 'router'}
		{if $hist.data.action == 'status'}
			{if $hist.data.from=='offline'}
				{$hist.create_date}: {$hist.additional_data.hostname} ({$hist.additional_data.nickname}) geht online.<br>
			{elseif $hist.data.from=='online'}
				{$hist.create_date}: {$hist.additional_data.hostname} ({$hist.additional_data.nickname}) geht offline.<br>
			{/if}
		{/if}
		{if $hist.data.action == 'reboot'}
			{$hist.create_date}: {$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) wurde rebootet.<br>
		{/if}
	{/if}-->

			</li>
		{/foreach}
	</ul>
{else}
<p>In den letzten {$portal_history_hours} Stunden ist nichts passiert.</p>
{/if}