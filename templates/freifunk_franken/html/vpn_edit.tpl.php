<h1>IP {$net_prefix}.{$ip_data.ip} editieren</h1>

<form action="./ipeditor.php?section=insert_edit&id={$ip_data.ip_id}" method="POST">
	<h2>VPN-CCD</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 55%;">
			<h3>CCD-Eintrag (mehrzeilige Einträge möglich)</h3>
			<textarea name="ccd" cols="50" rows="5">{$ccd}</textarea>
		</div>
		
		<div style="float:left; width: 45%;">
			<h3>Mögliche Optionen</h3>
			<ul>
				<li>
					push redirect-gateway
				</li>
			</ul>
        </div>
	</div>

	<p><input type="submit" value="Ändern"></p>
</form>