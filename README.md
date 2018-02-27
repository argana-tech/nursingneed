# Argana 看護必要度チェッカ―

## Development

Please install  
* [VirtualBox](https://www.virtualbox.org/)
* [Vagrant](https://www.vagrantup.com/)


```
$ git clone git@github.com:argana-tech/nursingneed.git

$ cd nursingneed/dev
$ vagrant up
```

When the virtual machine is up successfully, access to http://localhost:8080.

## SSHing to web server.

```
local$ cd dev
local$ vagrant ssh
argana$ docker exec -it argana-php /bin/bash
```

Source code is at /vagrant directory.


## SSHing to mysql server.

```
local$ cd dev
local$ vagrant ssh
argana$ docker exec -it argana-mysql /bin/bash
```
