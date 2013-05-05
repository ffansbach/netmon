<script src="lib/extern/jquery/jquery.min.js"></script>

{literal}
<script type="text/javascript">
	function fetchDestinationlist() {
		//get selected ressource record type and user_id of current logged in user
		var type = $('#type').val();
		{/literal}var user_id = {$smarty.session.user_id};{literal}
		///-----
		
		//empty selection and add loading... option
		$("#destination").empty();
		var option = document.createElement("option");
		option.text = "Loading...";
		option.value = "loading";
		document.getElementById("destination").appendChild(option);
					
		if(type == "A" || type == "AAAA") {
			$.ajax({
				type: "GET",
				url: "./api/rest/user/"+user_id+"/routerlist/",
				dataType: "xml",
				success: parse_ips,
				error:	function() {
							alert("Error: Something went wrong");
						}
			});
		} else if (type == "CNAME") {
			alert("not implemented");
		} else {
			alert("What?");
		}
	}
	
	function parse_ips(xml) {
		//get selected ressource record type
		var type = $('#type').val();
	
		var routerlist = $(xml).find("netmon_response routerlist router");
		
		//loop through all router elements
		routerlist.each(function() {
			//fetch hostname
			var hostname = $(this).find("hostname:first").text();
			
			//fetch all networkinterface elements and loop through them
			var interfacelist = $(this).find("networkinterfacelist networkinterface");
			interfacelist.each(function() {
				//fetch interface name
				var ifname = $(this).find("name:first").text();
				
				//fetch all ip elements in first level and loop through them
				var iplist = $(this).find("ip:nth-child(1)");
				iplist.each(function() {
					//fetch ip id and ip address
					var ip_id = $(this).find("ip_id").text();
					var ip = $(this).find("ip:first").text();
					var ipv = $(this).find("ipv").text();
					
					if((type == 'A' && ipv == 4) || (type == 'AAAA' && ipv == 6)) {
						var option = document.createElement("option");
						option.text = hostname+' -> '+ifname+' -> '+ip;
						option.value = ip_id;
						document.getElementById("destination").appendChild(option);
					}
				});
			});
		});
		//remove the loading... option
		$("#destination option[value='loading']").remove();
	}
	
	$(document).ready(function(){
		fetchDestinationlist('A');
	});
</script>
{/literal}

<h1>Ressource-Record hinzufügen</h1>
<p>Hier können Benutzer einen neuen DNS Ressource Record zu einer DNS-Zone hinzufügen. Hinzugefügt werden können A-, AAAA- und NS-Records
auf vorher eingetragene IP-Adressen, sowie CNAME-Records auf bereits existierende Records.</p>

<h2>Neuer Ressource Record</h2>
<form action="./dns_ressource_record.php?section=insert_add" method="POST">
	<p>
		<b>Zone:</b> 
		<select id="dns_zone_id" name="dns_zone_id">
			{foreach item=dns_zone from=$dns_zone_list}
				<option value="{$dns_zone->getDnsZoneId()}">{$dns_zone->getName()}</option>
			{/foreach}
		</select>
	</p>
	
	<p>
		<b>Host:</b> <br><input name="host" type="text" size="20" maxlength="100">
	</p>
	
	<p>
		<b>Type:</b> 
		<select id="type" name="type" onChange="fetchDestinationlist();">
			<option value="A" selected="true">A</option>
			<option value="AAAA">AAAA</option>
			<option value="NS">NS</option>
			<option value="CNAME">CNAME</option>
		</select>
	</p>
	
	<p>
		<b>Destination:</b> 
		<select id="destination" name="destination">
			<option value="loading">Loading...</option>
		</select>
	</p>
	
	<p><input type="submit" value="Absenden"></p>
</form>