<h1>Imagemaker</h1>

<p>Imagemaker ist ein tool, zum Up- und Download vorkompilierter, per Konfiguartionsscript konfigurierbarer Images.<br>
Administratoren können verschiedene Images mit verschiedenen Konfigurationsscripten uploaden, wärend Benutzer diese Images
konfigurieren und Downloaden können.</p>


<h2>Image hochladen</h2>
<form action="./imagemaker.php?section=upload_image" method="POST" enctype="multipart/form-data">
	<p>Achtung die Maximale Imagegröße beträgt <b>{$memory_limit}/{$post_max_size}/{$upload_max_filesize}MB</b>!</p>

	<p>Imagetitel:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Imagebeschreibung:<br>
		<textarea name="description" cols="57" rows="3"></textarea>
	</p>
	<p>Image (*.tar.gz):<br>
		<input name="file" type="file" size="40">
	</p>
	<p><input type="submit" value="Hochladen"></p>
</form>

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

<h2>Image konfigurieren und Downloaden</h2>

<p>
{foreach item=ip from=$user_ips}
	Image für <a href="./imagemaker.php?section=new&ip_id={$ip.id}">{$net_prefix}.{$ip.ip}</a><br>
{/foreach}
</p>