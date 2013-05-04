<h1>Informationen zum Event {$event.id}</h1>
<h2>Grunddaten</h2>
<b>Angelegt am:</b> {$event.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr<br>
<b>Objekt:</b> {$event.object}<br>
<b>Objekt-ID:</b> {$event.object_id}<br>
<b>Event:</b> {$event.action}<br>

<h2>Zusatzdaten</h2>

{foreach key=index item=data from=$event_data}
	<b>{$index}:</b> {$data}<br>
{/foreach}