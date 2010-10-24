<h1>Liste der Dienste im Freifunk Netzwerk</h1>

<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 250px;"><b>Title</b></div>
    <div style="float:left; width: 130px;"><b>Router</b></div>
    <div style="float:left; width: 100px;"><b>Router Stat.</b></div>
    <div style="float:left; width: 100px;"><b>Service Stat.</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
    <div style="float:left; width: 100px;"><b>Link</b></div>
  </div>
</div>

{if !empty($servicelist)}
{foreach key=count item=service from=$servicelist}
<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 250px;"><a href="./router_config.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></div>
    <div style="float:left; width: 130px;"><a href="./router_config.php?router_id={$service.router_id}">{$service.hostname}</a></div>
    <div style="float:left; width: 100px;">
		{if $service.router_status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $service.router_status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{/if}
    </div>
    <div style="float:left; width: 100px;">-</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$service.user_id}">{$service.nickname}</a></div>
    <div style="float:left; width: 100px;"><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Router vorhanden</p>
{/if}