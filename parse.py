#!/bin/python
import re
f = open("syshacker-com.txt","r")
content = f.read()
titleRe = re.compile(r'<h2 class="title"><a.*>(.*)</a>')
parsedContent = titleRe.findall(content)
f.close
f1 = open("test","rw")
for i in parsedContent:
	print i;
f1.close

