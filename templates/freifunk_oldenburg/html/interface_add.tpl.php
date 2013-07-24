<h1>Netzwerkinterface zu {$router->getHostname()} hinzufügen</h1>
<p>Hier kannst du deinem Router {$router->getHostname()} ein Netzwerkinterface hinzufügen. Nachdem du das Interface angelegt hast, kannst du dem Interface auch IP-Adressen hinzufügen.</p>
<form action="./interface.php?section=insert_add&router_id={$smarty.get.router_id}" method="POST">
	<h2>Eigenschaften</h2>
	<p><b>Name:</b> <input id="name" name="name" size="20" maxlength="20" ></p>
	
	<p><input type="submit" value="Absenden"></p>
</form>