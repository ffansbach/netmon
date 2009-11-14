<h1>IP anlegen:</h1>
<form action="./ipeditor.php?section=insert" method="POST">
	<h2>Subnetz</h2>
	<p>
		IP im Subnetz:
		<select name="subnet_id">
			{foreach item=subnet from=$existing_subnets}
				<option value="{$subnet.id}">{$net_prefix}.{$subnet.subnet_ip}.0/24 ({$subnet.title})</option>
			{/foreach}
		</select> anlegen.
	</p>
	
	<h2>IP</h2>
	<p>
		<input type="radio" name="ip_kind" value="simple" checked="checked" onchange="document.getElementById('extend').style.display = 'none';">IP vom System generieren lassen<br>
		<input type="radio" name="ip_kind" value="extend" onchange="document.getElementById('extend').style.display = 'block';">IP selber angeben<br>
	</p>
	<div id="extend" style="display: none;">
		<b>IP:</b>  {$net_prefix}.<input name="ip" type="text" size="7">
	</div>

	<h2>DHCP</h2>
	<p>
		<p>Für Clients zu reservierende IP's: <input name="ips" type="text" size="1" maxlength="3" value="5"><br>Für Vergabe per DHCP (Wenn keine IP´s vergeben werden sollen bitte 0 eintragen!)</p>
	</p>

	<h2>Reichweite</h2>

  <p>
	Radius (optional): <input name="radius" type="text" size="5" maxlength="10" value="80"><br>
    Sinnvoll wenn Typ "ip" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
  </p>

	<h1>Service anlegen</h1>

<script type="text/javascript" src="./templates/js/servicesAuswahl.js"></script>
<script type="text/javascript" src="./templates/js/LinkedSelection.js"></script>
<script type="text/javascript">

{literal}

window.onload = function()
{
  var vk = new LinkedSelection( [ 'typ', 'crawl'], serviceAuswahl );
}

{/literal}

</script>

<p>
<label id="typLabel" for="typ">Service:</label>
<select id="typ" name="typ">
  <option value="--">Bitte wählen:</option>
  <option value="node">node</option>
  <option value="vpn">vpn</option>
  <option value="client">client</option>
  <option value="service">service</option>
</select>

<label id="crawlLabel" for="crawl">Crawlart:</label>

<select id="crawl" name="crawler">
  <option value="--">------</option>
</select>

<span id="portInput" style="visibility:hidden; margin-left: 5px;">
  Portnummer: <input name="port" type="text" size="5" maxlength="10" value="">
</span> 
</p>
<h2>Beschreibung</h2>
  <p>
    <p>Titel:<br><input name="title" type="text" size="40" maxlength="40" value=""><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>Beschreibung: <br><textarea name="description" cols="50" rows="10"></textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>
  
  <h2>Privatsphäre:</h2>
  <p>
    <p>Diesen Service nicht angemeldeten Benutzer zeigen: 
    <select name="visible" size="1">
      <option value="1" selected>Ja</option>
      <option value="0">Nein</option>
    </select>
    <br>
    Alle Services sollten Sichbar sein. Wenn du aber einen Service anbietest bei dem es unter Umständen kritisch sein kann ihn öffentlich anzuzeigen, kannst du hier einstellen, dass nur angemeldete Personen den Service sehen können.</p>
  </p>

	<h2>Benachrichtigungen:</h2>
	<p>Ein Crawldurchgang dauert {$timeBetweenCrawls} Minuten.<br>
	Benachrichtige mich, wenn dieser Service länger als <input name="notification_wait" type="text" size="2" maxlength="2" value="6"> Crawldurchgänge nicht erreichbar ist
    <select name="notify" size="1">
      <option value="1" selected>Ja</option>
      <option value="0">Nein</option>
    </select>
	</p>


  <p><input type="submit" value="Absenden"></p>
</form>