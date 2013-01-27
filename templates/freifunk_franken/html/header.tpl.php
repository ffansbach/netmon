    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
    <title>Freifunk Franken | Netmon</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link href="./templates/{$template}/css/central_netmon.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 7]>
    <link href="css/central_patches.css" rel="stylesheet" type="text/css" />
    <![endif]-->
</head>
<body>

<div id="page_margins">
	<div id="page">
		<div id="header">
			<div id="topnav">
				<!-- start: skip link navigation -->
				<a class="skip" href="#navigation" title="skip link">Skip to the navigation</a><span class="hideme">.</span>
				<a class="skip" href="#content" title="skip link">Skip to the content</a><span class="hideme">.</span>
				<!-- end: skip link navigation -->
				<span>
					{if !$installation_mode}
<!--						{foreach $top_menu as $topmenu}
							<span class="topmenubox"><a style="color: #FFFFFF" href="{$topmenu.href}">{$topmenu.name}</a></span>
						{/foreach}-->
						{foreach $loginOutMenu as $menu key=count}
							{if $count != 0}| {/if}<a href="{$menu.href}">{$menu.name}</a>
						{/foreach}
					{/if}
					<!--<a href="http://wiki.freifunk-ol.de/index.php?title=Kontakt">Kontakt</a> | <a href="http://wiki.freifunk-ol.de/index.php?title=Freifunk_OL_Wiki:Impressum">Impressum</a>-->
				</span>
			</div>
		    <div id="headerbg">
		    </div>
			<h1>Freifunk Franken | Netmon</h1>
			<span>Die freie WLAN-Community aus Franken &#8226; Freie Netze für alle!</span>
		</div>				
		<!-- begin: main navigation #nav -->
		<div id="nav"> <a id="navigation" name="navigation"></a>
			<!-- skiplink anchor: navigation -->
			<div id="nav_main">
				<!--<ul>
					<li><a href="http://blog.freifunk-ol.de/">Neues</a></li>
					<li><a href="http://wiki.freifunk-ol.de/">Informationen</a></li>
					<li><a href="http://wiki.freifunk-ol.de/index.php?title=Kontakt">Kontakt</a></li>
					<li id="current"><a href="#">Netzwerk</a></li>
					<li><a href="http://ticket.freifunk-ol.de">Entwicklung</a></li>
				</ul>-->

				<div class="searchbox">
                  <form id="searchForm" name="searchForm" action="./search.php" method="POST">
                    <input class="suchBox" type="text" onblur="this.value='Suchbegriff eingeben...'" onclick="this.value=''" value="Suchbegriff eingeben..." name="search_string"/>
                    <input type="hidden" value="all" name="search_range"/>
                  </form>
                </div>
			</div>
			<div id="nav_sub">
					{if $installation_mode}
						{if isset($installation_menu)}
							<ul>
								{foreach $installation_menu as $menu}
									<li><a href="{$menu.href}">{$menu.name}</a></li>
								{/foreach}
							</ul>
						{/if}
					{/if}
					{if !$installation_mode}
						<ul>
							{foreach $normal_menu as $normalmenu}
								<li><a {if $normalmenu.selected=='true'}class="selected"{/if} href="{$normalmenu.href}">{$normalmenu.name}</a>{$normalmenu.selected}</li>
							{/foreach}
						</ul>
					{/if}
			
					{if !$installation_mode}
						{if isset($user_menu)}
							<ul>
								{foreach $user_menu as $usermenu}
									<li><a {if $usermenu.selected=='true'}class="selected"{/if} href="{$usermenu.href}">{$usermenu.name}</a></li>
								{/foreach}
							</ul>
						{/if}
					{/if}
					{if !$installation_mode}
						{if isset($admin_menu)}
							<ul>
								{foreach $admin_menu as $menu}
									<li><a {if $menu.selected=='true'}class="selected"{/if} href="{$menu.href}">{$menu.name}</a></li>
								{/foreach}
							</ul>
						{/if}
					{/if}
					{if !$installation_mode}
						{if isset($root_menu)}
							<ul>
								{foreach $root_menu as $menu}
									<li><a {if $menu.selected=='true'}class="selected"{/if} href="{$menu.href}">{$menu.name}</a></li>
								{/foreach}
							</ul>
						{/if}
					{/if}
			</div>
		</div>
		<!-- end: main navigation -->
		<!-- begin: main content area #main -->



<!--
  <head>
    <TITLE>
      {$site_title}
    </TITLE>

    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    
    

    {$html_head}

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="./templates/{$template}/css/central_netmon.css">
  </head>
  
  <body>


<div id="banner" style="background-image: url(./templates/{$template}/img/header/hafen_oldenburg_nacht.jpg);">

	<div style="float:left">
	<div id="time">
		Serverzeit: {$zeit}
	</div>
	<br style="clear:both;">
	<div id="time_next_crawl">
		Crawl Ende: {$actual_crawl_cycle.crawl_date_end|date_format:"%H:%M"} Uhr (noch {$actual_crawl_cycle.crawl_date_end_minutes} Minuten)
	</div>
	</div>

	<div id="topmenu">
		{if !$installation_mode}
			{foreach $top_menu as $topmenu}
				<span class="topmenubox"><a style="color: #FFFFFF" href="{$topmenu.href}">{$topmenu.name}</a></span>
			{/foreach}
				<span class="topmenubox">|</span>
			{foreach $loginOutMenu as $menu}
				<span class="topmenubox">{$menu.pretext} <a style="color: #FFFFFF" href="{$menu.href}"><b>{$menu.name}</b></a></span>
			{/foreach}
		{/if}
	</div>
</div>
-->


<div id="main">
	<!--Linkes Menü-->
<!--	<div id="left_menu">
		{if $installation_mode}
			<div class="user_menus">
				{if isset($installation_menu)}
					&nbsp;&nbsp;&nbsp;&nbsp;<b>Installationsmenü</b>
					<ul>
						{foreach $installation_menu as $menu}
							<li><a href="{$menu.href}">{$menu.name}</a></li>
						{/foreach}
					</ul>
				{/if}
			</div>
		{/if}
		{if !$installation_mode}
			<div class="user_menus">
				&nbsp;&nbsp;&nbsp;&nbsp;<b>Navigation</b>
				<ul>
					{foreach $normal_menu as $normalmenu}
						<li><a href="{$normalmenu.href}">{$normalmenu.name}</a></li>
					{/foreach}
				</ul>
			</div>
		{/if}

		{if !$installation_mode}
			<div class="user_menus">
				{if isset($user_menu)}
					&nbsp;&nbsp;&nbsp;&nbsp;<b>Benutzermenü</b>
					<ul>
						{foreach $user_menu as $usermenu}
							<li><a href="{$usermenu.href}">{$usermenu.name}</a></li>
						{/foreach}
					</ul>
				{/if}
			</div>
		{/if}
		{if !$installation_mode}
			<div class="user_menus">
				{if isset($admin_menu)}
					&nbsp;&nbsp;&nbsp;&nbsp;<b>Adminmenü</b>
					<ul>
						{foreach $admin_menu as $menu}
							<li><a href="{$menu.href}">{$menu.name}</a></li>
						{/foreach}
					</ul>
				{/if}
			</div>
		{/if}
		{if !$installation_mode}
			<div class="user_menus">
				{if isset($root_menu)}
					&nbsp;&nbsp;&nbsp;&nbsp;<b>Rootmenü</b>
					<ul>
						{foreach $root_menu as $menu}
							<li><a href="{$menu.href}">{$menu.name}</a></li>
						{/foreach}
					</ul>
				{/if}
			</div>
		{/if}

	</div>-->

  <div id="content">
    <!--Systemmeldungen-->
    {foreach $message as $output}
      {if $output.1==0}
	<div style="background-color: #f7ce3e">{$output.0}</div>
      {/if}
      {if $output.1==1}
	<div style="background-color: #97ff5f">{$output.0}</div>
      {/if}
      {if $output.1==2}
	<div style="background-color: #ff5353">{$output.0}</div>
      {/if}
    {/foreach}
