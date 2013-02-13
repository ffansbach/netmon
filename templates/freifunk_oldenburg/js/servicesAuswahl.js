var serviceAuswahl = {
  'typ' : {
    'node' : ['node', 'node'],
    'vpn' : ['vpn', 'vpn'],
    'client' : ['client', 'client'],
    'service' : ['service', 'service']
  },

  'crawl' : {
    'node' : [
      ['json', 'Luci Status Informationen (empfohlen)'],
      ['ping', 'Ping']
    ],

    'vpn' : [
      ['json', 'Luci Status Informationen (empfohlen)'],
      ['ping', 'Ping']
    ],

    'client' : [
      ['ping', 'Ping (empfohlen)'],
      ['json', 'Luci Status Informationen']
    ],

    'service' : [
      ['port', 'Port (empfohlen)'],
      ['ping', 'Ping'],
      ['json', 'Luci Status Informationen (empfohlen)']
    ]
  }
};