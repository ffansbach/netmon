<h1>Netmon installieren:</h1>

<h2>Installationsmethode</h2>
<p>Es sind bereits Tabellen in der Datenbank vorhanden!<br>
Sie haben jetzt die folgenden Möglichkeiten:</p>

<div style="width: 100%; display:inline-block">
    <div style="float:left; width: 50%;">
		<h3>Tabellen überschreiben</h3>
		<p>Vorhandene Tabellen werden überschrieben. Sie erhalten eine frische Netmon installation.</p>
		<form action="./install.php?section=db_insert" method="POST">
			<p><input type="submit" value="Tabellen Überschreiben"></p>
		</form>
	</div>
	<div style="float:left; width: 50%;">
		<h3>Vorhandene Tabellen benutzen</h3>
		<p>Die Datenbank wird nicht verändert, es werden die vorhandenen Tabellen ohne Änderungen genutzt.</p>
		<form action="./install.php?section=finish" method="POST">
			<p><input type="submit" value="Vorhandene Tabellen benutzen"></p>
		</form>
	</div>
</div>