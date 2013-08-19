<h1>Dienst anlegen</h2>
<p>Auf dieser Seite kannst du einen Dienst anlegen den du im Netzwerk betreibst um ihn so anderen Menschen bekannt zu machen.</p>

<h2>Allgemeines</h2>
<form action="./service.php?section=insert_add&user_id={$smarty.get.user_id}" method="POST">
	<p>
		<b>Titel:</b> <br><input name="title" type="text" size="40" maxlength="50" value="">
	</p>
	
	<p>
		<b>Kurze Beschreibung des Dienstes:</b><br>
		<textarea name="description" cols="40" rows="4"></textarea>
	</p>
	
	<h2>Netzwerk</h2>
	<p>
		<b>Port:</b> <br><input name="port" type="text" size="4" maxlength="10" value="">
	</p>
	
	<p>
		<b>Hinweis</b><br>
		Im Folgenden genügt es eine IP-Adresse oder einen Ressource Record auszuwählen. Mehrere IP-Adressen und Ressource Records können
		ausgewählt werden indem die STRG-Taste gedrückt gehalten wird.
	</p>
	
	<p>
		<b>Ressource Records unter denen dieser Dienst erreichbar ist</b><br>
		{if !empty($dns_ressource_record_list)}
			<select multiple size="6" name="dns_ressource_record_list[]">
				{foreach item=dns_ressource_record from=$dns_ressource_record_list}
				<option value="{$dns_ressource_record->getDnsRessourceRecordId()}">{$dns_ressource_record->getHost()}.{$dns_ressource_record->getDnsZone()->getName()}</option>
				{/foreach}
			</select>
		{else}
			<p>Du verwaltest keine Ressource Records.</p>
		{/if}
	</p>
	
	<p>
		<b>IP-Adressen unter denen dieser Dienst erreichbar ist:</b><br>
		{if !empty($routerlist)}
		<select multiple size="10" name="iplist[]">
			{foreach item=router from=$routerlist}
				{foreach item=networkinterface from=$router->getNetworkinterfaceList()->getNetworkinterfaceList()}
					{foreach item=ip from=$networkinterface->getIplist()->getIplist()}
						<option value="{$ip->getIpId()}">{$router->getHostname()} -> {$networkinterface->getName()} -> {$ip->getIp()}</option>
					{/foreach}
				{/foreach}
			{/foreach}
		</select>
		{else}
			<p>Du verwaltest keine Ip-Adressen.</p>
		{/if}
	</p>
	<p><input type="submit" value="Absenden"></p>
</form>