<h1>Edit Chipset Name von {$chipset_data.name} </h1>

<form action="./config.php?section=insert_edit_chipset_name&chipset_id={$smarty.get.chipset_id}" method="POST">
	Name:<br><input name="hardware_name" size="30" maxlength="30" value="{$chipset_data.hardware_name}">
	
	<p><input type="submit" value="Absenden"></p>
</form>