<h1>Mein Desktop</h1>

<h2>Projektdaten von {$project_name}</h2>

<div style="width: 800px; overflow: hidden; padding-bottom: 15px; border-top: solid 0px grey;">
  <div nstyle="white-space: nowrap;">


    <div style="float:left; width: 180px; margin-right: 20px;  border: solid 1px; black;">
      <div style="width: 180px;">Projekt ESSID:</div>
      <div style="width: 180px;">{$essid}</div>
    </div>

    <div style="float:left; width: 160px; border: solid 1px; black; margin-right: 20px;">
      <div style="width: 160px;">Projekt BSSID</div>
      <div style="width: 160px;">{$bssid}</div>
    </div>

    <div style="float:left; border: solid 1px; black;">
      <div style="">kanal: </div>
      <div style="">{$kanal}</div>
    </div>
  </div>
</div>

<h2>Meine Services</h2>

<div id="nodeitem" style="width: 800px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node-IP</b></div>
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Uptime</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{if !empty($servicelist)}
{foreach key=count item=nodelist from=$servicelist}
<div id="nodeitem" style="width: 800px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>{$count+1}</b></div>
    <div style="float:left; width: 95px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 250px;"><a href="./subnet.php?id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 110px;"><a href="./user.php?id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$nodelist.status}</div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$nodelist.status}</div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$nodelist.status}</div>
    {elseif $nodelist.status=="unbekannt"}
      <div style="float:left; width: 80px; background-color: #fff3c3;">{$nodelist.status}</div>
    {/if}
    <div style="float:left; width: 80px;">{$nodelist.uptime}</div>
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}