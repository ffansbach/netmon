<h1>Node {$net_prefix}.{$node_data.subnet_ip}.{$node_data.node_ip} editieren</h1>

<form action="./index.php?get=nodeeditor&section=delete&id={$node_data.id}" method="POST">
  <h2>Node Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>