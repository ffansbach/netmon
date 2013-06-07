<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#event_notification_list').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "desc" ]]
	} );
} );

var object_list = new Array();

object_list['router_offline'] = new Array();
{/literal}
{foreach key=count item=router from=$routerlist}
	object_list['router_offline'].push(new Array({$router->getRouterId()}, '{$router->getHostname()}'));
{/foreach}
{literal}

object_list['network_down'] = new Array( new Array('netmon', 'Netmon'));


function apply_object_list(f) {
	var action = f.action.options[f.action.selectedIndex].value;
	f.object.options.length = object_list[action].length;
	for (var i=0; i<object_list[action].length; i++) {
		f.object.options[i].value = object_list[action][i][0];
		f.object.options[i].text = object_list[action][i][1];
	}
	/*
	if(action=='router_offline') {
		// ganze XML-datei einlesen und in variable 'XMLmediaArray' speichern
        $.get("./api/rest/routerlist", function(XMLmediaArray){
			// suche nach jedem (each) 'bluray' abschnitt 
			console.log($(XMLmediaArray));
			$(XMLmediaArray).find("router").each(function(){
				// gefundenen abschnitt in variable zwischenspeichern (cachen)
				var $myMedia = $(this);
				
				// einzelne werte auslesen und zwischenspeichern
				// attribute: funktion 'attr()'
				// tags: nach dem tag suchen & text auslesen
				var router_id = $myMedia.children('router_id').text();
				var hostname = $myMedia.children('hostname').text();
				
				// daten von jeden treffer ausgeben
				// unformatiert...nur zum zeigen!
				// append = inhalt/string dem kontainer anhängen
				//console.log("id: "+router_id+", hostname: "+hostname);
				f.object.add(new Option(hostname, router_id),
							 null);
			});
		});
	}*/
}

{/literal}
</script>

<h1>Benachrichtigungen</h1>

<p>Benutzer können sich beim Auftreten bestimmter Ereignisse innerhalb des Netzwerks benachrichtigen lassen.
Diese Benachrichtigungen können hier konfiguriert werden.</p>

<h2>Eingetragene Benachrichtigungen</h2>
{if !empty($event_notification_list)}
	<table class="display" id="event_notification_list" style="width: 100%;">
		<thead>
			<tr>
				<th>Was passiert</th>
				<th>Wer meldet</th>
				<th>Benachrichtigung pausiert</th>
				<th>Letzte Benachrichtigung</th>
				<th>Aktionen</h2>
			</tr>
		</thead>
		<tbody>
			{foreach item=event_notification from=$event_notification_list}
				<tr>
					<td>{$event_notification->getAction()}</td>
					<td>{$event_notification->getObject()}</td>
					<td>{if $event_notification->getNotified() == true}Ja{else}Nein{/if}</td>
					<td>{$event_notification->getNotificationDate()|date_format:"%H:%M"} Uhr</td>
					<td><a href="./event_notifications.php?action=delete&event_notification_id={$event_notification->getEventNotificationId()}">Nicht mehr benachrichtigen</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Benachrichtigungen konfiguriert.</p>
{/if}

<h2>Neue Benachrichtigung eintragen</h2>
<form action="./event_notifications.php" method="POST">
		<p>
		<select name="action" onchange="apply_object_list(this.form);">
			<option value="router_offline">Router ist offline</option>
			<option value="network_down">Großer Teil des Netzwerks offline</option>
		</select>
		<select name="object">
			{foreach key=count item=router from=$routerlist}
				<option value="{$router->getRouterId()}">{$router->getHostname()}</option>
			{/foreach}
		</select>
		<input type="submit" value="Eintragen">
		</p>
</form>