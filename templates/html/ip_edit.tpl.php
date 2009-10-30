<h1>IP {$net_prefix}.{$ip_data.ip} editieren</h1>

<form action="./ipeditor.php?section=delete&id={$ip_data.ip_id}" method="POST">
  <h2>IP Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>