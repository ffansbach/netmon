<h1>Service zum Node {$net_prefix}.{$node_data.subnet_ip}.{$node_data.node_ip} hinzufügen</h1>

<form action="./index.php?get=serviceeditor&section=insert_service&node_id={$node_data.node_id}" method="POST">

  <p>
    <p>Typ: <input name="typ" type="text" size="5" maxlength="10" value="node"><br>
     Hier wird angegeben was sich hinter der IP verbirgt. (node, , vpn, client, service)</p>
  </p>

  <p>
    <p>Check: <input name="crawler" type="text" size="5" maxlength="10" value="json"><br>
    Hier wird angegeben wie die IP auf verfügbarkeit geprüft werden soll. Für node "json" eintragen, für vpn idR. auch json, für client "ping" und für service einen Port (bspw.: 21 oder 80), wenn nicht geprüft werden soll "no" eintragen!</p>
  </p>

  <p>
    <p>(Optional) Titel: <input name="title" type="text" size="40" maxlength="40" value=""><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Beschreibung: <br><textarea name="description" cols="50" rows="10"></textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p><input type="submit" value="Absenden"></p>
</form>