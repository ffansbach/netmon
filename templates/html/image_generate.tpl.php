<h1>Image generieren und downloaden</h1>

<h2>Ausgeführtes Kommando</h2>
<p>{$build_command}</p>

<h2>Ausgabe des Build prozesses</h2>
<p>
	{foreach item=line from=$build_prozess_return}
		{$line}<br>
	{/foreach}
</p>

<h2>Download</h2>
<a href="./scripts/imgbuild/dest/{$imagepath}/openwrt-atheros-root.squashfs" >openwrt-atheros-root.squashfs</a><br>
<a href="./scripts/imgbuild/dest/{$imagepath}/openwrt-atheros-vmlinux.lzma" >openwrt-atheros-vmlinux.lzma</a>

<h3>Optional</h2>
<a href="./imagemaker.php?section=download_config&imagepath={$imagepath}&ip_id=181" >config.zip</a><br>