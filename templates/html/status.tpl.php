<h1>Status um {$zeit} uhr</h1>

<div style="width: 800px; overflow: hidden; padding-bottom: 15px; border-top: solid 0px grey;">
  <div nstyle="white-space: nowrap;">


    <div style="float:left; width: 140px; margin-right: 20px;  border: solid 1px; black;">
      <div style="width: 140px;">Neuester Nutzer: </div>
      <div style="width: 140px;"><a href="./user.php?id={$newest_user.id}">{$newest_user.nickname}</a></div>
    </div>

    <div style="float:left; width: 240px; border: solid 1px; black; margin-right: 20px;">
      <div style="width: 140px;">Neueste Ip: </div>
      <div style="width: 240px;"><a href="./ip.php?id={$newest_ip.id}">{$net_prefix}.{$newest_ip.subnet_ip}.{$newest_ip.ip_ip}</a>, Benutzer <a href="./user.php?id={$newest_ip.user_id}">{$newest_ip.nickname}</a></div>
    </div>

    <div style="float:left; border: solid 1px; black;">
      <div style="">Neuester Service: </div>
      <div style="">{$newest_service.title} auf <a href="./ip.php?id={$newest_service.ip_id}">{$net_prefix}.{$newest_service.subnet_ip}.{$newest_service.ip_ip}</a></div>
    </div>
  </div>
</div>

<div style="width: 800px; overflow: hidden; padding-top: 15px; border-top: solid 1px grey;">
  <div nstyle="white-space: nowrap;">

    <!--<div style="float:left; width: 140px; margin-right: 20px;     border: solid 1px; black;">
      <div style="width: 140px;">Crawler Status: </div>
      {if $status.status=="Online"}
	<div style="width: 140px; background-color: green;">{$status.status}</div>
      {elseif $status.status=="Offline"}
	<div style="width: 140px; background-color: red;">{$status.status}</div>
      {/if}
    </div>

    <div style="float:left; width: 260px; margin-right: 20px;  border: solid 1px; black;">
      <div style="width: 260px;">Letzter Crawl um {$status.last_crawl} uhr </div>
      {if $status.status=="Online"}
	<div style="width: 260px;">Nächster Crwal um {$status.next_crawl} uhr</div>
      {elseif $status.status=="Offline"}
	<div style="width: 260px;">Nächster Crawl: Crawler ist offline!</div>
      {/if}
    </div>-->
<!--
    <div style="float:left; width: 290px; margin-right: 20px; border: solid 1px; black;">
      <div style="width: 290px;">Letzte Backups: </div>
      <div style="width: 290px;">Netmon FTP: 25.03.2009 15:20 uhr</div>
      <div style="width: 290px;">Netmon DB: 25.03.2009 15:20 uhr</div>
      <div style="width: 290px;">Wiki FTP: 25.03.2009 15:20 uhr</div>
      <div style="width: 290px;">Wiki DB: 25.03.2009 15:20 uhr</div>
      <div style="width: 290px;">Blog FTP: 25.03.2009 15:20 uhr</div>
      <div style="width: 290px;">Blog DB: 25.03.2009 15:20 uhr</div>
    </div>-->
  </div>
</div>