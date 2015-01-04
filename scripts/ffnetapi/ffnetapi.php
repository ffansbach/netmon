<?php

?>

{
  "name": "Freifunk Oldenburg",
  "url": "http://www.freifunk-ol.de",
  "location": {
    "city": "Oldenburg",
    "country": "DE",
    "lat": 53.1402606,
    "lon": 8.2152415,
    "address": {
      "Name": "KtT Mainframe",
      "Street": "Bahnhofsplatz",
      "Zipcode": "26122"
    }
  },
  "contact": {
    "email": "fragen@freifunk-ol.de",
    "irc": "irc://irc.freenode.net:6667/#ffol",
    "ml": "https://lists.nord-west.net/mailman/listinfo/freifunk-ol",
    "facebook": "https://www.facebook.com/FreifunkOL",
    "twitter": "@ff_ol"
  },
  "state": {
    "nodes": <?php echo "210"; ?>,
    "focus": [
      "Public Free Wifi",
      "Free internet access"
    ],
    "lastchange": "2014-07-07T15:30:09.147Z"
  },
  "nodeMaps": [
    {
      "url": "https://netmon.freifunk-ol.de/map.php",
      "interval": "10",
      "technicalType": "netmon",
      "mapType": "geographical"
    }
  ],
  "techDetails": {
    "firmware": {
      "url": "http://firmware.freifunk-ol.de",
      "name": "gluon"
    },
    "routing": [
      "batman-adv"
    ],
    "updatemode": [
      "manual",
      "autoupdate"
    ],
    "legals": [
      "vpnnational",
      "zappscript",
      "institutions"
    ]
  },
  "api": "0.4.0"
}
