<h2>Image hochladen</h2>
<form action="./imagemaker.php?section=upload_image" method="POST" enctype="multipart/form-data">
	<h3>Upload beschränkungen</h3>
	<ul>
		<li><b>memory_limit:</b> {$memory_limit} MB</li>
		<li><b>post_max_size:</b> {$post_max_size} MB</li>
		<li><b>upload_max_filesize:</b> {$upload_max_filesize} MB</li>
	</ul>

	<p>Imagetitel:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Imagebeschreibung:<br>
		<textarea name="description" cols="57" rows="3"></textarea>
	</p>
	<p>Image (*.squashfs):<br>
		<input name="file" type="file" size="40">
	<p>Image (*.lzma):<br>
		<input name="file2" type="file" size="40">
	</p>
	<p><input type="submit" value="Hochladen"></p>
</form>

<h2>Image bearbeiten</h2>
<ul>
{foreach item=image from=$images}
	<li>
		<b>{$image.title}</b> hochgeladen am {$image.create_date|date_format:"%e.%m.%Y"} von {$image.nickname} (<a href="./imagemaker.php?section=image_update&image_id={$image.image_id}">updaten</a>) (<a href="./imagemaker.php?section=image_delete&image_id={$image.image_id}">entfernen</a>)
	</li>
{/foreach}