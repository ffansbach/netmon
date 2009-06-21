<h1>Einen neuen Knoten im Netz {$net_prefix} eintragen:</h1>

<form action="./nodeeditor.php?section=insert" method="POST">

  <p>Subnetz wählen:
  <select name="subnet_id">
  {foreach item=subnet from=$existing_subnets}
    <option value="{$subnet.id}">{$subnet.subnet_ip}</option>
  {/foreach}
  </select>
  </p>

  <p>
    <p>Für Clients zu reservierende IP's: <input name="ips" type="text" size="1" maxlength="3" value="5"><br>Für Vergabe per DHCP (Wenn keine IP´s vergeben werden sollen bitte 0 eintragen!)</p>
  </p>

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

  <p>
    <p>(Optional) Titel: <input name="title" type="text" size="40" maxlength="40" value=""><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Beschreibung: <br><textarea name="description" cols="50" rows="10"></textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Radius: <input name="radius" type="text" size="5" maxlength="10" value="80"><br>
    Sinnvoll wenn Typ "node" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
  </p>

  <p>
    <p>Sichbar: 
    <select name="visible" size="1">
      <option value="1" selected>Ja</option>
      <option value="0">Nein</option>
    </select>
    <br>
    Alle Services sollten Sichbar sein. Wenn du aber einen Service anbietest bei dem es unter Umständen kritisch sein kann ihn öffentlich anzuzeigen, kannst du hier einstellen, dass nur angemeldete Personen den Service sehen können.</p>
  </p>

  <p>Hinweis:<br>Wenn eine IP gleichzeitig Node und Service ist, oder mehrere Services zur Verfügung stellt, können später noch weitere Services zum Node hinzugefügt werden. Hier bitte nur <b>einen</b> angeben!</p>

  <p><input type="submit" value="Absenden"></p>
</form>