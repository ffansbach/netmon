<script type="text/javascript">

{literal}
	function hideUrl(){
		if(document.getElementById('use_netmons_url').checked == true){
			document.getElementById('url').style.display = 'none';
		} else {
			document.getElementById('url').style.display = 'block';
		}
	}

	function writeSame() {
			document.getElementById('real_host').value = document.getElementById('host').value;
	}


window.onload = function()
{
  var vk = new LinkedSelection( [ 'typ', 'crawl'], serviceAuswahl );
}

{/literal}

</script>

<h1>Dienst zum Router <i>{$router_data.hostname}</i> hinzufügen</h1>

<form action="./serviceeditor.php?section=insert_add&router_id={$smarty.get.router_id}" method="POST">
	<h2>Port und URL</h2>
	<p>
		Portnummer: <input name="port" type="text" size="5" maxlength="10" value="">
	</p>

	<p>URL-Prefix
		<select name="url_prefix" size="1">
			<option value="http://" selected>http://</option>
			<option value="ftp://">ftp://</option>
			<option value="">Kein URL Prefix</option>
		</select>
	</p>
	
	<h2>Beschreibung</h2>
	<p>
		Titel:<br>
		<input name="title" type="text" size="40" maxlength="40" value="">
	</p>
	
	<p>
		<p>Beschreibung: <br>
		<textarea name="description" cols="50" rows="3"></textarea>
	</p>
	
	<p>
		<input id="use_netmons_url" name="use_netmons_url" type="checkbox" onchange="hideUrl()" value="1" checked="checked"> Netmon soll versuchen eine URL zu generieren.
	</p>
	
	<div id="url" style="display:none;">
		<p>URL:<br><input name="url" type="text" size="50" maxlength="250"><br>
		Gibt die URL an unter welcher die Seiten der Services ereichbar sind (optional).
	</div>
	
	<h2>Privatsphäre:</h2>
	<p>
		<p>Diesen Service nicht angemeldeten Benutzer zeigen: 
			<select name="visible" size="1">
				<option value="1" selected>Ja</option>
				<option value="0">Nein</option>
			</select>
		</p>
	</p>
	
	<h2>Benachrichtigungen:</h2>
	<p>Benachrichtige mich, wenn dieser Service länger als <input name="notification_wait" type="text" size="2" maxlength="2" value="6"> Crawldurchgänge nicht erreichbar ist
		<select name="notify" size="1">
			<option value="1" selected>Ja</option>
			<option value="0">Nein</option>
		</select>
	</p>
	
	<p><input type="submit" value="Absenden"></p>
</form>