#!/usr/bin/python
import mod_python, urllib

def index(req):
	req.content_type = 'text/xml'
	params = urllib.urlencode({"get":"getinfo", "section":"getgoogleearthkmlfile"})
	f = urllib.urlopen("http://freifunk-ol.de/netmon/index.php?%s" % params)
	return f.read()
