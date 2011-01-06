    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">

<html>
  <head>
    <TITLE>
      {$site_title}
    </TITLE>

    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    
    
    <!--<link rel="alternate" type="application/rss+xml"  href="./index.php?get=rss">
>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="{$globals_homepage_description}">
    <meta name="keywords" content="{$globals_homepage_keywords}">
    <meta name="author" content="{$globals_homepage_author}">
    <meta name="robots" content="index,follow">
    <meta http-equiv="content-language" content="{$globals_homepage_language}">
-->

    {$html_head}

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="./templates/css/design.css">
  </head>
  
  <body>

<!--Banner-->
<div id="banner" style="background-image: url(./templates/img/header/hafen_oldenburg_nacht.jpg);">
	<!--Top-Menü-->
	<div id="time">
		Serverzeit: {$zeit}
	</div>
	<div id="topmenu">
		{if !$installation_mode}
			{foreach $top_menu as $topmenu}
				<span class="topmenubox"><a style="color: #FFFFFF" href="{$topmenu.href}">{$topmenu.name}</a></span>
			{/foreach}
				<span class="topmenubox">|</span>
			{foreach $loginOutMenu as $menu}
				<span class="topmenubox">{$menu.pretext} <a style="color: #FFFFFF" href="{$loginOutMenu.href}"><b>{$menu.name}</b></a></span>
			{/foreach}
		{/if}
	</div>
</div>



<div id="main">
	<!--Linkes Menü-->
	<div id="left_menu">
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

	</div>

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
