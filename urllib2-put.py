import urllib2
opener = urllib2.build_opener(urllib2.HTTPHandler)
request = urllib2.Request('http://example.org', data='your_put_data')
request.add_header('Content-Type', 'your/contenttype')
request.get_method = lambda: 'PUT'
url = opener.open(request)
