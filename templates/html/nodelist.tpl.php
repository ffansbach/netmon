<h1>Verfügbarkeit</h1>

<h2>Nodes (Wlan)</h2>

<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node</b></div>
    <div style="float:left; width: 95px;"><b>Link</b></div>    
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Uptime</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{foreach key=count item=nodelist from=$nodelist}
<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>{$count+1}</b></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=node&id={$nodelist.node_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=service&service_id={$nodelist.service_id}">Erw. Info</a></div>
    <div style="float:left; width: 250px;"><a href="./index.php?get=subnet&id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 110px;"><a href="./index.php?get=user&id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$nodelist.status}</div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$nodelist.status}</div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$nodelist.status}</div>
    {/if}
    <div style="float:left; width: 80px;">{$nodelist.uptime}</div>
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}

<h2>VPN (Verbindungs-Netze)</h2>

<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node</b></div>
    <div style="float:left; width: 95px;"><b>Link</b></div>    
    <div style="float:left; width: 250px;"><b>Subnet</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Uptime</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{foreach key=count item=nodelist from=$vpnlist}
<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>{$count+1}</b></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=node&id={$nodelist.node_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=service&service_id={$nodelist.service_id}">Erw. Info</a></div>
    <div style="float:left; width: 250px;"><a href="./index.php?get=subnet&id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 110px;"><a href="./index.php?get=user&id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$nodelist.status}</div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$nodelist.status}</div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$nodelist.status}</div>
    {/if}
    <div style="float:left; width: 80px;">{$nodelist.uptime}</div>
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}

<h2>Services (Server, Webseiten etc.)</h2>

<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>#</b></div>
    <div style="float:left; width: 95px;"><b>Node</b></div>
    <div style="float:left; width: 95px;"><b>Link</b></div>    
    <div style="float:left; width: 250px;"><b>Titel</b></div>
    <div style="float:left; width: 110px;"><b>Inhaber</b></div>
    <div style="float:left; width: 80px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Uptime</b></div>
    <div style="float:left; width: 100px;"><b>Stand</b></div>
  </div>
</div>

{foreach key=count item=nodelist from=$servicelist}
<div id="nodeitem" style="width: 895px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 20px;"><b>{$count+1}</b></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=node&id={$nodelist.node_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 95px;"><a href="./index.php?get=service&service_id={$nodelist.service_id}">Erw. Info</a></div>
    <div style="float:left; width: 250px;"><a href="./index.php?get=service&service_id={$nodelist.service_id}">{$nodelist.services_title}</a></div>
    <div style="float:left; width: 110px;"><a href="./index.php?get=user&id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$nodelist.status}</div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$nodelist.status}</div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$nodelist.status}</div>
    {/if}
    <div style="float:left; width: 80px;">{$nodelist.uptime}</div>
    <div style="float:left; width: 100px;">{$nodelist.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}

<h2>Legende</h2>

<p>ping: IP ist Pingbar, also im prinzip online. Json-Daten lassen sich aber nicht abrufen was darauf hindeuted das sich hiter der IP kein OpenWrt router sondern ein Client verbirgt.<br>
online: OpenWrt Router ist erreichbar und Jason-Daten sind abrufbar.<br>
offline: Json-Daten lassen sich nicht abrufen, IP lässt sich nicht pingen.</p>