<h1>IP <a href="http://{$net_prefix}.{$node.subnet_ip}.{$node.node_ip}">{$net_prefix}.{$node.subnet_ip}.{$node.node_ip}</h1>

<b>Inhaber:</b> <a href="./index.php?get=user&id={$node.user_id}">{$node.nickname}</a><br>
<b>Eingetragen seit:</b> {$node.create_date}<br>
<b>Subnetz:</b>  <a href="./subnet.php?id={$node.subnet_id}">{$node.title}</a><br>

<h2>Services auf dieser IP</h2>

<div id="nodeitem" style="width: 80%; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 100px;">Typ</div>
    <div style="float:left; width: 95px;">Crawler</div>
    <div style="float:left; width: 80px;">Status</div>
    <div style="float:left; width: 100px;">Aktionen</div>
  </div>
</div>

{foreach key=count item=service from=$servicelist}
<div id="nodeitem" style="width: 80%; overflow: hidden;">
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



{if $node.is_node_owner}
<h2>Aktionen</h2>

<p>
  <a href="./serviceeditor.php?section=new&node_id={$node.id}">Service hinzufügen</a>
</p>

<p>
  <a href="./nodeeditor.php?section=edit&id={$node.id}">Node Editieren</a>
</p>

<p>
  <a href="./vpn.php?section=new&node_id={$node.id}">Neue VPN-Zertifikate generieren</a><br>
  <a href="./vpn.php?section=info&node_id={$node.id}">VPN-Zertifikat Info und Config-Datei ansehen</a><br>
  <a href="./vpn.php?section=insert_regenerate_ccd&node_id={$node.id}">CCD neu anlegen</a><br>
  <a href="./vpn.php?section=insert_delete_ccd&node_id={$node.id}">CCD löschen</a><br>
  <a href="./vpn.php?section=download&node_id={$node.id}">VPN-Zertifikate und Config-Datei downloaden</a><br>
</p>

{/if}