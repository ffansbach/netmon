<h1>Ip {$net_prefix}.{$ip_data.subnet_ip}.{$ip_data.ip_ip} editieren</h1>

<form action="./ipeditor.php?section=delete&id={$ip_data.ip_id}" method="POST">
  <h2>Ip Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>