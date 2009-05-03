<form action="./index.php?get=subneteditor&section=update&id={$subnet_data.id}" method="POST">

  <h1>Subnetz {$net_prefix}.{$subnet_data.subnet_ip}.0/24 editieren:</h1>
  
  <h2>Daten zum Netz</h2>

  <p>subnetz:
  <select name="subnet">
    <option selected value="{$subnet_data.subnet}">{$subnet_data.subnet}</option>
  {foreach item=subnet from=$avalailable_subnets}
    <option value="{$subnet}">{$subnet}</option>
  {/foreach}
  </select>
  </p>
-----------------
  <p>Aktueller VPN-Server<br><input name="vpnserver" type="text" size="30" value="{$subnet_data.vpnserver}"></p>
  
  <p><p><input type="checkbox" name="vpnserver_from_project_check" value="true">VPN-Server von Projekt:
  <select name="vpnserver_from_project">
  {foreach item=subnet from=$subnets_with_defined_vpnserver}
    <option value="{$subnet.id}">{$subnet.id}</option>
  {/foreach}
  </select>
  </p>

<p><input type="checkbox" name="no_vpnserver_check" value="true">Kein VPN-Server</p>

  <p>CA.CRT:<br><textarea name="vpn_cacrt" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>

  <h2>Beschreibung (Optional)</h2>

  <p>Titel:<input name="title" type="text" size="30" value="{$subnet_data.title}"></p>
  <p>Beschreibung:<br><textarea name="description" cols="50" rows="10">{$subnet_data.description}</textarea></p>

  <h2>Ort (Optional)</h2>
  <p>Länge:<input name="longitude" type="text" size="30" value="{$subnet_data.longitude}"></p>
  <p>Breite:<input name="latitude" type="text" size="30" value="{$subnet_data.latitude}"></p>
  <p>Radius:<input name="radius" type="text" size="30" value="{$subnet_data.radius}"></p>

    Openstreetmap<br>
    Länge, Breite und Umgebungsradius setzen

  <p><input type="submit" value="Absenden"></p>
</form>

<form action="./index.php?get=subneteditor&section=delete&subnet_id={$subnet_data.id}" method="POST">
  <h2>Subnet Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>