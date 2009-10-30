<h1>Verfügbarkeit</h1>



<h2>Ips (Wlan)</h2>

<div id="ipitem" style="width: 745px; overflow: hidden;">
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

{if !empty($iplist)}
{foreach key=count item=iplist from=$iplist}
<div id="ipitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$iplist.service_id}">{$net_prefix}.{$iplist.ip}</a></div>
    <div style="float:left; width: 50px;">{$iplist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$iplist.user_id}">{$iplist.nickname}</a></div>
    {if $iplist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $iplist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $iplist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$iplist.status}</div>
    {elseif $iplist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$iplist.crawl_time}</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$iplist.subnet_id}">{$iplist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($iplist.crawler)}<a href="{if $iplist.crawler=='80'}http://{elseif $iplist.crawler=='21'}ftp://{elseif $iplist.crawler=='8888'}http://{/if}{$net_prefix}.{$iplist.ip}">{$iplist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Ips vorhanden</p>
{/if}

<h2>VPN (Verbindungs-Netze)</h2>

<div id="ipitem" style="width: 745px; overflow: hidden;">
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

{if !empty($iplist)}
{foreach key=count item=iplist from=$vpnlist}
<div id="ipitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$iplist.service_id}">{$net_prefix}.{$iplist.ip}</a></div>
    <div style="float:left; width: 50px;">{$iplist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$iplist.user_id}">{$iplist.nickname}</a></div>
    {if $iplist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $iplist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $iplist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$iplist.status}</div>
    {elseif $iplist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$iplist.crawl_time}</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$iplist.subnet_id}">{$iplist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($iplist.crawler)}<a href="{if $iplist.crawler=='80'}http://{elseif $iplist.crawler=='21'}ftp://{elseif $iplist.crawler=='8888'}http://{/if}{$net_prefix}.{$iplist.ip}">{$iplist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Ips vorhanden</p>
{/if}

<h2>Clients</h2>

<div id="ipitem" style="width: 745px; overflow: hidden;">
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

{if !empty($iplist)}
{foreach key=count item=iplist from=$clientlist}
<div id="ipitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$iplist.service_id}">{$net_prefix}.{$iplist.ip}</a></div>
    <div style="float:left; width: 50px;">{$iplist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$iplist.user_id}">{$iplist.nickname}</a></div>
    {if $iplist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $iplist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $iplist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$iplist.status}</div>
    {elseif $iplist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$iplist.crawl_time}</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$iplist.subnet_id}">{$iplist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($iplist.crawler)}<a href="{if $iplist.crawler=='80'}http://{elseif $iplist.crawler=='21'}ftp://{elseif $iplist.crawler=='8888'}http://{/if}{$net_prefix}.{$iplist.ip}">{$iplist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Ips vorhanden</p>
{/if}

<h2 style="white-space: nowrap;">Services (Server, Webseiten etc.)<!--(1 nicht sichbar -> <a href="./login.php">Login</a>)--></h2>

<div id="ipitem" style="width: 745px; overflow: hidden;">
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

{if !empty($iplist)}
{foreach key=count item=iplist from=$servicelist}
<div id="ipitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./service.php?service_id={$iplist.service_id}">{$net_prefix}.{$iplist.ip}</a></div>
    <div style="float:left; width: 50px;">{$iplist.crawler}</div>

    <div style="float:left; width: 100px;"><a href="./user.php?id={$iplist.user_id}">{$iplist.nickname}</a></div>
    {if $iplist.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $iplist.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {elseif $iplist.status=="ping"}
      <div style="float:left; width: 60px; background-color: #00c5cc;">{$iplist.status}</div>
    {elseif $iplist.status=="unbekannt"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_pending_small.png" alt="offline"></div>
    {/if}
    <div style="float:left; width: 150px;">{$iplist.crawl_time}</div>
    <div style="float:left; width: 150px;"><a href="./subnet.php?id={$iplist.subnet_id}">{$iplist.title}</a></div>
    <div style="float:left; width: 150px;">{if is_numeric($iplist.crawler)}<a href="{if $iplist.crawler=='80'}http://{elseif $iplist.crawler=='21'}ftp://{elseif $iplist.crawler=='8888'}http://{/if}{$net_prefix}.{$iplist.ip}:{if $iplist.crawler=='80'}80{elseif $iplist.crawler=='21'}21{elseif $iplist.crawler=='8888'}8888{/if}">{$iplist.services_title|substr:0:20}...</a>{else}&nbsp;{/if}</div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Ips vorhanden</p>
{/if}