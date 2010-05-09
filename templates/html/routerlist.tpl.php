<h1>Liste der Router</h1>

<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 120px;"><b>Hostname</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 180px;"><b>letzte Aktualisierung</b></div>
    <div style="float:left; width: 230px;"><b>Standort</b></div>
    <div style="float:left; width: 130px;"><b>Technik</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
  </div>
</div>

{if !empty($routerlist)}
{foreach key=count item=router from=$routerlist}
<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 120px;"><a href="./router_status.php?router_id={$router.router_id}">{$router.hostname}</a></div>
    <div style="float:left; width: 60px;">
    {if $router.actual_crawl_data.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $router.actual_crawl_data.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {/if}
    </div>
    <div style="float:left; width: 180px;">{$router.actual_crawl_data.crawl_date|date_format:"%e.%m.%Y %H:%M"} Uhr</div>
    <div style="float:left; width: 230px;">{$router.short_location}...</div>
    <div style="float:left; width: 130px;">{$router.chipset_name}</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$router.user_id}">{$router.nickname}</a></div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Router vorhanden</p>
{/if}