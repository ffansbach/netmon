<h1>CCD-Einträge für ein Subnetz neu anlegen:</h1>

<form action="./vpn.php?section=insert_regenerate_ccd_subnet" method="POST">

  <p>Subnetz wählen:
  <select name="subnet_id">
  {foreach item=subnet from=$subnets}
    <option value="{$subnet.id}">{$net_prefix}.{$subnet.host}/{$subnet.netmask} ({$subnet.title})</option>
  {/foreach}
  </select>
  </p>

  <p><input type="submit" value="Absenden"></p>
</form>