<h1>Verfügbarkeit</h1>



<h2>Nodes (Wlan)</h2>

<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 85px;"><b>Service</b></div>
    <div style="float:left; width: 50px;"><b>Crawl</b></div>
    <div style="float:left; width: 100px;"><b>user</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
    <div style="float:left; width: 150px;"><b>Subnet</b></div>
    <div style="float:left; width: 150px;"><b>Info/Link</b></div>
  </div>
</div>

{if !empty($nodelist)}
{foreach key=count item=nodelist from=$nodelist}
<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 50px;">{$nodelist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$nodelist.status}</div>
    {elseif $nodelist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$nodelist.crawl_time} Uhr</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($nodelist.crawler)}<a href="{if $nodelist.crawler=='80'}http://{elseif $nodelist.crawler=='21'}ftp://{elseif $nodelist.crawler=='8888'}http://{/if}{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}">{$nodelist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2>VPN (Verbindungs-Netze)</h2>

<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 85px;"><b>Service</b></div>
    <div style="float:left; width: 50px;"><b>Crawl</b></div>
    <div style="float:left; width: 100px;"><b>user</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
    <div style="float:left; width: 150px;"><b>Subnet</b></div>
    <div style="float:left; width: 150px;"><b>Info/Link</b></div>
  </div>
</div>

{if !empty($nodelist)}
{foreach key=count item=nodelist from=$vpnlist}
<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 50px;">{$nodelist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$nodelist.status}</div>
    {elseif $nodelist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$nodelist.crawl_time} Uhr</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($nodelist.crawler)}<a href="{if $nodelist.crawler=='80'}http://{elseif $nodelist.crawler=='21'}ftp://{elseif $nodelist.crawler=='8888'}http://{/if}{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}">{$nodelist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2>Clients</h2>

<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 85px;"><b>Service</b></div>
    <div style="float:left; width: 50px;"><b>Crawl</b></div>
    <div style="float:left; width: 100px;"><b>user</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
    <div style="float:left; width: 150px;"><b>Subnet</b></div>
    <div style="float:left; width: 150px;"><b>Info/Link</b></div>
  </div>
</div>

{if !empty($nodelist)}
{foreach key=count item=nodelist from=$clientlist}
<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 50px;">{$nodelist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$nodelist.status}</div>
    {elseif $nodelist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$nodelist.crawl_time} Uhr</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($nodelist.crawler)}<a href="{if $nodelist.crawler=='80'}http://{elseif $nodelist.crawler=='21'}ftp://{elseif $nodelist.crawler=='8888'}http://{/if}{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}">{$nodelist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}

<h2 style="white-space: nowrap;">Services (Server, Webseiten etc.)<!--(1 nicht sichbar -> <a href="./login.php">Login</a>)--></h2>

<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 85px;"><b>Service</b></div>
    <div style="float:left; width: 50px;"><b>Crawl</b></div>
    <div style="float:left; width: 100px;"><b>user</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
    <div style="float:left; width: 150px;"><b>Subnet</b></div>
    <div style="float:left; width: 150px;"><b>Info/Link</b></div>
  </div>
</div>

{if !empty($nodelist)}
{foreach key=count item=nodelist from=$servicelist}
<div id="nodeitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$nodelist.service_id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></div>
    <div style="float:left; width: 50px;">{$nodelist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$nodelist.user_id}">{$nodelist.nickname}</a></div>
    {if $nodelist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $nodelist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $nodelist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$nodelist.status}</div>
    {elseif $nodelist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$nodelist.crawl_time} Uhr</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$nodelist.subnet_id}">{$nodelist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($nodelist.crawler)}<a href="{if $nodelist.crawler=='80'}http://{elseif $nodelist.crawler=='21'}ftp://{elseif $nodelist.crawler=='8888'}http://{/if}{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}:{if $nodelist.crawler=='80'}80{elseif $nodelist.crawler=='21'}21{elseif $nodelist.crawler=='8888'}8888{/if}">{$nodelist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Nodes vorhanden</p>
{/if}