<h1>CCD-Einträge für ein Subnetz neu anlegen:</h1>

<form action="./index.php?get=vpn&section=insert_regenerate_ccd_subnet" method="POST">

  <p>Subnetz wählen:
  <select name="subnet_id">
  {foreach item=subnet from=$subnets}
    <option value="{$subnet.id}">{$subnet.subnet_ip}</option>
  {/foreach}
  </select>
  </p>

  <p><input type="submit" value="Absenden"></p>
</form>