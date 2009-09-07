<h1>Verfügbarkeit</h1>

<h2>Nodes (Wlan)</h2>

<div id="nodeitem" style="width: 700px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node-IP</b></div>
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{if !empty($nodelist)}
{foreach key=count item=nodelist from=$nodelist}
<div id="nodeitem" style="width: 700px; overflow: hidden;">
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
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2>VPN (Verbindungs-Netze)</h2>

<div id="nodeitem" style="width: 700px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>VPN-IP</b></div>
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{if !empty($vpnlist)}
{foreach key=count item=nodelist from=$vpnlist}
<div id="nodeitem" style="width: 700px; overflow: hidden;">
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
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2 style="white-space: nowrap;">Services (Server, Webseiten etc.)<!--(1 nicht sichbar -> <a href="./login.php">Login</a>)--></h2>

<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 300px;"><b>Service</b></div>
    <div style="float:left; width: 140px;"><b>Crawl-URL</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Aktion</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{if !empty($servicelist)}
{foreach key=count item=nodelist from=$servicelist}
<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>{$count+1}</b></div>
    <div style="float:left; width: 300px;"><a href="./service.php?service_id={$nodelist.service_id}">{$nodelist.services_title}</a></div>
    <div style="float:left; width: 140px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip} : {$nodelist.crawler}</a></div>
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
    <div style="float:left; width: 80px;">{if is_numeric($nodelist.crawler)}<a href="{if $nodelist.crawler=='80'}http://{elseif $nodelist.crawler=='21'}ftp://{elseif $nodelist.crawler=='8888'}http://{/if}{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}">Besuchen</a>{else}&nbsp;{/if}</div>
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2>Clients</h2>

<div id="nodeitem" style="width: 700px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node-IP</b></div>
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{if !empty($clientlist)}
{foreach key=count item=nodelist from=$clientlist}
<div id="nodeitem" style="width: 700px; overflow: hidden;">
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
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}