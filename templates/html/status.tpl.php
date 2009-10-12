<h1>Statusübersicht</h1>

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