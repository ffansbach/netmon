#!/bin/sh

nickname="Floh1111"
password="hs7812kxlsqjog"
netmon_url="http://netmon.freifunk-ol.de/"

session_id=`curl --silent -d "class=main&section=login&nickname=$nickname&password=$password" $netmon_url"api.php"`

echo $session_id

if [ $session_id != false ]; then
echo "Login erfolgreich"
else
echo "Login fehlgeschlagen"
fi

user_info=`curl --silent -d "class=main&section=public_user_info&user_id=1" $netmon_url"api.php"`
echo $user_info

project_info=`curl --silent -d "class=main&section=project_info" $netmon_url"api.php"`
echo $project_info