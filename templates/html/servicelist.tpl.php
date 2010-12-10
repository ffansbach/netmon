<h1>Liste der Dienste im Freifunk Netzwerk</h1>

<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 240px;"><b>Title</b></div>
    <div style="float:left; width: 110px;"><b>Router</b></div>
    <div style="float:left; width: 80px;"><b>R-Status</b></div>
    <div style="float:left; width: 80px;"><b>S-Status</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
    <div style="float:left; width: 100px;"><b>Link</b></div>
  </div>
</div>

{if !empty($servicelist)}
{foreach key=count item=service from=$servicelist}
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 240px;"><a href="./router_config.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></div>
    <div style="float:left; width: 110px;"><a href="./router_config.php?router_id={$service.router_id}">{$service.hostname}</a></div>
    <div style="float:left; width: 80px;">
		{if $service.router_status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $service.router_status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{/if}
    </div>
    <div style="float:left; width: 80px;">
		{if $service.service_status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $service.service_status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{/if}
    </div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$service.user_id}">{$service.nickname}</a></div>
    <div style="float:left; width: 100px;"><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Services vorhanden</p>
{/if}