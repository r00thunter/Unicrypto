#!/bin/sh

DIR=$(cd "$( dirname "$0" )" && pwd)
while true; do
    if ! type "$nodejs" > /dev/null; then
		/usr/local/bin/nodejs $DIR/lib/js/sockets.js
	elif ! type "$node" > /dev/null; then
		/usr/local/bin/node $DIR/lib/js/sockets.js
	fi
done
