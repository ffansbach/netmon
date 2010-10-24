<h1>Liste der Projekte</h1>

<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 290px;"><b>Projektname</b></div>
    <div style="float:left; width: 80px;"><b>Bat. adv.</b></div>
    <div style="float:left; width: 80px;"><b>Olsr</b></div>
    <div style="float:left; width: 80px;"><b>Wlan</b></div>
    <div style="float:left; width: 80px;"><b>VPN</b></div>
    <div style="float:left; width: 80px;"><b>IPv4</b></div>
    <div style="float:left; width: 80px;"><b>IPv6</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
  </div>
</div>

{if !empty($projectlist)}
{foreach key=count item=project from=$projectlist}
<div id="ipitem" style="width: 1000px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 290px;"><a href="./project.php?project_id={$project.project_id}">{$project.title}</a></div>
    <div style="float:left; width: 80px;">{if $project.is_batman_adv=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 80px;">{if $project.is_olsr=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 80px;">{if $project.is_wlan=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 80px;">{if $project.is_vpn=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 80px;">{if $project.is_ipv4=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 80px;">{if $project.is_ipv6=='1'}Ja{else}Nein{/if}</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$project.user_id}">{$project.nickname}</a></div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Router vorhanden</p>
{/if}