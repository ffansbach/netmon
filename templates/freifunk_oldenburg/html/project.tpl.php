<h1>Projekt {$project_data.title}</h1>

<h2>Eigenschaften</h2>

<ul>
  {if $project_data.is_batman_adv=='1'}<li>B.A.T.M.A.N advanced</li>{/if}
  {if $project_data.is_olsr=='1'}<li>Olsr</li>{/if}
  {if $project_data.is_wlan=='1'}<li>Wlan</li>{/if}
  {if $project_data.is_vpn=='1'}<li>VPN</li>{/if}
  {if $project_data.is_ipv4=='1'}<li>IPv4</li>{/if}
  {if $project_data.is_ipv6=='1'}<li>IPv6</li>{/if}
</ul>