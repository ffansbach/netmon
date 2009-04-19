var serviceAuswahl = {
  'typ' : {
    'node' : ['node', 'node'],
    'vpn' : ['vpn', 'vpn'],
    'client' : ['client', 'client'],
    'service' : ['service', 'service']
  },

  'crawl' : {
    'node' : [
      ['json', 'json (Empfohlen)']
    ],

    'vpn' : [
      ['json', 'json (Empfohlen)'],
      ['ping', 'ping']
    ],

    'client' : [
      ['ping', 'ping (Empfohlen)'],
      ['json', 'json']
    ],

    'service' : [
      ['port', 'port (Empfohlen)'],
      ['ping', 'ping'],
      ['json', 'json']
    ]
  }
};