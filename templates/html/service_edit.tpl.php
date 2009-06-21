<h1>Service vom Node {$net_prefix}.{$servicedata.subnet_ip}.{$servicedata.node_ip} editieren</h1>

<form action="./serviceeditor.php?section=insert_edit&service_id={$servicedata.service_id}" method="POST">
  <p>
    <p>Typ: <input name="typ" type="text" size="5" maxlength="10" value="{$servicedata.typ}"><br>
     Hier wird angegeben was sich hinter der IP verbirgt. (node, , vpn, client, service)</p>
  </p>

  <p>
    <p>Check: <input name="crawler" type="text" size="5" maxlength="10" value="{$servicedata.crawler}"><br>
    Hier wird angegeben wie die IP auf verfügbarkeit geprüft werden soll. Für node "json" eintragen, für vpn idR. auch json, für client "ping" und für service einen Port (bspw.: 21 oder 80), wenn nicht geprüft werden soll "no" eintragen!</p>
  </p>

  <p>
    <p>(Optional) Titel: <input name="title" type="text" size="40" maxlength="40" value="{$servicedata.title}"><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Beschreibung: <br><textarea name="description" cols="50" rows="10">{$servicedata.description}</textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>
  
  <p>
    <p>(Optional) Radius: <input name="radius" type="text" size="5" maxlength="10" value="{$servicedata.radius}"><br>
    Sinnvoll wenn Typ "node" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
  </p>
  
  <p>
    <p>Sichbar: 
    <select name="visible" size="1">
      <option value="1" {if $servicedata.visible==1}selected{/if}>Ja</option>
      <option value="0" {if $servicedata.visible==0}selected{/if}>Nein</option>
    </select>
    <br>
    Alle Services sollten Sichbar sein. Wenn du aber einen Service anbietest bei dem es unter Umständen kritisch sein kann ihn öffentlich anzuzeigen, kannst du hier einstellen, dass nur angemeldete Personen den Service sehen können.</p>
  </p>
  
  <p><input type="submit" value="Absenden"></p>
</form>


<form action="./serviceeditor.php?section=delete&service_id={$servicedata.service_id}" method="POST">
  <h2>Service Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>