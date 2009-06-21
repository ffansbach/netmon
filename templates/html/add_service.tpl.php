<h1>Service zum Node {$net_prefix}.{$node_data.subnet_ip}.{$node_data.node_ip} hinzufügen</h1>

<form action="./serviceeditor.php?section=insert_service&node_id={$node_data.node_id}" method="POST">

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

  <p><input type="submit" value="Absenden"></p>
</form>