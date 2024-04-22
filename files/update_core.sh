#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

rm -rf "$DIR/core"

var="trunk"

curl -o $var.zip -J -L "https://github.com/WordPress/wordpress-develop/archive/refs/heads/$var.zip"

unzip $var -d "$DIR/$var"

cp -R "$DIR/$var/wordpress-develop-$var/tests/phpunit/includes" "$DIR/core"

rm -rf $var
rm $var.zip
