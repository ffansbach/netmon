<h1>Neues Image generieren</h1>

<h2>Hinweis:</h2>
<p>Die Felder unten sind in der Regel vorausgefüllt und müssen nicht geändert werden!</p>

<h2>Daten zur erstellung des Images:</h2>


<form action="./imagemaker.php?section=generate&ip_id={$ip_data.ip_id}" method="POST">
  <p>Chipset:<br><input name="chipset" type="text" size="30" maxlength="30"  value="{$configdata.chipset}"></p>
  <h2>Netzwerk</h2>
  <p>IP:<br><input name="ip" type="text" size="30" maxlength="30"  value="{$configdata.ip}"></p>
  <p>Subnetmask:<br><input name="subnetmask" type="text" size="30" maxlength="30"  value="{$configdata.subnetmask}"></p>
  <p>dhcp_start:<br><input name="dhcp_start" type="text" size="30" maxlength="30"  value="{$configdata.dhcp_start}"></p>
  <p>dhcp_limit:<br><input name="dhcp_limit" type="text" size="30" maxlength="30"  value="{$configdata.dhcp_limit}"></p>

  <h2>WLAN</h2>
  <p>essid:<br><input name="essid" type="text" size="30" maxlength="30"  value="{$configdata.essid}"></p>
  <p>bssid:<br><input name="bssid" type="text" size="30" maxlength="30"  value="{$configdata.bssid}"></p>
  <p>channel:<br><input name="channel" type="text" size="30" maxlength="30"  value="{$configdata.channel}"></p>

  <h2>VPN</h2>
  VPN-IP:
  <select name="vpn_ip_id">
	<option value="false" selected>Kein VPN</option>
		{foreach item=vpn_ip from=$vpn_ips}
			<option value="{$vpn_ip.ip_id}">{$net_prefix}.{$vpn_ip.ip} ({$vpn_ip.subnet_title})</option>
		{/foreach}
  </select>

  <h2>Geogrphisches</h2>
  <p>location:<br><input name="location" type="text" size="30" maxlength="30"  value="{$configdata.location}"></p>
  <p>longitude:<br><input name="longitude" type="text" size="30" maxlength="30"  value="{$configdata.longitude}"></p>
  <p>latitude:<br><input name="latitude" type="text" size="30" maxlength="30"  value="{$configdata.latitude}"></p>
  <h2>Kontakt</h2>
  <p>nickname:<br><input name="nickname" type="text" size="30" maxlength="30"  value="{$configdata.nickname}"></p>
  <p>vorname:<br><input name="vorname" type="text" size="30" maxlength="30"  value="{$configdata.vorname}"></p>
  <p>nachname:<br><input name="nachname" type="text" size="30" maxlength="30"  value="{$configdata.nachname}"></p>
  <p>Email :<br><input name="email" type="text" size="30" maxlength="30"  value="{$configdata.email}"></p>
  <h2>Community</h2>
  <p>prefix:<br><input name="prefix" type="text" size="30" maxlength="30"  value="{$configdata.prefix}"></p>
  <p>community_name:<br><input name="community_name" type="text" size="30" maxlength="30"  value="{$configdata.community_name}"></p>
  <p>community_website :<br><input name="community_website" type="text" size="30" maxlength="30"  value="{$configdata.community_website}"></p>

  <h2>Sonstiges</h2>
  <input name="imagepath" type="hidden" size="30" maxlength="30"  value="{$configdata.imagepath}">
  <p>Root  Passwort:<br><input name="rootpassword" type="text" size="30" maxlength="30"  value="root" disabled></p>
<p>Build Kommando:<br>
{$build_command}
</p>

  <p><input type="submit" value="Image generieren"></p>
</form>