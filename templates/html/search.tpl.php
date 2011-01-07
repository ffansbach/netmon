<h1>Netzwerk durchsuchen</h1>

<form action="./search.php" method="POST">

<input name="search_string" type="text" size="40">
<select name="search_range">
	<option value="all" >Alles durchsuchen</option>
	<option value="mac_addr" selected>Mac Adresse</option>
	<option value="ipv6_addr" selected>IPv6 Adresse</option>
</select>
<input type="submit" value="suchen">

</form>

{if !empty($search_result_crawled_interfaces)}
<h1>Crawled Interfaces</h1>

<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 120px;"><b>Hostname</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>IF-Name</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
  </div>
</div>

{foreach key=count item=interface from=$search_result_crawled_interfaces}
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 120px;"><a href="./router_status.php?router_id={$interface.router_id}&crawl_cycle_id={$interface.crawl_cycle_id}">{$interface.router_data.hostname}</a></div>
    <div style="float:left; width: 60px;">
    {if $interface.router_crawl_data.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $interface.router_crawl_data.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {/if}
    </div>
    <div style="float:left; width: 80px;">{$interface.name}</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$interface.router_data.user_id}">{$interface.router_data.nickname}</a></div>
  </div>
</div>
{/foreach}
{/if}