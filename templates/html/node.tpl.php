<h1>IP <a href="http://{$net_prefix}.{$node.subnet_ip}.{$node.node_ip}">{$net_prefix}.{$node.subnet_ip}.{$node.node_ip}</h1>

<b>Inhaber:</b> <a href="./index.php?get=user&id={$node.user_id}">{$node.nickname}</a><br>
<b>Eingetragen seit:</b> {$node.create_date}<br>
<b>Subnetz:</b>  <a href="./index.php?get=subnet&id={$node.subnet_id}">{$node.title}</a><br>

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
  <div style="float:left; width: 100px;"><a href="./index.php?get=service&service_id={$service.service_id}">{$service.typ}</a></div>
  <div style="float:left; width: 95px;">{$service.crawler}</div>
    {if $service.status=="online"}
      <div style="float:left; width: 80px; background-color: green;">{$service.status}</div>
    {elseif $service.status=="offline"}
      <div style="float:left; width: 80px; background-color: red;">{$service.status}</div>
    {elseif $service.status=="ping"}
      <div style="float:left; width: 80px; background-color: #00c5cc;">{$service.status}</div>
    {/if}
  <div style="float:left; width: 100px;"><a href="./index.php?get=serviceeditor&section=edit&service_id={$service.service_id}">Editieren</div>
</div>
{/foreach}



{if $node.is_node_owner}
<h2>Aktionen</h2>

<p>
  <a href="./index.php?get=serviceeditor&section=new&node_id={$node.id}">Service hinzufügen</a>
</p>

<p>
  <a href="./index.php?get=nodeeditor&section=edit&id={$node.id}">Node Editieren</a>
</p>

<p>
  <a href="./index.php?get=vpn&section=new&node_id={$node.id}">Neue VPN-Zertifikate generieren</a><br>
  <a href="./index.php?get=vpn&section=info&node_id={$node.id}">VPN-Zertifikat Info ansehen</a><br>
  <a href="./index.php?get=vpn&section=insert_regenerate_ccd&node_id={$node.id}">CCD neu anlegen</a><br>
  <a href="./index.php?get=vpn&section=download&node_id={$node.id}">VPN-Zertifikate downloaden</a><br>
</p>

{/if}