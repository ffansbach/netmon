<form action="./index.php?get=subneteditor&section=insert" method="POST">

  <h1>Ein neues Subnetz im Netz {$net_prefix} erstellen:</h1>
  
  <h2>Daten zum Netz</h2>

  <p>Freie Subnetze:
  <select name="subnet_ip">
  {foreach item=subnet from=$avalailable_subnets}
    <option value="{$subnet}">{$subnet}</option>
  {/foreach}
  </select>
  </p>

  <p>VPN-Server: <input name="vpn_server" type="text" size="30"> Port:<input name="vpn_server_port" type="text" size="5"></p>
  <p>Protokoll:<input name="vpn_server_proto" type="text" size="5"> Device:<input name="vpn_server_device" type="text" size="5"></p>

  <p>Server CA.CRT:<br><textarea name="vpn_server_ca" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>
  <p>Server Cert:<br><textarea name="vpn_server_cert" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>
  <p>Server Key<br><textarea name="vpn_server_key" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>

  <p>Passphrase:<input name="vpn_server_pass" type="password" size="30"></p>

  <p><p><input type="checkbox" name="vpnserver_from_project_check" value="true">VPN-Server von Projekt:
  <select name="vpnserver_from_project">
  {foreach item=subnet from=$subnets_with_defined_vpnserver}
    <option value="{$subnet.id}">{$subnet.subnet_ip}</option>
  {/foreach}
  </select>
  </p>
  
  <p><input type="checkbox" name="no_vpnserver_check" value="true">Kein VPN-Server</p>



  <h2>Beschreibung (Optional)</h2>

  <p>Titel:<input name="title" type="text" size="30"></p>
  <p>Beschreibung:<br><textarea name="description" cols="50" rows="10"></textarea></p>

  <h2>Ort (Optional)</h2>
  <p>Länge:<input name="longitude" type="text" size="30"></p>
  <p>Breite:<input name="latitude" type="text" size="30"></p>
  <p>Radius:<input name="radius" type="text" size="30"></p>

    Openstreetmap<br>
    Länge, Breite und Umgebungsradius setzen

  <p><input type="submit" value="Absenden"></p>
</form>