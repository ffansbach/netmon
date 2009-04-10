<h1>Einen neuen Knoten im Netz {$net_prefix} eintragen:</h1>

<form action="./index.php?get=nodeeditor&section=insert" method="POST">

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

  <p>
    <p>Typ: <input name="typ" type="text" size="5" maxlength="10" value="node"><br>
     Hier wird angegeben was sich hinter der IP verbirgt. (node, client, service)</p>
  </p>

  <p>
    <p>Check: <input name="crawler" type="text" size="5" maxlength="10" value="json"><br>
    Hier wird angegeben wie die IP auf verfügbarkeit geprüft werden soll. Für node "json" eintragen, für client "ping" und für service einen Port (bspw.: 21 oder 80), wenn nicht geprüft werden soll "no" eintragen!</p>
  </p>

  <p>
    <p>(Optional) Titel: <input name="title" type="text" size="40" maxlength="40" value=""><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Beschreibung: <br><textarea name="description" cols="50" rows="10"></textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>Hinweis:<br>Wenn eine IP gleichzeitig Node und Service ist, oder mehrere Services zur Verfügung stellt, können später noch weitere Services zum Node hinzugefügt werden. Hier bitte nur <b>einen</b> angeben!</p>

  <p><input type="submit" value="Absenden"></p>
</form>