#!/usr/bin/python
import pexpect
import sys,re,os,signal
server = '127.0.0.1'
user = 'syshack'
password = 'syshack@163.com'
ssh_newkey = 'Are you sure you want to continue connecting'
sshcmd = 'ssh ' + user + '@' + server
sshlogin = pexpect.spawn(sshcmd)
log = file('sshlogin.log','w')
sshlogin.logfile = log
index = sshlogin.expect([pexpect.TIMEOUT,ssh_newkey,"[pP]assword:"])
if index == 0:
        print 'Time Out'
	print sshlogin.before,sshlogin.after
if index == 1:
	sshlogin.sendline('yes')
	sshlogin.expect("[pP]assword:")
	index = sshlogin.expect([pexpect.TIMEOUT,"[pP]assword:"])
	if index == 0:
		print 'Time Out'
		print sshlogin.before,sshlogin.after
sshlogin.sendline(password)
try:
	sshlogin.interact()
	sys.exit(0)
except:
	sys.exit(1)
