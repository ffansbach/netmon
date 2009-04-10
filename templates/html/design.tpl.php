    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">

<html>
  <head>
    <TITLE>
      Freifunk Oldenburg Portal
    </TITLE>
    
    
    <!--<link rel="alternate" type="application/rss+xml"  href="./index.php?get=rss">
>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="{$globals_homepage_description}">
    <meta name="keywords" content="{$globals_homepage_keywords}">
    <meta name="author" content="{$globals_homepage_author}">
    <meta name="robots" content="index,follow">
    <meta http-equiv="content-language" content="{$globals_homepage_language}">
-->
  </head>
  
  <body>


<link rel="stylesheet" type="text/css" href="./templates/css/design.css">

<!--Banner-->
<div id="banner" style="background-image: url(./templates/img/header/banner.jpg);"></div>



<!--Top-Menü-->
<div id="topmenu">
{foreach item=topmenu from=$top_menu}
  <span class="topmenubox"><a href="{$topmenu.href}">{$topmenu.name}</a></span>
{/foreach}
</div>

<div id="main">
  <!--Linkes Menü-->
  <div id="left_menu">
    <div id="normal_menu">
      &nbsp;&nbsp;&nbsp;&nbsp;<u>Navigation</u>
      <ul>
	{foreach item=normalmenu from=$normal_menu}
	  <li><a href="{$normalmenu.href}">{$normalmenu.name}</a></li>
	{/foreach}
      </ul>
    </div>

    <div id="user_menu">
      {if isset($user_menu)}
        &nbsp;&nbsp;&nbsp;&nbsp;<u>Benutzermenü</u>
	<ul>
	  {foreach item=usermenu from=$user_menu}
	    <li><a href="{$usermenu.href}">{$usermenu.name}</a></li>
	  {/foreach}
	</ul>
      {/if}
    </div>

    <!--Linkes Menü-->
    <div id="admin_menu">
      {if isset($admin_menu)}
	&nbsp;&nbsp;&nbsp;&nbsp;<u>Adminmenü</u>
	<ul>
	  {foreach item=menu from=$admin_menu}
	    </li><a href="{$menu.href}">{$menu.name}</a><li>
	  {/foreach}
	</ul>
      {/if}
    </div>
    <div id="freifunklogo">
    <a href="http://www.freifunk.net" target="_new">
<img border=0 alt="freifunk.net" src="http://netmon.freifunk-ol.de/templates/img/ff/Logo_ffn_170x165.gif">
</a>
</div>
  </div>
  <div id="content">
    <!--Systemmeldungen-->
    {foreach item=output from=$message}
      {if $output.1==0}
	<div style="background-color: #ffffff">{$output.0}</div>
      {/if}
      {if $output.1==1}
	<div style="background-color: #97ff5f">{$output.0}</div>
      {/if}
      {if $output.1==2}
	<div style="background-color: #ff5353">{$output.0}</div>
      {/if}
    {/foreach}

    <!--Content-->
    {if (isset($get_content))}
      {include file="$get_content.tpl.php"}
    {/if}
  </div>
</div>


  </body>
</html>