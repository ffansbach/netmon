<h1>Mein Desktop</h1>

<h2>Menü Schnellzugriff</h2>

<div style="width: 800px; overflow: hidden; padding-bottom: 15px; border-top: solid 0px grey;">
  <div nstyle="white-space: nowrap;">


    <div style="float:left; padding: 5px; background: #ff6a5a; margin-right: 20px;  border: solid 1px; black; font-size: 14pt; font-weight: bold;">
      	<a href="./ipeditor.php?section=new">Neue IP</a>
    </div>

    <div style="float:left; padding: 5px; background: #ffac5b; margin-right: 20px;  border: solid 1px; black; font-size: 14pt; font-weight: bold;">
      	<a href="./ipeditor.php?section=new">IP-Liste</a>
    </div>

    <div style="float:left; padding: 5px; background: #fff55b; margin-right: 20px;  border: solid 1px; black; font-size: 14pt; font-weight: bold;">
      	<a href="./subnetlist.php">Projektliste</a>
    </div>

    <div style="float:left; padding: 5px; background: #5abbff; margin-right: 20px;  border: solid 1px; black; font-size: 14pt; font-weight: bold;">
	<a href="./user_edit.php?section=edit&id={$session_user_id}">Benutzereinstellungen</a>
    </div>

    <div style="float:left; padding: 5px; background: #8bfa69; margin-right: 20px;  border: solid 1px; black; font-size: 14pt; font-weight: bold;">
	<a href="http://wiki.freifunk-ol.de/index.php?title=Netmon" target="_blank">Hilfe</a>
    </div>
  </div>
</div>


<h2>Meine Dienste</h2>

{if !empty($servicelist)}
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


{foreach key=count item=iplist from=$servicelist}
<div id="ipitem" style="width: 745px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 85px;"><a href="./ip.php?id={$iplist.ip_id}">{$net_prefix}.{$iplist.ip}</a></div>
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
<div class="notice">Du hast noch keine IP´s und Services angelegt. Über den Link <a href="./ipeditor.php?section=new">Neue IP</a> eine neue IP mit Service anlegen.</div>
{/if}

{if !empty($subnetlist)}
<h2>Meine Projekte</h2>

<div id="ipitem" style="width: 410px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 200px;"><b>Name</b></div>
    <div style="float:left; width: 100px;"><b>Subnetz</b></div>
    <div style="float:left; width: 50px;"><b>Typ</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
  </div>
</div>


{foreach key=count item=subnetlist from=$subnetlist}
<div id="ipitem" style="width: 410px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 200px;"><a href="./subnet.php?id={$subnetlist.id}">{$subnetlist.title}</a></div>
    <div style="float:left; width: 100px;"><a href="./subnet.php?id={$subnetlist.id}">{$net_prefix}.{$subnetlist.host}/{$subnetlist.netmask}</a></div>
    <div style="float:left; width: 50px;">{$subnetlist.subnet_type}</div>
    <div style="float:left; width: 60px;">-/-</div>
  </div>
</div>
{/foreach}
{/if}

<h2>Hostory der letzten 24 Stunden</h2>

{if !empty($history)}
{foreach key=count item=hist from=$history}
	{$hist.create_date}: 
	{if $hist.type == 'user'}Der Benutzer <a href="./user.php?id={$hist.object_id}">{$hist.object_name_1}</a> hat sich registriert
	{elseif $hist.type == 'subnet'} Das Subnets <a href="./subnet.php?id={$hist.object_id}">{$hist.object_name_1}</a> wurde angelgt
	{elseif $hist.type == 'ip'} Die Ip <a href="./ip.php?id={$hist.object_id}">{$net_prefix}.{$hist.object_name_1}.{$hist.object_name_2}</a> wurde angelegt
	{elseif $hist.type == 'service'} Ein <a href="./service.php?id={$hist.service_id}">Service</a> wurde auf der IP <a href="./ip.php?id={$hist.ip_id}">{$net_prefix}.{$hist.ip}</a> angelegt
	{elseif $hist.data.action == 'status'}
		{if $hist.data.action == 'status' AND $hist.data.from=='offline'}
			<a href="./ip.php?id={$hist.additional_data.ip_id}"><b>{$net_prefix}.{$hist.additional_data.ip}</b></a>:<a href="./service.php?service_id={$hist.data.service_id}">{$hist.data.service_id}</a> geht <span style="background: green">online</span>.
		{elseif $hist.data.action == 'status' AND $hist.data.from=='online'}
			<a href="./ip.php?id={$hist.additional_data.ip_id}"><b>{$net_prefix}.{$hist.additional_data.ip}</b></a>:<a href="./service.php?service_id={$hist.data.service_id}">{$hist.data.service_id}</a> geht <span style="background: red">offline</span>.
		{/if}
	{elseif $hist.data.action == 'distversion'}
		{$net_prefix}.{$hist.additional_data.ip}:{$hist.data.service_id} ({$hist.additional_data.nickname}) Distversion geändert ({$hist.data.from} -> {$hist.data.to}).
	{/if}
<br>
{/foreach}
{else}
<div class="notice">In den letzten 24 Stunden ist nichts passiert.</div>
{/if}