#!/bin/bash
echo $PWD
mkdir -p "$PWD/data/logs/"
mkdir -p "$PWD/data/cache/"
chmod 644 "$PWD/data/logs"
chmod 644 "$PWD/data/cache"