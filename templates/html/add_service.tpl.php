<h1>Service zum Ip {$net_prefix}.{$ip_data.subnet_ip}.{$ip_data.ip_ip} hinzufügen</h1>
<h2>Art des Services und Crawloptionen</h2>
<form action="./serviceeditor.php?section=insert_service&ip_id={$ip_data.ip_id}" method="POST">

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