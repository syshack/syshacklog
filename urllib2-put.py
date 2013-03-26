import urllib2
opener = urllib2.build_opener(urllib2.HTTPHandler)
request = urllib2.Request('http://example.org', data='your_put_data')
request.add_header('Content-Type', 'your/contenttype')
request.get_method = lambda: 'PUT' //用这种可以定义任何的http方法，比如很多网站需要的put，update//
url = opener.open(request)
