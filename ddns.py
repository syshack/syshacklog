#!/usr/bin/env python
#coding:utf-8
# Author:  syshack --<syshack@msn.com>
# Purpose: yet another pyddns
# Created: 2013/3/19

import socket
import httplib, urllib
try: import json
except: import simplejson as json
import re

class ApiCn:
    def __init__(self, email, password, **kw):
        self.base_url = "dnsapi.cn"
        
        self.params = dict(
            login_email=email,
            login_password=password,
            format="json",
        )
        self.params.update(kw)
        self.path = None
    
    def request(self, **kw):
        self.params.update(kw)
        if not self.path:
            """Class UserInfo will auto request path /User.Info."""
            name = re.sub(r'([A-Z])', r'.\1', self.__class__.__name__)
            self.path = "/" + name[1:]
        conn = httplib.HTTPSConnection(self.base_url)
        headers = {"Content-type": "application/x-www-form-urlencoded", "Accept": "text/json", "User-Agent": "ddns-python/v1.0 (syshack@msn.com)"}
        conn.request("POST", self.path, urllib.urlencode(self.params), headers)
        
        response = conn.getresponse()
        data = response.read()
        conn.close()
        ret = json.loads(data)
        if ret.get("status", {}).get("code") == "1":
            return ret
        else:
            raise Exception(ret)
    __call__ = request
    
class DomainList(ApiCn):
    pass

class _DomainApiBase(ApiCn):
    def __init__(self, domain_id, **kw):
        kw.update(dict(domain_id=domain_id))
        ApiCn.__init__(self, **kw)
        
class RecordList(_DomainApiBase):
    pass
class RecordDdns(_DomainApiBase):
    def __init__(self, record_id, sub_domain,record_line, **kw):
        kw.update(dict(
            record_id=record_id,
            sub_domain=sub_domain,
            record_line=record_line,
        ))
        _DomainApiBase.__init__(self, **kw)
def ddns():
    email = "syshack@163.com"
    password = "7758521"
    domain = "7eus.com"
    sub_domain = "pi"
    record_type = "A"
    record_line='默认'
    api = DomainList(email=email, password=password)
    for d in api().get('domains'):
        if d['name']==domain:
            domain_id=d['id']
    api=RecordList(domain_id,email=email, password=password)
    for r in api().get('records'):
        if r['name']==sub_domain and r['type']==record_type:
            record_id=r['id']    
    try:
        api = RecordDdns(record_id,sub_domain,record_line,domain_id=domain_id,email=email, password=password)
        print api()
        print "Update Sucess!"
    except Exception,e:
        print e
if __name__ == '__main__':
    ddns()
