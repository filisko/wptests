#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

declare -a array=("5.9" "6.0" "6.1" "6.2" "6.3" "6.4" "6.5")

for val in "${array[@]}"; do
    echo $val

    mkdir -p "$DIR/versions/$val"

    rm -rf "$DIR/core"

    curl -o $val.zip -J -L "https://github.com/WordPress/wordpress-develop/archive/refs/heads/$val.zip"

    unzip $val -d "$DIR/$val"

    cp -R "$DIR/$val/wordpress-develop-$val/tests/phpunit/includes" "$DIR/versions/$val"

    rm -rf $val
    rm $val.zip

done

