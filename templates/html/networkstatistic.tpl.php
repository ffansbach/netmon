<h2>Router Status</h2>

<table style="text-align: center;
  vertical-align: baseline;
  font-size: 2em;
  font-weight: bold;">
<tr>

<td style="width: 33%; color: #007B0F;" ><img src="/templates/img/status_up_big.png" title="up - node is reachable" alt="up"/> {$router_status_history.0.online}</td>

<td class="node_status_down nodes" style="width: 33%; color: #CB0000;" ><img src="/templates/img/status_down_big.png" title="down - node is not visible via OLSR" alt="down"/> {$router_status_history.0.offline}</td>

<td class="node_status_pending nodes" style="width: 33%; color: #F8C901;" ><img src="/templates/img/status_pending_big.png" title="pending - node has not yet been seen since registration" alt="pending"/> {$router_status_history.0.unknown}</td>

</tr>
</table>

<h2>Router by Type</h2>
<p>
{foreach item=router_chipset from=$router_chipsets}
	<b>{$router_chipset.chipset_name}:</b> {$router_chipset.count}<br>
{/foreach}
</p>

<h2>Router status History</h2>
<img src="./tmp/networkstatistic_status.png">