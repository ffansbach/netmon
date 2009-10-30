<h1>IP <a href="http://{$net_prefix}.{$ip.ip}">{$net_prefix}.{$ip.ip}</a></h1>

<b>IP:</b> {$net_prefix}.{$ip.ip}<br>
<b>DHCP-bereich:</b> {if $ip.zone_start==0 OR $ip.zone_end==0}
						Kein DHCP-Bereich reserviert
						{else}
						{$net_prefix}.{$ip.subnet_ip}.{$ip.zone_start} bis {$net_prefix}.{$ip.subnet_ip}.{$ip.zone_end}
					{/if}<br>

<b>Benutzer:</b> <a href="./user.php?id={$ip.user_id}">{$ip.nickname}</a><br>
<b>Eingetragen seit:</b> {$ip.create_date}<br>
<b>Subnetz:</b>  <a href="./subnet.php?id={$ip.subnet_id}">{$net_prefix}.{$ip.subnet_host}/{$ip.subnet_netmask} ({$ip.title})</a><br>


<h2>Live-Status der IP:</h2>
<p>{if $ping}Aktueller Ping: {$ping} ms, online!{else}Ping nicht möglich, offline!{/if}</p>

<h2>Services auf dieser IP:</h2>

<div id="ipitem" style="width: 80%; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 100px;">Typ</div>
    <div style="float:left; width: 95px;">Crawler</div>
    <div style="float:left; width: 80px;">Status</div>
    <div style="float:left; width: 100px;">Aktionen</div>
  </div>
</div>

{foreach key=count item=service from=$servicelist}
<div id="ipitem" style="width: 80%; overflow: hidden;">
  <div style="float:left; width: 100px;"><a href="./service.php?service_id={$service.service_id}">{$service.typ}</a></div>
  <div style="float:left; width: 95px;">{$service.crawler}</div>
    {if $service.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$service.status}</div>
    {elseif $service.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$service.status}</div>
    {elseif $service.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$service.status}</div>
    {elseif $service.status=="unbekannt"}
      <div style="float:left; width: 80px; background-color: #fff3c3;">{$service.status}</div>
    {/if}
  <div style="float:left; width: 100px;"><a href="./serviceeditor.php?section=edit&service_id={$service.service_id}">Editieren</a></div>
</div>
{/foreach}



{if $ip.is_ip_owner}
<h2>Aktionen</h2>

<p>
  <a href="./serviceeditor.php?section=new&ip_id={$ip.ip_id}">Service hinzufügen</a>
</p>

<p>
  <a href="./ipeditor.php?section=edit&id={$ip.ip_id}">Ip Editieren</a>
</p>

<p>
  <a href="./vpn.php?section=new&ip_id={$ip.ip_id}">Neue VPN-Zertifikate generieren</a><br>
  <a href="./vpn.php?section=info&ip_id={$ip.ip_id}">VPN-Zertifikat Info und Config-Datei ansehen</a><br>
  <a href="./vpn.php?section=insert_regenerate_ccd&ip_id={$ip.ip_id}">CCD neu anlegen</a><br>
  <a href="./vpn.php?section=insert_delete_ccd&ip_id={$ip.ip_id}">CCD löschen</a><br>
  <a href="./vpn.php?section=download&ip_id={$ip.ip_id}">VPN-Zertifikate und Config-Datei downloaden</a><br>
</p>

{/if}