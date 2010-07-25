<h2>Konfigurationsscript hochladen</h2>
<form action="./imagemaker.php?section=upload_config" method="POST" enctype="multipart/form-data">
	<p>Image der Konfiguration: 
	<select name="image_id">
		{foreach item=images from=$images}
			<option value="{$images.image_id}">{$images.title} ({$images.nickname} am {$images.create_date})</option>
		{/foreach}
	</select>
	</p>
	<p>Konfigurationstitel:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Konfigurationsbeschreibung:<br>
		<textarea name="description" cols="57" rows="3"></textarea>
	</p>
	<p>Konfigurationsscript:<br>
		<input name="file" type="file" size="40">
	</p>
	<p><input type="submit" value="Hochladen"></p>
</form>

<h2>Konfiguration bearbeiten</h2>
<ul>
{foreach item=config from=$configs}
	<li>
		<b>{$config.title}</b> hochgeladen am {$config.create_date|date_format:"%e.%m.%Y"} von {$config.nickname} (<a href="./imagemaker.php?section=image_update&image_id={$image.image_id}">updaten</a>) (<a href="./imagemaker.php?section=image_delete&image_id={$image.image_id}">entfernen</a>)
	</li>
{/foreach}