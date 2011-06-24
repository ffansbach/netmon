<h1>Image generieren und downloaden</h1>

<h2>Ausgeführtes Kommando</h2>
<p>{$build_command}</p>

<h2>Ausgabe des Build prozesses</h2>
<p>
	{foreach $build_prozess_return as $line}
		{$line}<br>
	{/foreach}
</p>

<h2>Download</h2>
<a href="./tmp/{$imagepath}/openwrt-root.squashfs" >openwrt-root.squashfs</a><br>
<a href="./tmp/{$imagepath}/openwrt-vmlinux.lzma" >openwrt-vmlinux.lzma</a>

<h3>Optional</h2>
<a href="./imagemaker.php?section=download_config&imagepath={$imagepath}&ip_id={$smarty.get.ip_id}" >config.zip</a><br>