#!/bin/bash

startme() {
    docker-compose -f .docker/docker-compose.yml --project-directory .docker up -d
}

stopme() {
    docker-compose -f .docker/docker-compose.yml --project-directory .docker down
}

rebuildme() {
    docker-compose -f .docker/docker-compose.yml --project-directory .docker build --no-cache --parallel
}

buildme() {
    docker-compose -f .docker/docker-compose.yml --project-directory .docker build --parallel
}

run() {
    docker exec -ti minimalism bash
}

case "$1" in
    start)   startme ;;
    stop)    stopme ;;
    run)    run ;;
    rebuild)    rebuildme ;;
    build) buildme;;
    restart) stopme; startme ;;
    *) echo "usage: $0 start|stop|restart|run|build|rebuild" >&2
       exit 1
       ;;
esac
